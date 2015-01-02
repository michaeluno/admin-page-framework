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
 * @since   3.4.6
 */
class APF_Demo_Tool extends AdminPageFramework {

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
                'title'        => __( 'Tool', 'admin-page-framework-demo' ),
                'page_slug'    => 'apf_tool',
            )
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
        // load_ + {instantiated class name}
        add_action( 'load_' . get_class( $this ), array( $this, 'replyToLoadClass' ) );
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function replyToLoadClass( $oAdminPage ) { 

        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'apf_tool' ); // disable the page title of a specific page.
        
        // load_ + {page slug}
        add_action( 'load_' . 'apf_tool', array( $this, 'replyToLoadPage' ) );
        
    }
    
    /**
     * Do page specific settings.
     */
    public function replyToLoadPage( $oAdminPage ) {    // load_ + {page slug}
        
        include( dirname( __FILE__ ) . '/tool/APF_Demo_Tool_Tab_MinifiedVersion.php' ); 
        new APF_Demo_Tool_Tab_MinifiedVersion( $this, 'apf_tool', 'minifier' );

    }
    
}
