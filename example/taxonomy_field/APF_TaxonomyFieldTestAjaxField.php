<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed GPLv2
 * 
 */

class APF_TaxonomyFieldTestAjaxField extends AdminPageFramework_TaxonomyField {
        
    public function load() {
        new AjaxTestCustomFieldType( $this->oProp->sClassName );
    }

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
    new APF_TaxonomyFieldTestAjaxField(
        'apf_sample_taxonomy'   // taxonomy slug
    );
}
