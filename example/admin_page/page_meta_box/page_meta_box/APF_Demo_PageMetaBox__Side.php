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

class APF_Demo_PageMetaBox__Side extends AdminPageFramework_PageMetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array (
                'field_id' => 'color_field',
                'type' => 'color',
                'title' => __( 'Color', 'admin-page-framework-loader' ),
            ),
            array (
                'field_id' => 'size_field',
                'type' => 'size',
                'title' => __( 'Size', 'admin-page-framework-loader' ),
                'default' => array( 'size' => 5, 'unit' => '%' ),
            ),
            array (
                'field_id'          => 'side_single_checkbox',
                'type'              => 'checkbox',
                'title'             => __( 'Check Box', 'admin-page-framework-loader' ),
                'label'             => __( 'Check me!', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'          => 'submit_in_meta_box',
                'type'              => 'submit',
                'show_title_column' => false,
                'label_min_width'   => 0,
                'save'              => false,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; width:auto;',
                    ),
                ),
            ),
            array()
        );

    }

    /**
     * @callback        action      do_{instantiated class name}
     */
    public function do_APF_MetaBox_For_Pages_Side() {
        ?>
            <p><?php _e( 'This is a side meta box. This is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-loader' ) ?></p>
        <?php
    }


    /**
     * Validates the submitted form data.
     *
     * Alternatively you can use `validation_{class name}()` predefined callback method.
     */
    public function validate( $aNewOptions, $aOldOptions, $oAdminPage, $aSubmitInfo ) {
        return $aNewOptions;
    }


}
