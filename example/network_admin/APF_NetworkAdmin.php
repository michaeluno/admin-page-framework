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

class APF_NetworkAdmin extends AdminPageFramework_NetworkAdmin {
        
    /*
     * (Required) In the setUp() method, you will define how the pages and the form elements should be composed.
     */
    public function setUp() { // this method automatically gets triggered with the wp_loaded hook. 

        /* (optional) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        // $this->setCapability( 'read' );
        
        /* (required) Set the root page */
        $this->setRootMenuPage( 'Admin Page Framework' ); // or $this->setRootMenuPageBySlug( 'sites.php' );    
                    
        /* (optional) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
          
        /* (required) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(    
            array(
                'title' => __( 'Built-in Field Types', 'admin-page-framework-demo' ),
                'page_slug' => 'apf_builtin_field_types',
                'screen_icon' => 'options-general', // one of the screen type from the below can be used.
                /* Screen Types (for WordPress v3.7.x or below) :
                    'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
                    'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
                    'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',  
                */     
                'order' => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );
          
        /*
         * (optional) Contextual help pane
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
         * (optional) Add links in the plugin listing table. ( .../wp-admin/plugins.php )
         */
         $this->addLinkToPluginDescription( 
            "<a href='http://en.michaeluno.jp/donate'>Donate</a>",
            "<a href='https://github.com/michaeluno/admin-page-framework' title='Contribute to the GitHub repository!' >Repository</a>"
        );
        $this->addLinkToPluginTitle(
            "<a href='http://en.michaeluno.jp'>miunosoft</a>"
        );

        // Include custom field type pages (in-page tabs).
        $_sClassName = get_class( $this );
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Text.php' );
        new APF_Demo_BuiltinFieldTypes_Text;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Selector.php' );
        new APF_Demo_BuiltinFieldTypes_Selector;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_File.php' );
        new APF_Demo_BuiltinFieldTypes_File( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Checklist.php' );
        new APF_Demo_BuiltinFieldTypes_Checklist;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_MISC.php' );
        new APF_Demo_BuiltinFieldTypes_MISC;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Verification.php' );
        new APF_Demo_BuiltinFieldTypes_Verification( $_sClassName );
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Mixed.php' );
        new APF_Demo_BuiltinFieldTypes_Mixed;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_Sections.php' );
        new APF_Demo_BuiltinFieldTypes_Sections;
        include( APFDEMO_DIRNAME . '/example/admin_page/builtin_field_type/APF_Demo_BuiltinFieldTypes_System.php' );
        new APF_Demo_BuiltinFieldTypes_System;
 
    }
            
    /*
     * Built-in Field Types Page
     * */
    public function do_apf_builtin_field_types() { // do_{page slug}

        if ( isset( $_GET['tab'] ) && 'system' === $_GET['tab'] ) {
            return;
        }    
    
        submit_button();
        
    }
        
    /*
     * The sample page and the hidden page
     */
    public function do_apf_sample_page() {  // do_ + page slug
        
        echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-demo' ) . "</p>";
        $sLinkToHiddenPage = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) );
        echo "<a href='{$sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-demo' ). "</a>";
    
    }
    public function do_apf_hidden_page() {  // do_ + page slug
        
        echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-demo' ) . "</p>";
        echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-demo' ) . "</p>";
        $sLinkToGoBack = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) );
        echo "<a href='{$sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-demo' ). "</a>";
        
    }
    
    
    public function validation_APF_NetworkAdmin( $aInput, $aOldOptions ) { // validation_{instantiated class name}
        
        /* If the delete options button is pressed, return an empty array that will delete the entire options stored in the database. */
        if ( isset( $_POST[ $this->oProp->sOptionKey ]['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) { 
            return array();
        }
        return $aInput;
        
    }
                
}