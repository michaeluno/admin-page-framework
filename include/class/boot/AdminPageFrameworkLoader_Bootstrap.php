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

        // Check requirements.
        $this->_checkRequirements();
        
    }
        /**
         * 
         * @since            3.5.0
         */
        private function _checkRequirements() {

            $_oRequirementCheck = new AdminPageFramework_Requirement(
                array(
                    'php'       => array(
                        'version'    => AdminPageFrameworkLoader_Registry::$aRequirements['PHP'],
                        'error'      => __( 'The plugin requires the PHP version %1$s or higher.', 'uploader-anywheere' ),
                    ),
                    'wordpress' => array(
                        'version'    => AdminPageFrameworkLoader_Registry::$aRequirements['WordPress'],
                        'error'      => __( 'The plugin requires the WordPress version %1$s or higher.', 'uploader-anywheere' ),
                    ),
                    'mysql'     => array(
                        'version'    => AdminPageFrameworkLoader_Registry::$aRequirements['MySQL'],
                        'error' => __( 'The plugin requires the MySQL version %1$s or higher.', 'uploader-anywheere' ),
                    ),
                    'functions' =>  '', // disabled
                    // array(
                        // '_test'          => 'This is a test',
                        // 'curl_version' => sprintf( __( 'The plugin requires the %1$s to be installed.', 'uploader-anywheere' ), 'the cURL library' ),
                    // ),
                    'classes'       => '',  // disabled
                    // 'classes' => array(
                        // 'DOMDocument' => sprintf( __( 'The plugin requires the <a href="%1$s">libxml</a> extension to be activated.', 'pseudo-image' ), 'http://www.php.net/manual/en/book.libxml.php' ),
                    // ),
                    'constants'    => '',   // disabled
                ),
                AdminPageFrameworkLoader_Registry::Name
            );
            $_iWarnings = $_oRequirementCheck->check();
            if ( $_iWarnings ) {            

                $_oRequirementCheck->deactivatePlugin( 
                    $this->sFilePath, 
                    __( 'Deactivating the plugin', 'admin-page-framework-loader' ),  // additional message
                    true    // is in the activation hook. 
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
    
        // 3. Admin pages
        if ( $this->bIsAdmin ) {
            
            // 3.1. Create admin pages - just the example link in the submenu.
            // @todo check the option value and if the admin page option is true, load it.
            new AdminPageFrameworkLoader_AdminPage( 
                AdminPageFrameworkLoader_Registry::OptionKey,
                $this->sFilePath   // caller script path
            );
                    
        }            
 
    }
    
}