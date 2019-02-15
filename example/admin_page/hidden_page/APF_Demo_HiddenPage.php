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

/**
 * This example demonstrates the use of the set_up_{instantiated class name} hook.
 *
 * Note that this class does not extend any class unlike the other admin page classes in the demo plugin examples.
 */
class APF_Demo_HiddenPage {

    private $_sClassName = 'APF_Demo';

    /**
     * Sets up hooks.
     */
    public function __construct() {

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUpPages' )
        );

    }

    /**
     * Sets up pages.
     *
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUpPages( $oFactory ) {

        // ( required ) Add sub-menu items (pages or links)
        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Sample Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_sample_page',
                'screen_icon'   => AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/wp_logo_bw_32x32.png', // ( for WP v3.7.1 or below ) the icon _file path_ can be used
            ),
            array(
                'title'         => __( 'Hidden Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_hidden_page',
                'screen_icon'   => version_compare( $GLOBALS[ 'wp_version' ], '3.8', '<' )
                    ? AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/image/wp_logo_bw_32x32.png', AdminPageFrameworkLoader_Registry::$sFilePath )
                    : null, // ( for WP v3.7.1 or below )
                'show_in_menu'  => false,
            )
        );

        add_action( "load_" . "apf_sample_page", array( $this, 'replyToLoadPage' ) );
        add_action( "load_" . "apf_hidden_page", array( $this, 'replyToLoadPage' ) );
        add_action( "do_" . "apf_sample_page", array( $this, 'replyToModifySamplePage' ) );
        add_action( "do_" . "apf_hidden_page", array( $this, 'replyToModifyHiddenPage' ) );

    }

    /**
     * Called when the page starts loading.
     *
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        $oFactory->setPageTitleVisibility( true );
        $oFactory->setPageHeadingTabsVisibility( false );

    }

    /*
     * The sample page and the hidden page.
     *
     * @callback        action      do_{page slug}
     */
    public function replyToModifySamplePage( $oFactory ) {

        echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-loader' ) . "</p>";
        $_sLinkToHiddenPage = esc_url( $oFactory->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) ) );
        echo "<a href='{$_sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-loader' ). "</a>";

    }

    /**
     * @callback        action      do_{page slug}
     */
    public function replyToModifyHiddenPage( $oFactory ) {

        echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-loader' ) . "</p>";
        echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-loader' ) . "</p>";
        $_sLinkToGoBack = esc_url( $oFactory->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) ) );
        echo "<a href='{$_sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-loader' ). "</a>";

    }

}
