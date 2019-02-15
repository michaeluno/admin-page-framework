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

class APF_Demo_ManageOptions {

    private $_sClassName = 'APF_Demo';

    /**
     * Stores the page slug.
     */
    private $_sPageSlug = 'apf_manage_options';

    public function __construct() {

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );
    }

    /**
     * Sets up pages.
     *
     * @callback        set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {

        // Add sub-menu items (pages or links)
        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Manage Options', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,
                'screen_icon'   => 'link-manager',
                'order'         => 40,
            )
        );

        add_action(
            'load_' . $this->_sPageSlug,
            array( $this, 'replyToLoadPage' )
        );

    }

    /**
     * Called when the page starts loading.
     *
     * @callback        action      load_{page slug}
     * @return          void
     */
    public function replyToLoadPage( $oFactory ) {

        // Set up the page settings
        $oFactory->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $oFactory->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs

        // Enqueue styles
        $oFactory->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css' );

        // Tabs
        new APF_Demo_ManageOptions_SavedData( $oFactory, $this->_sPageSlug );
        new APF_Demo_ManageOptions_Property( $oFactory, $this->_sPageSlug );
        new APF_Demo_ManageOptions_Message( $oFactory, $this->_sPageSlug );
        new APF_Demo_ManageOptions_Export( $oFactory, $this->_sPageSlug );
        new APF_Demo_ManageOptions_Import( $oFactory, $this->_sPageSlug );
        new APF_Demo_ManageOptions_Reset( $oFactory, $this->_sPageSlug );

        // Disabled tab example
        $oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => 'disabled',
                'title'         => __( 'Disabled', 'admin-page-framework-loader' ),
                'disabled'      => true,
                'attributes'    => array(
                    'title'     => __( 'If the disabled argument is true, this tab will be disabled and has no link.', 'admin-page-framework-loader' ),
                ),
            )
        );

        // Link tab
        $oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => 'link',
                'title'         => __( 'Link', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'title'     => __( 'With the url argument, the tab can be linked to an external URL.', 'admin-page-framework-loader' ),
                ),
                'url'           => 'http://admin-page-framework.michaeluno.jp'
            )
        );
    }

}
