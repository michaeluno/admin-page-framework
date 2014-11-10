<?php
// Include the basic usage example that creates a root page and its sub-pages.
include( dirname( __FILE__ ) . '/APF_BasicUsage.php' ); 
new APF_BasicUsage(
    null,                       // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used
    APFDEMO_FILE,               // the caller script path.
    'manage_options',           // the default capability
    'admin-page-framework-demo' // the text domain    
);

// Create meta boxes that belongs to the 'apf_first_page' page.
include( dirname( __FILE__ ) .'/admin_page_meta_box/load_admin_page_meta_boxes.php' );