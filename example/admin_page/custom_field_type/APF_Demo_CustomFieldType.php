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
 */
class APF_Demo_CustomFieldType {

    public function __construct( $oFactory, $sPageSlug, $sTitle ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sPageTitle   = $sTitle;
    
        $this->_addPage();
    
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToLoadPage' ) );       
    
    }
        
        /**
         * Adds an admin page.
         */
        private function _addPage() {
            
            $this->oFactory->addSubMenuItems( 
                array(
                    'title'         => $this->sPageTitle,
                    'page_slug'     => $this->sPageSlug,    // page slug
                )
            );

            // Tabs
            new APF_Demo_CustomFieldType_ACE(
                $this->oFactory,    // factory object
                $this->sPageSlug,   // page slug
                'ace'       // tab slug 
            );   
            new APF_Demo_CustomFieldType_Sample(
                $this->oFactory,    // factory object
                $this->sPageSlug,   // page slug
                'sample'       // tab slug             
            );
            new APF_Demo_CustomFieldType_GitHub(
                $this->oFactory,    // factory object
                $this->sPageSlug,   // page slug
                'github'       // tab slug                         
            );
            
            // Add a link
            $this->oFactory->addInPageTabs(    
                $this->sPageSlug, // target page slug
                array(
                    'tab_slug'      => 'more',
                    'title'         => __( 'More', 'admin-page-framework-loader' ),
                    'url'           => 'http://en.michaeluno.jp/admin-page-framwork/field-type-pack',
                    'order'         => 999,
                )
            );  
                    
        }
        
    /**
     * Gets triggered when the page loads.
     * 
     * @remark      A callback of the "load_{page slug}" action hook.
     */
    public function replyToLoadPage( $oFactory ) {        
    } 
  
        
}
