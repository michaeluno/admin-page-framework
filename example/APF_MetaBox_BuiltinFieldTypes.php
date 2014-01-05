<?php
class APF_MetaBox_BuiltinFieldTypes extends AdminPageFramework_MetaBox {
		
	/*
	 * ( optional ) Use the setUp() method to define settings of this meta box.
	 */
	public function setUp() {
		
		/*
		 * ( optional ) Adds a contextual help pane at the top right of the page that the meta box resides.
		 */
		$this->addHelpText( 
			__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
			__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
		);
		
		/*
		 * ( optional ) Adds setting fields into the meta box.
		 */
		$this->addSettingFields(
			array(
				'field_id'		=> 'sample_metabox_text_field',
				'type'			=> 'text',
				'title'			=> __( 'Text Input', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'help'			=> 'This is help text.',
				'help_aside'	=> 'This is additional help text which goes to the side bar of the help pane.',
			),
			array(
				'field_id'		=> 'sample_metabox_textarea_field',
				'type'			=> 'textarea',
				'title'			=> __( 'Text Area', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'help'			=> __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
				'default'		=> __( 'This is a default text.', 'admin-page-framework-demo' ),
				'attributes'	=>	array(
					'cols'	=>	40,				
				),
			),
			array(	// Rich Text Editor
				'field_id' 		=> 'sample_rich_textarea',
				'type' 			=> 'textarea',
				'title' 		=> __( 'Rich Text Editor', 'admin-page-framework-demo' ),
				'rich' 			=> true,	// array( 'media_buttons' => false )  <-- a setting array can be passed. For the specification of the array, see http://codex.wordpress.org/Function_Reference/wp_editor
			),				
			array(
				'field_id'		=> 'checkbox_field',
				'type'			=> 'checkbox',
				'title'			=> __( 'Checkbox Input', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'label'			=> __( 'This is a check box.', 'admin-page-framework-demo' ),
			),
			array(
				'field_id'		=> 'select_filed',
				'type'			=> 'select',
				'title'			=> __( 'Select Box', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'label' => array( 
					'one' => __( 'One', 'admin-page-framework-demo' ),
					'two' => __( 'Two', 'admin-page-framework-demo' ),
					'three' => __( 'Three', 'admin-page-framework-demo' ),
				),
				'default' 			=> 'one',	// 0 means the first item
			),		
			array (
				'field_id'		=> 'radio_field',
				'type'			=> 'radio',
				'title'			=> __( 'Radio Group', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
				'label' => array( 
					'one' => __( 'Option One', 'demo' ),
					'two' => __( 'Option Two', 'demo' ),
					'three' => __( 'Option Three', 'demo' ),
				),
				'default' => 'one',
			),
			array (
				'field_id'		=> 'checkbox_group_field',
				'type'			=> 'checkbox',
				'title'			=> __( 'Checkbox Group', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
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
				'type'			=> 'image',
				'title'			=> __( 'Image', 'admin-page-framework-demo' ),
				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
			),		
			array (
				'field_id'		=> 'color_field',
				'type'			=> 'color',
				'title'			=> __( 'Color', 'admin-page-framework-demo' ),
			),	
			array (
				'field_id'		=> 'size_field',
				'type'			=> 'size',
				'title'			=> __( 'Size', 'admin-page-framework-demo' ),
				'default'		=> array( 'size' => 5, 'unit' => '%' ),
			),						
			array (
				'field_id'		=> 'sizes_field',
				'type'			=> 'size',
				'title'			=> __( 'Multiple Sizes', 'admin-page-framework-demo' ),
				'label'			=> __( 'Weight', 'admin-page-framework-demo' ),
				'default'		=> array( 'size' => 15, 'unit' => 'g' ),
				'units'			=> array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
				array(
					'label'		=> __( 'Length', 'admin-page-framework-demo' ),
					'default'	=> array( 'size' => 100, 'unit' => 'mm' ),
					'units'		=> array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
				),
				array(
					'label'	=> __( 'File Size', 'admin-page-framework-demo' ),
					'default' => array( 'size' => 30, 'unit' => 'mb' ),
					'units'	=> array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
				),				
				'delimiter' => '<br />',
			),		
			array (
				'field_id'		=> 'taxonomy_checklist',
				'type'			=> 'taxonomy',
				'title'			=> __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
				'taxonomy_slugs' => get_taxonomies( '', 'names' ),
			),				
			array()
		);		
	}
	
	
	public function validation_APF_MetaBox_BuiltinFieldTypes( $aInput, $aOldInput ) {	// validation_{extended class name}
		
		// You can check the passed values and correct the data by modifying them.
		// $this->oDebug->logArray( $aInput );
		return $aInput;
		
	}
	
}