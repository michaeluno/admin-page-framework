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
         * ( optional ) Set form sections - if not set, the system default section will be applied so you don't worry about it.
         */
        $this->addSettingSections(
            array(
                'section_id' => 'selectors',
                'title' => __( 'Selectors', 'admin-page-framework-demo' ),
                'description' => __( 'These are grouped in the <code>selectors</code> section.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'misc',
                'title' => __( 'MISC', 'admin-page-framework-demo' ),
                'description' => __( 'These are grouped in the <code>misc</code> section.', 'admin-page-framework-demo' ),
            )    
        );
        $this->addSettingSections(
            array(
                'section_id' => 'tabbed_sections_a',
                'section_tab_slug' => 'tabbed_sections',
                'title' => __( 'Section Tab A', 'admin-page-framework-demo' ),
                'description' => __( 'This is the first item of the tabbed section.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id' => 'tabbed_sections_b',
                'title' => __( 'Section Tab B', 'admin-page-framework-demo' ),
                'description' => __( 'This is the second item of the tabbed section.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id' => 'repeatable_tabbed_sections',
                'tab_slug' => 'sections',
                'section_tab_slug' => 'repeatable_tabbes_sections',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'description' => __( 'It is possible to tab repeatable sections.', 'admin-page-framework-demo' ),
                'repeatable' =>    true, // this makes the section repeatable
            ),
            array( 
                'section_tab_slug' => '', // reset the target tab slug  for the next use.
            )
        );
        
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id' => 'metabox_text_field',
                'type' => 'text',
                'title' => __( 'Text Input', 'admin-page-framework-demo' ),
                'description' => __( 'Type more than two characters.', 'admin-page-framework-demo' ),
                'help' => __( 'This is help text.', 'admin-page-framework-demo' ),
                'help_aside' => __( 'This is additional help text which goes to the side bar of the help pane.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id' => 'metabox_text_field_repeatable',
                'type' => 'text',
                'title' => __( 'Text Repeatable', 'admin-page-framework-demo' ),
                'repeatable' =>    true
            ),     
            array(
                'field_id' => 'metabox_textarea_field',
                'type' => 'textarea',
                'title' => __( 'Text Area', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'help' => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
                'default' => __( 'This is a default text value.', 'admin-page-framework-demo' ),
                'attributes' => array(
                    'cols' => 40,     
                ),
            ),
            array( // Rich Text Editor
                'field_id'          => 'rich_textarea',
                'type'              => 'textarea',
                'title'             => __( 'Rich Text Editor', 'admin-page-framework-demo' ),
                'rich'              =>    true, // array( 'media_buttons' => false )  <-- a setting array can be passed. For the specification of the array, see http://codex.wordpress.org/Function_Reference/wp_editor
            )
        );        
               
        $this->addSettingFields(
            array(
                'section_id'    => 'selectors',
                'field_id'      => 'checkbox_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Input', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'label'         => __( 'This is a check box.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'select_filed',
                'type'          => 'select',
                'title'         => __( 'Select Box', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'default'       => 'one', // 0 means the first item
                'label' => array( 
                    'one'   => __( 'One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Three', 'admin-page-framework-demo' ),
                ),
            ),     
            array (
                'field_id' => 'radio_field',
                'type' => 'radio',
                'title' => __( 'Radio Group', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'default' => 'one',
                'label' => array( 
                    'one' => __( 'This option is the first item of the radio button example field and lets the user choose one from many.', 'admin-page-framework-demo' ),
                    'two' => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-demo' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-demo' ),
                ),
            ),
            array (
                'field_id' => 'checkbox_group_field',
                'type' => 'checkbox',
                'title' => __( 'Checkbox Group', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'label' => array( 
                    'one' => __( 'This option is the first item of the checkbox button example field.', 'admin-page-framework-demo' ),
                    'two' => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-demo' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-demo' ),
                ),
                'default' => array(
                    'one' =>    true,
                    'two' => false,
                    'three' => false,
                ),
            ),
            array()
        );     

        $this->addSettingFields(
            array (
                'section_id' => 'misc',
                'field_id' => 'image_field',
                'type' => 'image',
                'title' => __( 'Image', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
            ),     
            array(
                'field_id' => 'metabox_password',
                'type' => 'password',
                'title' => __( 'Password', 'admin-page-framework-demo' ),
            ),
            array (
                'field_id' => 'color_field',
                'type' => 'color',
                'title' => __( 'Color', 'admin-page-framework-demo' ),
            ),    
            array (
                'field_id' => 'size_field',
                'type' => 'size',
                'title' => __( 'Size', 'admin-page-framework-demo' ),
                'default' => array( 'size' => 5, 'unit' => '%' ),
            ),     
            array (
                'field_id' => 'sizes_field',
                'type' => 'size',
                'title' => __( 'Multiple Sizes', 'admin-page-framework-demo' ),
                'label' => __( 'Weight', 'admin-page-framework-demo' ),
                'default' => array( 'size' => 15, 'unit' => 'g' ),
                'units' => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
                array(
                    'label' => __( 'Length', 'admin-page-framework-demo' ),
                    'default' => array( 'size' => 100, 'unit' => 'mm' ),
                    'units' => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
                ),
                array(
                    'label' => __( 'File Size', 'admin-page-framework-demo' ),
                    'default' => array( 'size' => 30, 'unit' => 'mb' ),
                    'units' => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
                ),     
                'delimiter' => '<br />',
            ),     
            array (
                'field_id' => 'taxonomy_checklist',
                'type' => 'taxonomy',
                'title' => __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
                'taxonomy_slugs' => get_taxonomies( '', 'names' ),
            ),     
            array()
        );     
  
        $this->addSettingFields(     
            array(
                'section_id' => 'tabbed_sections_a',
                'field_id' => 'text_field_in_tabbed_section',
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'xyz',
            ),
            array(
                'field_id' => 'repeatable_field_in_tabbed_sections',
                'title' => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type' => 'text',
                'repeatable' =>    true,
            ),     
            array(
                'section_id' => 'tabbed_sections_b',
                'field_id' => 'size_in_tabbed_sections',
                'title' => __( 'Size', 'admin-page-framework-demo' ),
                'type' => 'size',
            ),
            array(
                'field_id' => 'select_in_tabbed_sections',
                'title' => __( 'Select', 'admin-page-framework-demo' ),
                'type' => 'select',
                'default' => 'b',
                'label' => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            ),     
            array()
        );     

        $this->addSettingFields(    
            array(
                'section_id' => 'repeatable_tabbed_sections',
                'field_id' => 'tab_title',
                'type' => 'section_title',
                'label' => __( 'Name', 'admin-page-framework-demo' ),
                'attributes' => array(
                    'size' => 10,
                    // 'type' => 'number', // change the input type 
                ),
            ),     
            array(
                'field_id' => 'text_field_in_tabbed_section_in_repeatable_sections',
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'xyz',
            ),
            array(
                'field_id' => 'repeatable_field_in_tabbed_sections_in_repetable_sections',
                'title' => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type' => 'text',
                'repeatable' =>    true,
            ),     
            array(
                'field_id' => 'size_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Size', 'admin-page-framework-demo' ),
                'type' => 'size',
            ),
            array(
                'field_id' => 'select_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Select', 'admin-page-framework-demo' ),
                'type' => 'select',
                'default' => 'b',
                'label' => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            ),     
            array(
                'field_id' => 'color_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Color', 'admin-page-framework-demo' ),
                'type' => 'color',
            ),     
            array()
        );     
    }
    
    public function content_APF_MetaBox_BuiltinFieldTypes( $sContent ) { // content_{instantiated class name}
        
        // Modify the output $sContent . '<pre>Insert</pre>'
        $sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> hook.', 'admin-page-framework-demo' ), __FUNCTION__ ) . "</p>";
        return $sInsert . $sContent;
        
    }
    
    public function validation_APF_MetaBox_BuiltinFieldTypes( $aInput, $aOldInput, $oAdmin ) { // validation_{instantiated class name}
    
        $_bIsValid  = true;
        $_aErrors   = array();

        // You can check the passed values and correct the data by modifying them.
        // $this->oDebug->log( $aInput );     
        
        // Validate the submitted data.
        if ( strlen( trim( $aInput['metabox_text_field'] ) ) < 3 ) {
            
            $_aErrors['metabox_text_field'] = __( 'The entered text is too short! Type more than 2 characters.', 'admin-page-framework-demo' ) . ': ' . $aInput['metabox_text_field'];
            $_bIsValid = false;     
            
        }
        
        if ( ! $_bIsValid ) {
            
            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( 'There was an error in your input in meta box form fields', 'admin-page-framework-demo' ) );    
            return $aOldInput;
            
        }

        return $aInput;
        
    }
    
}