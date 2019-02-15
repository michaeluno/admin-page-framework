<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 * @sicne       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase
 */
class AdminPageFrameworkLoader_AdminPage_Help_Information extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {
    
    public function replyToDoTab( /* $oFactory */ ) {
    
        echo $this->_getReadmeContents( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt', 
            '',     // no TOC
            array( 'Support' )
        );    
 
    }
    
}
