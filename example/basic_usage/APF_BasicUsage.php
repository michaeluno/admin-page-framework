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
            version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 'dashicons-format-audio' : null // dash-icons are supported since WordPress v3.8
        );
        
        $this->addSubMenuItems(
            array(
                'title' => __( 'First Page', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_first_page',
            ),
            array(
                'title' => __( 'Second Page', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_second_page',
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
     */
    public function do_apf_first_page() { // do_ + {page slug}
        ?>
            <h3><?php _e( 'do_ + {...} Action Hooks', 'admin-page-framework-demo' ); ?></h3>
            <p><?php _e( 'Hi there! This text message is inserted by the <code>do_{page slug}</code> action hook and the callback method.', 'admin-page-framework-demo' ); ?></p>
        <?php

    }
    
    /**
     * Filter the page contents.
     */
    public function content_apf_second_page( $sContent ) { // content_ + {page slug}

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