<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Custom Field Type page to the loader plugin.
 * 
 * @since       3.6.2
 * @package     AdminPageFramework
 * @subpackage  Example 
 */
class APF_Demo_AdvancedUsage {

    public function __construct( $oFactory ) {
    
        $oFactory->addSubMenuItems( 
            array(
                'title'         => __( 'Advanced Usage', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_advanced_usage',    // page slug
            )
        );        
        
        // Define in-page tabs - here tabs are defined in the below classes.
        $_aTabClasses = array(
            'APF_Demo_AdvancedUsage_Section',
            'APF_Demo_AdvancedUsage_Nesting',
            'APF_Demo_AdvancedUsage_Argument',
            'APF_Demo_AdvancedUsage_Verification',
            'APF_Demo_AdvancedUsage_Mixed',
            'APF_Demo_AdvancedUsage_Callback',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;                
            }        
            new $_sTabClassName;
        }
              
        add_action(
            'do_' . 'apf_advanced_usage',
            array( $this, 'replyToDoPage' )
        );
        
    }
     
    /*
     * Handles the page output.
     * 
     * @callback        action      do_{page slug}
     * */
    public function replyToDoPage() { 
        submit_button();
    }     
     
}