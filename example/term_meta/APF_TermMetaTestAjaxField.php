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

class APF_TermMetaTestAjaxField extends AdminPageFramework_TermMeta {

    public function load() {
        new AjaxTestCustomFieldType( $this->oProp->sClassName );
    }

    /*
     * ( optional ) Use the setUp() method to define settings of this taxonomy fields.
     */
    public function setUp() {

        $this->addSettingFields(
            array(
                'field_id' => 'ajax_test_filed',
                'type'     => 'ajax_test',
                'title'    => __( 'Ajax', 'admin-page-framework-loader' ),
                'label'    => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
            )
        );

    }

}

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    new APF_TermMetaTestAjaxField(
        'apf_sample_taxonomy'   // taxonomy slug
    );
}
