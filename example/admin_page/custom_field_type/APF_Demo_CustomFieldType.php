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
 * @since       3.5.0
 * @package     AdminPageFramework/Example
 */
class APF_Demo_CustomFieldType {

    private $_oFactory;
    private $_sClassName   = 'APF_Demo';
    private $_sPageSlug    = 'custom_field_type';

    /**
     * Adds a page item and sets up hooks.
     */
    public function __construct() {

        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );

    }

    /**
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {

        $this->_oFactory     = $oFactory;

        $this->_oFactory->addSubMenuItems(
            array(
                'title'     => __( 'Custom Field Types', 'admin-page-framework-loader' ),
                'page_slug' => $this->_sPageSlug,    // page slug
                'order'     => 30,
                'style'     => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/field_example.css',
                ),
            )
        );

        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );

    }

    /**
     * Called when the page starts loading.
     *
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {

        // Tabs
        $_aTabClasses = array(
            'APF_Demo_CustomFieldType_Select2',
            'APF_Demo_CustomFieldType_Path',
            'APF_Demo_CustomFieldType_Toggle',
            'APF_Demo_CustomFieldType_NoUISlider',
            'APF_Demo_CustomFieldType_ACE',
            'APF_Demo_CustomFieldType_PostTypeTaxonomy',
            'APF_Demo_CustomFieldType_Sample',
            'APF_Demo_CustomFieldType_GitHub',
            'APF_Demo_CustomFieldType_Mixed',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;
            }
            new $_sTabClassName( $oFactory, $this->_sPageSlug );
        }

        // Add a link in tabs
        $oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => 'more',
                'title'         => __( 'More', 'admin-page-framework-loader' ),
                'url'           => 'http://admin-page-framework.michaeluno.jp/add-ons/field-type-pack/',
                'order'         => 999,
            )
        );

    }

}
