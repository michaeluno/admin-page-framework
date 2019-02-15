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
class APF_Demo_ManageOptions_Message {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'messages';
    private $_sSectionID = 'messages';

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
                'title'         => __( 'Messages', 'admin-page-framework-loader' ),
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
        <h3><?php _e( 'Framework System Messages', 'admin-page-framework-loader' ); ?></h3>
        <p><?php _e( 'You can change the framework\'s defined internal messages by using the <code>setMessage()</code> method.', 'admin-page-framework-loader' ); // ' syntax fixer ?>
            <?php _e( 'The keys and the default values can be obtained with the <code>getMessage()</code> method.', 'admin-page-framework-loader' ); ?>
        </p>
        <h4><?php _e( 'Check the Original Message', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;getMessage( 'option_updated' );</code></pre>
        <?php $this->_oFactory->oDebug->dump( $this->_oFactory->getMessage( 'option_updated' ) ); ?>

        <h4><?php _e( 'Modify a Message', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;setMessage( 'option_updated', 'This is a modified message.' );</code></pre>
        <?php
            $this->_oFactory->setMessage(
                'option_updated',
                __( 'This is a modified message', 'admin-page-framework-loader' )
            );
        ?>

        <h4><?php _e( 'List All the Messages', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;getMessage();</code></pre>
        <?php
        $this->_oFactory->oDebug->dump( $this->_oFactory->getMessage() );

    }

}
