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
class AdminPageFrameworkLoader_AdminPage_About_Welcome {

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
                'title'         => __( "What's New", 'admin-page-framework-loader' ),   // '
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
        add_filter( "content_top_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToModifyTopContent' ) );
    }
    
    public function replyToModifyTopContent( $sContent ) {
        
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            )
        );
        return "<div class='introduction'>" 
                . $_oWPReadmeParser->getSection( 'Introduction' ) 
            . "</div>"
            . $sContent;
            
    }
    
    public function replyToDoTab() {
    
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            )
        );    
        echo $_oWPReadmeParser->getSection( 'New Features' );          
    
    }
    
}
