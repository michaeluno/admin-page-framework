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

class APF_Demo_PageMetaBox__WithFormSection extends AdminPageFramework_PageMetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        $this->addSettingSections(
            array(
               'section_id'     => 'meta_box_with_a_form_section',
               'title'          => __( 'Section of a Page Meta-box', 'admin-page-framework-loader' ),
               'description'    => __( 'This meta box form has a section.', 'admin-page-framework-loader' ),
            )
        );

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            'meta_box_with_a_form_section', // section ID
            array (
                'field_id'   => 'image',
                'type'       => 'image',
                'title'      => __( 'Images', 'admin-page-framework-loader' ),
                'repeatable' => true,
                'sortable'   => true,
            ),
            array()
        );

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

