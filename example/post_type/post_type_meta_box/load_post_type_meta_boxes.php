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

include( dirname( __FILE__ ) . '/APF_MetaBox_BuiltinFieldTypes.php' );    
new APF_MetaBox_BuiltinFieldTypes(
    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
    __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ), // title
    array( 'apf_posts' ),                            // post type slugs: post, page, etc.
    'normal',                                        // context (what kind of metabox this is)
    'high'                                           // priority
);

include( dirname( __FILE__ ) . '/APF_MetaBox_TabbedSections.php' );    
new APF_MetaBox_TabbedSections(
    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
    __( 'Section Tabs', 'admin-page-framework-demo' ), // title
    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
    'normal',                                           // context (what kind of metabox this is)
    'default'                                           // priority
);    

include( dirname( __FILE__ ) . '/APF_MetaBox_RepeatableTabbedSections.php' );    
new APF_MetaBox_RepeatableTabbedSections(
    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
    __( 'Repeatable Section Tabs', 'admin-page-framework-demo' ), // title
    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
    'normal',                                           // context (what kind of metabox this is)
    'default'                                           // priority
);
    

include( dirname( __FILE__ ) . '/APF_MetaBox_CustomFieldTypes.php' );    
new APF_MetaBox_CustomFieldTypes(
    null,   // meta box ID - can be null.
    __( 'Demo Meta Box with Custom Field Types', 'admin-page-framework-demo' ), // title
    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
    'normal',                                           // context
    'low'                                           // priority
); 

include( dirname( __FILE__ ) . '/APF_MetaBox_DateFields.php' );
new APF_MetaBox_DateFields(
    null,       // meta box id
    __( 'Custom Date Fields', 'admin-page-framework-demo' ),
    array( 'apf_posts' ),                             
    'side'      // context                                      
);
 
include( dirname( __FILE__ ) . '/APF_MetaBox_CollapsibleSections.php' );
new APF_MetaBox_CollapsibleSections(
    null,   // meta box id
    __( 'Collapsible Sections', 'admin-page-framework-demo' ),
    array( 'apf_posts' ),                             
    'normal',
    'low'
);

include( dirname( __FILE__ ) . '/APF_MetaBox_RepeatableCollapsibleSections.php' );
new APF_MetaBox_RepeatableCollapsibleSections(
    null,   // meta box id
    __( 'Repeatable Collapsible Sections', 'admin-page-framework-demo' ),
    array( 'apf_posts' ),                             
    'normal',
    'low'
);

