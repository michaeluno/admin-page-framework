<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework' ) ) :
/**
 * The main class of the framework to create admin pages and forms.
 * 
 * This class should be extended and the setUp() method should be overridden to define how pages are composed.
 * Most of the internal methods are prefixed with the underscore like <code>_getSomething()</code> and callback methods are prefixed with <code>_reply</code>.
 * The methods for the users are public and do not have those prefixes.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and the actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><strong>start_{instantiated class name}</strong> – triggered at the end of the class constructor. This will be triggered in any admin page.</li>
 * 	<li><strong>load_{instantiated class name}</strong>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><strong>load_{page slug}</strong>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><strong>load_{page slug}_{tab slug}</strong>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><strong>do_before_{instantiated class name}</strong> – triggered before rendering the page. It applies to all the pages created by the instantiated class object.</li>
 * 	<li><strong>do_before_{page slug}</strong> – triggered before rendering the page.</li>
 * 	<li><strong>do_before_{page slug}_{tab slug}</strong> – triggered before rendering the page.</li>
 * 	<li><strong>do_{instantiated class name}</strong> – triggered in the middle of rendering the page. It applies to all the pages created by the instantiated class object.</li>
 * 	<li><strong>do_{page slug}</strong> – triggered in the middle of rendering the page.</li>
 * 	<li><strong>do_{page slug}_{tab slug}</strong> – triggered in the middle of rendering the page.</li>
 * 	<li><strong>do_after_{instantiated class name}</strong> – triggered after rendering the page. It applies to all the pages created by the instantiated class object.</li>
 * 	<li><strong>do_after_{page slug}</strong> – triggered after rendering the page.</li>
 * 	<li><strong>do_after_{page slug}_{tab slug}</strong> – triggered after rendering the page.</li>
 * 	<li><strong>submit_{instantiated class name}_{submit input id}</strong> – [3.0.0+] triggered after the form is submitted with the submit button of the specified input id.</li>
 * 	<li><strong>submit_{instantiated class name}_{submit field id}</strong> – [3.0.0+] triggered after the form is submitted with the submit button of the specified field that does not hava section is submitted.</li>
 * 	<li><strong>submit_{instantiated class name}_{submit section id}_{submit field id}</strong> – [3.0.0+] triggered after the form is submitted with the submit button of the specified section and field is submitted.</li>
 * 	<li><strong>submit_{instantiated class name}_{submit section id}</strong> – [3.0.0+] triggered after the form is submitted with the submit button of the specified section.</li>
 * 	<li><strong>submit_{instantiated class name}</strong> – [3.0.0+] triggered after the form is submitted.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><strong>content_foot_{page slug}_{tab slug}</strong> – receives the output of the top part of the page. [3.0.0+] Changed the name from head_{...}.</li>
 * 	<li><strong>content_foot_{page slug}</strong> – receives the output of the top part of the page. [3.0.0+] Changed the name from head_{...}.</li>
 * 	<li><strong>content_foot_{instantiated class name}</strong> – receives the output of the top part of the page, applied to all pages created by the instantiated class object. [3.0.0+] Changed the name from head_{...}.</li>
 * 	<li><strong>content_{page slug}_{tab slug}</strong> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><strong>content_{page slug}</strong> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><strong>content_{instantiated class name}</strong> – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><strong>content_bottom_{page slug}_{tab slug}</strong> – receives the output of the bottom part of the page. [3.0.0+] Changed the name from foot_{...}.</li>
 * 	<li><strong>content_bottom_{page slug}</strong> – receives the output of the bottom part of the page. [3.0.0+] Changed the name from foot_{...}.</li>
 * 	<li><strong>content_bottom_{instantiated class name}</strong> – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object. [3.0.0+] Changed the name from foot_{...}.</li>
 * 	<li><strong>section_head_{instantiated class name}_{section ID}</strong> – receives the title and the description output of the given form section ID. The first parameter: the output string.</li> 
 * 	<li><strong>field_{instantiated class name}_{field ID}</strong> – receives the form input field output of the given input field ID that does not have a section. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><strong>field_{instantiated class name}_{section id}_{field ID}</strong> – [3.0.0+] receives the form input field output of the given input field ID that has a section. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><strong>sections_{instantiated class name}</strong> – receives the registered section arrays. The first parameter: sections container array.</li> 
 * 	<li><strong>fields_{instantiated class name}</strong> – receives the registered field arrays. The first parameter: fields container array.</li> 
 * 	<li><strong>fields_{instantiated class name}_{section id}</strong> – [3.0.0+] receives the registered field arrays which belong to the specified section. The first parameter: fields container array.</li> 
 * 	<li><strong>field_definition_{instantiated class name}_{field ID}</strong> – [3.0.2+] receives the form field definition array of the given input field ID that does not have a section. The first parameter: the field definition array.</li>
 * 	<li><strong>field_definition_{instantiated class name}_{section id}_{field ID}</strong> – [3.0.2+] receives the form field definition array of the given input field ID that has a section. The first parameter: the field definition array. The second parameter: the integer representing sub-section index if the field belongs to a sub-section.</li>
 * 	<li><strong>pages_{instantiated class name}</strong> – receives the registered page arrays. The first parameter: pages container array.</li> 
 * 	<li><strong>tabs_{instantiated class name}_{page slug}</strong> – receives the registered in-page tab arrays. The first parameter: tabs container array.</li> 
 * 	<li><strong>validation_{instantiated class name}_{field id}</strong> – [3.0.0+] receives the form submission value of the field that does not have a section. The first parameter: ( string|array ) submitted input value. The second parameter: ( string|array ) the old value stored in the database.</li>
 * 	<li><strong>validation_{instantiated class name}_{section_id}_{field id}</strong> – [3.0.0+] receives the form submission value of the field that has a section. The first parameter: ( string|array ) submitted input value. The second parameter: ( string|array ) the old value stored in the database.</li>
 * 	<li><strong>validation_{instantiated class name}_{section id}</strong> – [3.0.0+] receives the form submission values that belongs to the section.. The first parameter: ( array ) the array of submitted input values that belong to the section. The second parameter: ( array ) the array of the old values stored in the database.</li>
 * 	<li><strong>validation_{page slug}_{tab slug}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><strong>validation_{page slug}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><strong>validation_{instantiated class name}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><strong>style_{page slug}_{tab slug}</strong> – receives the output of the CSS rules applied to the tab page of the slug.</li>
 * 	<li><strong>style_{page slug}</strong> – receives the output of the CSS rules applied to the page of the slug.</li>
 * 	<li><strong>style_{instantiated class name}</strong> – receives the output of the CSS rules applied to the pages added by the instantiated class object.</li>
 * 	<li><strong>script_{page slug}_{tab slug}</strong> – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 * 	<li><strong>script_{page slug}</strong> – receives the output of the JavaScript script applied to the page of the slug.</li>
 * 	<li><strong>script_{instantiated class name}</strong> – receives the output of the JavaScript script applied to the pages added by the instantiated class object.</li>
 * 	<li><strong>export_{instantiated class name}_{input id}</strong> – [2.1.5+] receives the exporting array submitted from the specific export button.</li>
 * 	<li><strong>export_{instantiated class name}_{field id}</strong> – [2.1.5+] receives the exporting array submitted from the specific field that does not have a section.</li>
 * 	<li><strong>export_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the exporting array submitted from the specific field that has a section.</li>
 * 	<li><strong>export_{page slug}_{tab slug}</strong> – receives the exporting array sent from the tab page.</li>
 * 	<li><strong>export_{page slug}</strong> – receives the exporting array submitted from the page.</li>
 * 	<li><strong>export_{instantiated class name}</strong> – receives the exporting array submitted from the plugin.</li>
 * 	<li><strong>export_name_{instantiated class name}_{input id}</strong> – receives the exporting file name submitted the specified input id.</li>
 * 	<li><strong>export_name_{instantiated class name}_{field id}</strong> – receives the exporting file name submitted from the specific field that does not have a section.</li>
 * 	<li><strong>export_name_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the exporting file name submitted from the specific field that has a section.</li>
 * 	<li><strong>export_name_{page slug}_{tab slug}</strong> – receives the exporting file name submitted from the tab page.</li>
 * 	<li><strong>export_name_{page slug}</strong> – receives the exporting file name submitted from the page.</li>
 * 	<li><strong>export_name_{instantiated class name}</strong> – receives the exporting file name submitted from the script.</li>
 * 	<li><strong>export_format_{instantiated class name}_{input id}</strong> – receives the exporting file format submitted from the specific export button.</li>
 * 	<li><strong>export_format_{instantiated class name}_{field id}</strong> – receives the exporting file format submitted from the specific field that does not have a section.</li>
 * 	<li><strong>export_format_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the exporting file format submitted from the specific field that has a section.</li>
 * 	<li><strong>export_format_{page slug}_{tab slug}</strong> – receives the exporting file format sent from the tab page.</li>
 * 	<li><strong>export_format_{page slug}</strong> – receives the exporting file format submitted from the page.</li>
 * 	<li><strong>export_format_{instantiated class name}</strong> – receives the exporting file format submitted from the plugin.</li> 
 * 	<li><strong>import_{instantiated class name}_{input id}</strong> – [2.1.5+] receives the importing array submitted from the specific import button.</li>
 * 	<li><strong>import_{instantiated class name}_{field id}</strong> – [2.1.5+] receives the importing array submitted from the specific import field that does not have a section.</li>
 * 	<li><strong>import_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the importing array submitted from the specific import field that has a section.</li>
 * 	<li><strong>import_{page slug}_{tab slug}</strong> – receives the importing array submitted from the tab page.</li>
 * 	<li><strong>import_{page slug}</strong> – receives the importing array submitted from the page.</li>
 * 	<li><strong>import_{instantiated class name}</strong> – receives the importing array submitted from the plugin.</li>
 * 	<li><strong>import_mime_types_{instantiated class name}_{input id}</strong> – [2.1.5+] receives the mime types of the import data submitted from the specific import button.</li>
 * 	<li><strong>import_mime_types_{instantiated class name}_{field id}</strong> – [2.1.5+] receives the mime types of the import data submitted from the specific import field that does not have a section.</li>
 * 	<li><strong>import_mime_types_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the mime types of the import data submitted from the specific import field that has a section.</li>
 * 	<li><strong>import_mime_types_{page slug}_{tab slug}</strong> – receives the mime types of the import data submitted from the tab page.</li>
 * 	<li><strong>import_mime_types_{page slug}</strong> – receives the mime types of the import data submitted from the page.</li>
 * 	<li><strong>import_mime_types_{instantiated class name}</strong> – receives the mime types of the import data submitted from the plugin.</li>
 * 	<li><strong>import_format_{instantiated class name}_{input id}</strong> – [2.1.5+] receives the import data format submitted from the specific import button.</li>
 * 	<li><strong>import_format_{instantiated class name}_{field id}</strong> – [2.1.5+] receives the import data format submitted from the specific import field that does not have a section.</li>
 * 	<li><strong>import_format_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the import data format submitted from the specific import field that has a section.</li>
 * 	<li><strong>import_format_{page slug}_{tab slug}</strong> – receives the import data format submitted from the tab page.</li>
 * 	<li><strong>import_format_{page slug}</strong> – receives the import data format submitted from the page.</li>
 * 	<li><strong>import_format_{instantiated class name}</strong> – receives the import data format submitted from the plugin.</li>
 * 	<li><strong>import_option_key_{instantiated class name}_{input id}</strong> – [2.1.5+] receives the option array key of the importing array submitted from the specific import button.</li>
 * 	<li><strong>import_option_key_{instantiated class name}_{field id}</strong> – [2.1.5+] receives the option array key of the importing array submitted from the specific import field that does not have a section.</li>
 * 	<li><strong>import_option_key_{instantiated class name}_{section id}_{field id}</strong> – [3.0.0+] receives the option array key of the importing array submitted from the specific import field that has a section.</li>
 * 	<li><strong>import_option_key_{page slug}_{tab slug}</strong> – receives the option array key of the importing array submitted from the tab page.</li>
 * 	<li><strong>import_option_key_{page slug}</strong> – receives the option array key of the importing array submitted from the page.</li>
 * 	<li><strong>import_option_key_{instantiated class name}</strong> – receives the option array key of the importing array submitted from the plugin.</li> 
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 * <h3>Examples</h3>
 * <p>If the instantiated class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_foot_Sample_Admin_Pages( $sContent ) {
 *         return '<div style="float:right;"><img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /></div>' 
 *             . $sContent;
 *     }
 * ...
 * }</code>
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $sContent ) {
 *         return $sContent . '<p>Hello world!</p>';
 *     }
 * ...
 * }</code>
 * <h3>Timing of Hooks</h3>
 * <code>------ When the class is instantiated ------
 *  
 *  start_{instantiated class name}
 *  load_{instantiated class name}
 *  load_{page slug}
 *  load_{page slug}_{tab slug}
 * 
 *  sections_{instantiated class name}
 *  fields_{instantiated class name}
 *  pages_{instantiated class name}
 *  tabs_{instantiated class name}_{page slug}
 * 
 *  ------ Start Rendering HTML ------
 *  
 *  <head>
 *      <style type="text/css" name="admin-page-framework">
 *          style_{page slug}_{tab slug}
 *          style_{page slug}
 *          style_{instantiated class name}
 *          script_{page slug}_{tab slug}
 *          script_{page slug}
 *          script_{instantiated class name}
 *      </style>
 *  
 *  <head/>
 *  
 *  do_before_{instantiated class name}
 *  do_before_{page slug}
 *  do_before_{page slug}_{tab slug}
 *  
 *  <div class="wrap">
 *  
 *      content_foot_{page slug}_{tab slug}
 *      content_foot_{page slug}
 *      content_foot_{instantiated class name}
 *  
 *      <div class="acmin-page-framework-container">
 *          <form action="options.php" method="post">
 *  
 *              do_form_{page slug}_{tab slug}
 *              do_form_{page slug}
 *              do_form_{instantiated class name}
 *  
 *              section_head_{instantiated class name}_{section ID}
 *              field_{instantiated class name}_{field ID}
 *  
 *              content_{page slug}_{tab slug}
 *              content_{page slug}
 *              content_{instantiated class name}
 *  
 *              do_{instantiated class name}
 *              do_{page slug}
 *              do_{page slug}_{tab slug}
 *  
 *          </form>
 *      </div>
 *  
 *          content_bottom_{page slug}_{tab slug}
 *          content_bottom_{page slug}
 *          content_bottom_{instantiated class name}
 *  
 *  </div>
 *  
 *  do_after_{instantiated class name}
 *  do_after_{page slug}
 *  do_after_{page slug}_{tab slug}
 *  
 *  ----- After Submitting the Form ------
 *  
 *  validation_{instantiated class name}_{submit button input id}
 *  validation_{instantiated class name}_{submit button field id}
 *  validation_{page slug}_{tab slug }
 *  validation_{page slug }
 *  validation_{instantiated class name }
 *  export_{page slug}_{tab slug}
 *  export_{page slug}
 *  export_{instantiated class name}
 *  import_{page slug}_{tab slug}
 *  import_{page slug}
 *  import_{instantiated class name}
 * </code>
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Property_Page
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Link_Page
 * @use				AdminPageFramework_Utility
 * @remark			This class stems from several abstract classes.
 * @extends			AdminPageFramework_Setting
 * @package			AdminPageFramework
 * @subpackage		Page
 */
abstract class AdminPageFramework extends AdminPageFramework_Setting_Validation {
		
	/**
	 * Registers necessary callbacks ans sets up internal components including properties.
	 * 
	 * <h4>Example</h4>
	 * <code>if ( is_admin() )
	 * 	new MyAdminPageClass( 'my_custom_option_key', __FILE__ );</code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Roles_and_Capabilities
	 * @see				http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains
	 * @param			string			( optional ) specifies the option key name to store in the options table. If this is not set, the instantiated class name will be used.
	 * @param			string			( optional ) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
	 * @param			string			( optional ) sets the overall access level to the admin pages created by the framework. The used capabilities are listed <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. If not set, <strong>manage_options</strong> will be assigned by default. The capability can be set per page, tab, setting section, setting field.
	 * @param			string			( optional ) the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains" target="_blank">text domain</a> used for the framework's system messages. Default: admin-page-framework.
	 * @return			void			returns nothing.
	 */
	public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ){
			
		parent::__construct( 
			$sOptionKey, 
			$sCallerPath ? $sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ), 	// this is important to attempt to find the caller script path here when separating the library into multiple files.
			$sCapability, 
			$sTextDomain 
		);
					
		$this->oUtil->addAndDoAction( $this, 'start_' . $this->oProp->sClassName );	// fire the start_{instantiated class name} action.

	}	

	/**
	 * The method for all the necessary set-ups. 
	 * 
	 * The users should override this method to set-up necessary settings. To perform certain tasks prior to this method, use the <strong>start_{instantiated class name}</strong> hook that is triggered at the end of the class constructor.
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
	 * @remark			This is a callback for the <em>wp_loaded</em> hook.
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
	 * <h4>Example</h4>
	 * <code>	$this->addHelpTab( 
	 *		array(
	 *			'page_slug'					=> 'first_page',	// ( mandatory )
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
	 * @param			array			The help tab array.
	 * <h4>Contextual Help Tab Array Structure</h4>
	 * <ul>
	 * 	<li><strong>page_slug</strong> - ( required ) the page slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>page_tab_slug</strong> - ( optional ) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>help_tab_title</strong> - ( required ) the title of the contextual help tab.</li>
	 * 	<li><strong>help_tab_id</strong> - ( required ) the id of the contextual help tab.</li>
	 * 	<li><strong>help_tab_content</strong> - ( optional ) the HTML string content of the the contextual help tab.</li>
	 * 	<li><strong>help_tab_sidebar_content</strong> - ( optional ) the HTML string content of the sidebar of the contextual help tab.</li>
	 * </ul>
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
	 * Use this method to pass multiple files to the same page.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueStyle(  
	 * 		array( 
	 * 			dirname( APFDEMO_FILE ) . '/asset/css/code.css',
	 * 			dirname( APFDEMO_FILE ) . '/asset/css/code2.css',
	 * 		),
	 * 		'apf_manage_options' 
	 * );</code>
	 * 
	 * @since			3.0.0
	 * @param			array			The sources of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: <em>array( '/css/mystyle.css', '/css/mystyle2.css' )</em>
	 * @param			string			(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * @return			array			The array holing the queued items.
	 */
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueStyle(  dirname( APFDEMO_FILE ) . '/asset/css/code.css', 'apf_manage_options' );
	 * $this->enqueueStyle(  plugins_url( 'asset/css/readme.css' , APFDEMO_FILE ) , 'apf_read_me' );</code>
	 * 
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			The source of the stylesheet to enqueue: the url, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * <h4>Argument Array</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * @return			string			The style handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * <h4>Example</h4>
	 * <code>
	 * $this->enqueueScripts(  
	 * 	array( 
	 *			plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *			plugins_url( 'asset/js/test2.js' , __FILE__ ),	
	 * 	)
	 * 	'apf_read_me', 	// page slug
	 * );
	 * </code>
	 *
	 * @since			2.1.5
	 * @param			array			The sources of the stylesheets to enqueue: the URL, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * @return			array			The array holding the queued items.
	 */
	public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
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
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * <h4>Argument Array</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before <code></head></code> or before<code></body></code> Default: <em>false</em>.</li>
	 * </ul>
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	
	/**
	* Adds the given link(s) into the description cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginDescription( 
	*		"<a href='http://www.google.com'>Google</a>",
	*		"<a href='http://www.yahoo.com'>Yahoo!</a>"
	*	);</code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			the tagged HTML link text.
	* @param			string			( optional ) another tagged HTML link text.
	* @param			string			( optional ) add more as many as want by adding items to the next parameters.
	* @access 			public
	* @return			void
	*/		
	public function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {
		$this->oLink->_addLinkToPluginDescription( func_get_args() );		
	}

	/**
	* Adds the given link(s) into the title cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginTitle( 
	*		"<a href='http://www.wordpress.org'>WordPress</a>"
	*	);</code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			the tagged HTML link text.
	* @param			string			( optional ) another tagged HTML link text.
	* @param			string			( optional ) add more as many as want by adding items to the next parameters.
	* @access 			public
	* @return			void
	*/	
	public function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {	
		$this->oLink->_addLinkToPluginTitle( func_get_args() );		
	}
	 
	/**
	 * Sets the overall capability.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setCpability( 'read' );		// let subscribers access the pages.
	 * </code>
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @see				http://codex.wordpress.org/Roles_and_Capabilities
	 * @param			string			The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
	 * @return			void
	 * @access			public
	 */ 
	public function setCapability( $sCapability ) {
		$this->oProp->sCapability = $sCapability;
		$this->oForm->sCapability = $sCapability;
	}

	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
	 * </code>
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @param			string			The HTML code to insert.
	 * @param			boolean			If true, the text will be appended; otherwise, it will replace the default text.
	 * @access			public
	 * @return			void
	 */	
	public function setFooterInfoLeft( $sHTML, $bAppend=true ) {
		$this->oProp->aFooterInfo['sLeft'] = $bAppend 
			? $this->oProp->aFooterInfo['sLeft'] . PHP_EOL . $sHTML
			: $sHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '<br />Custom Text on the right hand side.' );
	 * </code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @param			string			The HTML code to insert.
	 * @param			boolean			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	public function setFooterInfoRight( $sHTML, $bAppend=true ) {
		$this->oProp->aFooterInfo['sRight'] = $bAppend 
			? $this->oProp->aFooterInfo['sRight'] . PHP_EOL . $sHTML
			: $sHTML;
	}
			
	/**
	 * Sets an admin notice.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );
	 * </code>
	 * 
	 * @access			public
	 * @remark			It should be used before the 'admin_notices' hook is triggered.
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @param			string			The message to display
	 * @param			string			( optional ) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'
	 * @param			string			( optional ) The ID of the message. If not set, the hash of the message will be used.
	 */
	public function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {
			
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
	 * <code>$this->setDisallowedQueryKeys( 'my-custom-admin-notice' );
	 * </code>
	 * 
	 * @since			2.1.2
	 * @since			3.0.0			It also accepts a string. Changed the scope to public.
	 * @access			public
	 * @param			array|string	The query key(s) to disallow.
	 * @param			boolean			If true, the passed key(s) will be appended to the property; otherwise, it will override the property.
	 * @return			void
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
	 * Retrieves the saved option value from the given option key and the dimensional array key representation.
	 * 
	 * <h4>Example</h4>
	 * <code>
	 * $aData = AdminPageFramework::getOption( 'APF' );
	 * $aSection = AdminPageFramework::getOption( 'APF', 'my_section' );
	 * $sText = AdminPageFramework::getOption( 'APF', array( 'my_section', 'my_text_field' ), 'foo' );
	 * $sColor = AdminPageFramework::getOption( 'APF', 'my_color_field', '#FFF' );
	 * </code>
	 * 
	 * @since			3.0.1
	 * @param			string			$sOptionKey			the option key of the options table.
	 * @param			string			$asKey				the representation of dimensional array keys. If the returning option structure is like the following,
	 * <code>
	 * array(
	 * 	'a'	=>	array(
	 * 		'b'	=>	array(
	 * 			'c'	=> 'ccc',
	 * 		),
	 * 	),
	 * )
	 * </code>
	 * then the value 'ccc' can be retrieved with the key representation array of 
	 * <code>
	 * array( 'a', 'b', 'c' )
	 * </code>
	 * @param			mixed			$vDefault			the default value that will be returned if nothing is stored.
	 * @return			mixed			If the field ID is not specified
	 */
	static public function getOption( $sOptionKey, $asKey=null , $vDefault=null ) {
		return AdminPageFramework_WPUtility::getOption( $sOptionKey,$asKey, $vDefault );
	}
	
}
endif;