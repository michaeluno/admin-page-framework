<?php
class APF_MetaBox extends AdminPageFramework_MetaBox {
		
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
				'rich' 			=> true,	// array( 'media_buttons' => false )  <-- a setting array can be passed. For the specification of the array, see http://codex.wordpress.org/Function_Reference/wp_editor
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
	
	
	public function validation_APF_MetaBox( $aInput, $aOldInput ) {
		
		// You can check the passed values and correct the data by modifying them.
		// $this->oDebug->logArray( $aInput );
		return $aInput;
		
	}
	
}