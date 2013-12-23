<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.0.0b
	Requirements: PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

if ( ! class_exists( 'AdminPageFramework' ) )
    include_once( dirname( __FILE__ ) . '/class/admin-page-framework.php' );

class APF_BasicUsage extends AdminPageFramework {
	
	public function setUp() {
		
		$this->setRootMenuPage( 
			'Admin Page Framework',
			version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 'dashicons-format-audio' : null	// dash-icons are supported since WordPress v3.8
		);
		$this->addSubMenuItems(
			array(
				'title' => __( 'First Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_first_page',
			),
			array(
				'title' => __( 'Second Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_second_page',
			)
		);
		
		$this->setPageHeadingTabsVisibility( true );		// disables the page heading tabs by passing false.
	}	
	
	public function do_apf_first_page() {	// do_ + {page slug}
		?>
			<h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-demo' ); ?></h3>
			<p><?php _e( 'Hi there! This text is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-demo' ); ?></p>
		<?php

	}
	
	public function content_apf_second_page( $sContent ) {	// content_ + {page slug}
		
		return $sContent 
			. "<h3>" . __( 'content_ + {...} Filter Hooks', 'admin-page-framework-demo' ) . "</h3>"
			. "<p>" 
				. __( 'This message is inserted by the <code>content_{page slug}</code> filter.', 'admin-page-framework-demo' ) 
			. "</p>";
		
	}
	
}
new APF_BasicUsage;

class APF_Demo extends AdminPageFramework {

	public function start_APF_Demo() {	// start_{extended class name}
		
		/*
		 * ( Optional ) Register custom field types.
		 */			
		// 1. Include the file that defines the custom field type. 
		$aFiles = array(
			dirname( __FILE__ ) . '/third-party/date-time-custom-field-types/DateCustomFieldType.php',
			dirname( __FILE__ ) . '/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
			dirname( __FILE__ ) . '/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
			dirname( __FILE__ ) . '/third-party/dial-custom-field-type/DialCustomFieldType.php',
			dirname( __FILE__ ) . '/third-party/font-custom-field-type/FontCustomFieldType.php',
		);
		foreach( $aFiles as $sFilePath )
			if ( file_exists( $sFilePath ) )
				include_once( $sFilePath );
					
		// 2. Instantiate the classes - the $oMsg object is optional if you use the framework's messages.
		$oMsg = AdminPageFramework_Message::instantiate( 'admin-page-framework-demo' );
		new DateCustomFieldType( 'APF_Demo', 'date', $oMsg );
		new TimeCustomFieldType( 'APF_Demo', 'time', $oMsg );
		new DateTimeCustomFieldType( 'APF_Demo', 'date_time', $oMsg );
		new DialCustomFieldType( 'APF_Demo', 'dial', $oMsg );
		new FontCustomFieldType( 'APF_Demo', 'font', $oMsg );			
		
	}

	public function setUp() {

		$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
		$this->addSubMenuItems(
			/* 	
			  for sub-menu pages, e.g.
			  	'title' => 'Your Page Title',
				'page_slug' => 'your_page_slug',		// avoid hyphen(dash), dots, and white spaces
				'screen_icon' => 'edit',
				'capability' => 'manage-options',
				'order' => 10,
				
			  for sub-menu links, e.g.
				'title' => 'Google',
				'href' => 'http://www.google.com',
				
			*/
			array(
				'title' => __( 'Built-in Field Types', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_builtin_field_types',
				'screen_icon' => 'options-general',	// one of the screen type from the below can be used.
				/*	Screen Types:
					'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
					'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
					'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',		 
				*/							
				'order' => 1,	// optional
			),
			array(
				'title' => __( 'Custom Field Types', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_custom_field_types',
				'screen_icon' => 'options-general',
				'order' => 2,	// optional
			),			
			array(
				'title' => __( 'Manage Options', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_manage_options',
				'screen_icon' => 'link-manager',	
				'order' => 3,	// optional
			),
			array(
				'title' => __( 'Sample Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_sample_page',
				'screen_icon' => dirname( __FILE__ ) . '/asset/image/wp_logo_bw_32x32.png',	// the icon file path can be used
			),					
			array(
				'title' => __( 'Hidden Page', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_hidden_page',
				'screen_icon' => plugins_url( 'asset/image/wp_logo_bw_32x32.png', __FILE__ ),	// the icon url can be used
				'show_in_menu' => false,
			),						
			array(
				'title' => __( 'Read Me', 'admin-page-framework-demo' ),
				'page_slug' => 'apf_read_me',
				'screen_icon' => 'page',
			),			
			array(
				'title' => __( 'Documentation', 'admin-page-framework-demo' ),
				'href' => 'http://admin-page-framework.michaeluno.jp/en/v2/',
				'page_heading_tab_visibility' => false,
			)
		);
				
		$this->addInPageTabs(
			/*
			 * Built-in Field Types
			 * */
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'textfields',
				'title'		=> 'Text Fields',
				'order'		=> 1,				
			),		
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'selectors',
				'title'		=> 'Selectors',
			),					
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'files',
				'title'		=> __( 'Files', 'admin-page-framework-demo' ),
			),
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'checklist',
				'title'		=> 'Checklist',
			),					
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'misc',
				'title'		=> 'MISC',	
			),		
			array(
				'page_slug'	=> 'apf_builtin_field_types',
				'tab_slug'	=> 'verification',
				'title'		=> 'Verification',	
			)
		);
		$this->addInPageTabs(
			array(
				'page_slug'	=> 'apf_custom_field_types',
				'tab_slug'	=> 'geometry',
				'title'		=> __( 'Geometry', 'admin-page-framework-demo' ),	
			),
			array(
				'page_slug'	=> 'apf_custom_field_types',
				'tab_slug'	=> 'date',
				'title'		=> __( 'Date & Time', 'admin-page-framework-demo' ),	
			),
			array(
				'page_slug'	=> 'apf_custom_field_types',
				'tab_slug'	=> 'dial',
				'title'		=> __( 'Dials', 'admin-page-framework-demo' ),	
			),
			array(
				'page_slug'	=> 'apf_custom_field_types',
				'tab_slug'	=> 'font',
				'title'		=> __( 'Fonts', 'admin-page-framework-demo' ),	
			),			
			array()
		);
		$this->addInPageTabs(
			/*
			 * Manage Options
			 * */
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'saved_data',
				'title'		=> 'Saved Data',
			),
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'properties',
				'title'		=> __( 'Properties', 'admin-page-framework-demo' ),
			),
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'messages',
				'title'		=> __( 'Messages', 'admin-page-framework-demo' ),
			),			
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'export_import',
				'title'		=> __( 'Export / Import', 'admin-page-framework-demo' ),			
			),
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'delete_options',
				'title'		=> __( 'Reset', 'admin-page-framework-demo' ),
				'order'		=> 99,	
			),						
			array(
				'page_slug'	=> 'apf_manage_options',
				'tab_slug'	=> 'delete_options_confirm',
				'title'		=> __( 'Reset Confirmation', 'admin-page-framework-demo' ),
				'show_in_page_tab'			=> true,
				'parent_tab_slug' => 'delete_options',
				'order'		=> 97,
			)
		);
		$this->addInPageTabs(
			/*
			 * Read Me
			 * */
			array(
				'page_slug'	=> 'apf_read_me',
				'tab_slug'	=> 'description',
				'title'		=> __( 'Description', 'admin-page-framework-demo' ),
			),				
			array(
				'page_slug'	=> 'apf_read_me',
				'tab_slug'	=> 'installation',
				'title'		=> __( 'Installation', 'admin-page-framework-demo' ),
			),	
			array(
				'page_slug'	=> 'apf_read_me',
				'tab_slug'	=> 'frequently_asked_questions',
				'title'		=> __( 'FAQ', 'admin-page-framework-demo' ),
			),		
			array(
				'page_slug'	=> 'apf_read_me',
				'tab_slug'	=> 'other_notes',
				'title'		=> __( 'Other Notes', 'admin-page-framework-demo' ),
			),					
			array(
				'page_slug'	=> 'apf_read_me',
				'tab_slug'	=> 'changelog',
				'title'		=> __( 'Change Log', 'admin-page-framework-demo' ),
			),						
			array()
		);			
		
		// Page style.
		$this->setPageHeadingTabsVisibility( false );		// disables the page heading tabs by passing false.
		$this->setPageTitleVisibility( false, 'apf_read_me' );	// disable the page title of a specific page.
		$this->setInPageTabTag( 'h2' );		
		// $this->showInPageTabs( false, 'apf_read_me' );	// in-page tabs can be disabled like so.
		
		// Enqueue styles - $this->enqueueStyle(  'stylesheet url / path to the WordPress directory here' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
		$sStyleHandle = $this->enqueueStyle(  dirname( __FILE__ ) . '/asset/css/code.css', 'apf_manage_options' );
		$sStyleHandle = $this->enqueueStyle(  plugins_url( 'asset/css/readme.css' , __FILE__ ) , 'apf_read_me' );
		
		// Enqueue scripts - $this->enqueueScript(  'script url / relative path to the WordPress directory here' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
		$this->enqueueScript(  
			plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
			'apf_read_me', 	// page slug
			'', 	// tab slug
			array(
				'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
				'translation' => array( 
					'a' => 'hello world!',
					'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
				),
			)
		);
			
		// Contextual help tabs.
		$this->addHelpTab( 
			array(
				'page_slug'					=> 'apf_builtin_field_types',	// ( mandatory )
				// 'page_tab_slug'			=> null,	// ( optional )
				'help_tab_title'			=> 'Admin Page Framework',
				'help_tab_id'				=> 'admin_page_framework',	// ( mandatory )
				'help_tab_content'			=> __( 'This contextual help text can be set with the <code>addHelpTab()</code> method.', 'admin-page-framework' ),
				'help_tab_sidebar_content'	=> __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
			)
		);
		
		// Add setting sections
		$this->addSettingSections(
			array(
				'section_id'		=> 'text_fields',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'textfields',
				'title'			=> 'Text Fields',
				'description'	=> 'These are text type fields.',
				'order'			=> 10,
			),	
			array(
				'section_id'		=> 'selectors',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'selectors',
				'title'			=> 'Selectors and Checkboxes',
				'description'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
			),
			array(
				'section_id'		=> 'sizes',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'selectors',
				'title'			=> 'Sizes',
			),			
			array(
				'section_id'		=> 'image_select',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'files',
				'title'			=> 'Image Selector',
				'description'	=> 'Set an image url with jQuwey based image selector.',
			),
			array(
				'section_id'		=> 'color_picker',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'misc',
				'title'			=> __( 'Colors', 'admin-page-framework-demo' ),
			),
			array(
				'section_id'		=> 'media_upload',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'files',
				'title'			=> __( 'Media Uploader', 'admin-page-framework-demo' ),
				'description'	=> __( 'Upload binary files in addition to images.', 'admin-page-framework-demo' ),
			),
			array(
				'section_id'		=> 'checklists',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'checklist',
				'title'			=> 'Checklists',
				'description'	=> 'Post type and taxonomy checklists ( custom checkbox ).',
			),	
			array(
				'section_id'		=> 'hidden_field',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'misc',
				'title'			=> 'Hidden Fields',
				'description'	=> 'These are hidden fields.',
			),								
			array(
				'section_id'		=> 'file_uploads',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'files',
				'title'			=> __( 'File Uploads', 'admin-page-framework-demo' ),
				'description'	=> __( 'These are upload fields. Check the <code>$_FILES</code> variable in the validation callback method that indicates the temporary location of the uploaded files.', 'admin-page-framework-demo' ),
			),			
			array(
				'section_id'		=> 'submit_buttons',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'misc',
				'title'			=> __( 'Submit Buttons', 'admin-page-framework-demo' ),
				'description'	=> __( 'These are custom submit buttons.', 'admin-page-framework-demo' ),
			),			
			array(
				'section_id'		=> 'verification',
				'page_slug'		=> 'apf_builtin_field_types',
				'tab_slug'		=> 'verification',
				'title'			=> __( 'Verify Submitted Data', 'admin-page-framework-demo' ),
				'description'	=> __( 'Show error messages when the user submits improper option value.', 'admin-page-framework-demo' ),
			),					
			array()
		);
		
		$this->addSettingSections(	
			array(
				'section_id'		=> 'geometry',
				'page_slug'		=> 'apf_custom_field_types',
				'tab_slug'		=> 'geometry',
				'title'			=> __( 'Geometry Custom Field Type', 'admin-page-framework-demo' ),
				'description'	=> __( 'This is a custom field type defined externally.', 'admin-page-framework-demo' ),
			),				
			array(
				'section_id'		=> 'date_pickers',
				'page_slug'		=> 'apf_custom_field_types',
				'tab_slug'		=> 'date',
				'title'			=> __( 'Date Custom Field Type', 'admin-page-framework' ),
				'description'	=> __( 'These are date and time pickers.', 'admin-page-framework-demo' ),
			),
			array(
				'section_id'		=> 'dial',
				'page_slug'		=> 'apf_custom_field_types',
				'tab_slug'		=> 'dial',
				'title'			=> __( 'Dial Custom Field Type', 'admin-page-framework-demo' ),
			),
			array(
				'section_id'		=> 'font',
				'page_slug'		=> 'apf_custom_field_types',
				'tab_slug'		=> 'font',
				'title'			=> __( 'Font Custom Field Type', 'admin-page-framework-demo' ),
				'description' => __( 'This is still experimental.', 'admin-page-framework-demo' ),				
			),
			array()
		);
		
		$this->addSettingSections(	
			array(
				'section_id'		=> 'submit_buttons_manage',
				'page_slug'		=> 'apf_manage_options',
				'tab_slug'		=> 'delete_options',
				'title'			=> 'Reset Button',
				'order'			=> 10,
			),			
			array(
				'section_id'		=> 'submit_buttons_confirm',
				'page_slug'		=> 'apf_manage_options',
				'tab_slug'		=> 'delete_options_confirm',
				'title'			=> 'Confirmation',
				'description'	=> "<div class='settings-error error'><p><strong>Are you sure you want to delete all the options?</strong></p></div>",
				'order'			=> 10,
			),				
			array(
				'section_id'		=> 'exports',
				'page_slug'		=> 'apf_manage_options',
				'tab_slug'		=> 'export_import',
				'title'			=> 'Export Data',
				'description'	=> 'After exporting the options, change and save new options and then import the file to see if the options get restored.',
			),				
			array(
				'section_id'		=> 'imports',
				'page_slug'		=> 'apf_manage_options',
				'tab_slug'		=> 'export_import',
				'title'			=> 'Import Data',
			),			
			array()			
		);
		
		// Add setting fields
		$this->addSettingFields(
			array(	// Single text field
				'field_id' => 'text',
				'section_id' => 'text_fields',
				'title' => __( 'Text', 'admin-page-framework-demo' ),
				'description' => __( 'Type something here.', 'admin-page-framework-demo' ),	// additional notes besides the form field
				'help' => __( 'This is a text field and typed text will be saved.', 'admin-page-framework-demo' ),
				'type' => 'text',
				'order' => 1,
				'default' => 123456,
				'size' => 40,
			),	
			array(	// Password Field
				'field_id' => 'password',
				'section_id' => 'text_fields',
				'title' => __( 'Password', 'admin-page-framework-demo' ),
				'tip' => __( 'This input will be masked.', 'admin-page-framework-demo' ),
				'type' => 'password',
				'help' => __( 'This is a password type field; the user\'s entered input will be masked.', 'admin-page-framework-demo' ),	//'
				'size' => 20,
			),			
			array(	// number Field
				'field_id' => 'number',
				'section_id' => 'text_fields',
				'title' => __( 'Number', 'admin-page-framework-demo' ),
				'type' => 'number',
			),					
			array(	// Multiple text fields
				'field_id' => 'text_multiple',
				'section_id' => 'text_fields',
				'title' => __( 'Multiple Text Fields', 'admin-page-framework-demo' ),
				'description' => 'These are multiple text fields.',	// additional notes besides the form field
				'help' => __( 'Multiple text fields can be passed by setting an array to the label key.', 'admin-page-framework-demo' ),
				'type' => 'text',
				'default' => array(
					'Hello World',
					'Foo bar',
					'Yes, we can.'
				),
				'label' => array( 
					'First Item: ', 
					'Second Item: ', 
					'Third Item: ' 
				),
				'size' => array(
					20,
					40,
					60,
				),
				'delimiter' => '<br />',
			),		
			array(	// Repeatable text fields
				'field_id' => 'text_repeatable',
				'section_id' => 'text_fields',
				'title' => __( 'Repeatable Text Fields', 'admin-page-framework-demo' ),
				'description' => __( 'Press + / - to add / remove the fields.', 'admin-page-framework-demo' ),
				'type' => 'text',
				'delimiter' => '',
				'size' => 60,
				'repeatable' => true,
				'default' => array( 'a', 'b', 'c', ),
			),				
			array(	// Text Area
				'field_id' => 'textarea',
				'section_id' => 'text_fields',
				'title' => __( 'Single Text Area', 'admin-page-framework-demo' ),
				'description' => __( 'Type a text string here.', 'admin-page-framework-demo' ),
				'type' => 'textarea',
				'default' => 'Hello World! This is set as the default string.',
				'rows' => 6,
				'cols' => 60,
			),
			array(	// Repeatable Text Areas
				'field_id' => 'textarea_repeatable',
				'section_id' => 'text_fields',
				'title' => __( 'Repeatable Text Areas', 'admin-page-framework-demo' ),
				'type' => 'textarea',
				'repeatable' => true,
				'delimiter' => '',
				'rows' => 3,
				'cols' => 60,
			),			
			array(	// Rich Text Editors
				'field_id' => 'rich_textarea',
				'section_id' => 'text_fields',
				'title' => 'Rich Text Area',
				'type' => 'textarea',
				'label' => array(
					'default' => '',
					'custom' => '',
				),
				'vRich' => array( 
					'default' => true,	// just pass non empty value for the default rich editor.
					'custom' => array( 'media_buttons' => false, 'tinymce' => false ),	// pass the setting array to customize the editor. For the setting argument, see http://codex.wordpress.org/Function_Reference/wp_editor.
				),
			),			
			array(	// Multiple text areas
				'field_id' => 'textarea_multiple',
				'section_id' => 'text_fields',
				'title' => 'Multiple Text Areas',
				'description' => 'These are multiple text areas.',
				'type' => 'textarea',
				'label' => array(
					'First Text Area: ',
					'Second Text Area: ',
					'Third Text Area: ',
				),
				'default' => array( 
					'The first default text.',
					'The second default text.',
					'The third default text.',
				),
				'rows' => array(
					5,
					3,
					2,
				),
				'cols' => array(
					60,
					40,
					20,
				),
				'delimiter' => '<br />',
			)
		);
		$this->addSettingFields(
			array(	// Single Drop-down List
				'field_id' => 'select',
				'section_id' => 'selectors',
				'title' => 'Dropdown List',
				'description' => 'This is a drop down list.',
				'help' => __( 'This is the <em>select</em> field type.', 'admin-page-framework' ),
				'type' => 'select',
				'default' => 2,
				'label' => array( 'red', 'blue', 'yellow', 'orange' )
			),	
			array(	// Single Drop-down List with Multiple Options
				'field_id' => 'select_multiple_options',
				'section_id' => 'selectors',
				'title' => __( 'Dropdown List with Multiple Options', 'admin-page-framework-demo' ),
				'description' => __( 'Press the Shift key to select multiple items.', 'admin-page-framework-demo' ),
				'help' => __( 'This is the <em>select</em> field type with multiple elements.', 'admin-page-framework' ),
				'type' => 'select',
				'vMultiple' => true,
				'default' => 2,
				'size' => 10,	
				'vWidth' => '200px',	// The width property value of CSS.
				'label' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'October', 'December' )
			),			
			array(	// Drop-down Lists with Mixed Types
				'field_id' => 'select_mixed',
				'section_id' => 'selectors',
				'title' => __( 'Multiple Dropdown Lists with Mixed Types', 'admin-page-framework-demo' ),
				'description' => __( 'This is multiple sets of drop down list.', 'admin-page-framework-demo' ),
				'type' => 'select',
				'label' => array( 
					array( 'dark', 'light' ),
					array( 'river', 'mountain', 'sky', ),
					array( 'Monday', 'Tuesday', 'Wednessday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
				),
				'size' => array(
					1,
					1,
					5,
				),
				'default' => array(
					1,
					2,
					0
				),
				'vMultiple' => array(
					false,	// normal
					false,	// normal
					true,	// multiple options
				),
			),					
			array(	// Single set of radio buttons
				'field_id' => 'radio',
				'section_id' => 'selectors',
				'title' => 'Radio Button',
				'description' => 'Choose one from the radio buttons.',
				'type' => 'radio',
				'label' => array( 'a' => 'apple', 'b' => 'banana', 'c' => 'cherry' ),
				'default' => 'b',	// banana				
			),
			array(	// Multiple sets of radio buttons
				'field_id' => 'radio_multiple',
				'section_id' => 'selectors',
				'title' => 'Multiple Sets of Radio Buttons',
				'description' => 'Multiple sets of radio buttons.',
				'type' => 'radio',
				'label' => array( 
					array( 1 => 'one', 2 => 'two' ),
					array( 3 => 'three', 4 => 'four', 5 => 'five' ),
					array( 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine' ),
				),
				'default' => array(
					2,
					4,
					8,
				),
				'delimiter' => '<br />',
			),			
			array(	// Single Checkbox
				'field_id' => 'checkbox',
				'section_id' => 'selectors',
				'title' => 'Single Checkbox',
				'tip' => 'The description key can be omitted though.',
				'description' => 'Check box\'s label can be a string, not an array.',	//'
				'type' => 'checkbox',
				'label' => 'One',	// notice that the label key is not an array
				'default' => False,
			),	
			array(	// Multiple Checkboxes
				'field_id' => 'checkboxes',
				'section_id' => 'selectors',
				'title' => 'Multiple Checkboxes',
				'description' => 'The description can be omitted.',
				'type' => 'checkbox',
				'label' => array( 'moon' => 'Moon', 'earth' => 'Earth', 'sun' => 'Sun', 'mars' => 'Mars' ),
				'default' => array( 'moon' => True, 'earth' => False, 'sun' => True, 'mars' => False ),
			),
			array(	// Size
				'field_id'		=> 'size_filed',
				'section_id'		=> 'sizes',
				'title'			=> __( 'Size', 'admin-page-framework-demo' ),
				'help'			=> __( 'In order to set a default value for the size field type, an array with the \'size\' and the \'unit\' keys needs to be passed.', 'admin-page-framework-demo' ),
				'description'	=> __( 'The default units are the lengths for CSS.', 'admin-page-framework-demo' ),
				'type'			=> 'size',
				'default'			=> array( 'size' => 5, 'unit' => '%' ),
			),			
			array(	// Size with custom units
				'field_id'		=> 'size_custom_unit_filed',
				'section_id'		=> 'sizes',
				'title'			=> __( 'Size with Custom Units', 'admin-page-framework-demo' ),
				'help'			=> __( 'The units can be specified so it can be quantity, length, or capacity etc.', 'admin-page-framework-demo' ),
				'type'			=> 'size',
				'size_units'		=> array(
					'grain'	=> 'grains',
					'dram'	=> 'drams',
					'ounce'	=> 'ounces',
					'pounds'	=> 'pounds',
				),
				'default'			=> array( 'size' => 200, 'unit' => 'ounce' ),
			),						
			array(	// Multiple Sizes
				'field_id' => 'sizes_filed',
				'section_id' => 'sizes',
				'title' => __( 'Multiple Sizes', 'admin-page-framework-demo' ),
				'type' => 'size',
				'label' => array(
					'weight'	=> __( 'Weight', 'admin-page-framework-demo' ),
					'length'	=> __( 'Length', 'admin-page-framework-demo' ),
					'capacity'	=> __( 'File Size', 'admin-page-framework-demo' ),
				),
				'size_units' => array( 	// notice that the array key structure corresponds to the label array's.
					'weight'	=> array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
					'length'	=> array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
					'capacity'	=> array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
				),
				'default' => array(
					'weight' => array( 'size' => 15, 'unit' => 'g' ),
					'length' => array( 'size' => 100, 'unit' => 'mm' ),
					'capacity' => array( 'size' => 30, 'unit' => 'mb' ),
				),		
				'delimiter' => '<br />',
			)
		);
		$this->addSettingFields(			
			array( // Image Selector
				'field_id' => 'image_select_field',
				'section_id' => 'image_select',
				'title' => __( 'Select an Image', 'admin-page-framework-demo' ),
				'type' => 'image',
				'label' => array( 'First Image', 'Second Image', 'Third Image' ),
				'default' => array( admin_url( 'images/wordpress-logo.png' ) ), 
				'allow_external_source' => false,
			),		
			array( // Image selector with additional attributes
				'field_id' => 'image_with_attributes',
				'section_id' => 'image_select',
				'title' => __( 'Save Image Attributes', 'admin-page-framework-demo' ),
				'type' => 'image',
				'delimiter' => '',
				'attributes_to_capture' => array( 'alt', 'id', 'title', 'caption', 'width', 'height', 'align', 'link' ),	// some attributes cannot be captured with external URLs and the old media uploader.
			),					
			array(	// Repeatable Image Fields
				'field_id' => 'image_select_field_repeater',
				'section_id' => 'image_select',
				'title' => __( 'Repeatable Image Fields', 'admin-page-framework-demo' ),
				'delimiter' => '',
				'repeatable' => true,
				'type' => 'image',
			),
			array( // Media File
				'field_id' => 'media_field',
				'section_id' => 'media_upload',
				'title' => __( 'Media File', 'admin-page-framework-demo' ),
				'type' => 'media',
				'allow_external_source' => false,
			),	
			array( // Media File with Attributes
				'field_id' => 'media_with_attributes',
				'section_id' => 'media_upload',
				'title' => __( 'Media File with Attributes', 'admin-page-framework-demo' ),
				'type' => 'media',
				'attributes_to_capture' => array( 'id', 'caption', 'description' ),
			),				
			array( // Media Files
				'field_id' => 'media_fields',
				'section_id' => 'media_upload',
				'title' => __( 'Multiple Media Files', 'admin-page-framework-demo' ),
				'type' => 'media',
				'repeatable' => true,
			),				
			array( // Single File Upload Field
				'field_id' => 'file_single',
				'section_id' => 'file_uploads',
				'title' => __( 'Single File Upload', 'admin-page-framework-demo' ),
				'type' => 'file',
				'label' => 'Select the file:',
			),					
			array( // Multiple File Upload Fields
				'field_id' => 'file_multiple',
				'section_id' => 'file_uploads',
				'title' => __( 'Multiple File Uploads', 'admin-page-framework-demo' ),
				'type' => 'file',
				'label' => array( 'Fist File:', 'Second File:', 'Third File:' ),
				'delimiter' => '<br />',
			),	
			array( // Single File Upload Field
				'field_id' => 'file_repeatable',
				'section_id' => 'file_uploads',
				'title' => __( 'Repeatable File Uploads', 'admin-page-framework-demo' ),
				'type' => 'file',
				'repeatable' => true,
			)
		);
		$this->addSettingFields(			
			array(
				'field_id' => 'post_type_checklist',
				'section_id' => 'checklists',
				'title' => 'Post Types',
				'type' => 'posttype',
			),											
			array(
				'field_id' => 'taxonomy_checklist',
				'section_id' => 'checklists',
				'title' => __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
				'type' => 'taxonomy',
				'height' => '200px',
				'taxonomy_slugs' => array( 'category', 'post_tag' ),
			),				
			array(
				'field_id' => 'taxonomy_checklist_all',
				'section_id' => 'checklists',
				'title' => __( 'All Taxonomies', 'admin-page-framework-demo' ),
				'type' => 'taxonomy',
				'height' => '200px',
				'taxonomy_slugs' => get_taxonomies( '', 'names' ),
			)
		);
		$this->addSettingFields(			
			array( // Color Picker
				'field_id' => 'color_picker_field',
				'section_id' => 'color_picker',
				'title' => __( 'Color Picker', 'admin-page-framework-demo' ),
				'type' => 'color',
			),					
			array( // Multiple Color Pickers
				'field_id' => 'multiple_color_picker_field',
				'section_id' => 'color_picker',
				'title' => __( 'Multiple Color Pickers', 'admin-page-framework-demo' ),
				'type' => 'color',
				'label' => array( 'First Color', 'Second Color', 'Third Color' ),
				'delimiter' => '<br />',
			),				
			array( // Repeatable Color Pickers
				'field_id' => 'color_picker_repeatable_field',
				'section_id' => 'color_picker',
				'title' => __( 'Repeatable Color Picker Fields', 'admin-page-framework-demo' ),
				'type' => 'color',
				'repeatable' => true,
			),										
			array( // Single Hidden Field
				'field_id' => 'hidden_single',
				'section_id' => 'hidden_field',
				'title' => __( 'Single Hidden Field', 'admin-page-framework-demo' ),
				'type' => 'hidden',
				'default' => 'test value',
				'label' => 'Test label.',
			),			
			array( // Multiple Hidden Fields
				'field_id' => 'hidden_miltiple',
				'section_id' => 'hidden_field',
				'title' => 'Multiple Hidden Field',
				'type' => 'hidden',
				'default' => array( 'a', 'b', 'c' ),
				'label' => array( 'Hidden Field 1', 'Hidden Field 2', 'Hidden Field 3' ),
			),							
			array( // Submit button as a link
				'field_id' => 'submit_button_link',
				'section_id' => 'submit_buttons',
				'title' => 'Link Button',
				'type' => 'submit',
				'description' => 'This button serves as a hyper link.',
				'label' => array( 'Google', 'Yahoo', 'Bing' ),
				'links'	=> array( 'http://www.google.com', 'http://www.yahoo.com', 'http://www.bing.com' ),
				'class_attribute' => 'button button-secondary',
				'delimiter' => '',
			),			
			array( // Submit button as a redirect
				'field_id' => 'submit_button_redirect',
				'section_id' => 'submit_buttons',
				'title' => 'Redirect Button',
				'type' => 'submit',
				'description' => 'Unlike the above link buttons, this button saves the options and then redirects to: ' . admin_url(),
				'label' => 'Dashboard',
				'redirect_url'	=> admin_url(),
				'class_attribute' => 'button button-secondary',
			),
			array( // Reset Submit button
				'field_id' => 'submit_button_reset',
				'section_id' => 'submit_buttons',
				'title' => 'Reset Button',
				'type' => 'submit',
				'label' => __( 'Reset', 'admin-page-framework-demo' ),
				'is_reset' => true,
				// 'class_attribute' => 'button button-secondary',
			)
		);
		$this->addSettingFields(			
			array(
				'field_id' => 'verify_text_field',
				'section_id' => 'verification',
				'title' => __( 'Verify Text Input', 'admin-page-framework-demo' ),
				'type' => 'text',
				'description' => __( 'Enter a non numeric value here.', 'admin-page-framework-demo' ),
			),
			array(
				'field_id' => 'verify_text_field_submit',	// this submit field ID can be used in a validation callback method
				'section_id' => 'verification',
				'type' => 'submit',		
				'label' => __( 'Verify', 'admin-page-framework-demo' ),
			)
		);	
		
		$this->addSettingFields(			
			array(
				'field_id' => 'geometrical_coordinates',
				'section_id' => 'geometry',
				'title' => __( 'Geometrical Coordinates', 'admin-page-framework-demo' ),
				'type' => 'geometry',
				'description' => __( 'Get the coordinates from the map.', 'admin-page-framework-demo' ),
				'default' => array(
					'latitude' => 20,
					'longitude' => 20,
				),
			)
		);
		$this->addSettingFields(
			array(	// Single date picker
				'field_id' => 'date',
				'section_id' => 'date_pickers',
				'title' => __( 'Date', 'admin-page-framework-demo' ),
				'type' => 'date',
				'date_format' => 'yy/mm/dd',	// yy/mm/dd is the default format.
			),			
			array(	// Multiple date pickers
				'field_id' => 'dates',
				'section_id' => 'date_pickers',
				'title' => __( 'Dates', 'admin-page-framework-demo' ),
				'type' => 'date',
				'label' => array( 
					'start' => __( 'Start Date: ', 'amin-page-framework-demo' ), 
					'end' => __( 'End Date: ', 'amin-page-framework-demo' ), 
				),
				'date_format' => 'yy-mm-dd',	// yy/mm/dd is the default format.
				'delimiter' => '<br />',
			),	
			array(	// Single time picker
				'field_id' => 'time',
				'section_id' => 'date_pickers',
				'title' => __( 'Time', 'admin-page-framework-demo' ),
				'type' => 'time',
				'time_format' => 'H:mm',	// H:mm is the default format.
			),		
			array(	// Single date time picker
				'field_id' => 'date_time',
				'section_id' => 'date_pickers',
				'title' => __( 'Date & Time', 'admin-page-framework-demo' ),
				'type' => 'date_time',
				'date_format' => 'yy-mm-dd',	// H:mm is the default format.
				'time_format' => 'H:mm',	// H:mm is the default format.
			),		
			array(	// Multiple date time pickers
				'field_id' => 'dates_time_multiple',
				'section_id' => 'date_pickers',
				'title' => __( 'Multiple Date and Time', 'admin-page-framework-demo' ),
				'description' => __( 'With different time formats', 'admin-page-framework-demo' ),
				'type' => 'date_time',
				'label' => array( 
					__( 'Default', 'amin-page-framework-demo' ), 
					__( 'AM PM', 'amin-page-framework-demo' ), 
					__( 'Time Zone', 'amin-page-framework-demo' ), 
				),
				'time_format' => array(
					'H:mm',
					'hh:mm tt',
					'hh:mm tt z',
				),
				'date_format' => 'yy-mm-dd',	// yy/mm/dd is the default format.
				'delimiter' => '<br />',
			),				
			array()
		);
		$this->addSettingFields(			
			array(
				'field_id' => 'dials',
				'section_id' => 'dial',
				'title' => __( 'Multiple Dials', 'admin-page-framework-demo' ),
				'type' => 'dial',
				'label' => array(
					__( 'Disable display input', 'admin-page-framework-demo' ),
					__( 'Cursor mode', 'admin-page-framework-demo' ),
					__( 'Display previous value (effect)', 'admin-page-framework-demo' ),				
					__( 'Angle offset', 'admin-page-framework-demo' ),				
					__( 'Angle offset and arc', 'admin-page-framework-demo' ),				
					__( '5-digit values, step 1000', 'admin-page-framework-demo' ),				
				),
				// For details, see https://github.com/aterrien/jQuery-Knob
				'data_attribute' => array( 
					array(
						'width' => 100,
						'displayInput' => 'false',
					),
					array(
						'width' => 150,
						'cursor' => 'true',
						'thickness'	=> '.3', 
						'fgColor' => '#222222',
					),					
					array(
						'width' => 200,
						'min'	=> -100, 
						'displayPrevious'	=> 'true', // a boolean value also needs to be passed as string
					),
					array(
						'angleOffset' => 90,
						'linecap' => 'round',
					),
					array(
						'fgColor' => '#66CC66',
						'angleOffset' => -125,
						'angleArc' => 250,
					),
					array(
						'step' => 1000,
						'min' => -15000,
						'max' => 15000,
						'displayPrevious' => true,
					),                        
				),
			),
			array(
				'field_id' => 'dial_big',
				'section_id' => 'dial',
				'title' => __( 'Big', 'admin-page-framework-demo' ),
				'type' => 'dial',
				'data_attribute' => array(
					'width' => 400,
					'height' => 400,
				),
			),
			array()
		);
		
		$this->addSettingFields(			
			array(
				'field_id' => 'font_field',
				'section_id' => 'font',
				'title' => __( 'Font Upload', 'admin-page-framework-demo' ),
				'type' => 'font',
				'description' => __( 'Set the URL of the font.', 'admin-page-framework-demo' ),
			),
			array()
		);
		
		$this->addSettingFields(			
			array( // Delete Option Button
				'field_id' => 'submit_manage',
				'section_id' => 'submit_buttons_manage',
				'title' => 'Delete Options',
				'type' => 'submit',
				'class_attribute' => 'button-secondary',
				'label' => 'Delete Options',
				'links'	=> admin_url( 'admin.php?page=apf_manage_options&tab=delete_options_confirm' )
			),			
			array( // Delete Option Confirmation Button
				'field_id' => 'submit_delete_options_confirmation',
				'section_id' => 'submit_buttons_confirm',
				'title' => 'Delete Options',
				'type' => 'submit',
				'class_attribute' => 'button-secondary',
				'label' => 'Delete Options',
				'redirect_url'	=> admin_url( 'admin.php?page=apf_manage_options&tab=saved_data&settings-updated=true' )
			),			
			array(
				'field_id' => 'export_format_type',			
				'section_id' => 'exports',
				'title' => 'Export Format Type',
				'type' => 'radio',
				'description' => 'Choose the file format. Array means the PHP serialized array.',
				'label' => array( 'array' => 'Serialized Array', 'json' => 'JSON', 'text' => 'Text' ),
				'default' => 'array',
			),			
			array(	// Single Export Button
				'field_id' => 'export_single',
				'section_id' => 'exports',
				// 'title' => 'Single Export Button',
				'type' => 'export',
				'description' => __( 'Download the saved option data.', 'admin-page-framework-demo' ),
				'label' => 'Export Options',
			),
			array(	// Multiple Export Buttons
				'field_id' => 'export_multiple',
				'section_id' => 'exports',
				'title' => 'Multiple Export Buttons',
				'type' => 'export',
				'description' => __( 'Download the custom set data.', 'admin-page-framework-demo' ),
				'label' => array( 'Pain Text', 'JSON', 'Serialized Array' ),
				'export_file_name' => array( 'plain_text.txt', 'json.json', 'serialized_array.txt' ),
				'export_format' => array( 'text', 'json', 'array' ),
				'export_data' => array(
					'Hello World!',	// export plain text
					( array ) $this->oProp,	// export an object
					array( 'a', 'b', 'c' ),	// export a serialized array
				),
			),		
			array(
				'field_id' => 'import_format_type',			
				'section_id' => 'imports',
				'title' => 'Import Format Type',
				'type' => 'radio',
				'description' => 'The text format type will not set the option values properly. However, you can see that the text contents are directly saved in the database.',
				'label' => array( 'array' => 'Serialized Array', 'json' => 'JSON', 'text' => 'Text' ),
				'default' => 'array',
			),
			array(	// Single Import Button
				'field_id' => 'import_single',
				'section_id' => 'imports',
				'title' => 'Single Import Field',
				'type' => 'import',
				'description' => __( 'Upload the saved option data.', 'admin-page-framework-demo' ),
				'label' => 'Import Options',
				// 'vImportFormat' => isset( $_POST[ $this->oProp->sClassName ]['apf_manage_options']['imports']['import_format_type'] ) ? $_POST[ $this->oProp->sClassName ]['apf_manage_options']['imports']['import_format_type'] : 'array',
			),			
			array()
		);
		
 		$this->addLinkToPluginDescription( 
			"<a href='http://www.google.com'>Google</a>",
			"<a href='http://www.yahoo.com'>Yahoo!</a>",
			"<a href='http://en.michaeluno.jp'>miunosoft</a>",
			"<a href='https://github.com/michaeluno/admin-page-framework' title='Contribute to the GitHub repository!' >Repository</a>"
		);
		$this->addLinkToPluginTitle(
			"<a href='http://www.wordpress.org'>WordPress</a>"
		);
		
    }
		
	/*
	 * Built-in Field Types
	 * */
	public function do_apf_builtin_field_types() {
		submit_button();
	}
	
	/*
	 * Custon Field Types
	 * */
	public function do_apf_custom_field_types() {
		submit_button();
	}
	
	/*
	 * Manage Options
	 * */
	public function do_apf_manage_options_saved_data() {	// do_ + page slug + _ + tab slug
		?>
		<h3>Saved Data</h3>
		<?php
			echo $this->oDebug->getArray( $this->oProp->aOptions ); 
	}
	public function do_apf_manage_options_properties() {	// do_ + page slug + _ + tab slug
		?>
		<h3><?php _e( 'Framework Properties', 'admin-page-framework-demo' ); ?></h3>
		<p><?php _e( 'You can view and modify the property values stored in the framework.', 'admin-page-framework-demo' ); ?></p>
		<pre><code>$this-&gt;oDebug-&gt;getArray( get_object_vars( $this-&gt;oProp ) );</code></pre>		
		<?php
			$this->oDebug->dumpArray( get_object_vars( $this->oProp ) );
	}
	public function do_apf_manage_options_messages() {	// do_ + page slug + _ + tab slug
		?>
		<h3><?php _e( 'Framework Messages', 'admin-page-framework-demo' ); ?></h3>
		<p><?php _e( 'You can change the framework\'s defined internal messages by directly modifying the <code>$aMessages</code> array in the <code>oMsg</code> object.', 'admin-page-framework-demo' ); // ' syntax fixer ?></p>
		<pre><code>echo $this-&gt;oDebug-&gt;getArray( $this-&gt;oMsg-&gt;aMessages );</code></pre>
		<?php
			echo $this->oDebug->getArray( $this->oMsg->aMessages );
	}
	
	/*
	 * The sample page and the hidden page
	 */
	public function do_apf_sample_page() {
		
		echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-demo' ) . "</p>";
		$sLinkToHiddenPage = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) );
		echo "<a href='{$sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-demo' ). "</a>";
	
	}
	public function do_apf_hidden_page() {
		
		echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-demo' ) . "</p>";
		echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-demo' ) . "</p>";
		$sLinkToGoBack = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) );
		echo "<a href='{$sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-demo' ). "</a>";
		
	}
	
	/*
	 * Import and Export Callbacks
	 * */
	public function export_format_APF_Demo_export_single( $sFormatType, $sFieldID ) {	// export_format_ + {extended class name} + _ + {field id}
		
		return isset( $_POST[ $this->oProp->sOptionKey ]['apf_manage_options']['exports']['export_format_type'] ) 
			? $_POST[ $this->oProp->sOptionKey ]['apf_manage_options']['exports']['export_format_type']
			: $sFormatType;
		
	}	
	public function import_format_apf_manage_options_export_import( $sFormatType, $sFieldID ) {	// import_format_ + page slug + _ + tab slug
		
		return isset( $_POST[ $this->oProp->sOptionKey ]['apf_manage_options']['imports']['import_format_type'] ) 
			? $_POST[ $this->oProp->sOptionKey ]['apf_manage_options']['imports']['import_format_type']
			: $sFormatType;
		
	}
	public function import_APF_Demo_import_single( $vData, $aOldOptions, $sFieldID, $sInputID, $sImportFormat, $sOptionKey ) {	// import_ + {extended class name} + _ + {field id}

		if ( $sImportFormat == 'text' ) {
			$this->setSettingNotice( __( 'The text import type is not supported.', 'admin-page-framework-demo' ) );
			return $aOldOptions;
		}
		
		$this->setSettingNotice( __( 'Importing options are validated.', 'admin-page-framework-demo' ), 'updated' );
		return $vData;
		
	}
	
	/*
	 * Validation Callbacks
	 * */
	public function validation_APF_Demo_verify_text_field_submit( $aNewInput, $aOldOptions ) {	// validation_ + {extended class name} + _ + {field ID}
		
		// Set a flag.
		$bVerified = true;
		
		// We store values that have an error in an array and pass it to the setFieldErrors() method.
		// It internally stores the error array in a temporary area of the database called transient.
		// The used name of the transient is a md5 hash of 'instantiated class name' + '_' + 'page slug'. 
		// The library class will search for this transient when it renders the form fields 
		// and if it is found, it will display the error message set in the field array. 
		$aErrors = array();

		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
		if ( ! is_numeric( $aNewInput['apf_builtin_field_types']['verification']['verify_text_field'] ) ) {
			
			// Start with the section key in $aErrors, not the key of page slug.
			$aErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $aNewInput['apf_builtin_field_types']['verification']['verify_text_field'];	
			$bVerified = false;
			
		}
		
		// An invalid value is found.
		if ( ! $bVerified ) {
		
			// Set the error array for the input fields.
			$this->setFieldErrors( $aErrors );		
			$this->setSettingNotice( 'There was an error in your input.' );
			return $aOldOptions;
			
		}
				
		return $aNewInput;		
		
	}
	public function validation_apf_builtin_field_types_files( $aInput, $aOldPageOptions ) {	// validation_ + page slug + _ + tab slug

		// Display the uploaded file information.
		$aFileErrors = array();
		$aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['apf_builtin_field_types']['file_uploads']['file_single'];
		$aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['apf_builtin_field_types']['file_uploads']['file_multiple'][0];
		$aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['apf_builtin_field_types']['file_uploads']['file_multiple'][1];
		$aFileErrors[] = $_FILES[ $this->oProp->sOptionKey ]['error']['apf_builtin_field_types']['file_uploads']['file_multiple'][2];
		foreach( $_FILES[ $this->oProp->sOptionKey ]['error']['apf_builtin_field_types']['file_uploads']['file_repeatable'] as $aFile )
			$aFileErrors[] = $aFile;
			
		if ( in_array( 0, $aFileErrors ) ) 
			$this->setSettingNotice( '<h3>File(s) Uploaded</h3>' . $this->oDebug->getArray( $_FILES ), 'updated' );
		
		return $aInput;
		
	}
	
	public function validation_APF_Demo( $aInput, $aOldOptions ) {
		
		// If the delete options button is pressed, return an empty array that will delete the entire options stored in the database.
		if ( isset( $_POST[ $this->oProp->sOptionKey ]['apf_manage_options']['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) 
			return array();
			
		return $aInput;
		
	}
			
	/*
	 * Read Me
	 * */ 
	public function do_before_apf_read_me() {		// do_before_ + page slug 

		include_once( dirname( __FILE__ ) . '/third-party/wordpress-plugin-readme-parser/parse-readme.php' );
		$this->oWPReadMe = new WordPress_Readme_Parser;
		$this->aWPReadMe = $this->oWPReadMe->parse_readme( dirname( __FILE__ ) . '/readme.txt' );
	
	}
	public function do_apf_read_me_description() {		// do_ + page slug + _ + tab slug
		echo $this->aWPReadMe['sections']['description'];
		// var_dump( $this->aWPReadMe );
	}
	public function do_apf_read_me_installation() {		// do_ + page slug + _ + tab slug
		// echo htmlspecialchars( $this->aWPReadMe['sections']['installation'], ENT_QUOTES, bloginfo( 'charset' ) );
		echo $this->aWPReadMe['sections']['installation'];
	}
	public function do_apf_read_me_frequently_asked_questions() {	// do_ + page slug + _ + tab slug
		echo $this->aWPReadMe['sections']['frequently_asked_questions'];
	}
	public function do_apf_read_me_other_notes() {
		echo $this->aWPReadMe['remaining_content'];
	}
	public function do_apf_read_me_screenshots() {		// do_ + page slug + _ + tab slug
		echo $this->aWPReadMe['sections']['screenshots'];
	}	
	public function do_apf_read_me_changelog() {		// do_ + page slug + _ + tab slug
		echo $this->aWPReadMe['sections']['changelog'];
	}
	
	/*
	 * Custom field types - This is another way to register a custom field type. 
	 * This method gets fired when the framework tries to define field types. 
	 */
 	public function field_types_APF_Demo( $aFieldTypeDefinitions ) {	// field_types_ + {extended class name}
				
		// 1. Include the file that defines the custom field type. 
		// This class should extend the predefined abstract class that the library prepares already with necessary methods.
		$sFilePath = dirname( __FILE__ ) . '/third-party/geometry-custom-field-type/GeometryCustomFieldType.php';
		if ( file_exists( $sFilePath ) ) include_once( $sFilePath );
		
		// 2. Instantiate the class - use the getDefinitionArray() method to get the field type definition array.
		// Then assign it to the filtering array with the key of the field type slug. 
		$oFieldType = new GeometryCustomFieldType( 'APF_Demo', 'geometry', $this->oMsg );
		$aFieldTypeDefinitions['geometry'] = $oFieldType->getDefinitionArray();
		
		// 3. Return the modified array.
		return $aFieldTypeDefinitions;
		
	} 
	
}
// Instantiate the main framework class so that the pages and form fields will be created. 
if ( is_admin() )  
	new APF_Demo;			
	
class APF_PostType extends AdminPageFramework_PostType {
	
	public function start_APF_PostType() {	// start_ + extended class name
	
		// the setUp() method is too late to add taxonomies. So we use start_{class name} action hook.
	
		$this->setAutoSave( false );
		$this->setAuthorTableFilter( true );
		$this->addTaxonomy( 
			'apf_sample_taxonomy', // taxonomy slug
			array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
				'labels' => array(
					'name' => 'Sample Genre',
					'add_new_item' => 'Add New Genre',
					'new_item_name' => "New Genre"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_table_filter' => true,	// framework specific key
				'show_in_sidebar_menus' => true,	// framework specific key
			)
		);
		$this->addTaxonomy( 
			'apf_second_taxonomy', 
			array(
				'labels' => array(
					'name' => 'Non Hierarchical',
					'add_new_item' => 'Add New Taxonomy',
					'new_item_name' => "New Sample Taxonomy"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => false,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_table_filter' => true,	// framework specific key
				'show_in_sidebar_menus' => false,	// framework specific key
			)
		);

		$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
		$this->setFooterInfoRight( '<br />Custom text on the right hand side' );
		
	}
	
	/*
	 * Extensible methods
	 */
	public function setColumnHeader( $aColumnHeader ) {
		$aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
			'title'			=> __( 'Title', 'admin-page-framework' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> __( 'Author', 'admin-page-framework' ),		// Post author.
			// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
			// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> __( 'Date', 'admin-page-framework' ), 	// The date and publish status of the post. 
			'samplecolumn'			=> __( 'Sample Column' ),
		);		
		return array_merge( $aColumnHeader, $aColumnHeaders );
	}
	// public function setSortableColumns( $aColumns ) {
		// return array_merge( $aColumns, $this->oProp->aColumnSortable );		
	// }	
	
	/*
	 * Callback methods
	 */
	public function cell_apf_posts_samplecolumn( $sCell, $iPostID ) {	// cell_ + post type + column key
		
		return "the post id is : {$iPostID}";
		
	}

	
}
new APF_PostType( 
	'apf_posts', 	// post type slug
	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		'labels' => array(
			'name' => 'Admin Page Framework',
			'all_items' => __( 'Sample Posts', 'admin-page-framework-demo' ),
			'singular_name' => 'Admin Page Framework',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New APF Post',
			'edit' => 'Edit',
			'edit_item' => 'Edit APF Post',
			'new_item' => 'New APF Post',
			'view' => 'View',
			'view_item' => 'View APF Post',
			'search_items' => 'Search APF Post',
			'not_found' => 'No APF Post found',
			'not_found_in_trash' => 'No APF Post found in Trash',
			'parent' => 'Parent APF Post'
		),
		'public' => true,
		'menu_position' => 110,
		// 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),	// 'custom-fields'
		'supports' => array( 'title' ),
		'taxonomies' => array( '' ),
		'has_archive' => true,
		'show_admin_column' => true,	// ( framework specific key ) this is for custom taxonomies to automatically add the column in the listing table.
		'menu_icon' => plugins_url( 'asset/image/wp-logo_16x16.png', __FILE__ ),
		// ( framework specific key ) this sets the screen icon for the post type.
		'screen_icon' => dirname( __FILE__  ) . '/asset/image/wp-logo_32x32.png', // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', __FILE__ )
	)		
);	// should not use "if ( is_admin() )" for the this class because posts of custom post type can be accessed from the front-end pages.
	
	

class APF_MetaBox extends AdminPageFramework_MetaBox {
	
	public function start_APF_MetaBox() {
		
		add_filter( 'the_content', array( $this, 'printMetaFieldValues' ) );
		
	}
	
	public function setUp() {
		
		$this->addHelpText( 
			__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
			__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
		);
		
		$this->addSettingFields(
			array(
				'field_id'		=> 'sample_metabox_text_field',
				'title'			=> 'Text Input',
				'description'	=> 'The description for the field.',
				'type'			=> 'text',
				'help'			=> 'This is help text.',
				'help_aside'	=> 'This is additional help text which goes to the side bar of the help pane.',
			),
			array(
				'field_id'		=> 'sample_metabox_textarea_field',
				'title'			=> 'Textarea',
				'description'	=> 'The description for the field.',
				'help'			=> __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
				'type'			=> 'textarea',
				'cols'				=> 60,
				'default'			=> 'This is a default text.',
			),
			array(	// Rich Text Editor
				'field_id' 		=> 'sample_rich_textarea',
				'title' 			=> 'Rich Text Editor',
				'type' 			=> 'textarea',
				'vRich' 			=> true,	// array( 'media_buttons' => false )  <-- a setting array can be passed. For the specification of the array, see http://codex.wordpress.org/Function_Reference/wp_editor
			),				
			array(
				'field_id'		=> 'checkbox_field',
				'title'			=> 'Checkbox Input',
				'description'	=> 'The description for the field.',
				'type'			=> 'checkbox',
				'label'			=> 'This is a check box.',
			),
			array(
				'field_id'		=> 'select_filed',
				'title'			=> 'Select Box',
				'description'	=> 'The description for the field.',
				'type'			=> 'select',
				'label' => array( 
					'one' => __( 'One', 'demo' ),
					'two' => __( 'Two', 'demo' ),
					'three' => __( 'Three', 'demo' ),
				),
				'default' 			=> 'one',	// 0 means the first item
			),		
			array (
				'field_id'		=> 'radio_field',
				'title'			=> 'Radio Group',
				'description'	=> 'The description for the field.',
				'type'			=> 'radio',
				'label' => array( 
					'one' => __( 'Option One', 'demo' ),
					'two' => __( 'Option Two', 'demo' ),
					'three' => __( 'Option Three', 'demo' ),
				),
				'default' => 'one',
			),
			array (
				'field_id'		=> 'checkbox_group_field',
				'title'			=> 'Checkbox Group',
				'description'	=> 'The description for the field.',
				'type'			=> 'checkbox',
				'label' => array( 
					'one' => __( 'Option One', 'admin-page-framework-demo' ),
					'two' => __( 'Option Two', 'admin-page-framework-demo' ),
					'three' => __( 'Option Three', 'admin-page-framework-demo' ),
				),
				'default' => array(
					'one' => true,
					'two' => false,
					'three' => false,
				),
			),			
			array (
				'field_id'		=> 'image_field',
				'title'			=> 'Image',
				'description'	=> 'The description for the field.',
				'type'			=> 'image',
			),		
			array (
				'field_id'		=> 'color_field',
				'title'			=> __( 'Color', 'admin-page-framework-demo' ),
				'type'			=> 'color',
			),	
			array (
				'field_id'		=> 'size_field',
				'title'			=> __( 'Size', 'admin-page-framework-demo' ),
				'type'			=> 'size',
				'default'			=> array( 'size' => 5, 'unit' => '%' ),
			),						
			array (
				'field_id'		=> 'sizes_field',
				'title'			=> __( 'Multiple Sizes', 'admin-page-framework-demo' ),
				'type'			=> 'size',
				'label' => array(
					'weight'	=> __( 'Weight', 'admin-page-framework-demo' ),
					'length'	=> __( 'Length', 'admin-page-framework-demo' ),
					'capacity'	=> __( 'File Size', 'admin-page-framework-demo' ),
				),
				'size_units' => array( 	// notice that the array key structure corresponds to the label array's.
					'weight'	=> array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
					'length'	=> array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
					'capacity'	=> array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
				),
				'default' => array(
					'weight' => array( 'size' => 15, 'unit' => 'g' ),
					'length' => array( 'size' => 100, 'unit' => 'mm' ),
					'capacity' => array( 'size' => 30, 'unit' => 'mb' ),
				),		
				'delimiter' => '<br />',
			),		
			array (
				'field_id'		=> 'taxonomy_checklist',
				'title'			=> __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
				'type'			=> 'taxonomy',
				'taxonomy_slugs' => get_taxonomies( '', 'names' ),
			),				
			array()
		);		
	}
	
	public function printMetaFieldValues( $sContent ) {
		
		if ( ! isset( $GLOBALS['post']->ID ) || get_post_type() != 'apf_posts' ) return $sContent;
			
		// 1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
		// or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );
		$iPostID = $GLOBALS['post']->ID;
		$aPostData = array();
		foreach( ( array ) get_post_custom_keys( $iPostID ) as $sKey ) 	// This way, array will be unserialized; easier to view.
			$aPostData[ $sKey ] = get_post_meta( $iPostID, $sKey, true );
		
		// 2. To retrieve the saved options in the setting pages created by the framework - use the get_option() function.
		// The key name is the class name by default. This can be changed by passing an arbitrary string 
		// to the first parameter of the constructor of the AdminPageFramework class.		
		$aSavedOptions = get_option( 'APF_Demo' );
			
		return "<h3>" . __( 'Saved Meta Field Values', 'admin-page-framework-demo' ) . "</h3>" 
			. $this->oDebug->getArray( $aPostData )
			. "<h3>" . __( 'Saved Setting Options', 'admin-page-framework-demo' ) . "</h3>" 
			. $this->oDebug->getArray( $aSavedOptions );

	}
	
	public function validation_APF_MetaBox( $aInput, $aOldInput ) {
		
		// You can check the passed values and correct the data by modifying them.
		// $this->oDebug->logArray( $aInput );
		return $aInput;
		
	}
	
}
new APF_MetaBox(
	'sample_custom_meta_box',
	'My Custom Meta Box',
	array( 'apf_posts' ),	// post, page, etc.
	'normal',
	'default'
);
	
	
/*
 * 
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 * 
 */