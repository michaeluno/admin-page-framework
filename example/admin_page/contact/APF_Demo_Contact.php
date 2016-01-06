<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since   3.2.2
 */
class APF_Demo_Contact {

    public $sClassName = 'APF_Demo';
    
    public $sPageSlug  = 'apf_contact';

    /**
     * Sets up hooks.
     */
    public function __construct() {
        
        add_action( 
            'set_up_' . $this->sClassName,
            array( $this, 'replyToSetUpPages' )
        );

    }

    /**
     * Sets up pages.
     * 
     * This method automatically gets triggered with the wp_loaded hook. 
     */
    public function replyToSetUpPages( $oFactory ) {

        $oFactory->addSubMenuItems(     
            array(
                'title'         => __( 'Contact', 'admin-page-framework-loader' ),
                'page_slug'     => $this->sPageSlug,
                'screen_icon'   => 'page',
                'capability'    => 'manage_options',
                'order'         => 30,
            )
        );

        add_action( 
            'load_' . $this->sClassName,
            array( $this, 'replyToLoadClass' )
        );
        
        add_action( 
            'load_' . $this->sPageSlug,
            array( $this, 'replyToLoadPage' )
        );
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     * 
     * @callback        action      load_{instantiated class name}
     */
    public function replyToLoadClass( $oFactory ) { 

        // disables the page heading tabs by passing false.
        $oFactory->setPageHeadingTabsVisibility( false ); 
        
        // sets the tag used for in-page tabs.
        $oFactory->setInPageTabTag( 'h2' ); 
        
        // disable the page title.
        $oFactory->setPageTitleVisibility( false ); 

    }
    
    /**
     * Do page specific settings.
     * 
     * @callback        action      load_ + {page slug}
     */
    public function replyToLoadPage( $oFactory ) {   
        
        new APF_Demo_Contact_Tab_Report( $oFactory, $this->sPageSlug );
        new APF_Demo_Contact_Tab_Feedback( $oFactory, $this->sPageSlug );

    }
    
}

new APF_Demo_Contact(
    '',                         // passing an empty string will disable the form data to be saved.
    AdminPageFrameworkLoader_Registry::$sFilePath,               // the caller script path.
    'read',                     // the default capability
    'admin-page-framework-loader' // the text domain        
);   