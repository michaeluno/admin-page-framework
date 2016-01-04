<?php
/**
 * Loads Admin Page Framework loader plugin components.
 *    
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 * 
 */

if ( ! class_exists( 'AdminPageFramework_PluginBootstrap' ) ) {
    return;
}
 
/**
 * Loads the plugin.
 * 
 * @action      do      admin_page_framework_loader_action_after_loading_plugin
 * @since       3.5.0
 */
final class AdminPageFrameworkLoader_Bootstrap extends AdminPageFramework_PluginBootstrap {
    
    /**
     * Register classes to be auto-loaded.
     * 
     * @since       3.5.0
     */
    public function getClasses() {
        
        // Include the include lists. The including file reassigns the list(array) to the $_aClassFiles variable.
        $_aClassFiles   = array();
        $_bLoaded       = include( dirname( $this->sFilePath ) . '/include/admin-page-framework-loader-include-class-file-list.php' );
        return $_aClassFiles;
                
    }

    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginActivation() {

        $this->_checkRequirements();
        
    }
        /**
         * 
         * @since            3.5.0
         */
        private function _checkRequirements() {

            $_oRequirementCheck = new AdminPageFramework_Requirement(
                AdminPageFrameworkLoader_Registry::$aRequirements,
                AdminPageFrameworkLoader_Registry::NAME
            );
            
            if ( $_oRequirementCheck->check() ) {            
                $_oRequirementCheck->deactivatePlugin( 
                    $this->sFilePath, 
                    __( 'Deactivating the plugin', 'admin-page-framework-loader' ),  // additional message
                    true    // is in the activation hook. This will exit the script.
                );
            }        
             
        }    
            
    /**
     * Load localization files.
     * 
     * @callback    action      init
     */
    public function setLocalization() {
        
        // This plugin does not have messages to be displayed in the front end.
        if ( ! $this->bIsAdmin ) { 
            return; 
        }
       
        $_sPluginBaseNameDirName = dirname( plugin_basename( $this->sFilePath ) );
        load_plugin_textdomain( 
            AdminPageFrameworkLoader_Registry::TEXT_DOMAIN, 
            false, 
            $_sPluginBaseNameDirName . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
        );
            
        load_plugin_textdomain( 
            'admin-page-framework', 
            false, 
            $_sPluginBaseNameDirName . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
        );        
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function setUp() {

        // Admin pages
        if ( $this->_shouldShowAdminPages() ) {

            // Dashboard
            new AdminPageFrameworkLoader_AdminPageWelcome( 
                '', // disable saving form data.
                $this->sFilePath   // caller script path
            );

            // Loader plugin admin pages.
            new AdminPageFrameworkLoader_AdminPage( 
                AdminPageFrameworkLoader_Registry::$aOptionKeys[ 'main' ],    // the option key
                $this->sFilePath   // caller script path
            );

            new AdminPageFrameworkLoader_AdminPageMetaBox_Notification(
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Notification', 'admin-page-framework-loader' ), // title
                array( // page slugs
                    AdminPageFrameworkLoader_Registry::$aAdminPages[ 'tool' ],
                    AdminPageFrameworkLoader_Registry::$aAdminPages[ 'addon' ],
                    AdminPageFrameworkLoader_Registry::$aAdminPages[ 'help' ],
                ),
                'side',                                       // context
                'default'                                     // priority
            );       
            
            new AdminPageFrameworkLoader_AdminPageMetaBox_ExternalLinks(
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Resources', 'admin-page-framework-loader' ), // title
                array( // page slugs
                    AdminPageFrameworkLoader_Registry::$aAdminPages[ 'help' ],
                ),
                'side',                                       // context
                'default'                                     // priority            
            );

            new AdminPageFrameworkLoader_NetworkAdmin(
                AdminPageFrameworkLoader_Registry::$aOptionKeys[ 'main' ],    // the option key
                $this->sFilePath   // caller script path            
            );

        }   

        // Demo
        new AdminPageFrameworkLoader_Demo;
        
        // Events
        new AdminPageFrameworkLoader_Event;

    }
        /**
         * @return      boolean
         * @since       3.6.4
         */
        private function _shouldShowAdminPages() {
            
            if ( ! $this->bIsAdmin ) {
                return false;
            }
            if ( AdminPageFrameworkLoader_Utility::isSilentMode() ) {
                return false;
            }
            return true;
            
        }
    
}