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
 * @sicne       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase
 */
class AdminPageFrameworkLoader_AdminPageWelcome_Guide extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {
    
    /**
     * 
     * @return      void
     */
    public function replyToDoTab() {
        echo $this->_getReadmeContents( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt', 
            "<h3>" . __( 'Contents', 'admin-page-framework-loader' ) . "</h3>", 
            array( 'Getting Started', 'Tutorials' )
        );
    }
     
}