<?php
	/* 
		Plugin Name: Admin Page Framework Demo Plugin
		Plugin URI: http://michaeluno.jp/admin-page-framework
		Description: Demonstrates the features of the Admin Page Framework class.
		Author: Michael Uno
		Author URI: http://michaeluno.jp
		Version: 1.0.0.1
	*/

	// Include the library
	if ( !class_exists( 'Admin_Page_Framework' ) ) 
		include_once( dirname( __FILE__ ) . '/classes/admin-page-framework.php' );
	
	// Extend the class
	class APF_AdminPageFrameworkDemo extends Admin_Page_Framework {
	
		// Define the setup method to set how many pages, page titles and icons etc.
		function SetUp() {
						
			// Create the root menu - specifies to which parent menu we are going to add sub pages.
			$this->SetRootMenu( 'Admin Page Framework Demo Plugin' );	
			
			// Add the sub menus and the pages
			$this->AddSubMenu(	'My First Page',		// page and menu title
								'myfirstpage',		// page slug - this will be the option name saved in the database
								plugins_url( 'img/demo_01_32x32.png', __FILE__ ) );	// set the screen icon
			$this->AddSubMenu(	'Import and Export Options', 
								'mysecondpage',
								plugins_url( 'img/demo_02_32x32.png', __FILE__ ) );
			$this->AddSubMenu(	'Change Style',
								'mythirdpage',
								plugins_url( 'img/demo_03_32x32.png', __FILE__ ) );
			$this->AddSubMenu(	'Information',
								'myfourthpage',
								plugins_url( 'img/demo_04_32x32.png', __FILE__ ) );

								
			// Enable heading page tabs.
			$this->ShowPageHeadingTabs( True );
			
			// Add in-page tabs in the third page.			
			$this->AddInPageTabs( 'myfirstpage',	
						array(	// slug => title
							'firsttab' => 'Text Fields', 		
							'secondtab' => 'Selecters and Checkboxes', 		
							'thirdtab' => 'Image and Upload',
							'fourthtab' => 'Verify Form Data',
						) 
					);	
								
			// Add form elements.
			// Here we have four sections as an example.
			$this->AddFormSections( 
				// Section Arrays
				array( 	
					// Section Array
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
									'tip' => 'Type somethig here.',		// appears on mouse hover on the title
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
									'tip' => 'Type a text string here.',
									'description' => 'Type a text string here.',
									'type' => 'textarea',
									'rows' => 6,
									'cols' => 80,
									'default' => 'Hello World! This is set as the default string.'
								),								
							)
					),
					// Section Array
					array(  
						'pageslug' => 'myfirstpage',
						'tabslug' => 'secondtab',
						'id' => 'selecters', 
						'title' => 'Selecters and Checkboxes',
						'description' => 'These are selecter type options.',
						'fields' => 
							// Field Arrays
							array(
								// Dropdown List
								array(  
									'id' => 'select',
									'title' => 'Drop Down List',
									'tip' => 'This is a drop down list.',
									'description' => 'This is a drop down list.',
									'type' => 'select',
									'default' => 0,
									'label' => array( 'red', 'blue', 'yellow', 'orange' )
								),		
								// Radio Buttons
								array(  
									'id' => 'radio',
									'title' => 'Radio Button', 
									'tip' => 'Choose one from the radio buttons.',
									'description' => 'Choose one from the radio buttons.',
									'type' => 'radio',
									'label' => array( 'a' => 'apple', 'b' => 'banana', 'c' => 'cherry' ),
									'default' => 'b'	// banana
								),
								// Checkboxes
								array( 
									'id' => 'checkboxs',
									'title' => 'Multiple Checkboxes', 
									'tip' => 'The description key can be omitted though.',
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
												)
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
									'type' => 'import',	// set the input field type to file
									'label' => 'Import Options From File',	// set the input field type to file
								),							
								array(	
									'id' => 'field_export_button', 		
									'title' => 'Export',
									'type' => 'export',	// set the input field type to file
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
								array(  'id' => 'field_verify_text', 		// the option key name saved in the database. You will need this when retrieving the saved value later.
										'title' => 'Verify Text Form Field',
										'tip' => 'Try entering something that is not a number.',		// appears on mouse hover on the title
										'error' => 'Please enter a number! This message is set in the field array with the error key.',
										'description' => 'Try entering something that is not a number.',	// additional notes besides the form field
										'type' => 'text',	// set the input field type to be text
										'default' => 'xyz',	// the default value is set here.
									),
							)
					),				
				)
			);					
		}
		
		/*
		 * The first sub page.
		 * */
		// Notice that the name of the method is 'do_' + page slug + tab slug.
		function do_myfirstpage() {		
			submit_button();	// the save button

			// Show the saved option value. The page slugs are used as the option keys.
			if ( $options = ( array ) get_option( 'demo_my_option_key' ) )
				echo '<h3>Saved values</h3><h4>option table key: demo_my_option_key</h4><pre>'
					. print_r( $options, true ) 
					. '</pre>';
					
		}
	
		// This is a valiadation callback method with the name of validation_ + page slug + _ + tab slug.
		// Whennever you need to check the submitted data, use this method. The returned array will be saved in the database.
		function validation_myfirstpage_firsttab( $arrInput ) {
		
			// In order not to do anything with the submitted data, pass an empty array.
			if ( isset( $arrInput['myfirstpage']['buttons']['reload'] ) ) return array();

			// To discard all the savd option values, pass non array value such as null.
			if ( isset( $arrInput['myfirstpage']['buttons']['update']['delete'] ) ) {
				add_settings_error( $_POST['pageslug'], 
					'can_be_any_string',  
					__( 'Options were deleted.', 'admin-page-framework' ),
					'updated'
				);				
				return null;		
			}
			
			// This is useful to check the submitted values.
			add_settings_error( $_POST['pageslug'], 
					'can_be_any_string',  
					'<h3>Check Submitted Values</h3>' .
					'<h4>$arrInput - the passed value to the validation callback</h4><pre>' . print_r( $arrInput, true ) . '</pre>' .
					'<h4>$_POST</h4><pre>' . print_r( $_POST, true ) . '</pre>',
					'updated'
				);
			
			// The returned value will be saved in the database.
			return $arrInput;
		}
		function validation_myfirstpage_thirdtab( $arrInput ) {		// 'validation_' + page slug + tab slug

			// This is useful to check the submitted values.
			$arrErrors = $_FILES['demo_my_option_key']['error']['myfirstpage']['misc_types']['file_multiple_fields'];
			$arrErrors[] = $_FILES['demo_my_option_key']['error']['myfirstpage']['misc_types']['file_single_field'];
			if ( in_array( 0, $arrErrors ) )
				add_settings_error( $_POST['pageslug'], 
						'can_be_any_string',  
						'<h3>File was uploaded</h3>' .
						'<h4>$_FILES</h4><pre>' . print_r( $_FILES, true ) . '</pre>',
						'updated'
					);
	
			return $arrInput;
		}
		// The verification callback method for the fourth tab in the first sub page.
		function validation_myfirstpage_fourthtab( $arrInput ) {	// 'validation_' + page slug + tab slug
	
			// if the validation fails, return the originally stored value.
			$arrOriginal = (array) get_option( 'demo_my_option_key' );
			
			// Set the flag 
			$bIsValid = True;
			
			// We store values that have an error in an array and going to store it in a temporary area of the database called transient.
			// The used name of the transient is 'extended class name' + '_' + 'page slug'. The library class will serch for this transient 
			// when it renders the fields and if it is found, it will display the error message set in the field array.
			$arrErrors = array();
			
			// Check if the submitted value is numeric.
			if ( !is_numeric( $arrInput['myfirstpage']['section_verify_text']['field_verify_text'] ) ) {
				$arrErrors['section_verify_text']['field_verify_text'] = $arrInput['myfirstpage']['section_verify_text']['field_verify_text'];
				$bIsValid = False;
			}
			
			// If everything is okay, return the passed array so that Settings API can update the data.
			if ( $bIsValid ) {		
				// this displays message
				add_settings_error( $_POST['pageslug'], 'can_be_any_string',  __( 'The options were updated.' ), 'updated' );
				delete_transient( get_class( $this ) . '_' . $_POST['pageslug'] ); // delete the temporary data for errors.
				return $arrInput;
			}

			// This line is reached if there are invalid values.
			// Store the error array in the transient with the name of the extended class name + _ + page slug.
			set_transient( get_class( $this ) . '_' . $_POST['pageslug'], $arrErrors, 60*5 );	// store it for 5 minutes ( 60 seconds * 5 )
			
			// This displays the error message
			add_settings_error( $_POST['pageslug'], 'can_be_any_string',  __( 'The value must be numeric.' )  . '<br />Submitted Data: ' . print_r( $arrInput, true )  );	
			
			// This will sent to Settings API and it will be saved in the database. 
			// So the orgiginal data will be stored, meaning nothing changes.
			return $arrOriginal;
		}			
		
		/*
		 * The second sub page.
		 * */
		// These are filters so the filtering value is passed to the parameter and it needs to be returned.
		function head_mysecondpage( $strContent ) {		// 'head_' + page slug
			
			return $strContent . '<p>This message is inserted with the method: ' . __METHOD__  . '</p>';
		
		}
		function content_mysecondpage( $strContent ) {	// 'content_' + page slug
			
			return $strContent . '<p>After saving some values in the firs page. Try exporting the options and change some values. Then import the file and see if the settings gets reverted.</p>';

		}		
		
		
		/*
		 * The third sub page.
		 * */
		// Apply the custom style to the third page only
		function style_mythirdpage( $strRules ) {	// 'style_' + page slug 
			
			return $strRules . '#wpwrap {background-color: #EEE;}';
			
		}
		function do_mythirdpage() {	 // 'do_' + page slug
			
			echo 'See, the background color is changed.';
			
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
	
	// Instantiate the class object.
	// Passing a string to the constructor specifies the option key to use.
	new APF_AdminPageFrameworkDemo( 'demo_my_option_key' );	
