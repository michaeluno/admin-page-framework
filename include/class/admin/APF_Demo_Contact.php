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
 * Adds the Contact page to the demo plugin.
 * 
 * @since   3.2.2
 */
class APF_Demo_Contact extends AdminPageFramework {

    /**
     * Sets up pages.
     * 
     * This method automatically gets triggered with the wp_loaded hook. 
     */
    public function setUp() {

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );    
        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(     
            array(
                'title'        => __( 'Contact', 'admin-page-framework-demo' ),
                'page_slug'    => 'apf_contact',
                'screen_icon'  => 'page',
            )
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_Contact( $oAdminPage ) { // load_{instantiated class name}

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'apf_contact' ); // disable the page title of a specific page.

    }
    
    /**
     * Do page specific settings.
     */
    public function load_apf_contact() {    // load_ + {page slug}
        
        include( dirname( __FILE__ ) . '/contact/APF_Demo_Contact_Tab_Feedback.php' ); 
        new APF_Demo_Contact_Tab_Feedback( $this, 'apf_contact', 'feedback' );
        include( dirname( __FILE__ ) . '/contact/APF_Demo_Contact_Tab_Report.php' ); 
        new APF_Demo_Contact_Tab_Report( $this, 'apf_contact', 'report' );

    }
    
}
