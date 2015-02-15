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
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase
 */
class AdminPageFrameworkLoader_AdminPage_Help_Tip extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {
    
    public function replyToDoTab() {
        
        echo $this->_getReadmeContents( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/readme.txt', 
            "<h3>" . __( 'Tips', 'admin-page-framework-loader' ) . "</h3>",    
            array( 'Other Notes' )
        ); 
        
    }
    
}