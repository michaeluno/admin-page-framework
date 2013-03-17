<?php 
	/*
		Name: Admin Page Framework
		Plugin URI: http://wordpress.org/extend/plugins/admin-page-framework/
		Author:  Michael Uno
		Author URI: http://michaeluno.jp
		Version: 1.0.2.3
		Description: Provides simpler means of building administration pages for plugin and theme developers. 
		Usage: 1. Extend the class 2. Override the SetUp() method. 3. Use the hook functions.
	*/
	
class Admin_Page_Framework {
	
	/*
	 * Default Properties
	 * */
	 
	// Action prefixes	
	protected $prefix_do_before			= 'do_before_';			// action hook triggered before rendering a page. c.f. do_before_ + page slug
	protected $prefix_do 				= 'do_';				// action hook triggered after rendering page contents.
	protected $prefix_do_after			= 'do_after_';			// action hook triggered after finishing rendering a page.
	protected $do_global_before			= 'do_before_';			// global action hook triggered before rendering a page. c.f. do_before_ + class name
	protected $do_global 				= 'do_';				// global action hook triggered after rendering page contents.
	protected $do_global_after			= 'do_after_';			// global action hook triggered after finishing rendering a page.			
	protected $prefix_start				= 'start_';				// action hook triggered at the end of the constructer.
	protected $prefix_do_form			= 'do_form_';			// action hook triggered after the opening form tag, since 1.0.2.
	
	// Filter prefixes - not private to be extensible
	protected $filter_global_head 		= 'head_';				// glboal filter for head part of the page.
	protected $filter_global_content 	= 'content_';			// glboal filter for body part of the page.
	protected $filter_global_foot 		= 'foot_';				// glboal filter for foot part of the page.
	protected $prefix_content 			= 'content_';			// filter for the body part of the page
	protected $prefix_head 				= 'head_';				// filter for head part of the page.
	protected $prefix_foot 				= 'foot_';				// filter for foot part of the page.
	protected $prefix_validation 		= 'validation_';		// filter for Settings API validation callback.
	protected $prefix_section 			= 'section_';			// filter for form sections.
	protected $prefix_field 			= 'field_';				// filter for form fields.
	protected $prefix_style				= 'style_';
	protected $prefix_script			= 'script_';
	protected $prefix_import			= 'import_';			// filter for an importing array, since 1.0.2
	protected $prefix_export			= 'export_';			// filter for an exporting array, since 1.0.2
	
	// Flags
	protected $bShowPageHeadingTabs = False;
	protected $bAddedCSSStyleAdjuster = False;	// indicates whether the custom stylesheet has been added into the header or not.
	protected $bIsImported = False;				// used to determine if the import file was uploaded and processed
	protected $bHasRegisteredSetting = False; 	// indicates whether the register_setting() has been used.
	
	// Containter arrays
	protected $arrSections = array();		// stores registerd form(settings) sections.
	protected $arrFields = array();			// stores registerd form(settings) fields.
	protected $arrTabs = array();			// a two-dimensional array with the keys of sub-page slug in the first dimension and with the key of tab slug in the second dimension.
	protected $arrHiddenTabs = array();		// since 1.0.2.1 - a two-dimensional array similar to the above $arrTabs but stores the tab which should be hidden ( still accessible with the direct url. )
	protected $arrPageTitles = array();		// stores the added page titles with key of the page slug.
	protected $arrIcons = array();			// stores the page screen 32x32 icons. For the main root page, which is invisible, 16x16 icon url will be stored.
	protected $arrCallerInfo = array();		// stores the caller script information.
	
	// For referencing
	protected $arrRootMenuSlugs = array(
		// all keys must be lower case to support caese insensitive lookups.
		'dashboard' => 			'index.php',
		'posts' => 				'edit.php',
		'media' => 				'upload.php',
		'links' => 				'link-manager.php',
		'pages' => 				'edit.php?post_type=page',
		'comments' => 			'edit-comments.php',
		'appearance' => 		'themes.php',
		'plugins' => 			'plugins.php',
		'users' => 				'users.php',
		'tools' => 				'tools.php',
		'settings' => 			'options-general.php',
		'network admin' => 		"network_admin_menu",
	);
	
	// Default values
	protected $strFormEncType = 'application/x-www-form-urlencoded';
	protected $strCapability = 'manage_options';	// can be changed with SetCapability().
	protected $strPageTitle = null;		// the extended class name will be assigned.
	protected $strPathIcon16x16 = null; // set by the constructor and the SetMenuIcon() method.
	protected $numPosition	= null;		// this will be rarely used so put it aside until a good reason gets addressed to flexibly change it.
	protected $strPageSlug = '';		// the extended class name will be assigned by default in the constructor but will be overwritten by the SetRootMenu() method.	
	protected $strOptionKey = null;		// determins which key to use to store options in the database.
	protected $numRootPages = 0;		// stores the number of created root pages 
	protected $numSubPages = 0;			// stores the number of created sub pages 
	protected $strDefaultPageLink = '';	// stores href url link string for the setting link in a plugin listing page.
	protected $strThickBoxTitle = '';	// stores the media upload thick box's window title.
	protected $strThickBoxButtonUseThis = '';	// stores the media upload thick box's button label to insert the image.
	protected $strStyle = '.updated, .settings-error { clear: both; }';	// the default css rules
	protected $strScript = '';			// the default JavaScript 
	protected $arrPluginDescriptionLinks = array();	// stores links which will be added to the description column in the plugin lising page.
	protected $arrPluginTitleLinks = array();	// stores links which will be added to the title column in the plugin lising page.
	protected $strCallerPath;	// stores the caller path which can be manually set by the user. If not set, the framework will try to set it. since 1.0.2.2

	// for debugs
	// protected $numCalled = 0;
	// protected $arrCallbacks = array();
	
	function __construct( $strOptionKey=null, $strCallerPath=null ){
		/*
		 * $strOptionKey :	Specifies the option key name to store in the option database table. 
		 * 					If this is set, all the options will be stored in an array to the key of this passed string.
		 * $strCallerPath :	used to retrieve the plugin ( if it's a plugin ) to retrieve the plugin data to auto-insert credit info into the footer.
		 * */
		
		// Do not set the extended class name for this. It uses a page slug name if not set.
		$this->strClassName 	= get_class( $this );
		$this->strOptionKey 	= ( empty( $strOptionKey ) ) ? null : $strOptionKey;	
		$this->strPageTitle 	= ( !empty( $strOptionKey ) ) ? $strOptionKey : $this->strClassName;
		$this->strPageSlug 		= $this->strClassName;	// will be invisible anyway
		$this->strCallerPath	= $strCallerPath;	
		
		// Schedule removing the root sub-menu because it will be just a duplicate item to the root label.
		add_action( 'admin_menu', array( $this, 'RemoveRootSubMenu' ), 999 );
		
		// Hook the menu action - adds the menu items.
		add_action( 'admin_menu', array( $this, 'SetUp' ) );
		
		// Hook the admin header to insert custom admin stylesheet.
		add_action( 'admin_head', array( $this, 'AddStyle' ) );
		add_action( 'admin_head', array( $this, 'AddScript' ) );

		// For the media uploader.
		add_filter( 'gettext', array( $this, 'ReplaceThickBoxText' ) , 1, 2 );	
		
		// Store the information of the caller file.
		$this->arrCallerInfo = $this->GetCallerInfo( $strCallerPath ); 	
		
		// Create global filter hooks
		add_filter( $this->filter_global_head 	 . $this->strClassName , array( $this, $this->filter_global_head . $this->strClassName ) );
		add_filter( $this->filter_global_content . $this->strClassName , array( $this, $this->filter_global_content . $this->strClassName ) );
		add_filter( $this->filter_global_foot    . $this->strClassName , array( $this, $this->filter_global_foot . $this->strClassName ) );		
		add_action( $this->do_global			 . $this->strClassName , array( $this, $this->do_global . $this->strClassName ) );
		add_action( $this->do_global_before		 . $this->strClassName , array( $this, $this->do_global_before . $this->strClassName ) );
		add_action( $this->do_global_after		 . $this->strClassName , array( $this, $this->do_global_after . $this->strClassName ) );
		add_action( $this->prefix_do_form		 . $this->strClassName , array( $this, $this->prefix_do_form . $this->strClassName ) );	// since 1.0.2
		
		// For earlier loading than $this->Setup
		add_action( $this->prefix_start	. $this->strClassName , array( $this, $this->prefix_start . $this->strClassName ) );
		do_action( $this->prefix_start	. $this->strClassName );
						
	}	
	
	/*
		Extensible Methods - should be customized in the extended class.
	*/
	protected function SetUp() {
		
		$this->CreateRootMenu( $this->strPageTitle );
		
	}
	
	/*
		Front-End methods - the user may call it but it shoud not necessaliry be customized in the extended class.
	*/
	/*
	 *	Add Links 
	 * */
	protected function AddLinkToPluginDescription( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->arrPluginDescriptionLinks[] = $vLinks;
		else
			$this->arrPluginDescriptionLinks = array_merge( $this->arrPluginDescriptionLinks , $vLinks );
		add_filter( 'plugin_row_meta', array( $this, 'AddLinkToPluginDescription_Callback' ), 10, 2 );

	}
	protected function AddLinkToPluginTitle( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->arrPluginTitleLinks[] = $vLinks;
		else
			$this->arrPluginTitleLinks = array_merge( $this->arrPluginTitleLinks, $vLinks );
		add_filter( 'plugin_action_links_' . $this->GetCallerPluginBaseName() , array( $this, 'AddLinkToPluginTitle_Callback' ) );

		}
	/*
	 * Add Menu and pages
	 * */
	protected function SetRootMenu( $strRootMenu, $strPathIcon16x16=null ) {
		
		$strRootMenu = trim( $strRootMenu );
				
		// Check if it is one of the default menu.
		if ( array_key_exists( strtolower( $strRootMenu ) , $this->arrRootMenuSlugs ) ) {

			// do not use SetRootMenuBySlug since options-general contains a hyphen and it should not be converted to underscore.
			$this->strPageSlug = $this->arrRootMenuSlugs[ strtolower( $strRootMenu ) ];
			return $this->strPageSlug;
			
		}

		// If it does not match the existent menus.
		// Use the class name as the slug name.
		$this->strPageSlug = $this->strClassName;
		$this->CreateRootMenu( $strRootMenu, $strPathIcon16x16 );

		return $this->strPageSlug;
		
	}
	protected function SetRootMenuBySlug( $strSlug ) {
		
		$this->strPageSlug = $strSlug;
		
	}
	protected function ShowPageHeadingTabs( $bShowPageHeadingTabs ) {
		
		// Sets whether the tab in the top part of the page should be visible or not visible. True / False.
		$this->bShowPageHeadingTabs = $bShowPageHeadingTabs;
		
	}
	protected function SetRootMenuPosition( $numPosition ) {
		
		$this->numPosition = $numPosition;			
		
	}
	protected function SetCapability( $strCapability ) {
		
		// Sets the access right to the menu. This also can be set in the constructor.
		$this->strCapability = $strCapability;
		
		// This lets the Settings API to allow the custom capability. The Settings API requires manage_options by default.
		// the option_page_capability_{} filter is supported since WordPress 3.2 
		add_filter( "option_page_capability_{$this->strPageSlug}", array( $this, 'GetCapability' ) );

	}
	public function GetCapability( $strCapability ) {

		return $this->strCapability;
		
	}
	protected function SetMenuIcon( $strPathIcon16x16 ) {
		// Sets the menu icon.  This also can be set in the constructor.
		$this->strPathIcon16x16 = $strPathIcon16x16;
		$this->arrIcons[ $this->strPageSlug ] = $strPathIcon16x16;
	}
	protected function HideInPageTab( $strSubPageSlug, $strTabSlug, $strAltTab='' ) {
		
		// Just hides the in-page tab link; the page will be still accessible by the direct url.
		// If $strAltTab is set, the given tab will be rendered as activated in stead of the hidden tab.
		// since 1.0.2.1
		
		$this->arrHiddenTabs[ $strSubPageSlug ][ $strTabSlug ] = $strAltTab;
		// if ( isset( $this->arrTabs[ $strSubPageSlug ][ $strTabSlug ] ) )
			// unset( $this->arrTabs[ $strSubPageSlug ][ $strTabSlug ] );
		
	}
	protected function AddInPageTabs( $strSubPageSlug, $arrTabs ) {

		// Sanitize the slug strings in array keys. c.f. - => _
		$arrTabs = $this->SanitizeArrayKeys( $arrTabs );			
		$strSubPageSlug = $this->SanitizeSlug( $strSubPageSlug );
	
		// Adds in-page tab, which does not have a menu.
		$this->arrTabs[ $strSubPageSlug ] = $arrTabs;	
		
		// add hooks for in-page tabs
		foreach ( $arrTabs as $strTabSlug => $strTabTitle ) {
			
			// filters
			add_filter( $this->prefix_content	. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_content		. $strSubPageSlug . '_' . $strTabSlug ) );
			add_filter( $this->prefix_head		. $strSubPageSlug . '_' . $strTabSlug, 	array( $this, $this->prefix_head		. $strSubPageSlug . '_' . $strTabSlug ) );
			add_filter( $this->prefix_foot		. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_foot		. $strSubPageSlug . '_' . $strTabSlug ) );					
			
			// actions
			add_action( $this->prefix_do_before	. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_do_before	. $strSubPageSlug . '_' . $strTabSlug ) );
			add_action( $this->prefix_do		. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_do			. $strSubPageSlug . '_' . $strTabSlug ) );
			add_action( $this->prefix_do_after	. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_do_after	. $strSubPageSlug . '_' . $strTabSlug ) );			
			add_action( $this->prefix_do_form	. $strSubPageSlug . '_' . $strTabSlug,	array( $this, $this->prefix_do_form		. $strSubPageSlug . '_' . $strTabSlug ) );	// since 1.0.2

		}
	}

	protected function CreateRootMenu( $strTitle, $strPathIcon16x16=null ) {
		
		$strPathIcon16x16 = ( $strPathIcon16x16 ) ? $strPathIcon16x16 : $this->strPathIcon16x16;
		add_menu_page(  
			$this->strPageTitle,					// Page title - will be invisible anyway
			$strTitle,								// Menu title 
			$this->strCapability,					// Capability - accsess right
			$this->strPageSlug,						// Menu ID 
			array( $this, $this->strPageSlug ), 	// Menu display function	
			$strPathIcon16x16,						// icon
			empty( $this->numPosition ) ? null : $this->numPosition 	// menu position
		);
					
		// Store the page title and icon, and how many times a top level page has been created.
		$this->numRootPages++;
		$this->arrPageTitles[ $this->strPageSlug ] 	= trim( $this->strPageTitle );
		$this->arrIcons[ $this->strPageSlug ]			= $strPathIcon16x16;
		
		// Add a setting link in the plugin listing 
		if ( $this->arrCallerInfo['type'] == 'plugin' )
			add_filter( 'plugin_action_links_' . $this->GetCallerPluginBaseName() , array( $this, 'AddLinkInPluginListingPage' ) );
	
	}	
	protected function AddSubMenu( $strSubTitle, $strPageSlug, $strPathIcon32x32=null, $strCapability=null ) {
	
		$strCapability = isset( $strCapability ) ? $strCapability : $this->strCapability;
		
		if ( ! current_user_can( $strCapability ) ) return;
		
		// add the sub-menu and sub-page
		$strPageSlug = $this->SanitizeSlug( $strPageSlug );	// - => _, . => _
		add_submenu_page( 
			trim( $this->strPageSlug )			// $parent_slug
			, $strSubTitle						// $page_title
			, $strSubTitle						// $menu_title
			, $strCapability				 	// $strCapability
			, $strPageSlug						// $menu_slug
			, array( $this, $strPageSlug ) 
		);	// triggers the __Call method with the method name of this slug.
						
		// Set the default page link so that it can be referred from the methods add the link to the page including AddLinkInPluginListingPage()
		if ( $this->numRootPages == 1 && $this->numSubPages == 0 )	// means only the single root menu has been added so far
			$this->strDefaultPageLink = trim( $strPageSlug );	
			
		if ( $this->numRootPages == 0 && $this->numSubPages == 0 ) { // means that this is the first time adding a page and it belongs to an existent page.
		
			// Add a setting link in the plugin listing 
			$this->strDefaultPageLink = trim( $strPageSlug ) ;		// $this->strPageSlug should have been assigned the top level menu slug in SetRootMenu().
			if ( $this->arrCallerInfo['type'] == 'plugin' )
				add_filter( 'plugin_action_links_' . $this->GetCallerPluginBaseName() , array( $this, 'AddLinkInPluginListingPage' ) );
		
		}	
		
		// Modify the admin footer to add the plugin name and the version.
		add_filter( 'update_footer', array( $this, 'AddInfoInFooterRight' ), 11 );
		if ( isset( $this->strCallerPath ) )
			add_filter( 'admin_footer_text' , array( $this, 'AddInfoInFooterLeft' ) );
		
		// Store the page title and icon
		$this->numSubPages++;
		$this->arrPageTitles[ $strPageSlug ] = trim( $strSubTitle );
		$this->arrIcons[$strPageSlug] = $strPathIcon32x32;	// if it is not set, screen_icon() will be used.
		
		// hook the filters for the page output
		add_filter( $this->prefix_content	. $strPageSlug , array( $this, $this->prefix_content	. $strPageSlug ) );
		add_filter( $this->prefix_head		. $strPageSlug , array( $this, $this->prefix_head		. $strPageSlug ) );
		add_filter( $this->prefix_foot		. $strPageSlug , array( $this, $this->prefix_foot		. $strPageSlug ) );
		add_action( $this->prefix_do		. $strPageSlug , array( $this, $this->prefix_do			. $strPageSlug ) );
		add_action( $this->prefix_do_before	. $strPageSlug , array( $this, $this->prefix_do_before	. $strPageSlug ) );
		add_action( $this->prefix_do_after	. $strPageSlug , array( $this, $this->prefix_do_after	. $strPageSlug ) );
		add_action( $this->prefix_do_form	. $strPageSlug , array( $this, $this->prefix_do_form	. $strPageSlug ) );	// since 1.0.2
	
		// if this is a Settings API loading page behind the scene, which is options.php, do not register unnecessary callbacks.
		if ( isset( $_POST['pageslug'] ) && $_POST['pageslug'] != $strPageSlug ) return;	
		
		$strOptionName = ( empty( $this->strOptionKey ) ) ? $strPageSlug : $this->strOptionKey;
		register_setting(	
			$this->strClassName,	     	// the caller class name to be the option group name.
			$strOptionName,				// the option key name stored in the option table in the database.
			array( $this, $this->prefix_validation . 'pre_' . $strPageSlug )	  // validation method	
		);  
		
	}
	protected function AddFormSections( $arrSections ) {
	
		/*
		 * Adds Form Section for Settings API for pages created with this class.
		 * Slug name must be consist of alphabets and underscores. 
		 * */
		/* e.g. root dimension: numeric keys, second dimension: must have 'id' and 'title' keys. The 'description' key is optional.
		$arrSections = 
			array( 	
				array( 'pageslug'=>, 'my_first_page', 'id' => 'pageslug_section_a', 'title' => 'Section A', 'description' => 'This is Section A.' ),
				array( 'pageslug'=>, 'my_first_page', 'id' => 'pageslug_section_b', 'title' => 'Section B' ),
			);

		*/			
		
		foreach( ( array ) $arrSections as $index => $arrSection ) {
			
			// These slugs specify where this section belongs but the used characters have to be sanitized (without dots and hypens) for the callback functions.
			// Therefore, it is not possible to support pages using dots and hyphens. That means only pages created by this class should be the ones specified by 
			// this method.
			
			// Set keys in case some are not set - this prevents PHP invalid (undefined) index warnings
			$arrSection = ( array ) $arrSection + array( 
				'pageslug' => null,
				'tabslug' => null,
				'title' => null,
				'description' => null,
				'id' => null,
				'fields' => null,
				'capability' => null,				
			);

			$arrSection['pageslug'] = $this->SanitizeSlug( $arrSection['pageslug'] );	
			$arrSection['tabslug'] = $this->SanitizeSlug( $arrSection['tabslug'] );
			
			// If the page slug does not match the current loading page, there is no need to register form sections and fields.
			$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
			if ( !$strCurrentPageSlug || (string) $strCurrentPageSlug !== (string) $arrSection['pageslug'] ) continue;	
			
			// If the tab slug is specified, determine if the current page is the default tab or the current tab matches the given tab slug.
			if ( !$this->IsTabSpecifiedForFormSection( $arrSection ) ) continue;		

			// If the access level is set and it is not sufficient, skip.
			if ( isset( $arrSection['capability'] ) && ! current_user_can( $arrSection['capability'] ) ) continue;	// since 1.0.2.1
			
			// Store the registered sections internally in the class object.
			$this->arrSections[$arrSection['id']] = array(  
				'title' => $arrSection['title'],
				'pageslug' => $arrSection['pageslug'],
				'description' => $arrSection['description']
			);
			
			// Add the given section
			add_settings_section( 	
				$arrSection['id'],
				$arrSection['title'],
				array( $this, $this->prefix_section . 'pre_' . $arrSection['id'] ),  // callback function
				$arrSection['pageslug'] 
			);
			
			// Add the given form fields
			if ( is_array( $arrSection['fields'] ) ) 
				$this->AddFormFields(	
					$arrSection['pageslug'],
					$arrSection['id'],
					$arrSection['fields']
				);	
			
		}
	}

	/*
		Back-end methods - the user may not use these method unless they know what they are doing and what these methods do.
	*/
	protected function GetCallerInfo( $strFilePath ) {
		
		// since 1.0.2.2
		$arrDebugBacktrace = debug_backtrace();
		
		$arrCallerInfo = array();
		$arrCallerInfo['file'] = $strFilePath ? $strFilePath : $arrDebugBacktrace[0]['file'];
		$arrCallerInfo['type'] = $this->GetCallerType( $arrCallerInfo['file'] );
		
		if ( $arrCallerInfo['type'] == 'plugin' ) {
			
			if ( ! function_exists( 'get_plugin_data' )  ) 
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			$arrCallerInfo['data'] = get_plugin_data( $arrCallerInfo['file'], false );	// stores the plugin info array
			$arrCallerInfo['data']['ScriptURI'] = $arrCallerInfo['data']['PluginURI'];
			
		} else if ( $arrCallerInfo['type'] == 'theme' ) {

			if ( ! function_exists( 'wp_get_theme' )  ) 
				require_once( ABSPATH . 'wp-admin/includes/theme.php' );
		
			$oTheme = wp_get_theme();	// stores the theme info object
			$arrCallerInfo['data'] = array(
				'Name'			=> $oTheme->Name,
				'Version' 		=> $oTheme->Version,
				'ThemeURI'		=> $oTheme->get( 'ThemeURI' ),
				'ScriptURI'		=> $oTheme->get( 'ThemeURI' ),
				'AuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'Author'		=> $oTheme->get( 'Author' ),				
			);
			
		}
				
		return $arrCallerInfo;
		
	}
	protected function GetCallerType( $strPath ) {
		
		// since 1.0.2.2
		// Determines what kind of script this is, theme, plugin or something else from the given path.
		// Returns either 'theme', 'plugin', or 'unknown'
		
		if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $strPath, $matches ) )
			return 'theme';
			
		if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $strPath, $matches ) )
			return 'plugin';
			
		return 'unknown';
		
	}
	public function AddInfoInFooterLeft( $strText ) {  // method used by hooks should be public
		
		// since 1.0.2.2
		// callback for the filter hook, admin_footer_text.
		
		if ( ! isset( $_GET['page'] ) || ! array_key_exists( $_GET['page'] , $this->arrPageTitles )  ) return $strText;
		 
		$strPluginInfo = $this->arrCallerInfo['data']['Name'] . ' ' . $this->arrCallerInfo['data']['Version'];
		$strPluginInfo = empty( $this->arrCallerInfo['data']['ScriptURI'] ) ? $strPluginInfo : '<a href="' . $this->arrCallerInfo['data']['ScriptURI'] . '">' . $strPluginInfo . '</a>';
		$strAuthorInfo = empty( $this->arrCallerInfo['data']['AuthorURI'] )	? $this->arrCallerInfo['data']['Author'] : '<a href="' . $this->arrCallerInfo['data']['AuthorURI'] . '">' . $this->arrCallerInfo['data']['Author'] . '</a>';
		$strAuthorInfo = empty( $this->arrCallerInfo['data']['Author'] ) ? $strAuthorInfo : 'by ' . $strAuthorInfo;
		return $strPluginInfo . ' ' . $strAuthorInfo;			

	}
	public function AddInfoInFooterRight( $strText ) {	// method used by hooks should be public
	
		// Adds plugin info into the footer
		// since 1.0.2.2
		
		if ( ! isset( $_GET['page'] ) || ! array_key_exists( $_GET['page'] , $this->arrPageTitles )  ) return $strText;
		
		return __( 'Powered by <a href="http://wordpress.org/extend/plugins/admin-page-framework/">Admin Page Framework</a>' )
			. ', ' . '<a href="http://wordpress.org">WordPress</a>';
		
	}	
	public function AddLinkToPluginDescription_Callback( $arrLinks, $strFile ) {	// this is a callback method so should not be protected

		if ( $strFile != $this->GetCallerPluginBaseName() ) return $arrLinks;
		return array_merge( $arrLinks, $this->arrPluginDescriptionLinks );
		
	}	
	public function AddLinkToPluginTitle_Callback( $arrLinks ) {	// A callback method should not be protected.
		
		return array_merge( $arrLinks, $this->arrPluginTitleLinks );
	
	}
	public function AddLinkInPluginListingPage( $arrLinks ) {		// this is a callback method so should not be protected	
	
		array_unshift(	
			$arrLinks,
			'<a href="admin.php?page=' . $this->strDefaultPageLink . '">' . __( 'Settings', 'admin-page-framework' ) . '</a>'
		); 
		return $arrLinks;
		
	}	
	function IsTabSpecifiedForFormSection( $arrSection ) {
		
		// Determine: 
		// 1. if the current page is the default tab. Yes -> the section should be registered.
		// 2. if the current tab matches the given tab slug. Yes -> the section should be registered.
			
		// If the $_GET['tab'] is not set and the page slug is stored in the tab array, 
		// consider the default tab which should be loaded without the tab query value in the url
		if ( !isset( $_GET['tab'] ) && isset( $this->arrTabs[$arrSection['pageslug']] ) ) {
		
			// Get the first element stored in the $this->arrTabs[$strPageSlug] array
			foreach ( $this->arrTabs[$arrSection['pageslug']] as $strTabSlug => $strTabTitle ) {
				$strDefaultTabSlug = $strTabSlug; 
				break;
			}
			if ( (string) $strDefaultTabSlug === (string) $arrSection['tabslug'] ) return true;		// should be registered.			
		}
		
		// If the checking tab slug and the current loading tab slug is the same, it should be registered.
		$strCurrentTab =  isset( $_GET['tab'] ) ? $_GET['tab'] : null;
		if ( (string) $arrSection['tabslug'] === (string) $strCurrentTab ) return true;
		
	}	
	protected function SetFormEncType( $strEncType="application/x-www-form-urlencoded" ) {
		// Sets the form tag attribute of enctype. 
		// Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
		$this->strFormEncType = $strEncType;
	}
	protected function AddFormFields( $strPageSlug, $strSectionID, $arrFields ) {
	
		/* e.g. root dimension: numeric keys, second dimension: must have 'id' and 'title' keys.
		$arrFields = array(
						array( 'id' => 'section_a_field_a', 'title' => 'Option A' ),
						array( 'id' => 'section_a_field_b', 'title' => 'Option B' )
					);
		*/
		$strPageSlug = $this->SanitizeSlug( $strPageSlug );	// - => _, . => _
		foreach( ( array ) $arrFields as $index => $arrField ) {
			
			// The id and type keys are mandatory.
			if ( !isset( $arrField['id'] ) || !isset( $arrField['type'] ) ) continue;		
			
			// If the access level is not sufficient, skip.
			if ( isset( $arrField['capability'] ) && ! current_user_can( $arrField['capability'] ) ) continue;	// since 1.0.2.1
			
			// Sanitize the id since it is used as a callback method name.
			$arrField['id'] = $this->SanitizeSlug( $arrField['id'] );
			
			// If the input type is specified to file, set the enctype to 'multipart/form-data'
			if ( in_array( $arrField['type'], array( 'file', 'import', 'image' ) ) ) $this->strFormEncType = 'multipart/form-data';
			if ( $arrField['type'] == 'image' ) {
				
				// These two hooks should be enabled when the image field type is added in the field array.
				$this->strThickBoxTitle = isset( $arrField['label']['title'] ) ? $arrField['label']['title'] : __( 'Upload Image', 'admin-page-framework' );
				$this->strThickBoxButtonUseThis = isset( $arrField['label']['insert'] ) ? $arrField['label']['insert'] : __( 'Use This Image', 'admin-page-framework' ); 
				add_action( 'admin_enqueue_scripts', array( $this, 'EnqueUploaderScripts' ) );	// called later than the admin_menu hook		
				
				// Append the script
				$strFieldID = $arrField['id'];
				$strPreviewCSSRule = '{display:inline, border: none, max-width:100%}';
				$this->strScript .= "
					var field_id = '';
					jQuery(document).ready(function($){
						$('#upload_image_button_{$strFieldID}').click( function() {
							field_id = '{$strFieldID}';
							tb_show('{$this->strThickBoxTitle}', 'media-upload.php?referer={$strPageSlug}&amp;button_label={$this->strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true&amp;post_id=0', false );
							return false;	// do not click the button after the script by returning false.
						});
						$('#unset_image_button_{$strFieldID}').click( function() {
							var this_field_id = '{$strFieldID}';
							$( '#image_url_' + this_field_id ).val( '' );	// sets an empty value in the field value (not saved yet).
							$( '#update_preview_' + this_field_id + ' img' ).hide();
							$( '#upload_image_preview_url_' + this_field_id ).text( '' );	// sets the url of the preview image.
							return false;	// do not click the button after the script by returning false.
						});
						window.send_to_editor = function(html) {
							var image_url = $('img',html ).attr( 'src' );
							$( '#image_url_' + field_id ).val( image_url );	// sets the preview image in the field value (not saved yet).
							$( '#upload_image_preview_url_' + field_id ).text( image_url );	// sets the url of the preview image.
							$( '#delete_image_button_' + field_id ).show();	// shows the button
							$( '#unset_image_button_' + field_id ).show();	// shows the button
							tb_remove();	// close the thickbox
							$( '#update_preview_' + field_id + ' img' ).attr( 'src',image_url );	// updates the preview image
							$( '#update_preview_' + field_id + ' img' ).show()	//( 'style','display: inline; border: none; max-width: 100%;' );	// updates the visibility
							// $( '.submit_options_form' ).trigger( 'click' );		// presses the form button 
						}
					});";
			} 
			 
			// Store the registered field internally in the class object.
			$arrField = $arrField + array(
				'title' => '',
				'description' => '',
			);
			$this->arrFields[$arrField['id']] = $arrField + array(  
				'field_title'	=> $arrField['title'], 
				'page_slug' 	=> $strPageSlug,
				'field_ID' 		=> $arrField['id'],
				'section_ID' 	=> $strSectionID,
			);		
			
			add_settings_field( 
				$arrField['id'],
				'<span title="' . strip_tags( isset( $arrField['tip'] ) ? $arrField['tip'] : $arrField['description'] ) . '">' . $arrField['title'] . '</span>',
				array( $this, $this->prefix_field . 'pre_' . $arrField['id'] ),	// callback function
				$strPageSlug,
				$strSectionID,
				$this->arrFields[$arrField['id']] 
			);				
		}
	}	
	function RemoveRootSubMenu() {
		remove_submenu_page( $this->strClassName, $this->strClassName );
	}
	/*
	 * Settings API Related
	 * */
	function RenderSectionDescription( $strMethodName ) {

		// renders the section description and apply the filter to be extensible.
		$strSectionID = substr( $strMethodName, strlen( $this->prefix_section ) + 4 );	// section_pre_X
		if ( !isset( $this->arrSections[$strSectionID] ) ) return;	// if it is not added
		$strDescription = '<p>' . $this->arrSections[$strSectionID]['description'] . '</p>';
		
		add_filter( $this->prefix_section . $strSectionID , array( $this, $this->prefix_section . $strSectionID ), 10, 2 );
		echo apply_filters( $this->prefix_section . $strSectionID, $strDescription, $this->arrSections[$strSectionID]['description'] );	// the p-tagged description string and the original description is passed.
	}
	function RenderFormField( $strMethodName, $arrField ) {

		// renders the pre-defined (defined by this class as the default form field) form field by form type.
		$strFieldID = substr( $strMethodName, strlen( $this->prefix_field ) + 4 );	// field_pre_X
		if ( !isset( $this->arrFields[$strFieldID] ) ) return;	// if it is not added, return
		
		// Set up the option array - case 1. option key is specified. case 2 not specified in the constructor, then use the page slug as the key.
		$arrOptions = (array) get_option( ( empty( $this->strOptionKey ) ) ? $arrField['page_slug'] : $this->strOptionKey );
		if ( !empty( $this->strOptionKey ) ) {	// if the custom option key is set by the user,				
			$arrOptions = isset( $arrOptions[$arrField['page_slug']] ) ? $arrOptions[$arrField['page_slug']] : array();
		}
	
		// the 'settings-updated' key will be set in the $_GET array when redirected by Settings API 
		$arrErrors = get_transient( md5( $this->strClassName . '_' . $arrField['page_slug'] ) );
		$arrErrors = ( isset( $_GET['settings-updated'] ) &&  $arrErrors ) ? $arrErrors  : null;		
		$strOutput = $this->GetFormFieldsByType( $arrOptions, $arrField, $arrErrors ); 

		// Render the input field
		add_filter( $this->prefix_field . $arrField['field_ID'] , array( $this, $this->prefix_field . $arrField['field_ID'] ), 10, 2 );
		echo apply_filters( $this->prefix_field . $arrField['field_ID'], $strOutput, $arrField );	// the output and the field array is passed 

	}
	function GetFormFieldsByType( &$arrOptions, &$arrField, &$arrErrors=null ) {
		/* 
		 * Tag Attributes:
		 * 	name	: the key name of the $_POST, $_GET, or $_FILES array. $strFieldName will be assigned. 
		 * 			  For example, if $strFieldName is mysection[myfield], then the $_POST will have the key
		 * 			  $_POST["mysection"]["myfield"].
		 *  value	: the value which will be stored in the avove key in the $_POST, $_GET, or $_FILES array.
		 *  type	: determins the type of input field. For textarea and select, the mentioned tags will be created instead of the input tag.
		 *  class	: the class of CSS style. 
		 * 
		 * $arrField keys:
		 *  label	: this is used to construct and render elements .
		 *  default : this is similar to the above label key but used to specify the default values.
		 * 
		 * and more... ( I don't have the time to document all :/ )
		 * */
		// Avoid unset index warnings
		$arrField = $arrField + array(
			'class' => null,
			'description' => null,
			'default' => null,
			'label' => null,
			'error' => null,
			'file_name' => null,	// used by the export custom field 
			'transient' => null,	// used by the export custom field to look up exporting data, since 1.0.2
			'option_key' => null,
			'selectors' => null,
			'disable' => null,
			'max' => null,
			'min' => null,
			'step' => null,
			'pre_html' => null,
			'post_html' => null,
			'value' => null,
			'delimiter' => '<br />', // used by filed types which accept label as array and this delimiter value will be used to delimit the elements, since 1.0.2
			'update_message' => null,	// used by the import custom field
			'error_message' => null,	// used by the import custom field
			'capability' => null,	// since 1.0.2.1, used to determine whether the field should be displayed to the user; this should not be used in this method but the AddFormFields() method
		);
					
		// $strValue - the retrieved value from the database option table in which currently saved 
		$strValue = isset( $arrOptions[$arrField['section_ID']][$arrField['field_ID']] ) ? $arrOptions[$arrField['section_ID']][$arrField['field_ID']] : null;
		if ( $strValue === null && isset( $arrField['default'] ) ) $strValue = $arrField['default'];
		$strValue = isset( $arrField['value'] ) ? $arrField['value'] : $strValue;	// override the value if it is explicitly set

		$bIsDisabled = is_array( $arrField['disable'] ) ? $arrField['disable'] : ( $arrField['disable'] ? 'disabled="Disabled"' : '' );
		
		// $strFieldName - case 1: the option key is set, case 2: the option key is not set by the user and the page slug is used 
		$tmp = $this->strOptionKey;	// something looking like a bug occurs with the direct assignment in the ternary below.
		$strOptionKey = empty( $this->strOptionKey ) ? $arrField['page_slug'] : $tmp;	// it seems a bug occurs without assigning to a different variable
		$strFieldName = empty( $this->strOptionKey ) ? 
			"{$strOptionKey}[{$arrField['section_ID']}][{$arrField['field_ID']}]" :
			"{$strOptionKey}[{$arrField['page_slug']}][{$arrField['section_ID']}][{$arrField['field_ID']}]";
		$strOptionKeyForReference = empty( $this->strOptionKey ) ? 
			"{$strOptionKey}|{$arrField['section_ID']}|{$arrField['field_ID']}" :
			"{$strOptionKey}|{$arrField['page_slug']}|{$arrField['section_ID']}|{$arrField['field_ID']}";
		$strTagID = "{$arrField['section_ID']}_{$arrField['field_ID']}";
		
		// Error message handling
		$strOutput = isset( $arrErrors[ $arrField['section_ID'] ][ $arrField['field_ID'] ] ) ? '<span style="color:red;">*&nbsp;' . $arrField['error'] . $arrErrors[$arrField['section_ID']][$arrField['field_ID']] . '</span><br />' : '';
		// $strOutput = isset( $arrErrors[$arrField['page_slug']][$arrField['section_ID']][$arrField['field_ID']] ) ? '<span style="color:red;">*&nbsp;' . $arrField['error'] . '</span><br />' : '';
		
		// Start diverging
		switch ( $arrField['type'] ) {
			case 'text':
			case 'password':
			case 'color':
			case 'date':    
			case 'datetime':
			case 'datetime-local':
			case 'email':
			case 'month':
			case 'search':
			case 'tel':
			case 'time':
			case 'url':
			case 'week':	// attributes: size					
				$arrField['size'] = empty( $arrField['size'] ) ? 30 : $arrField['size']; 
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' name='{$strFieldName}' size='{$arrField['size']}' type='{$arrField['type']}' value='{$strValue}' {$bIsDisabled} />"; 			
				break;
			case 'number':	// HTML5, attributes: min, max, step,
			case 'range':						
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' name='{$strFieldName}' min='{$arrField['min']}' max='{$arrField['max']}' step='{$arrField['step']}' type='{$arrField['type']}' value='{$strValue}' {$bIsDisabled} />";
				break;
			case 'textarea':	// attributes: rows, cols
				$arrField['rows'] = empty( $arrField['rows'] ) ? 4 : $arrField['rows'];
				$arrField['cols'] = empty( $arrField['cols'] ) ? 80 : $arrField['cols'];
				$strOutput .= "<textarea id='{$strTagID}' class='{$arrField['class']}' name='{$strFieldName}' rows='{$arrField['rows']}' cols='{$arrField['cols']}' {$bIsDisabled} >{$strValue}</textarea>";
				break;	
			case 'radio':
				$strOutput .= "<div id='{$strTagID}'>";
				foreach ( $arrField['label'] as $strKey => $strLabel ) {
					$strChecked = ( $strValue == $strKey ) ? 'Checked' : '';
					$strOutput .= "<input id='{$strTagID}_{$strKey}' class='{$arrField['class']}' type='radio' name='{$strFieldName}' value='{$strKey}' {$strChecked}  {$bIsDisabled}>&nbsp;&nbsp;{$strLabel}";
					$strOutput .= $arrField['delimiter'];
				}
				$strOutput .= "</div>";
				break;
			case 'checkbox':	// support multiple creation with array of label
				if ( is_array( $arrField['label'] ) ) {
					$arrValues = ( array ) $strValue;
					$strOutput .= "<div id='{$strTagID}'>";
					foreach ( $arrField['label'] as $strKey => $strLabel ) {	
						$strChecked = ( $arrValues[$strKey] == 1 ) ? 'Checked' : '';
						$strOutput .= "<input type='hidden' name='{$strFieldName}[{$strKey}]' value='0' />";
						$strOutput .= "<input id='{$strTagID}_{$strKey}' class='{$arrField['class']}' type='checkbox' name='{$strFieldName}[{$strKey}]' value='1' {$strChecked} {$bIsDisabled} />&nbsp;&nbsp;{$strLabel}";
						$strOutput .= $arrField['delimiter'];
					}
					$strOutput .= "</div>";
					break;
				}
				// if the labels key is not an array,
				$strChecked = ( $strValue == 1 ) ? 'Checked' : '';			
				$strOutput .= "<input type='hidden' name='{$strFieldName}' value='0' />";
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' type='checkbox' name='{$strFieldName}' value='1' {$strChecked} {$bIsDisabled} />&nbsp;&nbsp;{$arrField['label']}<br />";
				break;
			case 'select':
				if ( !is_array( $arrField['label'] ) ) break;	// the label key must be an array for the select type.
				$strOutput .= "<select id='{$strTagID}' class='{$arrField['class']}' name='{$strFieldName}' {$bIsDisabled}>";
				foreach ( $arrField['label'] as $strKey => $strLabel ) {
					$strSelected = ( $strValue == $strKey ) ? 'Selected' : '';
					$strOutput .= "<option id='{$strTagID}_{$strKey}' value='{$strKey}' {$strSelected}>{$strLabel}</option>";
				}
				$strOutput .= "</select>";
				break;
			case 'hidden':	// support multiple creation with array of label
				if ( is_array( $arrField['label'] ) ) {
					$strOutput .= "<div id='{$strTagID}'>";
					foreach( $arrField['label'] as $strArrayKey => $strArrayValue ) {
						$strKey = 	isset( $arrField['default'][$strArrayKey] ) 	? $arrField['default'][$strArrayKey] : $strArrayKey;
						$strValue = isset( $arrField['default'][$strArrayValue] ) ? $arrField['default'][$strArrayValue] : $strArrayValue;
						$strValue = isset( $arrField['value'][$strArrayValue] ) ? $arrField['value'][$strArrayValue] : $strValue;
						$strOutput .= "<input id='{$strTagID}_{$strKey}' class='{$arrField['class']}' name='{$strFieldName}[{$strKey}]' type='hidden' value='{$strValue}' />";
					}
					$strOutput .= "</div>";
					break;
				}
				$strValue = isset( $arrField['value'] ) ? $arrField['value'] : $arrField['label'];
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' name='{$strFieldName}' type='hidden' value='{$strValue}' />";
				break;					
			case 'file':	// support multiple creation with array of label
				// $strName = ( isset( $arrField['name'] ) && !empty( $arrField['name'] ) ) ? $arrField['name'] : 'file';
				if ( is_array( $arrField['label'] ) ) {
					$strOutput .= "<div id='{$strTagID}'>";
					foreach( $arrField['label'] as $strKey => $strValue ) 
						$strOutput .= "<input id='{$strTagID}_{$strKey}' class='{$arrField['class']}' type='file' name='{$strFieldName}[{$strValue}]' />";
					$strOutput .= "</div>";
					break;
				}						
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' type='file' name='{$strFieldName}' {$bIsDisabled}/>";
				break;
			case 'submit':	// support multiple creation with array of label
				$strClass = ( $arrField['class'] ) ? $arrField['class'] : 'button button-primary';
				if ( is_array( $arrField['label'] ) ) {
					$strOutput .= "<div id='{$strTagID}'>";
					foreach( $arrField['label'] as $strArrayKey => $strArrayValue ) {
						$strLabel = ( $strArrayValue ) ? $strArrayValue : __( 'Submit' );
						$strOutput .= "<input id='{$strTagID}_{$strArrayKey}' class='{$strClass}' name='{$strFieldName}[{$strArrayKey}]' type='submit' value='{$strLabel}' {$bIsDisabled} />";
						$strOutput .= $arrField['delimiter'];
					}
					$strOutput .= "</div>";
					break;
				}
				$strLabel = ( $arrField['label'] ) ? $arrField['label'] : __( 'Submit' );
				$strOutput .= "<input id='{$strTagID}' class='{$strClass}' name='{$strFieldName}' type='submit' value='{$strLabel}' {$bIsDisabled} />";
				break;
			default:	
				// for anything else, 
				$strOutput .= $strValue;
				break;
			case 'import':	// import options
				$strLabel = ( $arrField['label'] ) ? $arrField['label'] : __( 'Import Options', 'admin-page-framework' );
				$strClass = ( $arrField['class'] ) ? $arrField['class'] : 'button button-primary';
				// $strOutput .= "<input class='{$strClass}' type='hidden' name='__import[option_key]' value='{$arrField['key']}' />";
				$strOutput .= "<input type='hidden' name='__import[error_message]' value='{$arrField['error']}' />";
				$strOutput .= "<input type='hidden' name='__import[update_message]' value='{$arrField['update_message']}' />";
				$strOutput .= "<input id='{$strTagID}' class='{$arrField['class']}' type='file'	name='__import' {$bIsDisabled} />";	// the file type will be stored in $_FILE 
				$strOutput .= $arrField['delimiter'];
				$strOutput .= "<input id='{$strTagID}_submit' class='{$strClass}' name='__import[submit]' type='submit' value='{$arrField['label']}' {$bIsDisabled} />";
				break;	
			case 'export':	// export options
				if ( is_array( $arrField['label'] ) ) { 
					foreach( $arrField['label'] as $numIndex => $strLabel ) {
						$strFileName = $this->GetCorrespondingArrayValue( $numIndex, $arrField['file_name'], $this->strClassName . '.txt' );
						$strClass = $this->GetCorrespondingArrayValue( $numIndex, $arrField['class'], 'button button-primary' );
						$strTransientKey = $this->GetCorrespondingArrayValue( $numIndex, $arrField['transient'], '' );	
						$bIsDisabled_ = is_array( $bIsDisabled ) ? $this->GetCorrespondingArrayValue( $numIndex, $bIsDisabled, '' ) : $bIsDisabled;
						if ( !empty( $strTransientKey ) )
							$strOutput .= "<input type='hidden' name='__export[transient][{$numIndex}]' value='{$strTransientKey}' />";
						$strOutput .= "<input type='hidden' name='__export[file_name][{$numIndex}]' value='{$strFileName}' />";
						$strOutput .= "<input type='hidden' name='__export[option_key][{$numIndex}]' value='{$arrField['option_key']}' />";
						$strOutput .= "<input id='{$strTagID}_{$numIndex}' class='{$strClass}' type='submit' value='{$strLabel}' name='__export[submit][{$numIndex}]' {$bIsDisabled_} />";
						$strOutput .= $arrField['delimiter'];
					}
					break;
				}
				$strLabel = ( $arrField['label'] ) ? $arrField['label'] : __( 'Export Options', 'admin-page-framework' );
				$strFileName = $arrField['file_name'] ? $arrField['file_name'] : $this->strClassName . '.txt';
				$strClass = ( $arrField['class'] ) ? $arrField['class'] : 'button button-primary';
				if ( isset( $arrField['transient'] ) && !empty( $arrField['transient'] ) )
					$strOutput .= "<input type='hidden' name='__export[transient]' value='{$arrField['transient']}' />";
				$strOutput .= "<input type='hidden' name='__export[file_name]' value='{$strFileName}' />";
				$strOutput .= "<input type='hidden' name='__export[option_key]' value='{$arrField['option_key']}' />";
				$strOutput .= "<input id='{$strTagID}' class='{$strClass}' type='submit' value='{$strLabel}' name='__export[submit]' {$bIsDisabled} />";
				break;
			case 'image':	// image uploader
				$strOutput .= $this->FormImageField( $strFieldName, $strOptionKeyForReference, $arrOptions, $arrField );
				break;

		}
		$strOutput = $arrField['pre_html'] . $strOutput;
		$strOutput .= ( !isset( $arrField['description'] ) ||  trim( $arrField['description'] ) == '' ) ? null : '<p class="field_description"><span class="description">' .  $arrField['description'] . '</span></p>';
		return $strOutput . $arrField['post_html'];
	}
	protected function FormImageField( $strFieldName, $strOptionKeyForReference, &$arrOptions, &$arrField ) {
		
		// Setup Variables
		$strOutput = '';		
		
		// $arrFieldOptions - the retrieved value from the database option table in which currently saved 					
		$arrFieldOptions = isset( $arrOptions[$arrField['section_ID']][$arrField['field_ID']] ) ? $arrOptions[$arrField['section_ID']][$arrField['field_ID']] : array();

		// the default value is assigned $strValue if $arrField['default'] is set.					
		$strDefaultImage = isset( $arrField['default'] ) ? $arrField['default'] : null;
		$strImageURL = ( !empty( $arrFieldOptions['imageurl'] ) ) ? esc_url( $arrFieldOptions['imageurl'] ) : $strDefaultImage;	
		$strStyleDisplay = $strImageURL ? '' : 'display: none;'; 
		
		/*	
			- Supported Labels
				$arrField['label'] = array(
					'title' => 'Pick an image from the Media Library or upload one.',
					'insert' => 'Use This Image',
					'upload' => 'Upload Image',
					'unset' => 'Unset Image',
					'delete' => 'Delete Image',
				);
			- Visibility
				$arrField['visibility'] => array(	
					'preview' => True,
					'image_url' => True,
					'unset_button' => True,
					'delete_button' => True,
				)	
		*/			
		$strLabelUploadImage = isset( $arrField['label']['upload'] ) ? $arrField['label']['upload'] : __( 'Upload Image', 'admin-page-framework' );
		$strLabelDeleteImage = isset( $arrField['label']['delete'] ) ? $arrField['label']['delete'] : __( 'Delete Image', 'admin-page-framework' );
		$strLabelUnsetImage =  isset( $arrField['label']['unset'] ) ? $arrField['label']['unset'] : __( 'Unset Image', 'admin-page-framework' );

		// For Debug
		// $strOutput .= '$strImageURL: ' . $strImageURL . '<br />';
		// $strOutput .= '$arrField["defalut"]<pre>' . $arrField["defalut"] . '</pre>';
		// $strOutput .= '<pre>' . print_r( $arrFieldOptions, true ) . '</pre>';
		
		// Start forming the field output
		// Button - Upload Image
		$strOutput .= "<input type='hidden' id='image_url_{$arrField['id']}' name='{$strFieldName}[imageurl]' value='{$strImageURL}' />";
		$strOutput .= "<input type='button' id='upload_image_button_{$arrField['id']}' class='button-secondary button' value='{$strLabelUploadImage}' />&nbsp;&nbsp;";

		// Button - Unset Image
		if ( !isset( $arrField['visibility']['unset_button'] ) || $arrField['visibility']['unset_button'] ) {		
			$strOutput .= "<input type='hidden' name='__image_unset[imageurl][{$arrField['id']}]' value='{$strImageURL}' />";
			$strOutput .= "<input type='hidden' name='__image_unset[option_key][{$arrField['id']}]' value='{$strOptionKeyForReference}|imageurl' />";
			$strOutput .= "<input style='{$strStyleDisplay}' type='submit' name='__image_unset[id][{$arrField['id']}]' id='unset_image_button_{$arrField['id']}' class='button button-secondary' value='{$strLabelUnsetImage}' />&nbsp;&nbsp;";						
		}
		// Button - Delete Image
		if ( !isset( $arrField['visibility']['delete_button'] ) || $arrField['visibility']['delete_button'] ) {			
			$strOutput .= "<input type='hidden' name='__image_delete[imageurl][{$arrField['id']}]' value='{$strImageURL}' />";
			$strOutput .= "<input type='hidden' name='__image_delete[option_key][{$arrField['id']}]' value='{$strOptionKeyForReference}|imageurl' />";
			$strOutput .= "<input style='{$strStyleDisplay}' type='submit' name='__image_delete[id][{$arrField['id']}]' id='delete_image_button_{$arrField['id']}' class='button button-secondary' value='{$strLabelDeleteImage}' />";
		}		
		// Preview Box 
		if ( !isset( $arrField['visibility']['preview'] ) || $arrField['visibility']['preview'] ) {		
			// $strMinHeight = $arrField['min-height'] ? $arrField['min-height'] : '100px';
			// $strMinWidth = $arrField['min-width'] ? $arrField['min-width'] : '320px';
			$strStyle = isset( $arrField['style'] ) ? $arrField['style'] : 'min-height: 100px;';
			$strStyleDisplay = ( isset( $arrFieldOptions['imageurl'] ) || $strImageURL ) ? '' : 'display: none;';	// for IE
			$strOutput .= 	"<div id='update_preview_{$arrField['id']}' style='{$strStyle}'>" .
							"<img style='{$strStyleDisplay} border: none; max-width:100%; margin-top: 20px;' src='{$strImageURL}' />" .
							"</div>";
		}
		// Image URL
		if ( !isset( $arrField['visibility']['image_url'] ) || $arrField['visibility']['image_url'] ) {
			$strOutput .= "<p id='upload_image_preview_url_{$arrField['id']}'>";
			$strOutput .= $strImageURL ? $strImageURL : __( 'No url has been set.', 'admin-page-framework');
			$strOutput .= "</p>";
		}
		return $strOutput;	
	}
	function MergeOptionArray( $strMethodName, $arrInput ) {
	
		// For debug
		// $this->numCalled++;			
		// $this->arrCallbacks[$strMethodName] = $_POST['pageslug'];	

		// $strMethodName is made up of validation_pre + page slug
		$strPageSlug = substr( $strMethodName, strlen( $this->prefix_validation ) + 4 );
		
		// Do not cast array. Check it manually since a casted array will have the index of 0 and will add it when it is merged.
		$arrOriginal = get_option( empty( $this->strOptionKey ) ? $strPageSlug : $this->strOptionKey );

		// In case the method is called unexpectedly from a different page, just return the original array or the original value is not an array, return the passed value.
		// Since the hidden input named 'pageslug' submits the page slug, check the value $_POST['pageslug'].
		// This must be done before the line returns null for deleting options.
		if ( $_POST['pageslug'] != $strPageSlug ) return ( array ) $arrOriginal;

		// Copy the original array to be used by Import and Export later on.
		$arrOriginalIntact = $arrOriginal;	
		
		// For Debug
		// $strOptionKeySet = empty( $this->strOptionKey ) ? 'No' : 'Yes';
		// add_settings_error( $_POST['pageslug'], 
				// 'can_be_any_string',  
				// '<h3>Submitted Values</h3>' .
				// '<h4>$arrKeys</h4>' . $this->DumpArray( $arrKeys ) . '' .
				// '<h4>Has Deleted?</h4><pre>' . $bDeleted . '</pre>' .
				// '<h4>Is Option Key Set?</h4><pre>' . $strOptionKeySet . '</pre>' .
				// '<h4>This Page Slug</h4><pre>' . $_GET['page'] . '</pre>' .
				// '<h4>The Current Processing URL</h4><pre>' . $_SERVER['REQUEST_URI'] . '</pre>' .
				// '<h4>Removed Callbacks</h4>' . $this->DumpArray( $this->arrDebug ) . '' .
				// '<h4>Number of times this method was called</h4><pre>' . $this->numCalled . '</pre>' .
				// '<h4>Registered Sections (to Settings API)</h4>' . $this->DumpArray( $this->arrSections )  . // <-- does not work beacause the validation callback is triggered in a diffeprent page load
				// '<h4>Called methods</h4>' . $this->DumpArray( $this->arrCallbacks ) .
				// '<h4>Passed Data - $arrInput</h4>' . $this->DumpArray( $arrInput ) .
				// '<h4>Submitted Data - $_POST</h4>' . $this->DumpArray( $_POST ) . 
				// '<h4>Currently Saved Data - $arrOptions</h4>' . $this->DumpArray( $arrOptions ),
				// 'updated'
			// );	
			// file_put_contents( dirname( __FILE__ ) . '/info.txt' , 
				// __FILE__ . PHP_EOL 
				// . __METHOD__ . PHP_EOL
				// . print_r( $_POST, true ) . PHP_EOL
				// ,FILE_APPEND );	
	
		// If the passed value is explicitly set to null, it means the user has chosen to discard the options.
		if ( is_null( $arrInput ) && ! isset( $_POST['__import']['submit'] ) && ! isset( $_POST['__export']['submit'] ) )
			return null;
			
		// Sanitize values - $arrInput could be passed as null
		$arrInput = ( array ) $arrInput;
		
		/*
		 * For the custom field type, image
		 * */
		if ( isset( $_POST['__image_unset']['id'] ) ) {
			
			// Get the field ID
			foreach( $_POST['__image_unset']['id'] as $strFieldID => $strButtonLabel ) {
				$strID = $strFieldID;
				break;
			}				
			
			// Remove the data from the option in the database.
			$arrKeys = explode("|", $_POST['__image_unset']['option_key'][$strID]);
			if ( count( $arrKeys ) == 4 ) {	// it should be either 4 or 5. 
				unset(  $arrInput[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]] ); 	// page slug[section id][field id][imageurl]
				unset(  $arrOriginal[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]] ); 	// page slug[section id][field id][imageurl]
			} else {
				unset( $arrInput[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]][$arrKeys[4]] );	// option key[page slug][section id][field id][imageurl]
				unset( $arrOriginal[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]][$arrKeys[4]] );	// option key[page slug][section id][field id][imageurl]
			}				
		}
		if ( isset( $_POST['__image_delete']['id'] ) ) {
			
			// Remove the file from the server.
			foreach( $_POST['__image_delete']['id'] as $strFieldID => $strButtonLabel ) {
				$strURL = $_POST['__image_delete']['imageurl'][$strFieldID];
				$this->DeleteFileFromMediaLibraryByURL( $strURL );		
				$strID = $strFieldID;
				break;
			}
			
			// Remove the data from the option in the database.
			$arrKeys = explode("|", $_POST['__image_delete']['option_key'][$strID]);
			if ( count( $arrKeys ) == 4 ) {	// it should be either 4 or 5. 
				unset(  $arrInput[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]] ); 	// page slug[section id][field id][imageurl]
				unset(  $arrOriginal[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]] ); 	// page slug[section id][field id][imageurl]
			} else {
				unset( $arrInput[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]][$arrKeys[4]] );	// option key[page slug][section id][field id][imageurl]
				unset( $arrOriginal[$arrKeys[1]][$arrKeys[2]][$arrKeys[3]][$arrKeys[4]] );	// option key[page slug][section id][field id][imageurl]
			}
			$bDeleted = true;
		}
					
		// For in-page tabs
		if ( isset( $_POST['tabslug'] ) && ! empty( $_POST['tabslug'] ) ) 
			$arrInput = $this->AddAndApplyFilter( 
				$this->prefix_validation . $strPageSlug . '_' . $_POST['tabslug'], 
				$arrInput 
			);				

		// For pages.
		// Do not cast array here either. Let the validation callback return non-array and make it consider as delete the option.
		$arrInput = $this->AddAndApplyFilter( 
			$this->prefix_validation . $strPageSlug, 
			$arrInput 
		);	

		/*
		 * For the custom field types, import and export - this must be done after appluing the validation filters for pages and tabs to allow to set transients.
		 * */
		// Check if the import file is sent. If so, do not continue and return.
		if ( isset( $_POST['__import']['submit'] ) && !$this->bIsImported ) {
			
			return $this->ImportOptions( $_POST['__import'] + $_FILES['__import'], $arrOriginalIntact );	// if it fails, it returns back the original array
		
		}
		
		// Check if the export button is pressed. If so, do not continue and return.
		if ( isset( $_POST['__export']['submit'] ) ) {
			
			$bIsExported = $this->ProcessExportOptions( $_POST['__export'], $arrOriginalIntact );			
			if ( $bIsExported ) exit;

		}
		
		/*
		 * Return the array to save into the option database table.
		 * */
		// return ( is_array( $arrOriginal ) && is_array( $arrInput ) ) ? wp_parse_args( $arrInput, $arrOriginal ) : $arrInput;		// <-- causes the settings get cleared in other pages
		// return ( is_array( $arrOriginal ) && is_array( $arrInput ) ) ? array_replace_recursive( $arrOriginal, $arrInput ) : $arrInput;		// <-- incompatible with PHP below 5.3
		return ( is_array( $arrOriginal ) && is_array( $arrInput ) ) ? $this->UniteArraysRecursive( $arrInput, $arrOriginal ) : $arrInput;		// merge them so that options saved in the other page slug keys will be saved as well.
	}
	/*
	 * The magic method.
	 * */
	function __Call( $strMethodName, $arrArgs=null ) {		
		
		/*
		 *  Undefined but called by the callback methods automatically inserted by the class will trigger this magic method, __call.
		 *  So determine which call back method triggered this and if nothing found, do nothing.
		 * */
		 
		// Variables
		// the currently loading in-page tab slug. Careful that not all cases $strMethodName have the page slug.
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->GetDefaultTabSlug( $strMethodName );	
		
		// For style filters, "style_" + page slug ( + _ + tab slug )
		if ( substr( $strMethodName, 0, strlen( $this->prefix_style ) ) 		== $this->prefix_style ) 	return $arrArgs[0];			
		if ( substr( $strMethodName, 0, strlen( $this->prefix_script ) ) 		== $this->prefix_script ) 	return $arrArgs[0];			
		
		// For export an import filters, "export_" ...
		if ( substr( $strMethodName, 0, strlen( $this->prefix_import ) ) 		== $this->prefix_import ) 	return $arrArgs[0];		// since 1.0.2
		if ( substr( $strMethodName, 0, strlen( $this->prefix_export ) ) 		== $this->prefix_export ) 	return $arrArgs[0];		// since 1.0.2
		
		// If it is the filter and action method that is not defined, do nothing.
		if ( substr( $strMethodName, 0, strlen( $this->prefix_do_before ) )		== $this->prefix_do_before )	return;				// do_before_X
		if ( substr( $strMethodName, 0, strlen( $this->prefix_do_after ) )		== $this->prefix_do_after )		return;				// do_after_X
		if ( substr( $strMethodName, 0, strlen( $this->prefix_do_form ) )		== $this->prefix_do_form )		return;				// do_form_X	// since 1.0.2
		if ( substr( $strMethodName, 0, strlen( $this->prefix_do ) ) 			== $this->prefix_do ) 			return;				// do_X
		if ( $strMethodName == $this->filter_global_head . $this->strClassName )			return $arrArgs[0];
		if ( $strMethodName == $this->filter_global_content . $this->strClassName )		return $arrArgs[0];
		if ( $strMethodName == $this->filter_global_foot . $this->strClassName )			return $arrArgs[0];
		if ( $strMethodName == $this->do_global . $this->strClassName )					return;
		if ( $strMethodName == $this->do_global_before . $this->strClassName )			return;
		if ( $strMethodName == $this->do_global_after . $this->strClassName )			return;
		if ( $strMethodName == $this->prefix_start . $this->strClassName )				return;

		// For content filters, "content_",  "head_", and "foot_"
		if ( substr( $strMethodName, 0, strlen( $this->prefix_content ) )		== $this->prefix_content )		return $arrArgs[0];		// content_X
		if ( substr( $strMethodName, 0, strlen( $this->prefix_head ) )			== $this->prefix_head )			return $arrArgs[0];		// head_X
		if ( substr( $strMethodName, 0, strlen( $this->prefix_foot ) )			== $this->prefix_foot )			return $arrArgs[0];		// foot_X

		// For Settings API callback methods.
		// If it is the validation callback method,
		if ( substr( $strMethodName, 0, strlen( $this->prefix_validation ) + 4 ) == $this->prefix_validation . 'pre_' )	return $this->MergeOptionArray( $strMethodName, $arrArgs[0] );	// $strMethodName does not contain the page slug
		if ( substr( $strMethodName, 0, strlen( $this->prefix_validation ) ) == $this->prefix_validation )	return $arrArgs[0];
		
		// If it is the field pre callback method, call the RenderFormField() method and if it is an undefined field_X() method, return the passed value.
		if ( substr( $strMethodName, 0, strlen( $this->prefix_field ) + 4 )	== $this->prefix_field . 'pre_' )	return $this->RenderFormField( $strMethodName, $arrArgs[0] );  // field_pre_
		if ( substr( $strMethodName, 0, strlen( $this->prefix_field ) )		== $this->prefix_field ) 			return $arrArgs[0];  // field_

		// If it is the section pre callback method, call the RenderSectionDescription() method and if it is an undefined section_X() method, return the passed value.
		if ( substr( $strMethodName, 0, strlen( $this->prefix_section ) + 4 )	== $this->prefix_section . 'pre_' ) return $this->RenderSectionDescription( $strMethodName );  // section_pre_
		if ( substr( $strMethodName, 0, strlen( $this->prefix_section ) )		== $this->prefix_section )			return $arrArgs[0];  // section_	

		// The callback of add_submenu_page() - render the page contents.
		if ( isset( $_GET['page'] ) && $_GET['page'] == $strMethodName ) $this->RenderPage( $strMethodName, $strTabSlug );
						
	}
	protected function RemoveValidationCallbacksExcept( $strPageSlug ) {
		
		// Removes the Settings API validation callbacks except for the given page. 
		// Returns an array holding the results with the key of the callback method and the value of True/False.
		// True to be removed; otherwise, False.
		
		$arrRemoved = array();
		foreach ( $this->arrPageTitles as $strStoredPageSlug => $strStoredPageTitle ) {
			$strOptionName = ( empty( $this->strOptionKey ) ) ? $strStoredPageSlug : $this->strOptionKey;
			$strValidationMethodName = $this->prefix_validation . 'pre_' . $strStoredPageSlug;
			$arrRemoved[$strValidationMethodName] = 0;
			if ( $strStoredPageSlug == $strPageSlug ) continue;				
			$arrRemoved[$strValidationMethodName] = remove_filter( "sanitize_option_{$strOptionName}", array( $this, $strValidationMethodName ) );
		}		
		return $arrRemoved;
	}
	protected function RenderPage( $strPageSlug, $strTabSlug=null ) {

		// this helps to prevent multiple validation callbacks for the case that the user sets custom option key.
		$this->RemoveValidationCallbacksExcept( $strPageSlug );	

		// variables
		$strHeader = '';
		
		// Do actions before rendering the page. In this order, global -> page -> in-page tab
		do_action( $this->do_global_before . $this->strClassName );
		do_action( $this->prefix_do_before . $strPageSlug );
		do_action( $this->prefix_do_before . $strPageSlug . '_' . $strTabSlug);
		?>
		<div class="wrap">
			<?php 				
				// Screen icon
				$strHeader .= $this->arrIcons[$strPageSlug] ? '<div class="icon32" style="background-image: url(' . $this->arrIcons[$strPageSlug] . ');"><br /></div>' : get_screen_icon();
				
				// Page heading tabs
				$strHeader .= ( $this->bShowPageHeadingTabs ) ? $this->AddPageHeadingTabs( $strPageSlug ) : '<h2>' . $this->arrPageTitles[$strPageSlug] . '</h2>';

				// in-page tabs
				if ( isset( $this->arrTabs[$strPageSlug] ) ) $strHeader .= $this->GetInPageTabs( $strPageSlug );
				
				// Apply filters in this order, in-page tab -> page -> global.
				$strHeader = apply_filters( $this->prefix_head . $strPageSlug . '_' . $strTabSlug, $strHeader );
				$strHeader = apply_filters( $this->prefix_head . $strPageSlug, $strHeader );
				echo apply_filters( $this->filter_global_head . $this->strClassName, $strHeader );
				
			?>
			<div class="admin-page-framework-container" style="">
				<form action="options.php" method="post" enctype="<?php echo $this->strFormEncType; ?>">
				<?php
					// this enabels add_settings_error() to trigger the error message. Should be inside a form tab. Otherwise, the font style breaks.
					settings_errors( $strPageSlug );	// the passed value must be the same as the first parameter of add_settings_error()
							
					// do custom actions - since 1.0.2
					do_action( $this->prefix_do_form . $this->strClassName  );
					do_action( $this->prefix_do_form . $strPageSlug );
					do_action( $this->prefix_do_form . $strPageSlug . '_' . $strTabSlug );

					// Capture output buffer
					ob_start(); // start buffer
					
					// Renders form elements
					settings_fields( $this->strClassName );
					do_settings_sections( $strPageSlug ); 
					
					$strContent = ob_get_contents(); // assign buffer contents to variable
					ob_end_clean(); // end buffer and remove buffer contents
								
					// render custom contents 
					// Apply filters in this order, in-page tab -> page -> global.
					$strContent = apply_filters( $this->prefix_content . $strPageSlug . '_' . $strTabSlug, $strContent );
					$strContent = apply_filters( $this->prefix_content . $strPageSlug, $strContent );
					echo apply_filters( $this->filter_global_content . $this->strClassName, $strContent );
						
					// do custom actions
					do_action( $this->do_global . $this->strClassName  );
					do_action( $this->prefix_do . $strPageSlug );
					do_action( $this->prefix_do . $strPageSlug . '_' . $strTabSlug );
				?>
				<input type="hidden" name="pageslug" value="<?php echo $strPageSlug; ?>" />
				<input type="hidden" name="tabslug" value="<?php echo $strTabSlug; ?>" />
				</form>					
			</div><!-- admin-page-framework-container -->
			<?php 
				$strFoot = apply_filters( $this->prefix_foot . $strPageSlug . '_' . $strTabSlug , '' ); 			
				$strFoot = apply_filters( $this->prefix_foot . $strPageSlug, $strFoot ); 
				echo apply_filters( $this->filter_global_foot . $this->strClassName, $strFoot );
			?>
		</div><!-- End Wrap -->
		<?php
		// do action after rendering the page
		do_action( $this->do_global_after . $this->strClassName );				
		do_action( $this->prefix_do_after . $strPageSlug );	
		do_action( $this->prefix_do_after . $strPageSlug . '_' . $strTabSlug );	
	}		
	function GetDefaultTabSlug( $strPageSlug ) {
		// retrieves the default in-page tab slug for the page.
		// if in-page tab is not added at all, returns nothing.
		if ( !IsSet( $this->arrTabs[$strPageSlug] ) ) return null;
		
		// means it's set, returns the first item 
		foreach ( $this->arrTabs[$strPageSlug] as $strTabSlug => $strTabTitle ) 
			return $strTabSlug;	// no need to iterate all, only the first one, which is the default
	}
	function GetCurrentSlug() {
		return isset( $_GET['page'] ) ? trim( $_GET['page'] ) : $this->strPageSlug;
	}
	function AddPageHeadingTabs( $strCurrentSlug ) {
		
		$strHeadingTabs = '<div><h2 class="nav-tab-wrapper">';
			foreach( $this->arrPageTitles as $strSlug => $strPageTitle ) {
				// Skip if it is the root menu title.
				if ( $strPageTitle == $this->strPageTitle ) continue;
				
				// checks if the current tab number matches the iteration number. If not matchi, then assign blank; otherwise put the active class name.
				$strClassActive = ( $strCurrentSlug == $strSlug ) ? 'nav-tab-active' : '';		
				$strHeadingTabs .= '<a class="nav-tab ' . $strClassActive . '" href="?page=' . $strSlug . '">' . $strPageTitle . '</a>';
			}
		$strHeadingTabs .= '</h2></div>';	
		return $strHeadingTabs;
		
	}	// end of tab menu
	protected function GetInPageTabs( $strCurrentPageSlug, $strHeadingTag='h3' ) {
		
		$strCurrentPageSlug = $this->SanitizeSlug( $strCurrentPageSlug );
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : null;
		$strInPageHeadingTabs = '<div><' . $strHeadingTag . ' class="nav-tab-wrapper in-page-tab">';			
		
		// First check if a hidden tab is specified
		if ( isset( $this->arrHiddenTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ] ) ) {
			
			$strCurrentTabSlug = $this->arrHiddenTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ];
			$strCurrentTabSlug = empty( $strCurrentTabSlug ) ? null : $strCurrentTabSlug;
			
		}
				
		foreach( $this->arrTabs[$strCurrentPageSlug] as $strTabSlug => $strTabTitle ) {
			
			// If the hide tab is set, skip
			if ( isset( $this->arrHiddenTabs[ $strCurrentPageSlug ][ $strTabSlug ] ) ) continue;
			
			// if the tab slug is not included in the loading url, set the first iterating slug to the default.
			if ( $strCurrentTabSlug === null ) $strCurrentTabSlug = $strTabSlug;
			
			// checks if the current tab slug matches the iteration slug. 
			// If not match, assign blank; otherwise, put the active class name.
			$strClassActive = ( (string) $strCurrentTabSlug === (string) $strTabSlug ) ? 'nav-tab-active' : '';		
			$strInPageHeadingTabs .= '<a class="nav-tab ' . $strClassActive . '" href="?page=' . $strCurrentPageSlug . '&tab=' . $strTabSlug . '">' . $strTabTitle . '</a>';
		
		}
		$strInPageHeadingTabs .= '</' . $strHeadingTag . '></div>';								
		return $strInPageHeadingTabs;
		
	}
	
	function AddStyle() {		// methods used by a WordPress hook callback cannot be protected, must be public.
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( !$this->IsPageAdded( $strPageSlug ) ) return;
					
		// Add and apply filters
		$strStyle = $this->AddAndApplyFilters( 
			$this->prefix_style, 
			array(
				'page' => $strPageSlug,
			), 
			$this->strStyle 
		);
		
		echo '<style type="text/css" name="admin-page-framework">' . $strStyle . '</style>';
		
	}	
	function AddScript() {		// methods used by a WordPress hook callback cannot be protected, must be public.
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( !$this->IsPageAdded( $strPageSlug ) ) return;

		// Add and apply filters.
		$strScript = $this->AddAndApplyFilters(
			$this->prefix_script, 
			array(
				'page' => $strPageSlug,
			),
			$this->strScript
		);
		echo '<script type="text/javascript" name="admin-page-framework">' . $strScript . '</script>';		
		
	}
	/*
	 * Image Uploader Methods
	 * */ 
	function DeleteFileFromMediaLibraryByURL( $strImageURL ) {
		global $wpdb;
		$strDBPrefix = $wpdb->prefix;
		$arrAttachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $strDBPrefix . "posts" . " WHERE guid='%s';", $strImageURL ) ); 
		$nAttachmentID = $arrAttachment[0];
		
		if ( empty( $nAttachmentID ) )	{	// could be a thumbnail url.
			$strImageURL = preg_replace( '/(\/.+)(-\d+x\d+)(\.\w+)$/i', '$1$3', $strImageURL );	// remove the thumbnail suffix, e.g. sunset-300x600.jpg -> sunset.jpg
			$arrAttachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $strDBPrefix . "posts" . " WHERE guid='%s';", $strImageURL ) ); 
			$nAttachmentID = $arrAttachment[0];
		}
					
		return wp_delete_attachment( $nAttachmentID, True );
	}
	function EnqueUploaderScripts() {	// public, not private since it is used by hooks.
		
		// Adds necessary scripts for image upload
		if ( !$this->IsPageAdded( $_GET['page'] ) ) return; 
	
		wp_enqueue_script('jquery');			
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');				
		wp_enqueue_script('media-upload');
	}		 
	function ReplaceThickBoxText( $strTranslated, $strText ) {	// called from a filter so do not protect
		global $pagenow;

		// replaces the button label in the popup uploader thick box.
		if ( !in_array( $pagenow, array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;

		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( !$this->IsReferredFromAddedPage( wp_get_referer() ) ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->strThickBoxButtonUseThis ?  $this->strThickBoxButtonUseThis : __( 'Use This Image', 'admin-page-framework' );
		
	}		 
	/*
	 * Export and Import Methods
	 * */
	protected function ImportOptions( $arrImportInfo, &$arrOriginal ) {
		/*
		 * This method is redirected from MergeOptionArray() called from the Setting API's validation callback
		 * when the __import key is set, which indicates that the user uploaded an import file.
		 * */
		$this->bIsImported = True;	// this prevents multiple error/update notices to be displayed.
		
		if ( $arrImportInfo['error'] > 0  ) {
			if ( $arrImportInfo['error_message'] ) $strMsg = $arrImportInfo['error_message']; 
			else if ( $arrImportInfo['error'] == 1 ) $strMsg =  __( 'The file is bigger than this PHP installation allows.', 'admin-page-framework' );
			else if ( $arrImportInfo['error'] == 2 ) $strMsg =  __( 'The file is bigger than this form allows.', 'admin-page-framework' );
			else if ( $arrImportInfo['error'] == 3 ) $strMsg =  __( 'Only part of the file was uploaded.', 'admin-page-framework' );
			else if ( $arrImportInfo['error'] == 4 ) $strMsg =  __( 'No file was uploaded.', 'admin-page-framework' );
			add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg );
			return $arrOriginal;
		}
		if ( $arrImportInfo['type'] != 'text/plain' ) {
			$strMsg = ( $arrImportInfo['error_message'] ) ? $arrImportInfo['error_message'] : __( 'Import Error: Wrong file type.', 'admin-page-framework' );				
			add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg );
			return $arrOriginal;
		}
		$arrImport = $this->UnserializeFromFile( $arrImportInfo['tmp_name'] );
		if ( !$arrImport ) {
			$strMsg = ( $arrImportInfo['error_message'] ) ? $arrImportInfo['error_message'] : __( 'Import Error: Wrong text format.', 'admin-page-framework' );
			add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg );
			return $arrOriginal;
		}
		
		// Specify which key of the option array to import - Not implemented yet 
		// if ( $arrImportInfo['__import']['option_key'] !== null ) {
			// $arrOriginal[]
		// }
		
		// Apply filters
		$arrImport = $this->AddAndApplyFilters( 
			$this->prefix_import, 
			array(
				'page' => $_POST['pageslug'],
				'tab' => isset( $_POST['tabslug'] ) ? $_POST['tabslug'] : null,
			),
			$arrImport,
			$arrImportInfo
		);		
		// If the user returns null explicitly, consider it as to decline the import process.
		if ( is_null( $arrImport ) ) {
			$strMsg = __( 'The importing process has been failed.', 'admin-page-framework' );
			add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg );	
			return $arrOriginal;
		}
		// if ( count( $arrImport ) == 0 ) {
			// $strMsg = __( 'Nothing could be imported.', 'admin-page-framework' );
			// add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg );	
			// return $arrOriginal;			
		// }
		
		// Okay, return the importing data!
		$strMsg = ( $arrImportInfo['update_message'] ) ? $arrImportInfo['update_message'] : __( 'Options were imported.', 'admin-page-framework' );
		add_settings_error( $_POST['pageslug'], 'can_be_any_string', $strMsg, 'updated' );	
		return $arrImport;				
	}
	protected function ProcessExportOptions( $arrPostExport, $arrOriginal ) {
		
		// added in 1.0.2
		// Avoid undefined key warnings
		$arrPostExport = $arrPostExport + array(
			'transient' => null,
			'file_name' => null,
			'option_key' => null,	// have not been inmplemented yet
			'submit'	=> null,
		);
		
		// Determine if multiple upload input fields were used or single.
		if ( is_array( $arrPostExport['submit'] ) ) {
			// the pressed submit button cannot be multiple as pressing buttons at the same time is impossible,
			// so just parse the first item.
			foreach( $arrPostExport['submit'] as $i => $v ) {
				$strTransientKey = $this->GetCorrespondingArrayValue( $i, $arrPostExport['transient'], null ); 
				$strFileName = $this->GetCorrespondingArrayValue( $i, $arrPostExport['file_name'], $this->strClassName . '.txt' ); 
				break;
			}
		} else {
			$strTransientKey = $arrPostExport['transient'];
			$strFileName = $arrPostExport['file_name'];
		}
		
		// Set up the exporting array.
		$arrExport = isset( $strTransientKey ) && ! empty( $strTransientKey ) ? ( array ) get_transient( $strTransientKey ) : $arrOriginal;
		$arrExport = $this->AddAndApplyFilters( 
			$this->prefix_export, 
			array(
				'page' => $_POST['pageslug'],
				'tab' => isset( $_POST['tabslug'] ) ? $_POST['tabslug'] : null,
			),
			$arrExport,
			$arrPostExport
		);
		
		// Delete the transient in case it remained.
		delete_transient( $strTransientKey );
		
		// Do export.
		if ( count( $arrExport ) > 0 )
			return $this->ExportOptions( $strFileName, $arrExport );
		
	}
	function ExportOptions( $strFileName, &$arr ) {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $strFileName );
		echo serialize( ( array ) $arr );
		return true;	// should be exited 
	}
	/*
		Misc Methods - utility methods which can be used by the user as well.
	*/
	function CheckKeys( $arrMandatoryKeys, $arrSubject, $arrAllowedMissingKeys=array() ) {
		
		// Checks if the subject array has all the necessary keys.
		// The $arrMandatoryKeys array must be numerically indexed with the values of necessary keys.
		// ( use array_keys() to format the array prior to pass it to the method. )
		
		foreach( $arrMandatoryKeys as $strKey ) {
			if ( in_array( $strKey, $arrAllowedMissingKeys ) ) continue;
			if ( ! array_key_exists( $strKey, $arrSubject ) ) return false;
		}
		
		return true;
	}
	function FixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {
	
		// Checks if the passed value is a number and set it to the default if not.
		// if it is a number and exceeds the set maximum number, it sets it to the max value.
		// if it is a number and is below the minimum number, it sets to the minimium value.
		// set a blank value for no limit.
		// This is useful for form data validation.
		
		if ( !is_numeric( trim( $numToFix ) ) ) return $numDefault;
			
		if ( $numMin != "" && $numToFix < $numMin) return $numMin;
			
		if ( $numMax != "" && $numToFix > $numMax ) return $numMax;

		return $numToFix;
		
	}	
	function UniteArraysRecursive( $arrPrecedence, $arrDefault ) {
		
		// since 1.0.1
		// Merges two multi-dimensional arrays recursively. The first parameter array takes its precedence.
		// This is useful to merge default option values.
		
		if ( is_null( $arrPrecedence ) )
			$arrPrecedence = array();
		
		if ( !is_array( $arrDefault ) || !is_array( $arrPrecedence ) ) return $arrPrecedence;
			
		foreach( $arrDefault as $strKey => $v ) {
			
			// If the precedence does not have the key, assign the default's value.
			if ( ! array_key_exists( $strKey, $arrPrecedence ) )
				$arrPrecedence[ $strKey ] = $v;
			else {
				
				// if the both are arrays, do the recursive process.
				if ( is_array( $arrPrecedence[ $strKey ] ) && is_array( $v ) ) 
					$arrPrecedence[ $strKey ] = $this->UniteArraysRecursive( $arrPrecedence[ $strKey ], $v );			
			
			}
		}
		
		return $arrPrecedence;
		
	}	
	/*
	 * Utilities - designed to be used by the framework internally.
	 * */
	protected function GetCorrespondingArrayValue( $strKey, $vSubject, $strDefault ) {
		
		// since 1.0.2
		// When there are multiple arrays and they have similar index struture but it's not certain,
		// use this method to retrieve the corresponding key value. This is mainly used by the field array
		// to insert user-defined key values.
		
		// $vSubject must be either string or array.
		if ( ! is_array( $vSubject ) ) return ( string ) $vSubject;	// consider it as string.
		
		// Consider $vSubject as array
		if ( isset( $vSubject[ $strKey ] ) ) return ( string ) $vSubject[ $strKey ];
		
		return $strDefault;
		
	}
	protected function AddAndApplyFilters( $strFilterPrefix, $arrSuffixes, $vInput, $vParams=null ) {
		
		// Creates filters of tab, page, and global and returns the filter-applied output.	 
		// The reason to add the filter before applying it is that without adding the filter, it won't trigger the __call magic method.	
		// Limitation: Accepts up to 2 parameters, $vInput (the subject to be filtered) and $vParams. If more than two params are needed to be passed, enclose them into an array and pass it to the seoncd parameter.
		
		// Prepare the filter suffixes
		// Avoid the undefined index warining by merging with the default keys.
		$arrSuffixes = $arrSuffixes + array(
			'tab' 	=> null,
			'page' 	=> null,
			'class'	=> $this->strClassName,
		);
		$strPageSlug = isset( $arrSuffixes['page'] ) ? $arrSuffixes['page'] : ( isset( $_GET['page'] ) ? $_GET['page'] : ( isset( $_POST['pageslug'] ) ? $_POST['pageslug'] : null ) );
		$strTabSlug = isset( $arrSuffixes['tab'] ) ? $arrSuffixes['tab'] : ( isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $_POST['tabslug'] ) ? $_POST['tabslug'] : $this->GetDefaultTabSlug( $strPageSlug ) ) ); 
 
		// If the loading page has a in-page tab
		if ( ! empty( $strPageSlug ) && ! empty( $strTabSlug ) ) 
			$vInput = $this->AddAndApplyFilter( $strFilterPrefix . $strPageSlug . '_' . $strTabSlug, $vInput, $vParams );
			
		// For regular added pages.
		if ( ! empty( $strPageSlug ) )
			$vInput = $this->AddAndApplyFilter( $strFilterPrefix . $strPageSlug, $vInput, $vParams );
		
		// For all the plugin pages added by the library.
		$vInput = $this->AddAndApplyFilter( $strFilterPrefix . $arrSuffixes['class'], $vInput, $vParams );		
		
		return $vInput;
		
	}
	protected function AddAndApplyFilter( $strFilter, $vInput, $vParams=null ) {
		// called from the AddAndApplyFilters() method
		add_filter( $strFilter , array( $this, $strFilter ), 10, isset( $vParams ) ? 2 : 1 );
		return apply_filters( $strFilter, $vInput, $vParams );	// at this point, the magic method __call(), gets triggred.
	}	
	protected function IsPageAdded( $strPageSlug ) {
		
		// returns true if the given page slug is one of the pages added by the library.
		if ( array_key_exists( trim( $strPageSlug ), $this->arrPageTitles ) ) return true; 
	}
	function GetCallerPluginBaseName() {
		
		return plugin_basename( $this->arrCallerInfo['file'] );
		
	}	
	function IsReferredFromAddedPage( $strURL ) {
		/*
		 * Used from the image uploader - checks the given url contains the page slug added by the library class
		 * */
		foreach ( $this->arrPageTitles as $strSlug => $strTitle ) 
			if ( stripos( $strURL, $strSlug ) ) return true;
	}	
	function SanitizeArrayKeys( $arr ) {
		foreach ( $arr as $key => $var ) { 
			unset( $arr[$key] );
			$new_key = $this->SanitizeSlug( $key ); //str_replace( "-", "_", $key );

			// check if the key already exists or not, skip if exists
			if ( isset( $arr[$new_key] ) ) continue;
			$arr[$new_key] = $var;
		}
		return $arr;
	}		
	function SanitizeSlug( $strSlug ) {
		$strSlug = preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', $strSlug );
		return $strSlug;
	}
	function UnserializeFromFile( $strFilePath ) {
		// returns an array from the contents of a given file
		$arr = unserialize( file_get_contents( $strFilePath, true ) );
		return ( $arr ) ? $arr : null; 
	}
	function AddAdminNotice( $strMsg, $nType=0 ) {
		// $nType - 0: update, 1: error
		$this->strAdminNotice = '<div class="' . ( $nType == 0 ) ? 'updated' : 'error' . '"><p>' . $strMsg . '</p></div>';
		add_action( 'admin_notices', array( $this, 'ShowAdminNotice' ) );
	}
	function ShowAdminNotice() {
		echo $this->strAdminNotice;
	}	
	/*
	 * Methods for Debug
	 * */
	function DumpArray( $arr ) {
		
		return '<pre>' . esc_html( print_r( $arr, true ) ) . '</pre>';
		
	}
}