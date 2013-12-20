<?php
if ( ! class_exists( 'AdminPageFramework_Menu' ) ) :
/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array	$_aBuiltInRootMenuSlugs	stores the WordPress built-in menu slugs.
 * @staticvar		array	$_aStructure_SubMenuPage	represents the structure of the sub-menu page array.
 */
abstract class AdminPageFramework_Menu extends AdminPageFramework_Page {
	
	/**
	 * Used to refer the built-in root menu slugs.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds the built-in root menu slugs.
	 * @static
	 * @internal
	 */ 
	protected static $_aBuiltInRootMenuSlugs = array(
		// All keys must be lower case to support case insensitive look-ups.
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

	/**
	 * Represents the structure of sub-menu page array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of sub-menu page.
	 * @static
	 * @internal
	 */ 
	protected static $_aStructure_SubMenuPage = array(
		'title' => null, 
		'page_slug' => null, 
		'screen_icon' => null,
		'sCapability' => null, 
		'order' => null,
		'fShowPageHeadingTab' => true,	// if this is false, the page title won't be displayed in the page heading tab.
		'fShowInMenu' => true,	// if this is false, the menu label will not be displayed in the sidebar menu.
	);
	 
	function __construct() {
		
		add_action( 'admin_menu', array( $this, '_replyToBuildMenu' ), 98 );		
		
		// Call the parent constructor.
		$aArgs = func_get_args();
		call_user_func_array( array( $this, "parent::__construct" ), $aArgs );

		
	}
	 
	/**
	 * Sets to which top level page is going to be adding sub-pages.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPage( 'Settings' );</code>
	 * <code>$this->setRootMenuPage( 
	 * 	'APF Form',
	 * 	plugins_url( 'image/screen_icon32x32.jpg', __FILE__ )
	 * );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.6			The $sIcon16x16 parameter accepts a file path.
	 * @remark			Only one root page can be set per one class instance.
	 * @param			string			$sRootMenuLabel			If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
	 * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
	 * @param			string			$sIcon16x16			( optional ) the URL or the file path of the menu icon. The size should be 16 by 16 in pixel.
	 * @param			string			$iMenuPosition			( optional ) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
	 * @return			void
	 */
	protected function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) {

		$sRootMenuLabel = trim( $sRootMenuLabel );
		$sSlug = $this->isBuiltInMenuItem( $sRootMenuLabel );	// if true, this method returns the slug
		$this->oProp->aRootMenu = array(
			'sTitle'			=> $sRootMenuLabel,
			'sPageSlug' 		=> $sSlug ? $sSlug : $this->oProp->sClassName,	
			'sIcon16x16'		=> $this->oUtil->resolveSRC( $sIcon16x16, true ),
			'iPosition'			=> $iMenuPosition,
			'fCreateRoot'		=> $sSlug ? false : true,
		);	
					
	}
	
	/**
	 * Sets the top level menu page by page slug.
	 * 
	 * The page should be already created or scheduled to be created separately.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );</code>
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @remark			The user may use this method in their extended class definition.
	 * @param			string			$sRootMenuSlug			The page slug of the top-level root page.
	 * @return			void
	 */ 
	protected function setRootMenuPageBySlug( $sRootMenuSlug ) {
		
		$this->oProp->aRootMenu['sPageSlug'] = $sRootMenuSlug;	// do not sanitize the slug here because post types includes a question mark.
		$this->oProp->aRootMenu['fCreateRoot'] = false;		// indicates to use an existing menu item. 
		
	}
	
	/**
	 * Adds sub-menu pages.
	 * 
	 * Use addSubMenuItems() instead, which supports external links.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	protected function addSubMenuPages() {
		foreach ( func_get_args() as $aSubMenuPage ) {
			$aSubMenuPage = $aSubMenuPage + self::$_aStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$aSubMenuPage['title'],
				$aSubMenuPage['page_slug'],
				$aSubMenuPage['screen_icon'],
				$aSubMenuPage['sCapability'],
				$aSubMenuPage['order'],
				$aSubMenuPage['fShowPageHeadingTab']
			);				
		}
	}
	
	/**
	 * Adds a single sub-menu page.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addSubMenuPage( 'My Page', 'my_page', 'edit-pages' );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			The key name page_heading_tab_visibility was changed to fShowPageHeadingTab
	 * @since			2.1.6			$sScreenIcon accepts a file path.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 * @param			string			$sPageTitle			The title of the page.
	 * @param			string			$sPageSlug			The slug of the page.
	 * @param			string			$sScreenIcon			( optional ) Either a screen icon ID, a url of the icon, or a file path to the icon, with the size of 32 by 32 in pixel. The accepted icon IDs are as follows.
	 * <blockquote>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</blockquote>
	 * <strong>Note:</strong> the <em>generic</em> ID is available since WordPress 3.5.
	 * @param			string			$sCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the page.
	 * @param			integer			$nOrder				( optional ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$bShowPageHeadingTab	( optional ) If this is set to false, the page title won't be displayed in the page heading tab. Default: true.
	 * @param			boolean			$bShowInMenu			( optional ) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.
	 * @return			void
	 */ 
	protected function addSubMenuPage( $sPageTitle, $sPageSlug, $sScreenIcon=null, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		
		$sPageSlug = $this->oUtil->sanitizeSlug( $sPageSlug );
		$iCount = count( $this->oProp->aPages );
		$aPreviouslySetPage = isset( $this->oProp->aPages[ $sPageSlug ] ) 
			? $this->oProp->aPages[ $sPageSlug ]
			: array();
		$aThisPage = array(  
			'title'				=> $sPageTitle,
			'page_slug'				=> $sPageSlug,
			'type'					=> 'page',	// this is used to compare with the link type.
			'hrefIcon32x32'			=> $this->oUtil->resolveSRC( $sScreenIcon, true ),
			'screen_iconID'			=> in_array( $sScreenIcon, self::$_aScreenIconIDs ) ? $sScreenIcon : null,
			'sCapability'				=> isset( $sCapability ) ? $sCapability : $this->oProp->sCapability,
			'order'					=> is_numeric( $nOrder ) ? $nOrder : $iCount + 10,
			'fShowPageHeadingTab'		=> $bShowPageHeadingTab,
			'fShowInMenu'				=> $bShowInMenu,	// since 1.3.4			
			'fShowPageTitle'			=> $this->oProp->bShowPageTitle,			// boolean
			'fShowPageHeadingTabs'		=> $this->oProp->bShowPageHeadingTabs,		// boolean
			'fShowInPageTabs'			=> $this->oProp->bShowInPageTabs,			// boolean
			'sInPageTabTag'			=> $this->oProp->sInPageTabTag,			// string
			'sPageHeadingTabTag'		=> $this->oProp->sPageHeadingTabTag,		// string			
		);
		$this->oProp->aPages[ $sPageSlug ] = $this->oUtil->uniteArraysRecursive( $aThisPage, $aPreviouslySetPage );
			
	}
	
	/**
	 * Checks if a menu item is a WordPress built-in menu item from the given menu label.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void|string			Returns the associated slug string, if true.
	 */ 
	protected function isBuiltInMenuItem( $sMenuLabel ) {
		
		$sMenuLabelLower = strtolower( $sMenuLabel );
		if ( array_key_exists( $sMenuLabelLower, self::$_aBuiltInRootMenuSlugs ) )
			return self::$_aBuiltInRootMenuSlugs[ $sMenuLabelLower ];
		
	}
	
	/**
	 * Registers the root menu page.
	 * 
	 * @since			2.0.0
	 */ 
	private function registerRootMenuPage() {

		$sHookName = add_menu_page(  
			$this->oProp->sClassName,						// Page title - will be invisible anyway
			$this->oProp->aRootMenu['sTitle'],				// Menu title - should be the root page title.
			$this->oProp->sCapability,						// Capability - access right
			$this->oProp->aRootMenu['sPageSlug'],			// Menu ID 
			'', //array( $this, $this->oProp->sClassName ), 	// Page content displaying function
			$this->oProp->aRootMenu['sIcon16x16'],		// icon path
			isset( $this->oProp->aRootMenu['iPosition'] ) ? $this->oProp->aRootMenu['iPosition'] : null	// menu position
		);

	}
	
	/**
	 * Registers the sub-menu page.
	 * 
	 * @since			2.0.0
	 * @remark			Used in the buildMenu() method. 
	 * @remark			Within the <em>admin_menu</em> hook callback process.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	private function registerSubMenuPage( $aArgs ) {
	
		// Format the argument array since it may be added by the third party scripts via the hook.
		$aArgs = isset( $aArgs['type'] ) && $aArgs['type'] == 'link' 
			? $aArgs + AdminPageFramework_Link::$_aStructure_SubMenuLink	// for link
			: $aArgs + self::$_aStructure_SubMenuPage;	// for page
		
		// Variables
		$sType = $aArgs['type'];	// page or link
		$sTitle = $sType == 'page' ? $aArgs['title'] : $aArgs['title'];
		$sCapability = $aArgs['sCapability'];
			
		// Check the capability
		$sCapability = isset( $sCapability ) ? $sCapability : $this->sCapability;
		if ( ! current_user_can( $sCapability ) ) return;		
		
		// Add the sub-page to the sub-menu
		$aResult = array();
		$sRootPageSlug = $this->oProp->aRootMenu['sPageSlug'];
		$sMenuLabel = plugin_basename( $sRootPageSlug );	// Make it compatible with the add_submenu_page() function.
		
		// If it's a page - it's possible that the page_slug key is not set if the user uses a method like setPageHeadingTabsVisibility() prior to addSubMenuItam().
		if ( $sType == 'page' && isset( $aArgs['page_slug'] ) ) {		
			
			$sPageSlug = $aArgs['page_slug'];
			$aResult[ $sPageSlug ] = add_submenu_page( 
				$sRootPageSlug,						// the root(parent) page slug
				$sTitle,								// page_title
				$sTitle,								// menu_title
				$sCapability,				 			// sCapability
				$sPageSlug,	// menu_slug
				// In admin.php ( line 149 of WordPress v3.6.1 ), do_action($page_hook) ( where $page_hook is $aResult[ $sPageSlug ] )
				// will be executed and it triggers the __call magic method with the method name of "md5 class hash + _page_ + this page slug".
				array( $this, $this->oProp->sClassHash . '_page_' . $sPageSlug )
			);			
			
			add_action( "load-" . $aResult[ $sPageSlug ] , array( $this, "load_pre_" . $sPageSlug ) );
				
			// If the visibility option is false, remove the one just added from the sub-menu array
			if ( ! $aArgs['fShowInMenu'] ) {

				foreach( ( array ) $GLOBALS['submenu'][ $sMenuLabel ] as $iIndex => $aSubMenu ) {
					
					if ( ! isset( $aSubMenu[ 3 ] ) ) continue;
					
					// the array structure is defined in plugin.php - $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title ) 
					if ( $aSubMenu[0] == $sTitle && $aSubMenu[3] == $sTitle && $aSubMenu[2] == $sPageSlug ) {
						unset( $GLOBALS['submenu'][ $sMenuLabel ][ $iIndex ] );
						
						// The page title in the browser window title bar will miss the page title as this is left as it is.
						$this->oProp->aHiddenPages[ $sPageSlug ] = $sTitle;
						add_filter( 'admin_title', array( $this, 'fixPageTitleForHiddenPages' ), 10, 2 );
						
						break;
					}
				}
			} 
				
		} 
		// If it's a link,
		if ( $sType == 'link' && $aArgs['fShowInMenu'] ) {
			
			if ( ! isset( $GLOBALS['submenu'][ $sMenuLabel ] ) )
				$GLOBALS['submenu'][ $sMenuLabel ] = array();
			
			$GLOBALS['submenu'][ $sMenuLabel ][] = array ( 
				$sTitle, 
				$sCapability, 
				$aArgs['href'],
			);	
		}
	
		return $aResult;	// maybe useful to debug.

	}
	
	/**
	 * A callback function for the admin_title filter to fix the page title for hidden pages.
	 * @since			2.1.4
	 */
	public function fixPageTitleForHiddenPages( $sAdminTitle, $sPageTitle ) {

		if ( isset( $_GET['page'], $this->oProp->aHiddenPages[ $_GET['page'] ] ) )
			return $this->oProp->aHiddenPages[ $_GET['page'] ] . $sAdminTitle;
			
		return $sAdminTitle;
		
	}
	
	/**
	 * Builds menus.
	 * 
	 * @since			2.0.0
	 */
	public function _replyToBuildMenu() {
		
		// If the root menu label is not set but the slug is set, 
		if ( $this->oProp->aRootMenu['fCreateRoot'] ) 
			$this->registerRootMenuPage();
		
		// Apply filters to let other scripts add sub menu pages.
		$this->oProp->aPages = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			"pages_{$this->oProp->sClassName}", 
			$this->oProp->aPages
		);
		
		// Sort the page array.
		uasort( $this->oProp->aPages, array( $this->oProp, 'sortByOrder' ) ); 
		
		// Set the default page, the first element.
		foreach ( $this->oProp->aPages as $aPage ) {
			
			if ( ! isset( $aPage['page_slug'] ) ) continue;
			$this->oProp->sDefaultPageSlug = $aPage['page_slug'];
			break;
			
		}
		
		// Register them.
		foreach ( $this->oProp->aPages as &$aSubMenuItem ) 
			$this->oProp->aRegisteredSubMenuPages = $this->registerSubMenuPage( $aSubMenuItem );
						
		// After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
		if ( $this->oProp->aRootMenu['fCreateRoot'] ) 
			remove_submenu_page( $this->oProp->aRootMenu['sPageSlug'], $this->oProp->aRootMenu['sPageSlug'] );
		
	}	
}
endif;