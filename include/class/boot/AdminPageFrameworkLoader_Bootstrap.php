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
        if ( ! $_bLoaded ) {
            return $_aClassFiles;
        }
        return $_aClassFiles;
                
    }

    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginActivation() {

        $this->_checkRequirements();
        // $this->_setOptions();
        
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
         * Sets transients.
         * 
         * One is for user redirect to the welcome page.
         * 
         * @since       3.5.0
         * @return      void
         */
        private function _setOptions() {
            
            // Check if the plugin option is set.
            $_aOptions = get_option( AdminPageFrameworkLoader_Registry::$aOptionKeys['main'] );
            if ( is_array( $_aOptions ) && ! empty( $_aOptions ) ) {
                return;           
            }
            
            // If not, it means the user newly installed the plugin.
            $_aOptions = array(
                'welcomed'  => false,
                'version' => '',
            );
            
        }
        
    /**
     * Load localization files.
     * 
     * @remark      A callback for the 'init' hook.
     */
    public function setLocalization() {
        
        // This plugin does not have messages to be displayed in the front end.
        if ( ! $this->bIsAdmin ) { return; }
        
        load_plugin_textdomain( 
            AdminPageFrameworkLoader_Registry::TEXT_DOMAIN, 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
        );
            
        load_plugin_textdomain( 
            'admin-page-framework', 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
        );        
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function setUp() {
    
        // Admin pages
        if ( $this->bIsAdmin ) {
            
            // Dashboard
            new AdminPageFrameworkLoader_AdminPageWelcome( 
                '', // disable saving form data.
                $this->sFilePath   // caller script path
            );
            
            // Loader plugin admin pages.
            new AdminPageFrameworkLoader_AdminPage( 
                AdminPageFrameworkLoader_Registry::$aOptionKeys['main'],    // the option key
                $this->sFilePath   // caller script path
            );

            new AdminPageFrameworkLoader_NetworkAdmin(
                AdminPageFrameworkLoader_Registry::$aOptionKeys['main'],    // the option key
                $this->sFilePath   // caller script path            
            );
            
        }   
        
        // Demo
        new AdminPageFrameworkLoader_Demo;
        
    }
    
}