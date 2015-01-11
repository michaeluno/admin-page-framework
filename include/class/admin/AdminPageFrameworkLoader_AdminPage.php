<?php
/**
 * One of the abstract class of the plugin admin page class.
 * 
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2014, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 */

class AdminPageFrameworkLoader_AdminPage extends AdminPageFramework {
    
    /**
     * User constructor.
     * 
     * @since       3.5.0
     */
    public function start() {
            
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        // Allows the user to switch the menu visibility.
        if ( isset( $_GET['enable_apfl_admin_pages'] ) ) {
            
            // Update the options and reload the page
            $_aOptions  = get_option( AdminPageFrameworkLoader_Registry::OptionKey, array() );
            $_aOptions['enable_admin_pages'] = $_GET['enable_apfl_admin_pages'] 
                ? true 
                : false;
            update_option( AdminPageFrameworkLoader_Registry::OptionKey, $_aOptions );
            exit( wp_redirect( remove_query_arg( 'enable_apfl_admin_pages' ) ) );
            
        }
        
    }
    
    /**
     * Sets up admin pages.
     * 
     * @since       3.5.0
     */
    public function setUp() {

        $_aOptions  = get_option( AdminPageFrameworkLoader_Registry::OptionKey );
        $_bEnabled  = ! is_array( $_aOptions )  // for the first time of loading, the option is not set and it is not an array. 
            || ( isset( $_aOptions['enable_admin_pages'] ) && $_aOptions['enable_admin_pages'] );    
    
        // Set the root page 
        if ( $_bEnabled ) {
            
            $this->setRootMenuPage( 
                'Admin Page Framework',     // menu slug
                AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/wp-logo_16x16.png',
                5  // menu position
            ); 
                        
            // Add pages
            new AdminPageFrameworkLoader_AdminPage_About(
                $this,
                AdminPageFrameworkLoader_Registry::$aAdminPages['about'],    // page slug
                __( 'About', 'admin-page-framework-loader' )                
            );
            new AdminPageFrameworkLoader_AdminPage_Tool( 
                $this,
                AdminPageFrameworkLoader_Registry::$aAdminPages['tool'],    // page slug
                __( 'Tools', 'admin-page-framework-loader' )
            );
            new AdminPageFrameworkLoader_AdminPage_Help( 
                $this ,
                AdminPageFrameworkLoader_Registry::$aAdminPages['help'],    // page slug
                __( 'Help', 'admin-page-framework-loader' )
            );
            
        }
        
        $this->oProp->sWrapperClassAttribute = "wrap about-wrap";
        
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setPluginSettingsLinkLabel( __( 'Tools', 'admin-page-framework-loader' ) ); // pass an empty string.
       
        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $_sPluginBaseName = plugin_basename( AdminPageFrameworkLoader_Registry::$sFilePath );
            add_filter( "plugin_action_links_{$_sPluginBaseName}", array( $this, 'replyToAddAdminPageSwitcher' ) );
        }
        
    }
    
    /**
     * Adds a admin page switcher link in the plugin listing table.
     * @since       3.5.0
     */
    public function replyToAddAdminPageSwitcher( $aLinks ) {
    
        $_aOptions  = get_option( AdminPageFrameworkLoader_Registry::OptionKey );
        $_bEnabled  = ! is_array( $_aOptions )  // for the first time of loading, the option is not set and it is not an array. 
            || ( isset( $_aOptions['enable_admin_pages'] ) && $_aOptions['enable_admin_pages'] );
        $_sLinks    = esc_url( add_query_arg( 
            array( 
                'enable_apfl_admin_pages' => $_bEnabled ? 0 : 1,
            )
        ) );
        $_sLink = $_bEnabled
            ? "<a href='{$_sLinks}'>" . __( 'Disable Admin Pages', 'admin-page-framework-loader' ) . "</a>"
            : "<a href='{$_sLinks}'>" . __( 'Enable Admin Pages', 'admin-page-framework-loader' ) . "</a>";
            
        $aLinks[] = $_sLink;
        return $aLinks;
    
    }
    
}