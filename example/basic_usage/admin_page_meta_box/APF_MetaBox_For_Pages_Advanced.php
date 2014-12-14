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

class APF_MetaBox_For_Pages_Advanced extends AdminPageFramework_MetaBox_Page {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {
        
        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
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
                'label'         => array( 
                    'one'   => __( 'One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Three', 'admin-page-framework-demo' ),
                ),
                'default'       => 'one', // 0 means the first item
            ),     
            array(
                'field_id'      => 'multiple_select_filed',
                'type'          => 'select',
                'title'         => __( 'Multiple Select Options', 'admin-page-framework-demo' ),
                'label'         => array( 
                    'a'     => 'Apple',
                    'b'     => 'Banana',
                    'c'     => 'Cherry',
                    'd'     => 'Durian',
                    'e'     => 'Eggplant',
                ),
                'is_multiple'      => true,
                'attributes'       => array(
                    'select'    =>  array(
                        'size'  => 5,
                    ),                
                ),
            ),                 
            array (
                'field_id'      => 'radio_field',
                'type'          => 'radio',
                'title'         => __( 'Radio Group', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'label'         => array( 
                    'one'   => __( 'Option One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Option Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Option Three', 'admin-page-framework-demo' ),
                ),
                'default'       => 'one',
            ),
            array (
                'field_id'      => 'checkbox_group_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Group', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'label'         => array( 
                    'one'   => __( 'Option One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Option Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Option Three', 'admin-page-framework-demo' ),
                ),
                'default' => array(
                    'one'   => true,
                    'two'   => false,
                    'three' => false,
                ),
            ),
            array (
                'field_id'      => 'image_field',
                'type'          => 'image',
                'title'         => __( 'Image', 'admin-page-framework-demo' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'attributes'    => array(
                    'style' => 'max-width:300px;',
                ),                
            )     
        );
        
    }
    
    public function do_APF_MetaBox_For_Pages_Advanced() { // do_{instantiated class name}
        ?>
            <p><?php _e( 'This meta box is placed with the <code>advanced</code> context and this text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-demo' ) ?></p>
        <?php
        
    }
    
    /**
     * Validates the submitted form data.
     * 
     * Alternatively you can use `validation_{class name}()` predefined callback method.
     */
    public function validate( $aNewOptions, $aOldOptions, $oAdminPage ) {
        return $aNewOptions;
    }
    
}