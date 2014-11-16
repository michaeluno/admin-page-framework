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

// Add pages and forms in the network admin area.
include( dirname( __FILE__ ) . '/APF_NetworkAdmin.php' );
new APF_NetworkAdmin(
    null,                       // passing the option key used by the main pages.
    APFDEMO_FILE,               // the caller script path.
    'manage_options',           // the default capability
    'admin-page-framework-demo' // the text domain        
);

include( dirname( __FILE__ ) . '/APF_NetworkAdmin_CustomFieldTypes.php' );
new APF_NetworkAdmin_CustomFieldTypes(
    'APF_NetworkAdmin',
    APFDEMO_FILE,               // the caller script path.
    'manage_options',           // the default capability
    'admin-page-framework-demo' // the text domain                    
);     

include( dirname( __FILE__ ) . '/APF_NetworkAdmin_ManageOptions.php' );
new APF_NetworkAdmin_ManageOptions( 
    'APF_NetworkAdmin', 
    APFDEMO_FILE,               // the caller script path.
    'manage_options',           // the default capability
    'admin-page-framework-demo' // the text domain                    
);

new APF_MetaBox_For_Pages_Side(    
    'apf_metabox_for_pages_side',       // meta box id
    __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
    array( 'apf_builtin_field_types' ), // page slugs - setting multiple slugs is possible
    'side',                             // context
    'default'                           // priority
);   