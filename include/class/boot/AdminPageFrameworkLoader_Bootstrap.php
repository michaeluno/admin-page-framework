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
        
    }
        /**
         * 
         * @since            3.5.0
         */
        private function _checkRequirements() {

            $_oRequirementCheck = new AdminPageFramework_Requirement(
                AdminPageFrameworkLoader_Registry::$aRequirements,
                AdminPageFrameworkLoader_Registry::Name
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
     * @remark      A callback for the 'init' hook.
     */
    public function setLocalization() {
        
        // This plugin does not have messages to be displayed in the front end.
        if ( ! $this->bIsAdmin ) { return; }
        
        load_plugin_textdomain( 
            AdminPageFrameworkLoader_Registry::TextDomain, 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TextDomainPath
        );
            
        load_plugin_textdomain( 
            'admin-page-framework', 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TextDomainPath
        );        
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function loadComponents() {
    
        // Admin pages
        if ( $this->bIsAdmin ) {
            
            new AdminPageFrameworkLoader_AdminPage( 
                AdminPageFrameworkLoader_Registry::$aOptionKeys['main'],
                $this->sFilePath   // caller script path
            );
                        
        }   
        
        // Demo
        // new AdminPageFrameworkLoader_Demo( 
            // AdminPageFrameworkLoader_Registry::$aOptionKeys['demo'],
            // $this->sFilePath   // caller script path
        // );        
 
    }
    
}