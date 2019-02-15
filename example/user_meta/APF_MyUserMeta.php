<?php
/**
 * Admin Page Framework - Loader
 *
 * Loads Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Demonstrates usage of the user meta factory class of Admin Page Framework.
 *
 * @since       3.5.3
 */
class APF_Demo_MyUserMeta extends AdminPageFramework_UserMeta {

    public function setUp() {

        $_sSectionID = 'apf_user_meta_demo';

        $this->addSettingSections(
            array(
                'section_id'    => $_sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'title'         => __( 'Admin Page Framework', 'admin-page-framework-loader' ),
            )
        );

        $this->addSettingFields(
            $_sSectionID,
            array(
                'field_id'      => 'text_field',
                'type'          => 'text',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => __( 'Type something here.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'text_area',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'default'       => __( 'Hi there!', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'image',
                'type'          => 'image',
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;',
                    ),
                ),
            ),
            array(
                'field_id'      => 'color',
                'type'          => 'color',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'radio_buttons',
                'type'          => 'radio',
                'title'         => __( 'Radio', 'admin-page-framework-loader' ),
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
                'default'       => 'b',
            )
        );

    }

    /**
     * A pre-defined validation callback method.
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }

}

new APF_Demo_MyUserMeta;
