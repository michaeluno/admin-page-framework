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

class APF_Demo_ManageOptions extends AdminPageFramework {
    
    /**
     * Stores the page slug.
     */
    private $sPageSlug = 'apf_manage_options';

    /**
     * Sets up pages.
     * 
     * This method automatically gets triggered with the wp_loaded hook. 
     */
    public function setUp() {

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'edit.php?post_type=' . AdminPageFrameworkLoader_Registry::$aPostTypes['demo'] );    
        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title'         => __( 'Manage Options', 'admin-page-framework-loader' ),
                'page_slug'     => $this->sPageSlug,
                'screen_icon'   => 'link-manager',    
            )
        );     
        
        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
            
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load_APF_Demo_ManageOptions( $oAdminPage ) { // load_{instantiated class name}
    
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     

        /* 
         * ( optional ) Enqueue styles  
         * $this->enqueueStyle(  'stylesheet url/path' , 'page slug (optional)', 'tab slug (optional)', 'custom argument array(optional)' );
         * */
        $this->enqueueStyle(  dirname( APFDEMO_FILE ) . '/asset/css/code.css', 'apf_manage_options' ); // a path can be used
                
    }

    /**
     * The pre-defined callback method that is triggered when the page loads.
     */ 
    public function load_apf_manage_options( $oAdminPage ) { // load_{page slug}
        
        // Tabs
        new APF_Demo_ManageOptions_SavedData(
            $this,              // factory object
            $this->sPageSlug,   // page slug
            'saved_data'        // tab slug
        );
        new APF_Demo_ManageOptions_Property(
            $this,
            $this->sPageSlug,
            'properties'        
        );
        new APF_Demo_ManageOptions_Message(
            $this,
            $this->sPageSlug,
            'messages'
        );
        new APF_Demo_ManageOptions_Export(
            $this,
            $this->sPageSlug,
            'export'        
        );
        new APF_Demo_ManageOptions_Import(
            $this,
            $this->sPageSlug,
            'import'        
        );
        new APF_Demo_ManageOptions_Reset(
            $this,
            $this->sPageSlug,
            'reset'        
        );
        new APF_Demo_ManageOptions_ResetConfirm(
            $this,
            $this->sPageSlug,
            'reset_confirm'                
        );

        // Disabled tab example
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => 'disabled',
                'title'         => __( 'Disabled', 'admin-page-framework-loader' ),
                'disabled'      => true,
                'attributes'    => array(
                    'title'     => __( ' the disabled argument is true, this tab will be disabled and has no link.', 'admin-page-framework-loader' ),
                ),
            )        
        );          
        
    }

}