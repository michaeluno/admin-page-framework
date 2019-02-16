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
 * Test Ajax fields for user meta fields.
 *
 */
class APF_UserMetaTestAjaxField extends AdminPageFramework_UserMeta {

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

    public function load() {
         new AjaxTestCustomFieldType( $this->oProp->sClassName );
    }

}

if ( defined( 'WP_DEBUG' ) && 'WP_DEBUG' ) {
    new APF_UserMetaTestAjaxField;
}
