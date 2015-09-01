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
class AdminPageFrameworkLoader_Demo_MetaBox {
    
    public function __construct() {     
            
        new APF_MetaBox_BuiltinFieldTypes(
            null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-loader' ), // title
            array( 'apf_posts' ),                            // post type slugs: post, page, etc.
            'normal',                                        // context (what kind of metabox this is)
            'high'                                           // priority
        );
        new APF_MetaBox_TabbedSections(
            null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Section Tabs', 'admin-page-framework-loader' ), // title
            array( 'apf_posts' ),                               // post type slugs: post, page, etc.
            'normal',                                           // context (what kind of metabox this is)
            'default'                                           // priority
        );    
        new APF_MetaBox_RepeatableTabbedSections(
            null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Repeatable Section Tabs', 'admin-page-framework-loader' ), // title
            array( 'apf_posts' ),                               // post type slugs: post, page, etc.
            'normal',                                           // context (what kind of metabox this is)
            'default'                                           // priority
        );
        new APF_MetaBox_CollapsibleSections(
            null,   // meta box id
            __( 'Collapsible Sections', 'admin-page-framework-loader' ),
            array( 'apf_posts' ),                             
            'normal',
            'low'
        );
        new APF_MetaBox_RepeatableCollapsibleSections(
            null,   // meta box id
            __( 'Repeatable Collapsible Sections', 'admin-page-framework-loader' ),
            array( 'apf_posts' ),                             
            'normal',
            'low'
        );
        
    }

}