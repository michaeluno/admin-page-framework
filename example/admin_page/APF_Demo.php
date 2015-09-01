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
 * Creates a root menu page.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo extends AdminPageFramework {

    /**
     * Sets up pages.
     * 
     * ( Required ) In this `setUp()` method, you will define admin pages.
     */
    public function setUp() { 

        /* ( optional ) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        $this->setCapability( 'read' );
        
        /* ( required ) Set the root page */
        $this->setRootMenuPageBySlug( 'edit.php?post_type=' . AdminPageFrameworkLoader_Registry::$aPostTypes['demo'] );    
        
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            /*     Example
              for sub-menu pages, e.g.
                  'title' => 'Your Page Title',
                'page_slug' => 'your_page_slug', // avoid hyphen(dash), dots, and white spaces
                'screen_icon' => 'edit', // for WordPress v3.7.x or below
                'capability' => 'manage-options',
                'order' => 10,
                
              for sub-menu links, e.g.
                'title' => 'Google',
                'href' => 'http://www.google.com',
                
            */
            array(
                'title'         => __( 'Built-in Field Types', 'admin-page-framework-loader' ),
                'page_slug'     => 'apf_builtin_field_types',
                'screen_icon'   => 'options-general', // one of the screen type from the below can be used.
                /* Screen Types (for WordPress v3.7.x or below) :
                    'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
                    'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
                    'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',  
                */     
                'order' => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );
                            
        /*
         * ( optional ) Contextual help pane
         */
        $this->addHelpTab( 
            array(
                'page_slug' => 'apf_builtin_field_types', // ( mandatory )
                // 'page_tab_slug' => null, // ( optional )
                'help_tab_title' => 'Admin Page Framework',
                'help_tab_id' => 'admin_page_framework', // ( mandatory )
                'help_tab_content' => __( 'This contextual help text can be set with the <code>addHelpTab()</code> method.', 'admin-page-framework' ),
                'help_tab_sidebar_content' => __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
            )
        );
                
        /*
         * ( optional ) Add links in the plugin listing table. ( .../wp-admin/plugins.php )
         */
        /* 
        $this->addLinkToPluginDescription( 
            "<a href='http://en.michaeluno.jp/donate'>Donate</a>",
            "<a href='https://github.com/michaeluno/admin-page-framework' title='Contribute to the GitHub repository!' >Repository</a>"
        );
        $this->addLinkToPluginTitle(
            "<a href='http://en.michaeluno.jp'>miunosoft</a>"
        );
        */
       
        // Disable the action link in the plugin listing table.
        $this->setPluginSettingsLinkLabel( '' );    
        // $this->setPluginSettingsLinkLabel( __( 'Built-in Field Types', 'admin-page-framework-loader' ) );
        
        // Add pages       
        new APF_Demo_CustomFieldType(
            $this,
            'custom_field_type',
            __( 'Custom Field Type', 'admin-page-framework-loader' )
        );
        
        // Define in-page tabs - here tabs are defined in the below classes.
        $_aTabClasses = array(
            'APF_Demo_BuiltinFieldTypes_Text',
            'APF_Demo_BuiltinFieldTypes_Selector',
            'APF_Demo_BuiltinFieldTypes_File',
            'APF_Demo_BuiltinFieldTypes_Checklist',
            'APF_Demo_BuiltinFieldTypes_MISC',
            'APF_Demo_BuiltinFieldTypes_Verification',
            'APF_Demo_BuiltinFieldTypes_Mixed',
            'APF_Demo_BuiltinFieldTypes_Section',
            'APF_Demo_BuiltinFieldTypes_Callback',
            'APF_Demo_BuiltinFieldTypes_System',        
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( class_exists( $_sTabClassName ) ) {
                new $_sTabClassName;
            }
        }

    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     * 
     * @callback        action      load_{instantiated class name}
     */
    public function load_APF_Demo( $oAdminPage ) { 
    
        /* ( optional ) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
    
    }
          
    /*
     * Built-in Field Types Page
     * 
     * @callback        action      do_{page slug}
     * */
    public function do_apf_builtin_field_types() { 
    
        if ( isset( $_GET['tab'] ) && 'system' === $_GET['tab'] ) {
            return;
        }
        submit_button();
        
    }
        
    /**
     * Modifies the left footer text.
     * 
     * @callback        filter      footer_left_{class name}
     */
    public function footer_left_APF_DEMO( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'Custom text inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_left_{class name}'
                ) 
            . "</span><br />" 
            . $sHTML;
    }
    /**
     * Modifies the left footer text.
     * 
     * @callback        filter      footer_left_{class name}
     */
    public function footer_right_APF_DEMO( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'Inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_right_{class name}'
                ) 
            . "</span><br />" 
            . $sHTML;
    }    
    
    /**
     * Modifies the left footer text.
     * 
     * @callback        filter      footer_left_{class name}
     */
    public function footer_left_apf_builtin_field_types( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_left_{page slug}'
                ) 
            . "</span><br />" 
            . $sHTML;
    }
    /**
     * Modifies the right footer text.
     * 
     * @callback        filter      footer_right_{class name}
     */
    public function footer_right_apf_builtin_field_types( $sHTML ) {
        return "<span>" . sprintf(
                    __( 'Inserted with the <code>%1$s</code> filter.', 'admin-page-framework-loader' ),
                    'footer_right_{page slug}'
                ) 
            . "</span><br />" 
            . $sHTML;
    }
    
}