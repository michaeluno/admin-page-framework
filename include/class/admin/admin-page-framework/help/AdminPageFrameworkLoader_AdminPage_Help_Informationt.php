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
class AdminPageFrameworkLoader_AdminPage_Help_Information extends AdminPageFrameworkLoader_AdminPage_Tab_Base {
    
    public function replyToDoTab( $oFactory ) {
    
        $_aReplacements   = array(
            '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
            '%WP_ADMIN_URL%'    => admin_url(),
        );
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            $_aReplacements
        );    
        echo $_oWPReadmeParser->get( 'Support' );  
 
    }
    
}
