<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a page to the loader plugin.
 *
 * @since       3.8.14
 * @package     AdminPageFramework/Example
 */
class APF_Demo_Test {

    private $_sClassName = 'APF_Demo';

    private $_sPageSlug  = 'apf_test_page';

    /**
     * Adds a page item and sets up hooks.
     */
    public function __construct() {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );

    }

    /**
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {
// new AjaxTestCustomFieldType( $oFactory->oProp->sClassName );
        /**
         * ( required ) Add sub-menu items (pages or links)
         */
        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Test', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,
                'order'         => 35,
            )
        );
// new APF_Demo_Test_AjaxField( $oFactory, $this->_sPageSlug );
        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );

    }

    /**
     * @return      void
     * @callback    action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        /**
         * (optional) Add in-page tabs - here tabs are defined in the below classes.
         */
        $_aTabClasses = array(
            'APF_Demo_Test_AjaxField',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;
            }
            new $_sTabClassName( $oFactory, $this->_sPageSlug );
        }

    }

}
