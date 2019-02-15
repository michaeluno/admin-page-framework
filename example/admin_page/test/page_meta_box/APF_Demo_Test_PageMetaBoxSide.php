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

class APF_Demo_Test_PageMetaBoxSide extends AdminPageFramework_PageMetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'ajax_test_in_page_meta_box',
                'type'          => 'ajax_test',
                'title'         => __( 'Ajax', 'admin-page-framework-loader' ),
                'label'         => array(
                    'a'              => 'A',
                    'b'              => 'B',
                    'c'              => 'C',
                ),
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

    public function load() {
        new AjaxTestCustomFieldType( $this->oProp->sClassName );
    }

}
