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
class AdminPageFrameworkLoader_AdminPageWelcome_Guide extends AdminPageFrameworkLoader_AdminPage_Tab_Base {
    
    public function replyToDoTab() {
            
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            )
        );    
        $_sContent  = $_oWPReadmeParser->getSection( 'Getting Started' );  
        $_sContent .= $_oWPReadmeParser->getSection( 'Tutorials' );
        
        $_oTOC = new AdminPageFramework_TableOfContents(
            $_sContent,
            4,
            "<h3>" . __( 'Contents', 'admin-page-framework-loader' ) . "</h3>"
        );
        echo $_oTOC->get();
        
    }
    
}