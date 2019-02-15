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
class APF_Demo_ManageOptions_SavedData {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'saved_data';
    private $_sSectionID = 'saved_data';

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
                'title'         => __( 'Saved Data', 'admin-page-framework-loader' ),
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
        <h3><?php _e( 'Saved Data', 'admin-page-framework-loader' ); ?></h3>
        <p>
        <?php
            echo sprintf( __( 'To retrieve the saved option values simply you can use the WordPress <code>get_option()</code> function. The key is the instantiated class name by default unless it is specified in the constructor. In this demo plugin, <code>%1$s</code>, is used as the option key.', 'admin-page-framework-loader' ), $this->_oFactory->oProp->sOptionKey );
            echo ' ' . sprintf( __( 'It is stored in the <code>$this->oProp-sOptionKey</code> class property so you may access it directly to confirm the value. So the required code would be <code>get_option( %1$s );</code>.', 'admin-page-framework-loader' ), $this->_oFactory->oProp->sOptionKey );
            echo ' ' . __( 'If you are retrieving them within the framework class, simply call <code>$this->oProp->aOptions</code>.', 'admin-page-framework-loader' );
        ?>
        </p>
        <p>
        <?php
            echo __( 'Alternatively, there is the <code>AdminPageFramework::getOption()</code> static method. This allows you to retrieve the array element by specifying the option key and the array key (field id or section id).', 'admin-page-framework-loader' );
            echo ' ' . __( 'Pass the option key to the first parameter and an array representing the dimensional keys to the second parameter', 'admin-page-framework-loader' );
            echo ' ' . __( '<code>$aData = AdminPageFramework::getOption( \'APF_Demo\', array( \'text_fields\', \'text\' ), \'default value\' );</code> will retrieve the option array value of <code>$aArray[\'text_field\'][\'text\']</code>.', 'admin-page-framework-loader' );
            echo ' ' . __( 'This method is merely to avoid multiple uses of <code>isset()</code> to prevent PHP warnings.', 'admin-page-framework-loader' );
            echo ' ' . __( 'So if you already know how to retrieve a value of an array element, you don\'t have to use it.', 'admin-page-framework-loader' ); // ' syntax fixer
        ?>
        </p>
        <?php
            echo $this->_oFactory->oDebug->getDetails( $this->_oFactory->oProp->aOptions );

    }

}
