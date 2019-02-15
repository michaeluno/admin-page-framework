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
 * @since       3.5.0
 */
class APF_Demo_ManageOptions_Property {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'properties';
    private $_sSectionID = 'properties';

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
                'title'         => __( 'Properties', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
        add_action( 'load_' . $this->_sPageSlug . '_' . $this->_sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {

        add_action( 'do_' . $this->_sPageSlug . '_' . $this->_sTabSlug, array( $this, 'replyToDoTab' ) );

    }

    public function replyToDoTab() {

        ?>
        <h3><?php _e( 'Framework Properties', 'admin-page-framework-loader' ); ?></h3>
        <p><?php _e( 'These are the property values stored in the framework. Advanced users may change the property values by directly modifying the <code>$this->oProp</code> object.', 'admin-page-framework-loader' ); ?></p>
        <pre class="dump-array"><code>echo $this-&gt;oDebug-&gt;getDetails( get_object_vars( $this-&gt;oProp ) );</code></pre>
        <?php
            echo $this->_oFactory->oDebug->getDetails( get_object_vars( $this->_oFactory->oProp ) );


    }

}
