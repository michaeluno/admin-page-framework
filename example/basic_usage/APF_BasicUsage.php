<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

/**
 * Demonstrates the usage of Admin Page Framework.
 */
class APF_BasicUsage extends AdminPageFramework {
    
    /**
     * 
     */
    public function start() {}
    
    /**
     * Sets up pages.
     */
    public function setUp() {
        
        $this->setRootMenuPage( 
            "<span id='apf-demo-2-menu-label'>" 
                . __( 'APF Demo 2', 'admin-page-framework-loader' ) 
            . "</span>"
            ,
            version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ) 
                ? 'dashicons-format-audio' 
                : null // dash-icons are supported since WordPress v3.8
        );
                
        $this->addSubMenuItems(
            array(
                'title'         => __( 'First Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_first_page',
            ),
            array(
                'title'         => __( 'Second Page', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_second_page',
            ),
            array(
                'title'         => __( 'Disabled', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_disabled',
                'disabled'      => true,
                'attributes'    => array(
                    'title'     => __( 'When the disabled argument is true, this tab will be disabled and has no link.', 'admin-page-framework-loader' ),
                ),
                'show_in_menu'  => false,
            )
        );
                
        // Disable it
        $this->setPluginSettingsLinkLabel( '' ); 
        
    }    
    
    /**
     * @callback        action      load_{instantiated class name}
     */
    public function load_APF_BasicUsage() {

        $this->setPageHeadingTabsVisibility( true ); // disables the page heading tabs by passing false.
 
    }
    
    /**
     * Do page specific settings.
     * 
     * @callback        action      load_{page slug}
     */
    public function load_apf_first_page() { 
        
        new AdminPageFramework_PointerToolTip(
            array( 
                'apf_first_page',  // page slugs
            ),     
            'apf_demo_page_meta_boxes', // unique id for the pointer tool box
            array(        // pointer data
                'target'    => '#apf_metabox_for_pages_normal',
                'options'   => array(
                    'content' => sprintf( '<h3> %1$s </h3> <p> %2$s </p>',
                        __( 'Page Meta Boxes' ,'admin-page-framework-loader' ),
                        __( 'Demonstrates the use of meta boxes for admin pages.','admin-page-framework-loader')
                        . ' ' . __( 'Usually meta boxes are displayed in post editing pages but with Admin Page Framework, you can display them in generic admin pages you create with the framework.','admin-page-framework-loader')
                    ),
                    'position'  => array( 'edge' => 'top', 'align' => 'middle' )
                )
            )
        );      
    
    }    
    
    /**
     * Do page specific settings.
     * 
     * @callback        action      load_{page slug}
     */
    public function load_apf_second_page() { 

        $this->enqueueStyle( 
            AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/css/code.css', AdminPageFrameworkLoader_Registry::$sFilePath ), // source path/url
            'apf_second_page'   // page slug
        );              
        
    }
    
    /**
     * Do render the page contents.
     * 
     * @callback        action      do_ + {page slug}
     */
    public function do_apf_first_page() { 
        ?>
            <h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-loader' ); ?></h3>
            <p><?php _e( 'Hi there! This text message is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-loader' ); ?></p>
        <?php

    }
    
    /**
     * Filter the page contents.
     * 
     * @callback        filter      content_ + {page slug}
     */
    public function content_apf_second_page( $sContent ) { 

        return $sContent 
            . "<h3>" . __( 'content_ + {...} Filter Hooks', 'admin-page-framework-loader' ) . "</h3>"
            . "<p>" 
                . __( 'This message is inserted with the <code>content_{page slug}</code> filter.', 'admin-page-framework-loader' ) 
            . "</p>"
            . "<h3>" . __( 'Saved Options', 'admin-page-framework-loader' ) . "</h3>"
            . $this->oDebug->get( $this->getValue() );
            
    }
    
}

new APF_BasicUsage(
    null,                           // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used           
    AdminPageFrameworkLoader_Registry::$sFilePath, // the caller script path.
    'manage_options',               // the default capability
    'admin-page-framework-loader'   // the text domain    
);