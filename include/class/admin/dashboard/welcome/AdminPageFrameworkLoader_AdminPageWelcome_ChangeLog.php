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
 * @sicne       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_Base
 */
class AdminPageFrameworkLoader_AdminPageWelcome_ChangeLog extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     * @return      void
     * @since       3.5.0
     */
    public function replyToLoadTab( $oAdminPage ) {        
        add_action( "style_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToAddInlineCSS' ) );
    }     
        /**
         * 
         * @since       3.5.0
         * @return      void
         */
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
     * @return      void
     */
    public function replyToDoTab() {
        echo "<div class='changelog'>" 
                . $this->_getChangeLog( 'Changelog' ) 
            . "</div>";
            
    }
        /**
         * Retrieves contents of a change log section of a readme file.
         * @since       3.5.0
         * @return      void
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