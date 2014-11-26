<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

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
                'section_id'        => 'selectors',
                'title'             => __( 'Selectors', 'admin-page-framework-demo' ),
                'description'       => __( 'These are grouped in the <code>selectors</code> section.', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'        => 'misc',
                'title'             => __( 'MISC', 'admin-page-framework-demo' ),
                'description'       => __( 'These are grouped in the <code>misc</code> section.', 'admin-page-framework-demo' ),
            )    
        );
        
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'metabox_text_field',
                'type'          => 'text',
                'title'         => __( 'Text Input', 'admin-page-framework-demo' ),
                'description'   => __( 'Type more than two characters.', 'admin-page-framework-demo' ),
                'help'          => __( 'This is help text.', 'admin-page-framework-demo' ),
                'help_aside'    => __( 'This is additional help text which goes to the side bar of the help pane.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'metabox_text_field_repeatable',
                'type'          => 'text',
                'title'         => __( 'Text Repeatable & Sortable', 'admin-page-framework-demo' ),
                'repeatable'    => true,
                'sortable'      => true,
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
            'selectors',    // section id
            array(
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
                'label'         => array( 
                    'one'   => __( 'One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Three', 'admin-page-framework-demo' ),
                ),
            ),     
            array (
                'field_id'      => 'radio_field',
                'type'          => 'radio',
                'title'         => __( 'Radio Group', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'default'       => 'one',
                'label'         => array( 
                    'one'   => __( 'This option is the first item of the radio button example field and lets the user choose one from many.', 'admin-page-framework-demo' ),
                    'two'   => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-demo' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-demo' ),
                ),
            ),
            array (
                'field_id'      => 'checkbox_group_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Group', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'label'         => array( 
                    'one'   => __( 'This option is the first item of the checkbox button example field.', 'admin-page-framework-demo' ),
                    'two'   => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-demo' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-demo' ),
                ),
                'default'       => array(
                    'one'   => true,
                    'two'   => false,
                    'three' => false,
                ),
            )
        );     

        $this->addSettingFields(
            'misc', // section id
            array (
                'field_id'          => 'image_field',
                'type'              => 'image',
                'title'             => __( 'Image', 'admin-page-framework-demo' ),
                'description'       => __( 'The description for the field.', 'admin-page-framework-demo' ),
            ),      
            array(  
                'field_id'          => 'metabox_password',
                'type'              => 'password',
                'title'             => __( 'Password', 'admin-page-framework-demo' ),
            ),  
            array ( 
                'field_id'          => 'color_field',
                'type'              => 'color',
                'title'             => __( 'Color', 'admin-page-framework-demo' ),
            ),      
            array ( 
                'field_id'          => 'size_field',
                'type'              => 'size',
                'title'             => __( 'Size', 'admin-page-framework-demo' ),
                'default'           => array( 'size' => 5, 'unit' => '%' ),
            ),      
            array ( 
                'field_id'          => 'sizes_field',
                'type'              => 'size',
                'title'             => __( 'Multiple Sizes', 'admin-page-framework-demo' ),
                'label'             => __( 'Weight', 'admin-page-framework-demo' ),
                'default'           => array( 'size' => 15, 'unit' => 'g' ),
                'units'             => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
                array(  
                    'label'         => __( 'Length', 'admin-page-framework-demo' ),
                    'default'       => array( 'size' => 100, 'unit' => 'mm' ),
                    'units'         => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
                ),  
                array(  
                    'label'         => __( 'File Size', 'admin-page-framework-demo' ),
                    'default'       => array( 'size' => 30, 'unit' => 'mb' ),
                    'units'         => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
                ),      
                'delimiter'         => '<br />',
            ),     
            array (
                'field_id'          => 'taxonomy_checklist',
                'type'              => 'taxonomy',
                'title'             => __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
                'taxonomy_slugs'    => get_taxonomies( '', 'names' ),
            ),     
            array()
        );     
  
    }
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> method.', 'admin-page-framework-demo' ), __FUNCTION__ ) . "</p>";
        return $_sInsert . $sContent;        
        
    }
    
    /**
     * The content filter callback method.
     */
    public function content_APF_MetaBox_BuiltinFieldTypes( $sContent ) { // content_{instantiated class name}
        
        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> hook.', 'admin-page-framework-demo' ), __FUNCTION__ ) . "</p>";
        return $sContent . $_sInsert;
        
    }
    
    /**
     * One of the predefined validation callback methods,
     * 
     * Alternatively, you may use `validataion_{instantiated class name}()` method,
     */
    public function validate( $aInput, $aOldInput, $oAdmin ) {
    
        $_bIsValid  = true;
        $_aErrors   = array();

        // You can check the passed values with the log() method of the oDebug object.
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