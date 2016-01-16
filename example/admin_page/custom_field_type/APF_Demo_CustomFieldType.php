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
        new APF_Demo_CustomFieldType_ACE(
            $oFactory,    // factory object
            $this->_sPageSlug   // page slug
        );   
        new APF_Demo_CustomFieldType_Sample(
            $oFactory,    // factory object
            $this->_sPageSlug   // page slug
        );
        new APF_Demo_CustomFieldType_GitHub(
            $oFactory,    // factory object
            $this->_sPageSlug   // page slug
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
