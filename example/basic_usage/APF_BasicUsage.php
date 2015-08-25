<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Demonstrates the usage of Admin Page Framework.
 */
class APF_BasicUsage extends AdminPageFramework {
    
    /**
     * Sets up pages.
     */
    public function setUp() {
        
        $this->setRootMenuPage( 
            'Demo',
            version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                ? 'dashicons-format-audio' 
                : null // dash-icons are supported since WordPress v3.8
        );
        
        $this->addSubMenuItems(
            array(
                'title'         => __( 'First Page', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_first_page',
            ),
            array(
                'title'         => __( 'Second Page', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_second_page',
            ),
            array(
                'title'         => __( 'Disabled', 'admin-page-framework-demo' ),
                'page_slug'     => 'apf_disabled',
                'disabled'      => true,
                'attributes'    => array(
                    'title'     => __( 'When the disabled argument is true, this tab will be disabled and has no link.', 'admin-page-framework-loader' ),
                ),
                'show_in_menu'  => false,
            )
        );
        
        $this->setPageHeadingTabsVisibility( true ); // disables the page heading tabs by passing false.
        
        // Disable it
        $this->setPluginSettingsLinkLabel( '' ); 
        
    }    

    /**
     * Do page specific settings.
     */
    public function load_apf_second_page() { // load_{page slug}

        $this->enqueueStyle( 
            plugins_url( 'asset/css/code.css', APFDEMO_FILE ), // a url can be used as well
            'apf_second_page'
        );     

    }
    
    /**
     * Do render the page contents.
     * 
     * @callback        action      do_ + {page slug}
     */
    public function do_apf_first_page() { 
        ?>
            <h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-demo' ); ?></h3>
            <p><?php _e( 'Hi there! This text message is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-demo' ); ?></p>
        <?php

    }
    
    /**
     * Filter the page contents.
     * 
     * @callback        filter      content_ + {page slug}
     */
    public function content_apf_second_page( $sContent ) { 

        return $sContent 
            . "<h3>" . __( 'content_ + {...} Filter Hooks', 'admin-page-framework-demo' ) . "</h3>"
            . "<p>" 
                . __( 'This message is inserted with the <code>content_{page slug}</code> filter.', 'admin-page-framework-demo' ) 
            . "</p>"
            . "<h3>" . __( 'Saved Options', 'admin-page-framework-demo' ) . "</h3>"
            . $this->oDebug->get( 
                $this->getValue()
            )                     
            ;
            
    }
    
}