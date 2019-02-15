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
 * @since       3.7.12
 */
class APF_Demo_PageMetaBox__FirstTab {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'first';
    private $_sSectionID = 'first';

    /**
     * Sets uo properties, hooks, and in-page tabs.
     */
    public function __construct( $oFactory, $sPageSlug ) {

        $this->_oFactory     = $oFactory;
        $this->_sClassName   = $oFactory->oProp->sClassName;
        $this->_sPageSlug    = $sPageSlug;

        $this->_oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->_sTabSlug,
                'title'         => __( 'First', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
        add_action( 'load_' . $this->_sPageSlug . '_' . $this->_sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {



    }


}
