<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 */
class AdminPageFrameworkLoader_AdminPage_Help_About {

    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'About', 'admin-page-framework-loader' ),
                'url'           => add_query_arg( 
                    array( 'page' => AdminPageFrameworkLoader_Registry::$aAdminPages['about'] ),
                    admin_url( 'index.php' )   // Dashboard
                )
                
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }
    
    public function replyToDoTab() {}
    
}