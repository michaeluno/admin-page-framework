<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab.
 *
 * @since 3.9.1
 */
class APF_Demo_Debug_AdminScreen {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'admin_screen';

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
                'title'         => 'Admin Screen',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

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
                'collapsible'       => array(
                    'active' => true,
                ),
                'data'              => array(),
                'caption'           => 'Current Screen',
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

}