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
class AdminPageFrameworkLoader_AdminPageWelcome_ChangeLog {

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
                'title'         => __( 'Change Log', 'admin-page-framework-loader' ),
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
        add_action( "style_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToAddInlineCSS' ) );
        
    }
        
    public function replyToAddInlineCSS( $sCSSRules ) {
   
        return $sCSSRules
            . ".changelog h4 {
                /* margin: 0; */
            }
            ";
        
    }    
        
    /**
     * Do something specific to the tab.
     * 
     * @since       3.5.0
     */
    public function replyToDoTab() {
        echo "<div class='changelog'>" 
                . $this->_getChangeLog( 'Changelog' ) 
            . "</div>";
        
    }
    
    /**
     * Retrieves contents of a change log section of a readme file.
     * @since       3.5.0
     */
	private function _getChangeLog( $sSection ) {
        
        $_aReplacements   = array(
            '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
            '%WP_ADMIN_URL%'    => admin_url(),
        );
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/readme.txt',
            $_aReplacements
        );    
        $_sChangeLog = $_oWPReadmeParser->getSection( $sSection );  
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/changelog.md',
            $_aReplacements
        );    
        $_sChangeLog .= $_oWPReadmeParser->getSection( $sSection );  
           
        return $_sChangeLog
            ? $_sChangeLog
            : '<p>' . __( 'No valid changlog was found.', 'admin-page-framework-loader' ) . '</p>';
        
	}    
       
}