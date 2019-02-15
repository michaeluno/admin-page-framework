<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds the Custom Field Type page to the loader plugin.
 *
 * @since       3.6.2
 * @package     AdminPageFramework/Example
 */
class APF_Demo_AdvancedUsage {

    private $_sClassName = 'APF_Demo';

    private $_sPageSlug  = 'apf_advanced_usage';

    /**
     * Adds a page item and sets up hooks.
     */
    public function __construct( $sClassName='' ) {

        $this->_sClassName = $sClassName ? $sClassName : $this->_sClassName;

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );

    }

    /**
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {

        $oFactory->addSubMenuItems(
            array(
                'title'         => __( 'Advanced Usage', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,    // page slug
                'style'         => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/field_example.css',
                ),
            )
        );

        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );
        add_action( 'do_' . $this->_sPageSlug, array( $this, 'replyToDoPage' ) );

    }

    /**
     * Called when the page starts loading.
     *
     * @callback        action      load_{page slug}
     * */
    public function replyToLoadPage( $oFactory ) {

        // Define in-page tabs - here tabs are defined in the below classes.
        $_aTabClasses = array(
            'APF_Demo_AdvancedUsage_Section',
            'APF_Demo_AdvancedUsage_Nested',
            'APF_Demo_AdvancedUsage_Argument',
            'APF_Demo_AdvancedUsage_Verification',
            'APF_Demo_AdvancedUsage_Mixed',
            'APF_Demo_AdvancedUsage_Callback',
            'APF_Demo_AdvancedUsage_Complex',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;
            }
            new $_sTabClassName( $oFactory );
        }

    }

    /*
     * Handles the page output.
     *
     * @callback        action      do_{page slug}
     * */
    public function replyToDoPage() {
        submit_button();
    }

}
