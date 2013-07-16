<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 1.1.0
	Requirements: PHP 5.2.4 or above, WordPress 3.2 or above.
*/ 

if ( ! class_exists( 'AdminPageFramework' ) )
    include_once( dirname( __FILE__ ) . '/class/admin-page-framework.php' );
    
class APF_Demo extends AdminPageFramework {

    public function setUp() {
    
        // $this->setRootMenuPage( 'My Demo Plugin' );   // specifies to which parent menu to belong.
		$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );
		$this->addSubMenuPages(
			/* 	e.g.
			 * 	'strPageTitle' => 'Your Page Title',
				'strPageSlug'] => 'your_page_slug',		// avoid hyphen(dash), dots, and white spaces
				'strScreenIcon' => 'edit',
				'strCapability' => 'manage-options',
				'numOrder' => 10,
			*/
			array(
				'strPageTitle' => 'Various Form Fields',
				'strPageSlug' => 'first_page',
				'numOrder' => 1,
			),
			array(
				'strPageTitle' => 'Manage Options',
				'strPageSlug' => 'second_page',
				'strScreenIcon' => 'link-manager',
				'numOrder' => 2,
				/*	Screen Types:
					'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
					'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
					'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',		 
				*/				
			)		
		);
				
		$this->addInPageTabs(
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'textfields',
				'strTitle'		=> 'Text Fields',
				'numOrder'		=> 1,				
			),		
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'selectors',
				'strTitle'		=> 'Selectors',
			),					
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'images',
				'strTitle'		=> 'Images',
			),
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'checklist',
				'strTitle'		=> 'Checklist',
			),					
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'misc',
				'strTitle'		=> 'MISC',	
			),		
			array(
				'strPageSlug'	=> 'first_page',
				'strTabSlug'	=> 'verification',
				'strTitle'		=> 'Verification',	
			),					
			array(
				'strPageSlug'	=> 'second_page',
				'strTabSlug'	=> 'saved_data',
				'strTitle'		=> 'Saved Data',
			),
			array(
				'strPageSlug'	=> 'second_page',
				'strTabSlug'	=> 'properties',
				'strTitle'		=> 'Properties',
			),
			array(
				'strPageSlug'	=> 'second_page',
				'strTabSlug'	=> 'export_import',
				'strTitle'		=> 'Export / Import',			
			),
			array(
				'strPageSlug'	=> 'second_page',
				'strTabSlug'	=> 'delete_options',
				'strTitle'		=> 'Delete',
				'numOrder'		=> 99,	
			),						
			array(
				'strPageSlug'	=> 'second_page',
				'strTabSlug'	=> 'delete_options_confirm',
				'strTitle'		=> 'Delete Confirm',
				'fHide'			=> true,
				'strParentTabSlug' => 'delete_options',
				'numOrder'		=> 97,
			),					
			array()
		);			
		
		$this->showPageHeadingTabs( false );		// disables the page heading tabs by passing false.
		$this->setInPageTabTag( 'h2' );		
		
		// Add setting sections
		$this->addSettingSections(
			array(
				'strSectionID'		=> 'text_fields',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'textfields',
				'strTitle'			=> 'Text Fields',
				'strDescription'	=> 'These are text type fields.',
				'numOrder'			=> 10,
			),	
			array(
				'strSectionID'		=> 'selectors',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'selectors',
				'strTitle'			=> 'Selectors and Checkboxes',
				'strDescription'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
			),
			array(
				'strSectionID'		=> 'image_select',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'images',
				'strTitle'			=> 'Image Selector',
				'strDescription'	=> 'Set an image url with jQuwey based image selector.',
			),
			array(
				'strSectionID'		=> 'checklists',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'checklist',
				'strTitle'			=> 'Checklists',
				'strDescription'	=> 'Post type and taxonomy checklists ( custom checkbox ) are supported.',
			),			
			array(
				'strSectionID'		=> 'hidden_field',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'misc',
				'strTitle'			=> 'Hidden Fields',
				'strDescription'	=> 'These are hidden fields.',
			),								
			array(
				'strSectionID'		=> 'files',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'misc',
				'strTitle'			=> 'File Uploads',
				'strDescription'	=> 'These are upload fields.',
			),			
			array(
				'strSectionID'		=> 'submit_buttons',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'misc',
				'strTitle'			=> 'Submit Buttons',
				'strDescription'	=> 'These are custom submit buttons.',
			),			
			array(
				'strSectionID'		=> 'verification',
				'strPageSlug'		=> 'first_page',
				'strTabSlug'		=> 'verification',
				'strTitle'			=> 'Verify Submitted Data',
				'strDescription'	=> 'Show error messages when the user submits improper option value.',
			),			
			array(
				'strSectionID'		=> 'submit_buttons_manage',
				'strPageSlug'		=> 'second_page',
				'strTabSlug'		=> 'delete_options',
				'strTitle'			=> 'Submit Buttons',
				'strDescription'	=> 'These are submit type options.',
				'numOrder'			=> 10,
			),			
			array(
				'strSectionID'		=> 'submit_buttons_confirm',
				'strPageSlug'		=> 'second_page',
				'strTabSlug'		=> 'delete_options_confirm',
				'strTitle'			=> 'Confirmation',
				'strDescription'	=> "<div class='settings-error error'><p><strong>Are you sure you want to delete all the options?</strong></p></div>",
				'numOrder'			=> 10,
			),				
			array(
				'strSectionID'		=> 'exports',
				'strPageSlug'		=> 'second_page',
				'strTabSlug'		=> 'export_import',
				'strTitle'			=> 'Export Data',
				'strDescription'	=> 'After exporting the options, change and save new options and then import the file to see if the options get restored.',
			),				
			array(
				'strSectionID'		=> 'imports',
				'strPageSlug'		=> 'second_page',
				'strTabSlug'		=> 'export_import',
				'strTitle'			=> 'Import Data',
			),			
			array()			
		);
		
		// Add setting fields
		$this->addSettingFields(
			array(	// Single text field
				'strFieldID' => 'text',
				'strSectionID' => 'text_fields',
				'strTitle' => 'Text',
				'strDescription' => 'Type something here.',	// additional notes besides the form field
				'strType' => 'text',
				'numOrder' => 1,
				'vDefault' => 123456,
				'vSize' => 40,
			),
			array(	// Multiple text fields
				'strFieldID' => 'text_multiple',
				'strSectionID' => 'text_fields',
				'strTitle' => 'Multiple Text Fields',
				'strDescription' => 'These are multiple text fields.',	// additional notes besides the form field
				'strType' => 'text',
				'numOrder' => 2,
				'vDefault' => array(
					'Hello World',
					'Foo bar',
					'Yes, we can.'
				),
				'vLabel' => array( 
					'First Item: ', 
					'Second Item: ', 
					'Third Item: ' 
				),
				'vSize' => array(
					60,
					90,
					120,
				),
			),			
			array(	// Password Field
				'strFieldID' => 'password',
				'strSectionID' => 'text_fields',
				'strTitle' => 'Password',
				'strTip' => 'This input will be masked.',
				'strType' => 'password',
				'vSize' => 20,
			),
			array(	// Text Area
				'strFieldID' => 'textarea',
				'strSectionID' => 'text_fields',
				'strTitle' => 'Single Text Area',
				'strDescription' => 'Type a text string here.',
				'strType' => 'textarea',
				'vDefault' => 'Hello World! This is set as the default string.',
				'vRows' => 6,
				'vCols' => 80,
			),
			array(	// Multiple text areas
				'strFieldID' => 'textarea_multiple',
				'strSectionID' => 'text_fields',
				'strTitle' => 'Multiple Text Areas',
				'strDescription' => 'These are multiple text areas.',
				'strType' => 'textarea',
				'vLabel' => array(
					'First Text Area: ',
					'Second Text Area: ',
					'Third Text Area: ',
				),
				'vDefault' => array( 
					'The first default text.',
					'The second default text.',
					'The third default text.',
				),
				'vRows' => array(
					5,
					3,
					2,
				),
				'vCols' => array(
					120,
					80,
					50,
				),
			),
			array(	// Single Dropdown List
				'strFieldID' => 'select',
				'strSectionID' => 'selectors',
				'strTitle' => 'Dropdown List',
				'strDescription' => 'This is a drop down list.',
				'strType' => 'select',
				'vDefault' => 2,
				'vLabel' => array( 'red', 'blue', 'yellow', 'orange' )
			),	
			array(	// Multiple Dropdown Lists
				'strFieldID' => 'select_multiple',
				'strSectionID' => 'selectors',
				'strTitle' => 'Multiple Dropdown Lists',
				'strDescription' => 'This is a multiple sets of drop down list.',
				'strType' => 'select',
				'vLabel' => array( 
					array( 'river', 'mountain', 'sky', ),
					array( 'Monday', 'Tuesday', 'Wednessday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
					array( 'dark', 'light' ),
				),
				'vDefault' => array(
					2,
					1,
					0
				),
			),				
			array(	// Single set of radio buttons
				'strFieldID' => 'radio',
				'strSectionID' => 'selectors',
				'strTitle' => 'Radio Button',
				'strDescription' => 'Choose one from the radio buttons.',
				'strType' => 'radio',
				'vLabel' => array( 'a' => 'apple', 'b' => 'banana', 'c' => 'cherry' ),
				'vDefault' => 'b',	// banana				
			),
			array(	// Multiple sets of radio buttons
				'strFieldID' => 'radio_multiple',
				'strSectionID' => 'selectors',
				'strTitle' => 'Multipe Sets of Radio Buttons',
				'strDescription' => 'Multiple sets of radio buttons.',
				'strType' => 'radio',
				'vLabel' => array( 
					array( 1 => 'one', 2 => 'two' ),
					array( 3 => 'three', 4 => 'four', 5 => 'five' ),
					array( 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine' ),
				),
				'vDefault' => array(
					2,
					4,
					8,
				),
			),			
			array(	// Single Checkbox
				'strFieldID' => 'checkbox',
				'strSectionID' => 'selectors',
				'strTitle' => 'Single Checkbox',
				'strTip' => 'The description key can be omitted though.',
				'strDescription' => 'Check box\'s label can be a string, not an array.',	//'
				'strType' => 'checkbox',
				'vLabel' => 'One',	// notice that the label key is not an array
				'vDefault' => False,
			),	
			array(	// Multiple Checkboxes
				'strFieldID' => 'checkboxes',
				'strSectionID' => 'selectors',
				'strTitle' => 'Multiple Checkboxes',
				'strDescription' => 'The description can be omitted.',
				'strType' => 'checkbox',
				'vLabel' => array( 'moon' => 'Moon', 'earth' => 'Earth', 'sun' => 'Sun', 'mars' => 'Mars' ),
				'vDefault' => array( 'moon' => True, 'earth' => False, 'sun' => True, 'mars' => False ),
			),
			array( // Image Selector
				'strFieldID' => 'image_select_field',
				'strSectionID' => 'image_select',
				'strTitle' => 'Select an Image',
				'strType' => 'image',
				'vLabel' => array( 'First Image', 'Second Image', 'Third Image' ),
				'vDefault' => array( admin_url( 'images/wordpress-logo-2x.png' ) ), 
			),				
			array(
				'strFieldID' => 'post_type_checklist',
				'strSectionID' => 'checklists',
				'strTitle' => 'Post Types',
				'strType' => 'posttype',
			),				
			array(
				'strFieldID' => 'taxonomy_checklist',
				'strSectionID' => 'checklists',
				'strTitle' => 'Category Checklist',
				'strType' => 'taxonomy',
				'vLabel' => $this->getTaxonomyLabels( 'menu_name' ),
				'vTaxonomySlug' => get_taxonomies( '', 'names' ), // or simply 'category' to get a category list.
			),					
			array( // Single Hidden Field
				'strFieldID' => 'hidden_single',
				'strSectionID' => 'hidden_field',
				'strTitle' => 'Single Hidden Field',
				'strType' => 'hidden',
				'vDefault' => 'test value',
				'vLabel' => 'Test label.',
			),			
			array( // Multiple Hidden Fields
				'strFieldID' => 'hidden_miltiple',
				'strSectionID' => 'hidden_field',
				'strTitle' => 'Multiple Hidden Field',
				'strType' => 'hidden',
				'vDefault' => array( 'a', 'b', 'c' ),
				'vLabel' => array( 'Hidden Field 1', 'Hidden Field 2', 'Hidden Field 3' ),
			),			
			array( // Single File Upload Field
				'strFieldID' => 'file_single',
				'strSectionID' => 'files',
				'strTitle' => 'Single File Upload',
				'strType' => 'file',
				'vLabel' => 'Select the file:',
			),					
			array( // Multiple File Upload Fields
				'strFieldID' => 'file_multiple',
				'strSectionID' => 'files',
				'strTitle' => 'Multiple File Uploads',
				'strType' => 'file',
				'vLabel' => array( 'Fist File:', 'Second File:', 'Third File:' ),
				'vDelimiter' => '<br />',
			),			
			array( // Multiple File Upload Fields
				'strFieldID' => 'verify_text_field',
				'strSectionID' => 'verification',
				'strTitle' => 'Verify Text Input',
				'strType' => 'text',
				'strDescription' => 'Enter a non numeric value here.',
			),						
			array( // Submit button as a link
				'strFieldID' => 'submit_button_link',
				'strSectionID' => 'submit_buttons',
				'strTitle' => 'Link Button',
				'strType' => 'submit',
				'strDescription' => 'This button serves as a hyper link.',
				'vLabel' => array( 'Google', 'Yahoo', 'Bing' ),
				'vLink'	=> array( 'http://www.google.com', 'http://www.yahoo.com', 'http://www.bing.com' ),
				'vClassAttribute' => 'button button-secondary',
				'vDelimiter' => '',
			),			
			array( // Submit button as a redirect
				'strFieldID' => 'submit_button_redirect',
				'strSectionID' => 'submit_buttons',
				'strTitle' => 'Redirect Button',
				'strType' => 'submit',
				'strDescription' => 'Unlike the above link buttons, this button saves the options and then redirects to: ' . admin_url(),
				'vLabel' => 'Dashboard',
				'vRedirect'	=> admin_url(),
				'vClassAttribute' => 'button button-secondary',
			),
			array( // Delete Option Button
				'strFieldID' => 'submit_manage',
				'strSectionID' => 'submit_buttons_manage',
				'strTitle' => 'Delete Options',
				'strType' => 'submit',
				'vClassAttribute' => 'button-secondary',
				'vLabel' => 'Delete Options',
				'vLink'	=> admin_url( 'admin.php?page=second_page&tab=delete_options_confirm' )
			),			
			array( // Delete Option Confirmation Button
				'strFieldID' => 'submit_delete_options_confirmation',
				'strSectionID' => 'submit_buttons_confirm',
				'strTitle' => 'Delete Options',
				'strType' => 'submit',
				'vClassAttribute' => 'button-secondary',
				'vLabel' => 'Delete Options',
				'vRedirect'	=> admin_url( 'admin.php?page=second_page&tab=saved_data&settings-updated=true' )
			),			
			array(
				'strFieldID' => 'export_format_type',			
				'strSectionID' => 'exports',
				'strTitle' => 'Export Format Type',
				'strType' => 'radio',
				'strDescription' => 'Choose the file format. Array means the PHP serialized array.',
				'vLabel' => array( 'array' => 'Serialized Array', 'json' => 'JSON', 'text' => 'Text' ),
				'vDefault' => 'array',
			),			
			array(	// Single Export Button
				'strFieldID' => 'export_single',
				'strSectionID' => 'exports',
				'strTitle' => 'Single Export Button',
				'strType' => 'export',
				'strDescription' => __( 'Download the saved option data as serialized PHP array.', 'admin-page-framework-demo' ),
				'vLabel' => 'Export Options',
			),
			array(	// Multiple Export Buttons
				'strFieldID' => 'export_multiple',
				'strSectionID' => 'exports',
				'strTitle' => 'Multiple Export Buttons',
				'strType' => 'export',
				'strDescription' => __( 'Download the custom set data.', 'admin-page-framework-demo' ),
				'vLabel' => array( 'Pain Text', 'JSON', 'Serialized Array' ),
				'vExportFileName' => array( 'plain_text.txt', 'json.json', 'serialized_array.txt' ),
				'vExportFormat' => array( 'text', 'json', 'array' ),
				'vExportData' => array(
					'Hello World!',	// export plain text
					( array ) $this->oProps,	// export an object
					array( 'a', 'b', 'c' ),	// export a serialized array
				),
			),		
			array(
				'strFieldID' => 'import_format_type',			
				'strSectionID' => 'imports',
				'strTitle' => 'Import Format Type',
				'strType' => 'radio',
				'strDescription' => 'The text format type will not set the option values properly. However, you can see that the text contents are directly saved in the database.',
				'vLabel' => array( 'array' => 'Serialized Array', 'json' => 'JSON', 'text' => 'Text' ),
				'vDefault' => 'array',
			),
			array(	// Single Import Button
				'strFieldID' => 'import_single',
				'strSectionID' => 'imports',
				'strTitle' => 'Single Import Field',
				'strType' => 'import',
				'strDescription' => __( 'Upload the saved option data.', 'admin-page-framework-demo' ),
				'vLabel' => 'Import Options',
				// 'vImportFormat' => isset( $_POST[ $this->oProps->strClassName ]['second_page']['imports']['import_format_type'] ) ? $_POST[ $this->oProps->strClassName ]['second_page']['imports']['import_format_type'] : 'array',
			),			
			array()
		);
		
    }
	
	// Custom helper function for the taxonomy check list.
	private function getTaxonomyLabels( $strKey='name' ) {

		$arrTaxonomies = array();
		foreach( ( array ) get_taxonomies( '', 'objects' ) as $strSlug => $oDetail ) 
			$arrTaxonomies[ $strSlug ] = $oDetail->labels->$strKey;
		return $arrTaxonomies;
		
	}
	
	/*
	 * First Page
	 * */
	public function do_first_page() {
		
		submit_button();
		
		// echo $this->oDebug->getArray( $GLOBALS['submenu'] );
		// echo $this->oDebug->getArray( get_taxonomies( '', 'names' ) );
		// echo $this->oDebug->getArray( get_taxonomies( '', 'objects' ) );
		
	}
		
	/*
	 * Second Page
	 * */
	public function do_second_page_saved_data() {	// do_ + page slug + _ + tab slug
		?>
		<h3>Saved Data</h3>
		<?php
			echo $this->oDebug->getArray( $this->oProps->arrOptions ); 
	}
	public function do_second_page_properties() {	// do_ + page slug + _ + tab slug
		?>
		<h3>Framework Properties</h3>
		<?php
			echo $this->oDebug->getArray( $this->oProps ); 
	}
	
	/*
	 * Import and Export Callbacks
	 * */
	public function export_format_second_page_export_import( $strFormatType, $strFieldID ) {	// import_format_ + page slug + _ + tab slug
		
		return isset( $_POST[ $this->oProps->strOptionKey ]['second_page']['exports']['export_format_type'] ) 
			? $_POST[ $this->oProps->strOptionKey ]['second_page']['exports']['export_format_type']
			: $strFormatType;
		
	}	
	public function import_format_second_page_export_import( $strFormatType, $strFieldID ) {	// import_format_ + page slug + _ + tab slug
		
		return isset( $_POST[ $this->oProps->strOptionKey ]['second_page']['imports']['import_format_type'] ) 
			? $_POST[ $this->oProps->strOptionKey ]['second_page']['imports']['import_format_type']
			: $strFormatType;
		
	}
	
	/*
	 * Validation Callbacks
	 * */
	public function validation_first_page_verification( $arrInput, $arrOldPageOptions ) {	// valication_ + page slug + _ + tab slug

// $this->oDebug->getArray( $arrInput, dirname( __FILE__ ) . '/classes/input.txt' ); 		
				
		// Set a flag.
		$fVerified = true;
		
		// We store values that have an error in an array and pass it to the setFieldErrors() method.
		// It internally stores the error array in a temporary area of the database called transient.
		// The used name of the transient is a md5 hash of 'instantiated class name' + '_' + 'page slug'. 
		// The library class will search for this transient when it renders the form fields 
		// and if it is found, it will display the error message set in the field array. 
		$arrErrors = array();
		
		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
		if ( isset( $arrInput['first_page']['verification']['verify_text_field'] ) && ! is_numeric( $arrInput['first_page']['verification']['verify_text_field'] ) ) {
			
			// Start with the section key in $arrErrors, not the key of page slug.
			$arrErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $arrInput['first_page']['verification']['verify_text_field'];	
			$fVerified = false;
			
		}
		
		// An invalid value is found.
		if ( ! $fVerified ) {
		
			// Set the error array for the input fields.
			$this->setFieldErrors( $arrErrors );		
			$this->setSettingsNotice( 'There was an error in your input.' );
			return $arrOldPageOptions;
			
		}
		
				
		return $arrInput;
		
	}
	public function validation_first_page_misc( $arrInput, $arrOldPageOptions ) {	// validation_ + page slug + _ + tab slug

		// Display the uploaded file information.
		$arrFileErrors = array();
		$arrFileErrors[] = $_FILES[ $this->oProps->strOptionKey ]['error']['first_page']['files']['file_single'];
		$arrFileErrors[] = $_FILES[ $this->oProps->strOptionKey ]['error']['first_page']['files']['file_multiple'][0];
		$arrFileErrors[] = $_FILES[ $this->oProps->strOptionKey ]['error']['first_page']['files']['file_multiple'][1];
		$arrFileErrors[] = $_FILES[ $this->oProps->strOptionKey ]['error']['first_page']['files']['file_multiple'][2];
		if ( in_array( 0, $arrFileErrors ) ) 
			$this->setSettingsNotice( '<h3>File(s) Uploaded</h3>' . $this->oDebug->getArray( $_FILES ), 'updated' );
		
		return $arrInput;
		
	}
	
	public function validation_APF_Demo( $arrInput, $arrOldOptions ) {
		
		// If the delete options button is pressed, return an empty array that will delete the entire options stored in the database.
		if ( isset( $_POST[ $this->oProps->strOptionKey ]['second_page']['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) 
			return array();
			
		return $arrInput;
		
	}
		
}
if ( is_admin() )
	new APF_Demo;

	
class APF_PostType extends AdminPageFramework_PostType {
	
	public function setUp() {
	// public function start_APF_PostType() {

		$this->setAutoSave( false );
		$this->setAuthorTableFilter( true );
		$this->addTaxonomy( 
			'sample_taxonomy', // taxonomy slug
			array(			// argynebt - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
				'labels' => array(
					'name' => 'Genre',
					'add_new_item' => 'Add New Genre',
					'new_item_name' => "New Genre"
				),
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_table_filter' => true,	// framework specific key
				'show_in_sidebar_menus' => false,	// framework specific key
			)
		);
		$this->addTaxonomy( 
			'second_taxonomy', 
			array(
				'labels' => array(
					'name' => 'Non Hierarchyal',
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

	}
	
	/*
	 * Extensible methods
	 */
	public function setColumnHeader( $arrColumnHeader ) {
		return array_merge( $arrColumnHeader, $this->arrColumnHeaders );
	}
	public function setSortableColumns( $arrColumns ) {
		return array_merge( $arrColumns, $this->arrColumnSortable );		
	}	
	
	/*
	 * Callback methods
	 */
	public function set_cell_sample_post_type_url( $intPostID ) {
		
		// echo '<p>this is the URL cell and the post ID is: ' . $intPostID . '</p>';
		echo '<p>custom_radio: ' . get_post_meta( $intPostID, 'custom_radio', true ) . '</p>';
	}
	public function set_cell_sample_post_type_referrer( $intPostID ) {
		
		echo '<p>this is the Referrer cell and the post ID is : ' . $intPostID . '</p>';
	}	
	
}
new APF_PostType( 
	'apf_posts', 	// post type slug
	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		'labels' => array(
			'name' => 'Admin Page Framework Custom Post Type',
			'singular_name' => 'Admin Page Framework Custom Post Type',
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
		'menu_icon' => null,
		'has_archive' => true,
		'show_admin_column' => true,
	)		
);	// should not use "if ( is_admin() )" for the this class because posts of custom post type can be accessed from the regular pages.
	
	

class APF_MetaBox extends AdminPageFramework_MetaBox {
	
	public function start_APF_MetaBox() {
		
		$this->addSettingFields(
			array(
				'strFieldID'		=> 'sample_metabox_text_field',
				'strTitle'			=> 'Text Input',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'text',
			),
			array(
				'strFieldID'		=> 'sample_metabox_textarea_field',
				'strTitle'			=> 'Textarea',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'textarea',
				'vDefault'			=> 'This is a default text.',
			),
			array(
				'strFieldID'		=> 'checkbox_field',
				'strTitle'			=> 'Checkbox Input',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'checkbox',
				'vLabel'			=> 'This is a check box.',
			),
			array(
				'strFieldID'		=> 'select_filed',
				'strTitle'			=> 'Select Box',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'select',
				'vLabel' => array( 
					'one' => __( 'One', 'demo' ),
					'two' => __( 'Two', 'demo' ),
					'three' => __( 'Three', 'demo' ),
				),
				'vDefault' 			=> 'one',	// 0 means the first item
			),		
			array (
				'strFieldID'		=> 'radio_field',
				'strTitle'			=> 'Radio Group',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'radio',
				'vLabel' => array( 
					'one' => __( 'Option One', 'demo' ),
					'two' => __( 'Option Two', 'demo' ),
					'three' => __( 'Option Three', 'demo' ),
				),
				'vDefault' => 'one',
			),
			array (
				'strFieldID'		=> 'checkbox_group_field',
				'strTitle'			=> 'Checkbox Group',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'checkbox',
				'vLabel' => array( 
					'one' => __( 'Option One', 'demo' ),
					'two' => __( 'Option Two', 'demo' ),
					'three' => __( 'Option Three', 'demo' ),
				),
				'vDefault' => array(
					'one' => true,
					'two' => false,
					'three' => false,
				),
			),			
			array (
				'strFieldID'		=> 'image_field',
				'strTitle'			=> 'Image',
				'strDescription'	=> 'The description for the field.',
				'strType'			=> 'image',
			),			
			array()
		);
		
		
		add_filter( 'the_content', array( $this, 'printMetaFieldValues' ) );
		
	}
	
	public function printMetaFieldValues( $strContent ) {
		
		if ( get_post_type() != 'apf_posts'  ) return $strContent;
		
		global $post;
		
		$arrOutput = array();
		foreach( $this->arrFields as $arrField )
			$arrOutput[ $arrField['strFieldID'] ] = get_post_meta( $post->ID, $arrField['strFieldID'], true );
		
		return "<h3>Saved Meta Field Values</h3>" . $this->oDebug->getArray( $arrOutput );
		
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
 