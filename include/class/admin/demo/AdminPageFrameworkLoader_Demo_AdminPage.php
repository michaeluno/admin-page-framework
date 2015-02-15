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
class AdminPageFrameworkLoader_Demo_AdminPage {
    
    public function __construct() {     
        
        if ( ! is_admin() ) { 
            return; 
        }
       
        // Example components
        new AdminPageFrameworkLoader_Demo_BasicExample;
        new AdminPageFrameworkLoader_Demo_NetworkAdminPage;
       
        // Admin Pages
        // Add pages and forms in the custom post type root page
        new APF_Demo( 
            null,                       // the option key - when null is passed the class name in this case 'APF_Demo' will be used
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain
        );
                    
        // Add the Manage Options page.
        new APF_Demo_ManageOptions( 
            'APF_Demo',                 // passing the option key used by the main pages.
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-demo' // the text domain        
        );
        
        // Add a hidden page. This class does not extend the framework factory class.
        new APF_Demo_HiddenPage;
        
        // Add the contact page
        new APF_Demo_Contact(
            '',                         // passing an empty string will disable the form data to be saved.
            APFDEMO_FILE,               // the caller script path.
            'read',                     // the default capability
            'admin-page-framework-demo' // the text domain        
        );        
         
    }

}