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
class AdminPageFrameworkLoader_Demo_BasicExample {
    
    public function __construct() {     
        
        if ( ! is_admin() ) { 
            return; 
        }
        
        new APF_BasicUsage(
            null,                       // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used           
            APFDEMO_FILE,               // the caller script path.
            'manage_options',           // the default capability
            'admin-page-framework-loader' // the text domain    
        );

        new APF_MetaBox_For_Pages_Normal(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-loader' ), // title
            'apf_first_page',                               // page slugs
            'normal',                                       // context
            'default'                                       // priority
        );
        new APF_MetaBox_For_Pages_Advanced(    
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-loader' ), // title
            'apf_first_page',                               // page slugs
            'advanced',                                     // context
            'default'                                       // priority
        );    
        new APF_MetaBox_For_Pages_Side(    
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-loader' ), // title
            array( 'apf_first_page', 'apf_second_page' ),   // page slugs - setting multiple slugs is possible
            'side',                                         // context
            'default'                                       // priority
        );            
        new APF_MetaBox_For_Pages_WithFormSection(
            null,
            __( 'Meta box with a Form Section', 'admin-page-framework-loader' ), // title        
            array( 'apf_first_page', 'apf_second_page' ),   
            'side',
            'low'
        );
        
    }

}