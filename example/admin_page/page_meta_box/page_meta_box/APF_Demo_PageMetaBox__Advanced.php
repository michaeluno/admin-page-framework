<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

class APF_Demo_PageMetaBox__Advanced extends AdminPageFramework_PageMetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'select_filed',
                'type'          => 'select',
                'title'         => __( 'Select Box', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'one'   => __( 'One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Three', 'admin-page-framework-loader' ),
                ),
                'default'       => 'one', // 0 means the first item
            ),
            array(
                'field_id'      => 'multiple_select_filed',
                'type'          => 'select',
                'title'         => __( 'Multiple Select Options', 'admin-page-framework-loader' ),
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
                'default'       => array(
                    'c', 'e',
                ),
            ),
            array (
                'field_id'      => 'radio_field',
                'type'          => 'radio',
                'title'         => __( 'Radio Group', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'one'   => __( 'Option One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Option Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Option Three', 'admin-page-framework-loader' ),
                ),
                'default'       => 'one',
            ),
            array (
                'field_id'      => 'checkbox_group_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Group', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'one'   => __( 'Option One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Option Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Option Three', 'admin-page-framework-loader' ),
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
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'style' => 'max-width:300px;',
                ),
            )
        );


    }

    /**
     * @callback        action      do_{instantiated class name}
     */
    public function do_APF_Demo_PageMetaBox__Advanced() {
        ?>
            <p><?php _e( 'This meta box is placed with the <code>advanced</code> context and this text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-loader' ) ?></p>
        <?php

    }

    /**
     * Validates the submitted form data.
     *
     * Alternatively you can use `validation_{class name}()` predefined callback method.
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage ) {
        return $aInputs;
    }

}
