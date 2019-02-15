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
 * Adds the Contact page to the demo plugin.
 *
 * @since   3.2.2
 */
class APF_Demo_Contact {

    private $_sClassName = 'APF_Demo';

    private $_sPageSlug  = 'apf_contact';

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
     * This method automatically gets triggered with the wp_loaded hook.
     */
    public function replyToSetUpPages( $oFactory ) {

        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Contact', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,
                'screen_icon'   => 'page',
                'capability'    => 'manage_options',
                'order'         => 60,
            )
        );

        add_action(
            'load_' . $this->_sPageSlug,
            array( $this, 'replyToLoadPage' )
        );

    }

    /**
     * Do page specific settings.
     *
     * @callback        action      load_ + {page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        // disables the page heading tabs by passing false.
        $oFactory->setPageHeadingTabsVisibility( false );

        // sets the tag used for in-page tabs.
        $oFactory->setInPageTabTag( 'h2' );

        // disable the page title.
        $oFactory->setPageTitleVisibility( false );

        new APF_Demo_Contact_Tab_Feedback( $oFactory, $this->_sPageSlug );
        new APF_Demo_Contact_Tab_Report( $oFactory, $this->_sPageSlug );

    }

}
