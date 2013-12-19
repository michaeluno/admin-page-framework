<?php
if ( ! class_exists( 'AdminPageFramework' ) ) :
/**
 * The main class of the framework. 
 * 
 * The user should extend this class and define the set-ups in the setUp() method. Most of the public methods are for hook callbacks and the private methods are internal helper functions. So the protected methods are for the users.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor. This will be triggered in any admin page.</li>
 * 	<li><code>load_ + extended class name</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug + _ + tab slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>do_before_ + extended class name</code> – triggered before rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_before_ + page slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_before_ + page slug + _ + tab slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_ + extended class name</code> – triggered in the middle of rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_ + page slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_ + page slug + _ + tab slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_after_ + extended class name</code> – triggered after rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_after_ + page slug</code> – triggered after rendering the page.</li>
 * 	<li><code>do_after_ + page slug + _ + tab slug</code> – triggered after rendering the page.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>head_ + page slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + page slug + _ + tab slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + extended class name</code> – receives the output of the top part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>content_ + page slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + page slug + _ + tab slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + extended class name</code> – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>foot_ + page slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + page slug + _ + tab slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + extended class name</code> – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>section_ + extended class name + _ + section ID</code> – receives the description output of the given form section ID. The first parameter: output string. The second parameter: the array of option.</li> 
 * 	<li><code>field_ + extended class name + _ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>validation_ + page slug + _ + tab slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + page slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + extended class name + _ + input id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The input ID is the one used to the name attribute of the submit input tag. For a submit button that is inserted without using the framework's method, it will not take effect.</li>
 * 	<li><code>validation_ + extended class name + _ + field id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The field ID is the one that is passed to the field array to create the submit input field.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>style_ + page slug + _ + tab slug</code> – receives the output of the CSS rules applied to the tab page of the slug.</li>
 * 	<li><code>style_ + page slug</code> – receives the output of the CSS rules applied to the page of the slug.</li>
 * 	<li><code>style_ + extended class name</code> – receives the output of the CSS rules applied to the pages added by the instantiated class object.</li>
 * 	<li><code>script_ + page slug + _ + tab slug</code> – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 * 	<li><code>script_ + page slug</code> – receives the output of the JavaScript script applied to the page of the slug.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript script applied to the pages added by the instantiated class object.</li>
 * 	<li><code>export_ + page slug + _ + tab slug</code> – receives the exporting array sent from the tab page.</li>
 * 	<li><code>export_ + page slug</code> – receives the exporting array submitted from the page.</li>
 * 	<li><code>export_ + extended class name + _ + input id</code> – [2.1.5+] receives the exporting array submitted from the specific export button.</li>
 * 	<li><code>export_ + extended class name + _ + field id</code> – [2.1.5+] receives the exporting array submitted from the specific field.</li>
 * 	<li><code>export_ + extended class name</code> – receives the exporting array submitted from the plugin.</li>
 * 	<li><code>import_ + page slug + _ + tab slug</code> – receives the importing array submitted from the tab page.</li>
 * 	<li><code>import_ + page slug</code> – receives the importing array submitted from the page.</li>
 * 	<li><code>import_ + extended class name + _ + input id</code> – [2.1.5+] receives the importing array submitted from the specific import button.</li>
 * 	<li><code>import_ + extended class name + _ + field id</code> – [2.1.5+] receives the importing array submitted from the specific import field.</li>
 * 	<li><code>import_ + extended class name</code> – receives the importing array submitted from the plugin.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 * <h3>Examples</h3>
 * <p>If the extended class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function head_Sample_Admin_Pages( $sContent ) {
 *         return '&lt;div style="float:right;"&gt;&lt;img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /&gt;&lt;/div&gt;' 
 *             . $sContent;
 *     }
 * ...
 * }</code>
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $sContent ) {
 *         return $sContent . '&lt;p&gt;Hello world!&lt;/p&gt;';
 *     }
 * ...
 * }</code>
 * <h3>Timing of Hooks</h3>
 * <blockquote>------ When the class is instantiated ------
 *  
 *  start_ + extended class name
 *  load_ + extended class name
 *  load_ + page slug
 *  load_ + page slug + _ + tab slug
 *  
 *  ------ Start Rendering HTML ------
 *  
 *  &lt;head&gt;
 *      &lt;style type="text/css" name="admin-page-framework"&gt;
 *          style_ + page slug + _ + tab slug
 *          style_ + page slug
 *          style_ + extended class name
 *          script_ + page slug + _ + tab slug
 *          script_ + page slug
 *          script_ + extended class name       
 *      &lt;/style&gt;
 *  
 *  &lt;/head&gt;
 *  
 *  do_before_ + extended class name
 *  do_before_ + page slug
 *  do_before_ + page slug + _ + tab slug
 *  
 *  &lt;div class="wrap"&gt;
 *  
 *      head_ + page slug + _ + tab slug
 *      head_ + page slug
 *      head_ + extended class name                 
 *  
 *      &lt;div class="acmin-page-framework-container"&gt;
 *          &lt;form action="options.php" method="post"&gt;
 *  
 *              do_form_ + page slug + _ + tab slug
 *              do_form_ + page slug
 *              do_form_ + extended class name
 *  
 *              extended class name + _ + section_ + section ID
 *              extended class name + _ + field_ + field ID
 *  
 *              content_ + page slug + _ + tab slug
 *              content_ + page slug
 *              content_ + extended class name
 *  
 *              do_ + extended class name                   
 *              do_ + page slug
 *              do_ + page slug + _ + tab slug
 *  
 *          &lt;/form&gt;                 
 *      &lt;/div&gt;
 *  
 *          foot_ + page slug + _ + tab slug
 *          foot_ + page slug
 *          foot_ + extended class name         
 *  
 *  &lt;/div&gt;
 *  
 *  do_after_ + extended class name
 *  do_after_ + page slug
 *  do_after_ + page slug + _ + tab slug
 *  
 *  
 *  ----- After Submitting the Form ------
 *  
 *  validation_ + page slug + _ + tab slug 
 *  validation_ + page slug 
 *  validation_ + extended class name + _ + submit button input id
 *  validation_ + extended class name + _ + submit button field id
 *  validation_ + extended class name 
 *  export_ + page slug + _ + tab slug 
 *  export_ + page slug 
 *  export_ + extended class name
 *  import_ + page slug + _ + tab slug
 *  import_ + page slug
 *  import_ + extended class name</blockquote>
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Property_Page
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Link
 * @use				AdminPageFramework_Utility
 * @remark			This class stems from several abstract classes.
 * @extends			AdminPageFramework_Setting
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AdminPageFramework extends AdminPageFramework_Setting {
		
	/**
    * The common properties shared among sub-classes. 
	* 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Property_Page will be assigned in the constructor.
    */		
	protected $oProp;	
	
	/**
    * The object that provides the debug methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Debug will be assigned in the constructor.
    */		
	protected $oDebug;
	
	/**
    * Provides the methods for text messages of the framework. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Message will be assigned in the constructor.
    */	
	protected $oMsg;
	
	/**
    * Provides the methods for creating HTML link elements. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Link will be assigned in the constructor.
    */		
	protected $oLink;
	
	/**
    * Provides the utility methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AdminPageFramework_Utility will be assigned in the constructor.
    */			
	protected $oUtil;
	
	/**
	 * Provides the methods to insert head tag elements.
	 * 
	 * @since			2.1.5
	 * @access			protected
	 * @var				object			an instance of AdminPageFramework_HeadTag_Page will be assigne in the constructor.
	 */
	protected $oHeadTag;
	
	/**
	 * Inserts page load information into the footer area of the page. 
	 * 
	 * @since			2.1.7
	 * @access			protected
	 * @var				object			
	 */
	protected $oPageLoadInfo;
	
	/**
	 * Provides methods to manipulate contextual help pane.
	 * 
	 * @since			3.0.0
	 * @access			protected
	 * @var				object			
	 */
	protected $oHelpPane;
	
	/**
	 * The constructor of the main class.
	 * 
	 * <h4>Example</h4>
	 * <code>if ( is_admin() )
	 * 		new MyAdminPageClass( 'my_custom_option_key', __FILE__ );
	 * </code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @param			string			$sOptionKey			( optional ) specifies the option key name to store in the options table. If this is not set, the extended class name will be used.
	 * @param			string			$sCallerPath		( optional ) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
	 * @param			string			$sCapability		( optional ) sets the overall access level to the admin pages created by the framework. The used capabilities are listed here( http://codex.wordpress.org/Roles_and_Capabilities ). If not set, <strong>manage_options</strong> will be assigned by default. The capability can be set per page, tab, setting section, setting field.
	 * @param			string			$sTextDomain		( optional ) the text domain( http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains ) used for the framework's text strings. Default: admin-page-framework.
	 * @return			void			returns nothing.
	 */
	public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ){
				 		
		// Objects
		$this->oProp = new AdminPageFramework_Property_Page( $this, get_class( $this ), $sOptionKey, $sCapability );
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_Page::instantiate( $this->oProp, $this->oMsg );
		$this->oHelpPane = new AdminPageFramework_HelpPane_Page( $this->oProp );
		$this->oLink = new AdminPageFramework_Link( $this->oProp, $sCallerPath, $this->oMsg );
		$this->oHeadTag = new AdminPageFramework_HeadTag_Page( $this->oProp );
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oDebug = new AdminPageFramework_Debug;
								
		if ( is_admin() ) 
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		
		parent::__construct();
																					
		// For earlier loading than $this->setUp
		$this->oUtil->addAndDoAction( $this, 'start_' . $this->oProp->sClassName );

	}	

	/**
	* The method for all the necessary set-ups.
	* 
	* The users should override this method to set-up necessary settings. 
	* To perform certain tasks prior to this method, use the <em>start_ + extended class name</em> hook that is triggered at the end of the class constructor.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 	$this->setRootMenuPage( 'APF Form' ); 
	* 	$this->addSubMenuItems(
	* 		array(
	* 			'title' => 'Form Fields',
	* 			'page_slug' => 'apf_form_fields',
	* 		)
	* 	);		
	* 	$this->addSettingSections(
	* 		array(
	* 			'section_id'		=> 'text_fields',
	* 			'page_slug'		=> 'apf_form_fields',
	* 			'title'			=> 'Text Fields',
	* 			'description'	=> 'These are text type fields.',
	* 		)
	* 	);
	* 	$this->addSettingFields(
	* 		array(	
	* 			'field_id' => 'text',
	* 			'section_id' => 'text_fields',
	* 			'title' => 'Text',
	* 			'type' => 'text',
	* 		)	
	* 	);			
	* }</code>
	* @abstract
	* @since			2.0.0
	* @remark			This is a callback for the <em>wp_loaded</em> hook. Thus, its public.
	* @remark			In v1, this is triggered with the <em>admin_menu</em> hook; however, in v2, this is triggered with the <em>wp_loaded</em> hook.
	* @access 			public
	* @return			void
	*/	
	public function setUp() {}
		
	/*
	 * Help Pane Methods
	 */
	/**
	 * Adds the given contextual help tab contents into the property.
	 * 
	 * <h4>Contextual Help Tab Array Structure</h4>
	 * <ul>
	 * 	<li><strong>page_slug</strong> - ( required ) the page slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>page_tab_slug</strong> - ( optional ) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>help_tab_title</strong> - ( required ) the title of the contextual help tab.</li>
	 * 	<li><strong>help_tab_id</strong> - ( required ) the id of the contextual help tab.</li>
	 * 	<li><strong>help_tab_content</strong> - ( optional ) the HTML string content of the the contextual help tab.</li>
	 * 	<li><strong>help_tab_sidebar_content</strong> - ( optional ) the HTML string content of the sidebar of the contextual help tab.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>	$this->addHelpTab( 
	 *		array(
	 *			'page_slug'				=> 'first_page',	// ( mandatory )
	 *			// 'page_tab_slug'			=> null,	// ( optional )
	 *			'help_tab_title'			=> 'Admin Page Framework',
	 *			'help_tab_id'				=> 'admin_page_framework',	// ( mandatory )
	 *			'help_tab_content'			=> __( 'This contextual help text can be set with the <em>addHelpTab()</em> method.', 'admin-page-framework' ),
	 *			'help_tab_sidebar_content'	=> __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
	 *		)
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			Called when registering setting sections and fields.
	 * @remark			The user may use this method.
	 * @param			array			$aHelpTab				The help tab array. The key structure is detailed in the description part.
	 * @return			void
	 */ 
	public function addHelpTab( $aHelpTab ) {
		$this->oHelpPane->_addHelpTab( $aHelpTab );
	}

	/*
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @remark			The user may use this method.
	 */
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$sPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version/strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	
	/**
	* Adds sub-menu items on the left sidebar of the administration panel. 
	* 
	* It supports pages and links. Each of them has the specific array structure.
	* 
	* <h4>Sub-menu Page Array</h4>
	* <ul>
	* <li><strong>title</strong> - ( string ) the page title of the page.</li>
	* <li><strong>page_slug</strong> - ( string ) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	* <li><strong>screen_icon</strong> - ( optional, string ) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel.
	*	<pre>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</pre>
	*	<p><strong>Notes</strong>: the <em>generic</em> icon is available WordPress version 3.5 or above.</p>
	* </li>
	* <li><strong>sCapability</strong> - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* <h4>Sub-menu Link Array</h4>
	* <ul>
	* <li><strong>title</strong> - ( string ) the link title.</li>
	* <li><strong>href</strong> - ( string ) the URL of the target link.</li>
	* <li><strong>sCapability</strong> - ( optional, string ) the access level to show the item, defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSubMenuItems(
	*		array(
	*			'title' => 'Various Form Fields',
	*			'page_slug' => 'first_page',
	*			'screen_icon' => 'options-general',
	*		),
	*		array(
	*			'title' => 'Manage Options',
	*			'page_slug' => 'second_page',
	*			'screen_icon' => 'link-manager',
	*		),
	*		array(
	*			'title' => 'Google',
	*			'href' => 'http://www.google.com',	
	*			'fShowPageHeadingTab' => false,	// this removes the title from the page heading tabs.
	*		),
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array		$aSubMenuItem1		a first sub-menu array.
	* @param			array		$aSubMenuItem2		( optional ) a second sub-menu array.
	* @param			array		$_and_more				( optional ) third and add items as many as necessary with next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) {
		foreach ( func_get_args() as $aSubMenuItem ) 
			$this->addSubMenuItem( $aSubMenuItem );		
	}
	
	/**
	* Adds the given sub-menu item on the left sidebar of the administration panel.
	* 
	* This only adds one single item, called by the above <em>addSubMenuItem()</em> method.
	* 
	* The array structure of the parameter is documented in the <em>addSubMenuItem()</em> method section.
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			This is not intended to be used by the user.
	* @param			array		$aSubMenuItem			a first sub-menu array.
	* @access 			private
	* @return			void
	*/	
	private function addSubMenuItem( $aSubMenuItem ) {
		if ( isset( $aSubMenuItem['href'] ) ) {
			$aSubMenuLink = $aSubMenuItem + AdminPageFramework_Link::$_aStructure_SubMenuLink;
			$this->oLink->addSubMenuLink(
				$aSubMenuLink['title'],
				$aSubMenuLink['href'],
				$aSubMenuLink['sCapability'],
				$aSubMenuLink['order'],
				$aSubMenuLink['fShowPageHeadingTab'],
				$aSubMenuLink['fShowInMenu']
			);			
		}
		else { // if ( $aSubMenuItem['type'] == 'page' ) {
			$aSubMenuPage = $aSubMenuItem + self::$_aStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$aSubMenuPage['title'],
				$aSubMenuPage['page_slug'],
				$aSubMenuPage['screen_icon'],
				$aSubMenuPage['sCapability'],
				$aSubMenuPage['order'],	
				$aSubMenuPage['fShowPageHeadingTab'],
				$aSubMenuPage['fShowInMenu']
			);				
		}
	}

	/**
	* Adds the given link into the menu on the left sidebar of the administration panel.
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$sMenuTitle			the menu title.
	* @param			string		$sURL					the URL linked to the menu.
	* @param			string		$sCapability			( optional ) the access level. ( http://codex.wordpress.org/Roles_and_Capabilities)
	* @param			string		$nOrder				( optional ) the order number. The larger it is, the lower the position it gets.
	* @param			string		$bShowPageHeadingTab		( optional ) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
	* @access 			protected
	* @return			void
	*/	
	protected function addSubMenuLink( $sMenuTitle, $sURL, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		$this->oLink->addSubMenuLink( $sMenuTitle, $sURL, $sCapability, $nOrder, $bShowPageHeadingTab, $bShowInMenu );
	}

	/**
	* Adds the given link(s) into the description cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginDescription( 
	*		"&lt;a href='http://www.google.com'&gt;Google&lt;/a&gt;",
	*		"&lt;a href='http://www.yahoo.com'&gt;Yahoo!&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$sTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$sTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {				
		$this->oLink->addLinkToPluginDescription( func_get_args() );		
	}

	/**
	* Adds the given link(s) into the title cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginTitle( 
	*		"&lt;a href='http://www.wordpress.org'&gt;WordPress&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$sTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$sTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/	
	protected function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {	
		$this->oLink->addLinkToPluginTitle( func_get_args() );		
	}
	 
	/**
	 * Sets the overall capability.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setCpability( 'read' );		// let subscribers access the pages.</code>
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Roles_and_Capabilities
	 * @remark			The user may directly edit <code>$this->oProp->sCapability</code> instead.
	 * @param			string			$sCapability			The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
	 * @return			void
	 */ 
	protected function setCapability( $sCapability ) {
		$this->oProp->sCapability = $sCapability;	
	}

	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProp->aFooterInfo['sLeft']</code> instead.
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $sHTML, $bAppend=true ) {
		$this->oProp->aFooterInfo['sLeft'] = $bAppend 
			? $this->oProp->aFooterInfo['sLeft'] . PHP_EOL . $sHTML
			: $sHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProp->aFooterInfo['sRight']</code> instead.
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoRight( $sHTML, $bAppend=true ) {
		$this->oProp->aFooterInfo['sRight'] = $bAppend 
			? $this->oProp->aFooterInfo['sRight'] . PHP_EOL . $sHTML
			: $sHTML;
	}
			
	/**
	 * Sets an admin notice.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );</code>
	 * 
	 * @remark			It should be used before the 'admin_notices' hook is triggered.
	 * @since			2.1.2
	 * @param			string			$sMessage				The message to display
	 * @param			string			$sClassSelector		( optional ) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'
	 * @param			string			$sID					( optional ) The ID of the message. If not set, the hash of the message will be used.
	 */
	protected function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {
			
		$sID = $sID ? $sID : md5( $sMessage );
		$this->oProp->aAdminNotices[ md5( $sMessage ) ] = array(  
			'sMessage' => $sMessage,
			'sClassSelector' => $sClassSelector,
			'sID' => $sID,
		);
		add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
		
	}
	/**
	 * A helper function for the above setAdminNotice() method.
	 * @since			2.1.2
	 * @internal
	 */
	public function _replyToPrintAdminNotices() {
		
		foreach( $this->oProp->aAdminNotices as $aAdminNotice ) 
			echo "<div class='{$aAdminNotice['sClassSelector']}' id='{$aAdminNotice['sID']}' ><p>"
				. $aAdminNotice['sMessage']
				. "</p></div>";
		
	}	
	
	/**
	 * Sets the disallowed query keys in the links that the framework generates.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setDisallowedQueryKeys( 'my-custom-admin-notice' );</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @since			3.0.0			It also accepts a string.
	 */
	public function setDisallowedQueryKeys( $asQueryKeys, $bAppend=true ) {
		
		if ( ! $bAppend ) {
			$this->oProp->aDisallowedQueryKeys = ( array ) $asQueryKeys;
			return;
		}
		
		$aNewQueryKeys = array_merge( ( array ) $asQueryKeys, $this->oProp->aDisallowedQueryKeys );
		$aNewQueryKeys = array_filter( $aNewQueryKeys );	// drop non-values
		$aNewQueryKeys = array_unique( $aNewQueryKeys );	// drop duplicates
		$this->oProp->aDisallowedQueryKeys = $aNewQueryKeys;
		
	}
	
	/**
	 * The magic method which redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
	 * 
	 * @access			public
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string		$sMethodName		the called method name. 
	 * @param			array		$aArgs			the argument array. The first element holds the parameters passed to the called method.
	 * @return			mixed		depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
	 * @since			2.0.0
	 */
	public function __call( $sMethodName, $aArgs=null ) {		
				 
		// The currently loading in-page tab slug. Be careful that not all cases $sMethodName have the page slug.
		$sPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		$sTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug );	

		// If it is a pre callback method, call the redirecting method.		
		if ( substr( $sMethodName, 0, strlen( 'section_pre_' ) )	== 'section_pre_' ) return $this->renderSectionDescription( $sMethodName );  // add_settings_section() callback				
		if ( substr( $sMethodName, 0, strlen( 'field_pre_' ) )	== 'field_pre_' ) return $this->renderSettingField( $aArgs[ 0 ], $sPageSlug );  // add_settings_field() callback		
		if ( substr( $sMethodName, 0, strlen( 'validation_pre_' ) )	== 'validation_pre_' ) return $this->doValidationCall( $sMethodName, $aArgs[ 0 ] ); // register_setting() callback
		if ( substr( $sMethodName, 0, strlen( 'load_pre_' ) )	== 'load_pre_' ) return $this->doPageLoadCall( substr( $sMethodName, strlen( 'load_pre_' ) ), $sTabSlug, $aArgs[ 0 ] );  // load-{page} callback

		// The callback of the call_page_{page slug} action hook
		if ( $sMethodName == $this->oProp->sClassHash . '_page_' . $sPageSlug )
			return $this->renderPage( $sPageSlug, $sTabSlug );	
		
		// If it's one of the framework's callback methods, do nothing.	
		if ( $this->_isFrameworkCallbackMethod( $sMethodName ) )
			return isset( $aArgs[0] ) ? $aArgs[0] : null;	// if $aArgs[0] is set, it's a filter, otherwise, it's an action.		
		
	}	
		/**
		 * Determines whether the method name matches the pre-defined hook prefixes.
		 * @access			private
		 * @since			2.0.0
		 * @remark			the users do not need to call or extend this method unless they know what they are doing.
		 * @param			string			$sMethodName			the called method name
		 * @return			boolean			If it is a framework's callback method, returns true; otherwise, false.
		 */
		private function _isFrameworkCallbackMethod( $sMethodName ) {

			if ( substr( $sMethodName, 0, strlen( "{$this->oProp->sClassName}_" ) ) == "{$this->oProp->sClassName}_" )	// e.g. {instantiated class name} + _field_ + {field id}
				return true;
			
			if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}_" ) ) == "validation_{$this->oProp->sClassName}_" )	// e.g. validation_{instantiated class name}_ + {field id / input id}
				return true;

			if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" )	// e.g. field_types_{instantiated class name}
				return true;
				
			foreach( self::$_aPrefixes as $sPrefix ) {
				if ( substr( $sMethodName, 0, strlen( $sPrefix ) )	== $sPrefix  ) 
					return true;
			}
			return false;
		}
	
}
endif;