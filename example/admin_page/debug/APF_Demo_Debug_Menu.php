<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the test page to the loader plugin.
 *
 * @since 3.9.1
 */
class APF_Demo_Debug_Menu {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'screen_info';

    public $sSectionID;

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => 'Screen Information',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
         add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback add_action() load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        $oAdminPage->addSettingSection( array(
            'section_id' => 'debug',
        ) );

        /**
         * @var WP_Screen
         */
        $_oWPScreen = get_current_screen();
        $oAdminPage->addSettingFields(
            'debug',
            array(
                'field_id'          => 'screen',
                'show_title_column' => false,
                'type'              => 'table',
                'collapsible'       => true,
                'data'              => array(),
                'caption'           => 'Current Screen',
            ),
            array(
                'field_id'          => 'sidebar_menu',
                'show_title_column' => false,
                'type'              => 'table',
                'collapsible'       => true,
                'data'              => $GLOBALS[ 'submenu' ],
                'caption'           => 'Admin Sidebar Menu',
            )
        );

        add_filter( 'field_definition_' . $oAdminPage->oProp->sClassName . '_debug_screen', array( $this, 'replyToGetCurrentScreenData' ) );

    }


    public function replyToGetCurrentScreenData( $aFieldset ) {

        /**
         * @var WP_Screen
         */
        $_oWPScreen = get_current_screen();
        $aFieldset[ 'data' ] = ( $_oWPScreen instanceof WP_SCreen )
            ? get_object_vars( $_oWPScreen )
            : array();
        return $aFieldset;

    }

    /**
     * @param AdminPageFramework $oAdminPage
     */
    public function replyToDoTab( $oAdminPage ) {}

}