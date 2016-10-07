<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Custom Field Type page to the loader plugin.
 * 
 * @since       3.5.0  
 * @package     AdminPageFramework
 * @subpackage  Example 
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
        new APF_Demo_CustomFieldType_Select2(
            $oFactory,
            $this->_sPageSlug
        );
        new APF_Demo_CustomFieldType_Path(
            $oFactory,
            $this->_sPageSlug
        );        
        new APF_Demo_CustomFieldType_Toggle(
            $oFactory,
            $this->_sPageSlug
        );        
        new APF_Demo_CustomFieldType_NoUISlider(
            $oFactory,
            $this->_sPageSlug
        );
        new APF_Demo_CustomFieldType_ACE(
            $oFactory,
            $this->_sPageSlug
        );   
        new APF_Demo_CustomFieldType_Sample(
            $oFactory,    
            $this->_sPageSlug
        );
        new APF_Demo_CustomFieldType_GitHub(
            $oFactory,    
            $this->_sPageSlug   
        );
        new APF_Demo_CustomFieldType_Mixed(
            $oFactory,    
            $this->_sPageSlug   
        );

        
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
