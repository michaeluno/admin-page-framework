<?php
/* 
	Plugin Name: Admin Page Framework Demo Plugin
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 1.0.3
	Requirements: PHP 5.2.4 or above, WordPress 3.2 or above.
*/

	/*
	 * Brief Instruction: How to Use the Framework - Basic Five Steps
	 * 	1. Include the library.
	 * 	2. Extend the library class.
	 * 	3. Define the SetUp() method. Include the following methods in the definition. Decide the page title and the slug.
	 * 		SetRootMenu() - use it to specify the root menu.
	 * 	 	AddSubMenu() - use it to specify the sub menu and the page. This page will be the actual page your users will be going to access.
	 * 			IMPORTANT: Decide the page slug witout hyphens and dots. This is very important since the page slug serves as the callback methods name.
	 * 		for other methods and more details, visit, http://en.michaeluno.jp/admin-page-framework/methods/
	 * 	4. Define callback methods.
	 * 	5. Instantiate the extended class.
	 * 	
	 * To get started, visit http://en.michaeluno.jp/admin-page-framework/get-started . It has the simplest example so you'll see how it works.
	 * */ 
 
// Step 1. Include the library
if ( !class_exists( 'Admin_Page_Framework' ) ) 
	include_once( dirname( __FILE__ ) . '/classes/admin-page-framework.php' );

// Step 2. Extend the class
class APF_AdminPageFrameworkDemo extends Admin_Page_Framework {

	// Step 3. Define the setup method to set how many pages, page titles and icons etc.
	function SetUp() {
					
		// Create the root menu - specifies to which parent menu we are going to add sub pages.
		$this->SetRootMenu( 'Admin Page Framework Demo Plugin' );	
		
		// Add the sub menus and the pages
		// You need to decide what page title and the slug to use. 
		// Important: do not use dots and hyphens in the page slug. Alphabets and numbers only! 
		// You are going to use the page slug later on for the callback method.
		$this->SetCapability( 'read' );		// *Optional: allow subscribers to access the pages
		$this->AddSubMenu(
			'My First Page',	// page and menu title
			'myfirstpage',		// page slug - this will be the option name saved in the database
			plugins_url( 'img/demo_01_32x32.png', __FILE__ )
		);	// set the screen icon, it should be 32 x 32.
		$this->AddSubMenu(
			'Import and Export Options', 
			'mysecondpage',
			plugins_url( 'img/demo_02_32x32.png', __FILE__ ),
			'manage_options'	// *Optional: restrict the access rights to only administrators. 
		);
		$this->AddSubMenu(
			'Change Style',
			'mythirdpage',
			plugins_url( 'img/demo_03_32x32.png', __FILE__ )
		);
		$this->AddSubMenu(
			'Information',
			'myfourthpage',
			plugins_url( 'img/demo_04_32x32.png', __FILE__ )
		);
		
		// There are two kinds of tabs supported by this framework: page heading tabs and in-page tabs.
		// Enable page heading tabs. 
		$this->ShowPageHeadingTabs( True );
		
		// Add in-page tabs in the first page.			
		$this->AddInPageTabs(
			'myfirstpage',	
			array(	// slug => title
				'firsttab'		=> 'Text Fields', 		
				'secondtab'		=> 'Selectors and Checkboxes', 		
				'thirdtab'		=> 'Image and Upload',
				'fourthtab'		=> 'Verify Form Data',
				'fifthtab'		=> 'Hidden Tab',			
			) 
		);	
		
		// This hides the fifth tab. But the page is still accessible. This is useful when you need to create a proceeding page for advanced sections 
		// that the user reaches after submitting the initial set up in the same page.
		$this->HideInPageTab( 			// since 1.0.2.1
			'myfirstpage', 	// to which page it belongs
			'fifthtab', 	// which tab it is
			'firsttab' 		// when the hidden page is displayed, this tab will be marked as the active one.
		);	
		
		// Add form elements.
		// Here we have four sections as an example.
		// If you wonder what array keys are need to be used, please refer to http://en.michaeluno.jp/admin-page-framework/methods/
		$this->AddFormSections( 
			// Section Arrays - numerically indexed.
			array( 	
				// Section Array 1
				array(  
					'pageslug' => 'myfirstpage',
					'tabslug' => 'firsttab',
					'id' => 'text_fields', 
					'title' => 'Text Fields',
					'description' => 'These are text type fields.',
					'fields' => 
						// Field Arrays
						array(
							// Text Field
							array(  
								'id' => 'text', 
								'title' => 'Text',
								'description' => 'Type somethig here.',	// additional notes besides the form field
								'type' => 'text',
								'default' => 123456,
								'size' => 40 
							),
							// Password Field
							array(  
								'id' => 'password',
								'title' => 'Password',
								'tip' => 'This input will be masked.',
								'type' => 'password',
								'size' => 20
							),
							// Text Area
							array(  
								'id' => 'textarea',
								'title' => 'Text Area', 
								'description' => 'Type a text string here.',
								'type' => 'textarea',
								'rows' => 6,
								'cols' => 80,
								'default' => 'Hello World! This is set as the default string.'
							),								
						)
				),
				// Section Array 2
				array(  
					'pageslug' => 'myfirstpage',
					'tabslug' => 'secondtab',
					'id' => 'selectors', 
					'title' => 'Selectors and Checkboxes',
					'description' => 'These are selector type options.',
					'fields' => 
						// Field Arrays
						array(
							// Dropdown List
							array(  
								'id' => 'select',
								'title' => 'Drop Down List',
								'description' => 'This is a drop down list.',
								'type' => 'select',
								'default' => 0,
								'label' => array( 'red', 'blue', 'yellow', 'orange' )
							),		
							// Radio Buttons
							array(  
								'id' => 'radio',
								'title' => 'Radio Button', 
								'description' => 'Choose one from the radio buttons.',
								'type' => 'radio',
								'label' => array( 'a' => 'apple', 'b' => 'banana', 'c' => 'cherry' ),
								'default' => 'b'	// banana
							),
							// Checkboxes
							array( 
								'id' => 'checkboxs',
								'title' => 'Multiple Checkboxes', 
								'description' => 'The description key can be omitted though.',
								'type' => 'checkbox',
								'label' => array( 'moon' => 'Moon', 'earth' => 'Earth', 'sun' => 'Sun', 'mars' => 'Mars' ),
								'default' => array( 'moon' => True, 'earth' => False, 'sun' => True, 'mars' => False )
							),
							// Single Checkbox
							array( 
								'id' => 'checkbox',
								'title' => 'Single Checkbox', 
								'tip' => 'The description key can be omitted though.',
								'description' => 'Check box\'s label can be a string, not an array.',
								'type' => 'checkbox',
								'label' => 'One',	// notice that the label key is not an array
								'default' => False
							),								
						)
				),
				// Section Array 3
				array(  
					'pageslug' => 'myfirstpage',
					'tabslug' => 'thirdtab',
					'id' => 'misc_types', 
					'title' => 'Hidden Field, Image, and File',
					'description' => 'Hidden fields and file also can be created with field arrays..',
					'fields' => 
						// Field Arrays
						array(
							// Hidden fields - invisible but the set value be sent 
							array(  // single hidden field
								'id' => 'hidden_field',
								'title' => 'Hidden Fields',
								'type' => 'hidden',
								'label' => 'This is sent in the hidden input field.',		// use the label key to set the value
							),	
							array(  // multiple hidden fields
								'id' => 'hidden_fields',
								'type' => 'hidden',
								'description' => 'Hidden fields are embedded here.',
								'label' => array( 'a' => true, 'b' => false, 'c' =>false, 'd' => true, 'e' =>true ),	// if the label key is an array, it will create multiple hidden keys with the values.
							),	
							// Image Uploader - this is for uploading images. There are more keys for custom settings.
							// For other keys, please refer to the Demo 12 plugin.
							array( 
								'id' => 'upload_image',
								'title' => 'Set Image',
								'type' => 'image',
								'default' => admin_url( 'images/wordpress-logo-2x.png' ) ,
							),										
							// File - this is for uploading files. The values will be stored in the $_FILES array.
							array(  // single file upload
								'id' => 'file_single_field',
								'title' => 'Single Upload',
								'type' => 'file',
							),						
							array(  // multiple file uploads
								'id' => 'file_multiple_fields',
								'title' => 'Multiple Uploads',
								'type' => 'file',
								// just set empty values so that the $_FILES array stores them in a single array key.
								'label' => array( '', '', '', '' ),	
							),						
							
						)
				),	
				// Section Array 4
				array(  
					'pageslug' => 'myfirstpage',
					'tabslug' => 'firsttab',
					'id' => 'buttons', 
					'title' => 'Submit Buttons',
					'description' => 'Buttons also can be created with field arrays.',
					'fields' => 
						// Field Arrays
						array(
							// Submit Buttons
							array(  // single button
								'id' => 'reload',
								'type' => 'submit',		// the submit type creates a button
								'label' => 'Reload the Page',
							),
							array(  // multiple buttons
								'id' => 'update',
								'type' => 'submit',		// the submit type creates a button
								'label' => array( 
									'save' => 'Update the Options',
									'delete' => 'Delete the Options'
								),
								'delimiter' => '&nbsp;&nbsp;',
							),	
							array(  // make the button like a hyper link
								'id' => 'link_button',
								'type' => 'submit',		// the submit type creates a button
								'label' => __( 'Button as Link', 'admin-page-framework-demo' ),
								'pre_field' => '<span title="' . __( 'Go to the forth page.', 'admin-page-framework-demo' ) . '">',
								'href' => admin_url( "admin.php?page=myfourthpage" ),
								'post_field' => '</span>',
							),							
						)
				),
			)
		);
		
		// For the second page - export and import options.
		$this->AddFormSections( 
			array( 			
				array(  
					'pageslug' => 'mysecondpage',	
					'id' => 'section_import_and_export', 	
					'title' => 'Import and Export',		
					'description' => 'With the import and export buttons, option can be saved to a file and restored. ' . 
						'For that, the <strong>import</strong> and <strong>export</strong> types need to be passed to the field array.',
					'fields' => 
						array(
							array(	// this field type is file and it is for uploading a file.
								'id' => 'field_import_button', 		
								'title' => 'Import',
								'description' => 'Choose a file to import.',
								'update_message' => 'Options were imported and updated.',
								'type' => 'import',	// set the input field type to import
								'label' => 'Import Options From File',	// set the input field type to file
								'delimiter' => '&nbsp;&nbsp;',
							),							
							array(	
								'id' => 'field_export_button', 		
								'title' => 'Export',
								'type' => 'export',	// set the input field type to export
								'label' => 'Export Options',
							)								
						)									
				),							
			)
		);	
		
		// for the fourth tab in the first page
		$this->AddFormSections( 
			array( 	
				array(  
					'pageslug' => 'myfirstpage',	
					'tabslug' => 'fourthtab',
					'id' => 'section_verify_text', 	
					'title' => 'Verify Text Input',		
					'description' => 'Submitted data can be verified. If it fails, show an error message.',
					'fields' => 
						array(
							array(  
								'id' => 'field_verify_text', 		// the option key name saved in the database. You will need this when retrieving the saved value later.
								'title' => 'Verify Text Form Field',
								'tip' => 'Try entering something that is not a number.',		// appears on mouse hover on the title
								'error' => 'Please enter a number! This message is set in the field array with the error key. The invalid Value: ',
								'description' => 'Try entering something that is not a number.',	// additional notes besides the form field
								'type' => 'text',	// set the input field type to be text
								'default' => 'xyz',	// the default value is set here.
							),
						)
				),				
			)
		);					
	}
	
	// Step 4. Define the callback methods.
	/*
	 * The first sub page.
	 * */
	// Notice that the name of the method is 'do_' + page slug.
	// If you wonder what else we have for this kind of pre-defined methods and callbacks, 
	// please refer to http://en.michaeluno.jp/admin-page-framework/hooks-and-callbacks/
	function do_myfirstpage() {	

		// Disable the below output in the fifth tab.
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'fifthtab' ) return;
	
		submit_button();	// the save button

		// Show the saved option value. The option key is the string passed to the first parameter to the constructor of the class (at the end of this plugin).
		// If the option key is not set, the page slug will be used. 
		if ( $options = get_option( 'demo_my_option_key' ) )
			echo '<h3>Saved values</h3><h4>option table key: demo_my_option_key</h4>'
				. $this->DumpArray( ( array ) $options );	// DumpArray will be useful to output array contents.
				
	}
	/*
	 * First Tab
	 * */
	function do_myfirstpage_firsttab() {
		
		$strURL = admin_url( 'admin.php?page=myfirstpage&tab=fifthtab' );
		echo '<p><a href="' . $strURL . '">' . __( 'Go to the hidden page.', 'admin-page-framework' ). '</a></p>';
		
	}
	/*
	 * * Fifth Tab - a hidden tab page which belongs to the first tab.
	*/ 
	function head_myfirstpage_fifthtab( $strHTML ) {	 // 'head_' + page slug + tab slug
		
		$strURL = admin_url( 'admin.php?page=myfirstpage&tab=firsttab' );
		return $strHTML 
			. '<h3>Hidden Page</h3>'
			. '<p>This is a hidden page.</p>'
			. '<p><a href="' . $strURL . '">' . __( 'Go Back', 'admin-page-framework' ) . '</a></p>';
		
	}
	
	// This is a valiadation callback method with the name of 'validation_' + page slug + _ + tab slug.
	// Whennever you need to check the submitted data, use this method. The returned array will be saved in the database.
	function validation_myfirstpage_firsttab( $arrInput ) {
	
		// In order not to do anything with the submitted data, return an empty array. 
		// ( Or altenatively retrieve the saved option array from the database and return it. )
		if ( isset( $arrInput['myfirstpage']['buttons']['reload'] ) ) return array();

		// To discard all the saved option values, return null.
		if ( isset( $arrInput['myfirstpage']['buttons']['update']['delete'] ) ) {
			$this->AddSettingsError(
				'firsttab_delete',  // pick a unique ID for the message. If a same ID is used, only the newly added one will be displayed.
				__( 'Options were deleted.', 'admin-page-framework' ),
				'updated'
			);				
			return null;		
		}
		
		// AddSettingsError() is useful to display the submitted values.
		$this->AddSettingsError(
			'firsttab_value_check',  // pick a unique ID for the message. If a same ID is used, only the newly added one will be displayed.
			'<h3>Check Submitted Values</h3>'
			. '<h4>$arrInput - the passed value to the validation callback</h4>' . $this->DumpArray( $arrInput )
			. '<h4>$_POST</h4>' . $this->DumpArray( $_POST ),
			'updated'
		);
		
		// The returned value will be saved in the database.
		return $arrInput;
		
	}
	function validation_myfirstpage_thirdtab( $arrInput ) {		// 'validation_' + page slug + _ + tab slug

		// This is useful to check the submitted values.
		$arrErrors = $_FILES['demo_my_option_key']['error']['myfirstpage']['misc_types']['file_multiple_fields'];
		$arrErrors[] = $_FILES['demo_my_option_key']['error']['myfirstpage']['misc_types']['file_single_field'];
		if ( in_array( 0, $arrErrors ) )
			$this->AddSettingsError(	
				'third_tab',  // this will be added to the div tag ID of the message 
				'<h3>' . __( 'File(s) was(were) uploaded.', 'admin-page-framework-demo' ) . '</h3>'
				. '<h4>$_FILES</h4><pre>' . $this->DumpArray( $_FILES, dirname( __FILE__ ) . '/debug_log.txt' ) . '</pre>',
				'updated'
			);

		return $arrInput;
		
	}
	// The verification callback method for the fourth tab in the first sub page.
	function validation_myfirstpage_fourthtab( $arrInput ) {	// 'validation_' + page slug + _ + tab slug
		
		// Set the flag 
		$bIsValid = True;
		
		// We store values that have an error in an array and pass it to the SetFieldErrors() method.
		// It internally stores the error array in a temporary area of the database called transient.
		// The used name of the transient is a md5 hash of 'extended class name' + '_' + 'page slug'. The library class will serch for this transient 
		// when it renders the fields and if it is found, it will display the error message set in the field array.
		$arrErrors = array();
		
		// Check if the submitted value is numeric.
		if ( !is_numeric( $arrInput['myfirstpage']['section_verify_text']['field_verify_text'] ) ) {
			$arrErrors['section_verify_text']['field_verify_text'] = $arrInput['myfirstpage']['section_verify_text']['field_verify_text'];
			$bIsValid = False;
		}
		
		// If everything is okay, return the passed array so that Settings API can update the data.
		if ( $bIsValid ) {		
		
			// this displays a message.
			$this->AddSettingsError( 
				'fourthtab',  	// will be used in the ID attribute in the message tag element.
				__( 'The options were updated.' ), 	// the message to display
				'updated' 	// the type. Use 'error' for a red box.
			);
			return $arrInput;
			
		}

		// This line is reached if there are invalid values.
		// Set the error array for the input fields.
		$this->SetFieldErrors( $_POST['pageslug'], $arrErrors );
		
		// This displays the error message at the top of the plugin admin page.
		$this->AddSettingsError( 
			'fourthtab',  // will be used in the ID attribute in the message tag element.
			__( 'The value must be numeric.' )  . '<br />Submitted Data: ' . $this->DumpArray( $arrInput ) 
		);	
		
		// Returning an empty array will not change options.
		return array();
		
	}			
	
	/*
	 * The second sub page.
	 * */
	// These are filters so the filtering value is passed to the parameter and it needs to be returned.
	function head_mysecondpage( $strContent ) {		// 'head_' + page slug
		
		return $strContent . '<p>This message is inserted with the method: ' . __METHOD__  . '</p>';
	
	}
	function content_mysecondpage( $strContent ) {	// 'content_' + page slug
		
		return $strContent . '<p>After saving some values in the first page. Try exporting the options and change some values. Then import the file and see if the settings gets reverted.</p>';

	}		
	
	/*
	 * The third sub page.
	 * */
	// Apply the custom style to the third page only.
	function style_mythirdpage( $strRules ) {	// 'style_' + page slug 
		
		return $strRules . '#wpwrap {background-color: #EEE;}';
		
	}
	function do_mythirdpage() {	 // 'do_' + page slug
		
		echo '<p>See, the background color is changed.</p>';
		
	}
	
	/*
	 * The fourth sub page.
	 * */
	function content_myfourthpage( $strContent ) {
		
		return $strContent . '<h3>Documentation</h3>'
			. '<ul class="admin-page-framework">'
			. '<li><a href="http://en.michaeluno.jp/admin-page-framework/get-started/">Get Started</a></li>'
			. '<li><a href="http://en.michaeluno.jp/admin-page-framework/demos/">Demos</a></li>'
			. '<li><a href="http://en.michaeluno.jp/admin-page-framework/methods/">Methods</a></li>'
			. '<li><a href="http://en.michaeluno.jp/admin-page-framework/hooks-and-callbacks/">Hooks and Callbacks</a></li>'
			. '</ul>'
			. '<h3>Participate in the Project</h3>'
			. '<p>The repository is available at GitHub. <a href="https://github.com/michaeluno/admin-page-framework">https://github.com/michaeluno/admin-page-framework</a></p>'
		;
		
	}
	function style_myfourthpage( $strRules ) {
		
		return $strRules . ' ul.admin-page-framework { list-style:disc; padding-left: 20px; }';
		
	}
}

// Step 5. Instantiate the class object.
new APF_AdminPageFrameworkDemo( 
	'demo_my_option_key',	// the first parameter specifies the option key to use. If not set, each page slug will be used for the key.
	__FILE__	// this tells the framework the caller script path so that the script info will be embedded in the footer.
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
 