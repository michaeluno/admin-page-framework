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

class APF_PostMetaBox_TestAjaxField extends AdminPageFramework_MetaBox {

    public function load() {
        new AjaxTestCustomFieldType( $this->oProp->sClassName );
        new Select2CustomFieldType( $this->oProp->sClassName );
    }

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        $this->addSettingFields(
            array(
                'field_id'      => '_test_ajax_field',
                'type'          => 'ajax_test',
                'title'         => __( 'Ajax Test', 'admin-page-framework-loader' ),
                'label'    => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
            ),
            array(
                'field_id'      => '_select2',
                'type'          => 'select2',
                'title'         => __( 'Select2 Test', 'admin-page-framework-loader' ),
                'options'         => array(
                    'minimumInputLength' => 2,
                    'width' => '100%',
                ),
                'callback'        => array(
                    'search'    => __CLASS__ . '::getPosts',
                ),
            )
        );

    }

    static public function getPosts( $aQueries, $aFieldset ) {

        $_aArgs         = array(
            'post_type'         => 'post',
            'paged'             => $aQueries[ 'page' ],
            's'                 => $aQueries[ 'q' ],
            'posts_per_page'    => 30,
            'nopaging'          => false,
        );
        $_oResults      = new WP_Query( $_aArgs );
        $_aPostTitles   = array();
        foreach( $_oResults->posts as $_iIndex => $_oPost ) {
            $_aPostTitles[] = array(    // must be numeric
                'id'    => $_oPost->ID,
                'text'  => $_oPost->post_title,
            );
        }
        return array(
            'results'       => $_aPostTitles,
            'pagination'    => array(
                'more'  => intval( $_oResults->max_num_pages ) !== intval( $_oResults->get( 'paged' ) ),
            ),
        );

    }

}


if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

    new APF_PostMetaBox_TestAjaxField(
        null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
        __( 'Test Ajax Field', 'admin-page-framework-loader' ), // title
        array( 'apf_posts' ),                                   // post type slugs: post, page, etc.
        'side',                                        // context (what kind of metabox this is)
        'high'                                                  // priority
    );

}
