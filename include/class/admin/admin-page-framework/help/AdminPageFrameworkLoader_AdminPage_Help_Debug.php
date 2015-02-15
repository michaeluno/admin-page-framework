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
class AdminPageFrameworkLoader_AdminPage_Help_Debug extends AdminPageFrameworkLoader_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        $oAdminPage->addSettingFIeld(
            array(
                'field_id'  => 'reset',
                'type'      => 'submit',
                'reset'     => true,
                'show_title_column' => false,
                'value'     => __( 'Reset', 'admin-page-framework-loader' ),                
            )
        );
    }
    
    public function replyToDoTab() {
        
        echo "<h3>" . __( 'Saved Options', 'admin-page-framework-loader' ) . "</h3>";
        $this->oFactory->oDebug->dump( $this->oFactory->oProp->aOptions );
        
    }
    
}