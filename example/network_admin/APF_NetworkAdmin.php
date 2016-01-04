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

class APF_NetworkAdmin extends AdminPageFramework_NetworkAdmin {
        
    /**
     * (required) In the setUp() method, you will define how the pages and the form elements should be composed.
     * 
     * @ramark      this method automatically gets triggered with the wp_loaded hook. 
     */
    public function setUp() { 

        /* (optional) this can be set via the constructor. For available values, see https://codex.wordpress.org/Roles_and_Capabilities */
        // $this->setCapability( 'read' );
        
        /* (required) Set the root page */
        $this->setRootMenuPage( 'Admin Page Framework' ); // or $this->setRootMenuPageBySlug( 'sites.php' );    
                    
        // Pages
        new APF_Demo_BuiltinFieldType( $this );
        new APF_Demo_AdvancedUsage( $this );
                    
        /* (optional) Determine the page style */
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
                                 
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
        
    /*
     * The sample page and the hidden page
     * 
     * @callback        action      do_{page slug}
     */
    public function do_apf_sample_page() { 
        
        echo "<p>" . __( 'This is a sample page that has a link to a hidden page created by the framework.', 'admin-page-framework-loader' ) . "</p>";
        $sLinkToHiddenPage = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_hidden_page' ) );
        echo "<a href='{$sLinkToHiddenPage}'>" . __( 'Go to Hidden Page', 'admin-page-framework-loader' ). "</a>";
    
    }
    
    /**
     * @callback        action      do_{page slug}
     */
    public function do_apf_hidden_page() {  
        
        echo "<p>" . __( 'This is a hidden page.', 'admin-page-framework-loader' ) . "</p>";
        echo "<p>" . __( 'It is useful when you have a setting page that requires a proceeding page.', 'admin-page-framework-loader' ) . "</p>";
        $sLinkToGoBack = $this->oUtil->getQueryAdminURL( array( 'page' => 'apf_sample_page' ) );
        echo "<a href='{$sLinkToGoBack}'>" . __( 'Go Back', 'admin-page-framework-loader' ). "</a>";
        
    }
    
    /**
     * 
     * @callback        filter      validation_{instantiated class name}
     */
    public function validation_APF_NetworkAdmin( $aInput, $aOldOptions ) { 
        
        /* If the delete options button is pressed, return an empty array that will delete the entire options stored in the database. */
        if ( isset( $_POST[ $this->oProp->sOptionKey ]['submit_buttons_confirm']['submit_delete_options_confirmation'] ) ) { 
            return array();
        }
        return $aInput;
        
    }
                
}

new APF_NetworkAdmin(
    null,                         // passing the option key used by the main pages.
    AdminPageFrameworkLoader_Registry::$sFilePath,  // the caller script path.
    'manage_options',             // the default capability
    'admin-page-framework-loader' // the text domain        
); 