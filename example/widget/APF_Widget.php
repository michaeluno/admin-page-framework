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

/**
 * Creates a widget.
 * 
 * @since   3.2.0
 */
class APF_Widget extends AdminPageFramework_Widget {
    
    /**
     * The user constructor.
     * 
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {}
    
    /**
     * Sets up arguments.
     * 
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments( 
            array(
                'description'   =>  __( 'This is a sample widget with built-in field types created by Admin Page Framework.', 'admin-page-framework-demo' ),
            ) 
        );
    
    }    

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load( $oAdminWidget ) {
        
        $this->addSettingFields(
            array(
                'field_id'      => 'title',
                'type'          => 'text',
                'title'         => __( 'Title', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'repeatable_text',
                'type'          => 'text',
                'title'         => __( 'Text Repeatable', 'admin-page-framework-demo' ),
                'repeatable'    => true,
                'sortable'      => true,
            ),
            array(
                'field_id'      => 'textarea',
                'type'          => 'textarea',
                'title'         => __( 'TextArea', 'admin-page-framework-demo' ),
                // 'rich'          => true,
            ),
            array(
                'field_id'      => 'checkbox',
                'type'          => 'checkbox',
                'title'         => __( 'Check Box', 'admin-page-framework-demo' ),
                'label'         => __( 'This is a check box in a widget form.', 'admin-page-framework-demo' ),
            ),     
            array(
                'field_id'      => 'radio',
                'type'          => 'radio',
                'title'         => __( 'Radio Buttons', 'admin-page-framework-demo' ),
                'label'         => array(
                    'one'   =>  __( 'One', 'admin-page-framework-demo' ),
                    'two'   =>  __( 'Two', 'admin-page-framework-demo' ),
                    'three' =>  __( 'Three', 'admin-page-framework-demo' ),
                ),
                'default'       => 'two',
            ),      
            array(
                'field_id'      => 'select',
                'type'          => 'select',
                'title'         => __( 'Dropdown', 'admin-page-framework-demo' ),
                'label'         => array(
                    'i'     =>  __( 'I', 'admin-page-framework-demo' ),
                    'ii'    =>  __( 'II', 'admin-page-framework-demo' ),
                    'iii'   =>  __( 'III', 'admin-page-framework-demo' ),
                ),
            ),                
            array(
                'field_id'      => 'image',
                'type'          => 'image',
                'title'         => __( 'Image', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'media',
                'type'          => 'media',
                'title'         => __( 'Media', 'admin-page-framework-demo' ),
            ),            
            array(
                'field_id'      => 'color',
                'type'          => 'color',
                'title'         => __( 'Color', 'admin-page-framework-demo' ),
            ),
            array()
        );        

        
    }
    
    /**
     * Validates the submitted form data.
     * 
     * Alternatively you may use validation_{instantiated class name} method.
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );
        
        return $aSubmit;
        
    }    
    
    /**
     * Print out the contents in the front-end.
     * 
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {
        
        return $sContent
            . '<p>' . __( 'Hello world! This is a widget created by Admin Page Framework.', 'admin-page-framework-demo' ) . '</p>'
            . AdminPageFramework_Debug::get( $aArguments )
            . AdminPageFramework_Debug::get( $aFormData );
    
    }
        
}