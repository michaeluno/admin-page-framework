<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a page to the loader plugin.
 *
 * @since   3.9.1
 * @package AdminPageFramework/Example
 */
class APF_Demo_Debug {

    private $_sClassName = 'APF_Demo';

    private $_sPageSlug  = 'apf_demo_debug';

    /**
     * Adds a page item and sets up hooks.
     */
    public function __construct() {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        add_action( 'set_up_' . $this->_sClassName, array( $this, 'replyToSetUp' ) );

    }

    /**
     * @callback add_action() set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {

        /**
         * (required) Add sub-menu items (pages or links)
         */
        $oFactory->addSubMenuItems(
            array(
                'title'     => __( 'Debug', 'admin-page-framework-loader' ),
                'page_slug' => $this->_sPageSlug,
                'order'     => 110,
            )
        );

        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );

    }

    /**
     * @callback add_action() load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        /**
         * (optional) Add in-page tabs - here tabs are defined in the below classes.
         */
        $_aTabClasses = array(
            'APF_Demo_Debug_PostMeta',
            'APF_Demo_Debug_Option',
            'APF_Demo_Debug_AdminSidebarMenu',
            'APF_Demo_Debug_AdminScreen',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;
            }
            new $_sTabClassName( $oFactory, $this->_sPageSlug );
        }

    }

}