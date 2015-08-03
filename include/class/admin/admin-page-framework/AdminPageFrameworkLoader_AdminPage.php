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
            $_oOption = AdminPageFrameworkLoader_Option::getInstance( AdminPageFrameworkLoader_Registry::$aOptionKeys['main'] );
            $_oOption->update( 'enable_admin_pages', $_GET['enable_apfl_admin_pages'] );                        
            exit( wp_safe_redirect( remove_query_arg( 'enable_apfl_admin_pages' ) ) );
            
        }
        
        // Enable / disable the demo pages
        if ( isset( $_GET['enable_apfl_demo_pages'] ) ) {
            
            // Update the options and reload the page
            $_oOption = AdminPageFrameworkLoader_Option::getInstance( AdminPageFrameworkLoader_Registry::$aOptionKeys['main'] );
            $_oOption->update( 'enable_demo', $_GET['enable_apfl_demo_pages'] );            
            exit( wp_safe_redirect( remove_query_arg( 'enable_apfl_demo_pages' ) ) );
            
        }
             
    }

    /**
     * Sets up admin pages.
     * 
     * @since       3.5.0
     */
    public function setUp() {

        $_aOptions          = $this->oProp->aOptions;
        $_bAdminPageEnabled = ! is_array( $_aOptions )  // for the first time of loading, the option is not set and it is not an array. 
            || ( isset( $_aOptions['enable_admin_pages'] ) && $_aOptions['enable_admin_pages'] );    
    
        // Set up pages
        if ( $_bAdminPageEnabled ) {
            
            $this->setRootMenuPage( 
                AdminPageFrameworkLoader_Registry::SHORTNAME,     // menu slug
                AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/wp-logo_16x16.png',
                4  // menu position
            ); 
                        
            // Add pages
            new AdminPageFrameworkLoader_AdminPage_Tool( 
                $this,
                array(
                    'page_slug' => AdminPageFrameworkLoader_Registry::$aAdminPages['tool'],
                    'title'     => __( 'Tools', 'admin-page-framework-loader' ),
                )
            );
            new AdminPageFrameworkLoader_AdminPage_Addon(
                $this,
                array(
                    'page_slug' => AdminPageFrameworkLoader_Registry::$aAdminPages['addon'],    // page slug
                    'title'     => __( 'Add Ons', 'admin-page-framework-loader' ),
                )                
            );
            new AdminPageFrameworkLoader_AdminPage_Help( 
                $this,
                array(
                    'page_slug' => AdminPageFrameworkLoader_Registry::$aAdminPages['help'],
                    'title'     => __( 'Help', 'admin-page-framework-loader' ),
                    'order'     => 1000, // to be the last menu item
                )                
            );
            
        }
              
        $this->_doPageSettings( $_bAdminPageEnabled, $_aOptions );
        
    }
        /**
         * Does page settings.
         */
        private function _doPageSettings( $_bAdminPageEnabled, $_aOptions ) {
                
            // Page Settings
            $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
            $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
               
            // Styles
            $this->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css' );
            $this->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/admin.css' );
           
            // Action Links (plugin.php)
            $this->addLinkToPluginTitle(
                $this->_getAdminURLTools( $_bAdminPageEnabled ),
                $this->_getAdminPageSwitchLink( $_bAdminPageEnabled ),
                $this->_getDemoSwitcherLink( $_bAdminPageEnabled, $_aOptions )
            );
            $this->addLinkToPluginDescription(
                "<a href='https://wordpress.org/support/plugin/admin-page-framework' target='_blank'>" . __( 'Support', 'admin-page-framework-loader' ) . "</a>"
            );            
            
        }
        /**
         * Returns the Tools admin page link.
         */
        private function _getAdminURLTools( $_bAdminPageEnabled ) {
            if ( ! $_bAdminPageEnabled ) {
                return;
            }
            $_sLink    = esc_url(
                add_query_arg( 
                    array( 
                        'page' => AdminPageFrameworkLoader_Registry::$aAdminPages['tool'],
                    ),
                    admin_url( 'admin.php' )
                )
            );                
            return "<a href='{$_sLink}'>" . __( 'Tools', 'admin-page-framework-loader' ) . "</a>";             
        }
        /**
         * Returns the Enable /Disable Admin Pages link.
         */
        private function _getAdminPageSwitchLink( $bEnabled ) {
            $_sLink    = esc_url( 
                add_query_arg( 
                    array( 
                        'enable_apfl_admin_pages' => $bEnabled ? 0 : 1,
                    )
                )
            );            
            return $bEnabled
                ? "<a href='{$_sLink}'>" . __( 'Disable Admin Pages', 'admin-page-framework-loader' ) . "</a>"
                : "<a href='{$_sLink}'>" . __( 'Enable Admin Pages', 'admin-page-framework-loader' ) . "</a>";                     
        }
        /**
         * Returns the switch link of the demo pages.
         */
        private function _getDemoSwitcherLink( $_bAdminPageEnabled, $mOptions=array() ) {
            
            if ( ! $_bAdminPageEnabled ) {
                return '';
            }
            $_bEnabled  = isset( $mOptions['enable_demo'] ) && $mOptions['enable_demo'];
            $_sLink    = esc_url( 
                add_query_arg( 
                    array( 
                        'enable_apfl_demo_pages' => $_bEnabled ? 0 : 1,
                    )
                )
            );        
            return $_bEnabled
                ? "<a href='{$_sLink}'>" . __( 'Disable Demo', 'admin-page-framework-loader' ) . "</a>"
                : "<a href='{$_sLink}'><strong style='font-size: 1em;'>" . __( 'Enable Demo', 'admin-page-framework-loader' ) . "</strong></a>";
            
        }            

}