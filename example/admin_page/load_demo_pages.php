<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

// Add pages and forms in the custom post type root page
include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo.php' );
new APF_Demo( 
    null,                       // the option key - when null is passed the class name in this case 'APF_Demo' will be used
    APFDEMO_FILE,               // the caller script path.
    'manage_options',           // the default capability
    'admin-page-framework-demo' // the text domain
);

    // Include the demo class that creates various forms.
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_CustomFieldTypes.php' ); 
    new APF_Demo_CustomFieldTypes( 
        'APF_Demo',                 // passing the option key used by the main pages.
        APFDEMO_FILE,               // the caller script path.
        'manage_options',           // the default capability
        'admin-page-framework-demo' // the text domain            
    ); 
        
    // Add the Manage Options page.
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_ManageOptions.php' );
    new APF_Demo_ManageOptions( 
        'APF_Demo',                 // passing the option key used by the main pages.
        APFDEMO_FILE,               // the caller script path.
        'manage_options',           // the default capability
        'admin-page-framework-demo' // the text domain        
    );
    
    // Add a hidden page. This class does not extend the framework factory class.
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_HiddenPage.php' );
    new APF_Demo_HiddenPage;
    
    // Add the readme and the documentation sub-menu items to the above main demo plugin root page.
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_Readme.php' );
    new APF_Demo_Readme(
        '',                         // passing an empty string will disable the form data to be saved.
        APFDEMO_FILE,               // the caller script path.
        'read',                     // the default capability
        'admin-page-framework-demo' // the text domain        
    );

    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_Tool.php' );
    new APF_Demo_Tool(
        '',
        APFDEMO_FILE,
        'read',
        'admin-page-framework-demo'
    );
    
    // Add the contact page
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_Contact.php' );
    new APF_Demo_Contact(
        '',                         // passing an empty string will disable the form data to be saved.
        APFDEMO_FILE,               // the caller script path.
        'read',                     // the default capability
        'admin-page-framework-demo' // the text domain        
    );
    
    // Modify the top part of the pages from a separate script
    include( APFDEMO_DIRNAME . '/example/admin_page/APF_Demo_AddPluginTitle.php' );
    new APF_Demo_AddPluginTitle;