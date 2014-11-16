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

include( dirname( __FILE__ ) . '/APF_MetaBox_For_Pages_Normal.php' );
new APF_MetaBox_For_Pages_Normal(
    null,                                           // meta box id - passing null will make it auto generate
    __( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ), // title
    'apf_first_page',                               // page slugs
    'normal',                                       // context
    'default'                                       // priority
);
include( dirname( __FILE__ ) . '/APF_MetaBox_For_Pages_Advanced.php' );
new APF_MetaBox_For_Pages_Advanced(    
    null,                                           // meta box id - passing null will make it auto generate
    __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
    'apf_first_page',                               // page slugs
    'advanced',                                     // context
    'default'                                       // priority
);    
include( dirname( __FILE__ ) . '/APF_MetaBox_For_Pages_Side.php' );    
new APF_MetaBox_For_Pages_Side(    
    null,                                           // meta box id - passing null will make it auto generate
    __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
    array( 'apf_first_page', 'apf_second_page' ),   // page slugs - setting multiple slugs is possible
    'side',                                         // context
    'default'                                       // priority
);       