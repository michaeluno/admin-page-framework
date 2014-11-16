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

/**
 * This example demonstrates the use of the 'content_top_{...}' hooks.
 * 
 * Note that this class does not extend any class unlike the other admin page classes in the demo plugin examples.
 */
class APF_Demo_AddPluginTitle {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
        
        /**
         * We can use the 'content_top_{...}' filters to modify the top part of the page.
         * 
         * - content_top_{instantiated class name}
         * - content_top_{page slug}
         */
        add_filter( "content_top_APF_Demo_Readme", array( $this, 'replyToInsertPluginTitle' ) );
        add_filter( "content_top_apf_read_me", array( $this, 'replyToInsertDonationButton' ) );
             
        /**
         * Modify the CSS rules of the framework.
         * 
         * We can use the 'style_common_admin_page_framework' filter.
         */
        add_filter( "style_common_admin_page_framework", array( $this, 'replyToAddStyle' ) );
         
    }
    
    /**
     * Modifies the top part of the page output.
     */
    public function replyToInsertPluginTitle( $sContent )         {
        return "<div class='plugin_icon'>"
                . "<a href='" . esc_url( 'http://en.michaeluno.jp/donate' ) . "' target='_blank' >"
                    . "<img src='" . AdminPageFramework_WPUtility::resolveSRC( APFDEMO_DIRNAME . '/asset/image/icon-64x64.png' ) . "' alt='" . esc_attr( __( 'Please donate!', 'admin-page-framework' ) ). "' />"
                . "</a>"
                . "</div>"
            . "<div class='page_title'>"
                . "<h1>" 
                    . "Admin Page Framework - Demo"
                    . "</h1>"
            . "</div>"
            . $sContent
            ;
    }
    
    /**
     * Inserts
     */
    public function replyToInsertDonationButton( $sContent ) {
        
        return "<div class='donate'>"
            . "<a href='" . esc_url( 'http://en.michaeluno.jp/donate' ) . "' target='_blank' >"
                . "<img src='" . AdminPageFramework_WPUtility::resolveSRC( APFDEMO_DIRNAME . '/asset/image/donation.gif' ) . "' alt='" . esc_attr( __( 'Please donate!', 'admin-page-framework' ) ). "' />"
            . "</a>"
            . "</div>"
            . $sContent;
        
    }
     
    /**
     * Adds CSS rules.
     */
    public function replyToAddStyle( $sCSSRules ) {
        return $sCSSRules
            . "
.page_title {
    display: inline-block;
    margin-top: 1em;
    margin-bottom: 1em;
}
.page_title h1{
    margin: 0;
    display: inline-block;
    vertical-align: middle;
    font-size: 2.32em;
    color: #222;
    font-weight: 400;
}
.donate {
   float: right;
   clear: right;
   margin: 1em;
}
.donate img {
    width: 120px;
}     
.plugin_icon {
   float: left;
   clear: left;
   margin: 1em 1em 0 0;
   vertical-align: top;
   display: inline-block;
}       
";
    }
}