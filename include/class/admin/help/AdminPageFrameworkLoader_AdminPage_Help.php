<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the loader plugin.
 * 
 * @since       3.5.0       
 */
class AdminPageFrameworkLoader_AdminPage_Help {

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
            new AdminPageFrameworkLoader_AdminPage_Help_Information(
                $this->oFactory,
                $this->sPageSlug,   // page slug
                'information'       // tab slug 
            );     
            new AdminPageFrameworkLoader_AdminPage_Help_Tip(
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'tips'                  // tab slug                                    
            );               
            new AdminPageFrameworkLoader_AdminPage_Help_FAQ(
                $this->oFactory,
                $this->sPageSlug,   // page slug
                'faq'               // tab slug 
            );      
            new AdminPageFrameworkLoader_AdminPage_Help_Report(
                $this->oFactory,
                $this->sPageSlug,   // page slug
                'report'            // tab slug 
            );      
            
        }
        
    /**
     * Gets triggered when the page loads.
     * 
     * @remark      A callback of the "load_{page slug}" action hook.
     */
    public function replyToLoadPage( $oFactory ) {

        // add_filter( "content_top_{$this->sPageSlug}", array( $this, 'replyToFilterContentTop' ) );
        // add_action( "style_{$this->sPageSlug}", array( $this, 'replyToAddInlineCSS' ) );
    
        $oFactory->enqueueStyle( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/help.css', 
            $this->sPageSlug
        );
        
    } 
            
        
}
