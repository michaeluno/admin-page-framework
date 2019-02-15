<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.8.14
 */
class APF_Demo_Test_AjaxField {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'ajax';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Ajax', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        new AjaxTestCustomFieldType( $this->sClassName );

        // add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

         $this->___addFormElements( $oAdminPage );

        new APF_Demo_Test_PageMetaBoxSide(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Side', 'admin-page-framework-loader' ),    // title
            array(
                $this->sPageSlug => array(
                    'ajax', // tab slugs
                ),
            ),
            'side',                                         // context
            'default'                                       // priority
        );

    }

    private function ___addFormElements( $oAdminPage ) {

        // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'  => $this->sSectionID,
                'tab_slug'    => $this->sTabSlug,
                'title'       => __( 'Ajax Field Test', 'admin-page-framework-loader' ),
                'description' => array(
                    __( 'Tests fields that use Ajax.', 'admin-page-framework-loader' ),
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
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

    public function replyToDoTab() {
        submit_button();
    }

}
