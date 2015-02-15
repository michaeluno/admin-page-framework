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
class AdminPageFrameworkLoader_AdminPageWelcome_Welcome extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

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
