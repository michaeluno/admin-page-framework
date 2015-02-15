<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the demo components.
 * 
 * @since        3.5.3
 */
class AdminPageFrameworkLoader_Demo_NetworkAdminPage {
    
    public function __construct() {     

        if ( ! is_network_admin() ) {
            return;
        }
        new APF_NetworkAdmin(
            null,                       // passing the option key used by the main pages.
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain        
        ); 
        new APF_NetworkAdmin_ManageOptions( 
            'APF_NetworkAdmin', 
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain                    
        );    
    
    }

}