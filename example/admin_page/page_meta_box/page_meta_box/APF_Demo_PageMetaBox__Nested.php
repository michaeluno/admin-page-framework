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

class APF_Demo_PageMetaBox__Nested extends AdminPageFramework_PageMetaBox {

    /**
     *
     */
    public function setUp() {

        $this->addSettingSections(
            array(
                'section_id'     => 'page_meta_box_nested_section',
                'title'          => __( 'Parent', 'admin-page-framework-loader' ),
                'content'        => array(
                    array(
                        'section_id'    => 'a',
                        'title'         => __( 'Parent', 'admin-page-framework-loader' )
                            . ' -> ' . __( 'A', 'admin-page-framework-loader' ),
                    ),
                    array(
                        'section_id'    => 'b',
                        'title'         => __( 'Parent', 'admin-page-framework-loader' )
                            . ' -> ' . __( 'B', 'admin-page-framework-loader' ),
                    ),
                ),
            )
        );

        $this->addSettingFields(
            array( 'page_meta_box_nested_section', 'a' ), // section path
            array(
                'field_id'          => 'color',
                'type'              => 'color',
                'title'             => __( 'Color', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'          => 'select',
                'type'              => 'select',
                'label'             => array(
                    1 => __( 'Yes', 'admin-page-framework-loader' ),
                    0 => __( 'No', 'admin-page-framework-loader' ),
                ),
                'default'   => 0,
            )
        );
        $this->addSettingFields(
            array( 'page_meta_box_nested_section', 'b' ), // section path
            array(
                'field_id'          => 'radio',
                'type'              => 'radio',
                'label'             => array(
                    1 => __( 'Yes', 'admin-page-framework-loader' ),
                    0 => __( 'No', 'admin-page-framework-loader' ),
                ),
                'default'   => 1,
            )
        );

    }



    /**
     * Validates the submitted form data.
     *
     * Alternatively you can use `validation_{class name}()` predefined callback method.
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        return $aInputs;
    }


}
