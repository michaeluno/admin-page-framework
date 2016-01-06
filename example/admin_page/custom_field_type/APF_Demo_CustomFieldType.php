<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
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

    public $oFactory;
    public $sClassName;
    public $sPageSlug;
    public $sPageTitle;
        
    public function __construct( $oFactory ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = 'custom_field_type';
        $this->sPageTitle   = __( 'Custom Field Types', 'admin-page-framework-loader' );

        $this->oFactory->addSubMenuItems( 
            array(
                'title'         => $this->sPageTitle,
                'page_slug'     => $this->sPageSlug,    // page slug
            )
        );

        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToLoadPage' ) );
        
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
            $this->sPageSlug   // page slug
        );   
        new APF_Demo_CustomFieldType_Sample(
            $oFactory,    // factory object
            $this->sPageSlug   // page slug
        );
        new APF_Demo_CustomFieldType_GitHub(
            $oFactory,    // factory object
            $this->sPageSlug   // page slug
        );
        
        // Add a link
        $oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => 'more',
                'title'         => __( 'More', 'admin-page-framework-loader' ),
                'url'           => 'http://admin-page-framework.michaeluno.jp/add-ons/field-type-pack/',
                'order'         => 999,
            )
        );  
                
    }
        
}