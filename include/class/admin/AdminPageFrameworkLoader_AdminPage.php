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
                version_compare( $GLOBALS['wp_version'], '3.8', '>=' )   // menu icon
                    ? 'dashicons-wordpress' 
                    : AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/wp-logo_16x16.png'
            ); 
        
            // Add pages
            $this->addSubMenuItems( 
                array(
                    'title'         => __( 'Tools', 'admin-page-framework-loader' ),
                    'page_slug'     => AdminPageFrameworkLoader_Registry::AdminPage_Tool,    // page slug
                ),       
                array()
                
            );
            
        }
        
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setPluginSettingsLinkLabel( __( 'Tools', 'admin-page-framework-loader' ) ); // pass an empty string.
                 
        // Define the tool page.
        new AdminPageFrameworkLoader_Tool_Minifier( 
            $this,
            AdminPageFrameworkLoader_Registry::AdminPage_Tool,
            'minifier'
        );
                
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