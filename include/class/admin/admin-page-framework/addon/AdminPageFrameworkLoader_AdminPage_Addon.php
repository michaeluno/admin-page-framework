<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Addon page to the loader plugin.
 * 
 * @since       3.5.0       
 */
class AdminPageFrameworkLoader_AdminPage_Addon {

    public function __construct( $oFactory, $sPageSlug, $sTitle ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sPageTitle   = $sTitle;
    
        $this->_addPage();
    
    }
        
        /**
         * Adds an admin page.
         */
        private function _addPage() {
            
            $this->oFactory->addSubMenuItems( 
                array(
                    'title'         => $this->sPageTitle,
                    'page_slug'     => $this->sPageSlug,    // page slug
                    'order'         => 990, // the Help page is 1000
                )
            );

            // Tabs
            new AdminPageFrameworkLoader_AdminPage_Addon_Top( 
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'top'                   // tab slug
            );            
            
        }
        
}
