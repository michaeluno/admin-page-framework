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
final class AdminPageFrameworkLoader_Bootstrap {
    
    /**
     * Indicates whether the bootstrap has been loaded or not so that multiple instances of this class won't be created.      
     */
    static public $_bLoaded = false;
        
    /**
     * Sets up properties and hooks.
     * 
     */
    public function __construct( $sPluginFilePath ) {
        
        // Do not allow multiple instances per page load.
        if ( self::$_bLoaded ) {
            return;
        }
        self::$_bLoaded = true;
        
        // Set up properties
        $this->_sFilePath = $sPluginFilePath;
        $this->_bIsAdmin = is_admin();
        
        // 1. Define constants.
        // $this->_defineConstants();
        
        // 2. Set global variables.
        // $this->_setGlobalVariables();
            
        // 3. Set up auto-load classes.
        $this->_loadClasses( $this->_sFilePath );

        // 4. Set up activation hook.
        register_activation_hook( $this->_sFilePath, array( $this, '_replyToPluginActivation' ) );
        
        // 5. Set up deactivation hook.
        register_deactivation_hook( $this->_sFilePath, array( $this, '_replyToPluginDeactivation' ) );
                 
        // 6. Schedule to load plugin specific components.
        add_action( 'plugins_loaded', array( $this, '_replyToLoadPluginComponents' ) );
                        
    }    
    
    /**
     * Sets up constants.
     */
    // private function _defineConstants() {}
    
    /**
     * Sets up global variables.
     */
    // private function _setGlobalVariables() {}
    
    /**
     * Register classes to be auto-loaded.
     * 
     * @since       3.5.0
     */
    private function _loadClasses( $sFilePath ) {
        
        $_sPluginDir    = dirname( $sFilePath );
                                        
        // Include the include lists. The including file reassigns the list(array) to the $_aClassFiles variable.
        $_aClassFiles   = array();
        $_bLoaded       = include( $_sPluginDir . '/include/admin-page-framework-loader-include-class-file-list.php' );
        if ( ! $_bLoaded ) {
            return;
        }
        
        // Register classes
        new AdminPageFramework_RegisterClasses( 
            array(),        // scanning dirs
            array(),        // autoloader options
            $_aClassFiles   // pre-generated class list
        );
                
    }

    /**
     * The plugin activation callback method.
     */    
    public function _replyToPluginActivation() {

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
            if ( $_iWarnings  ) {            

                $_oRequirementCheck->deactivatePlugin( 
                    $this->_sFilePath, 
                    __( 'Deactivating the plugin', 'admin-page-framework-loader' ),  // additional message
                    true    // is in the activation hook. 
                );
                            
            }        
             
        }    

    /**
     * The plugin deactivation callback method.
     */
    public function _replyToPluginDeactivation() {}
        
    /**
     * Load localization files.
     *
     */
    private function _localize() {
        
        // This plugin does not have messages to be displayed in the front end.
        if ( ! $this->_bIsAdmin ) { return; }
        
        load_plugin_textdomain( 
            AdminPageFrameworkLoader_Registry::TextDomain, 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TextDomainPath
        );
            
        load_plugin_textdomain( 
            'admin-page-framework', 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/' . AdminPageFrameworkLoader_Registry::TextDomainPath
        );        
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     * @remark        All the necessary classes should have been already loaded.
     */
    public function _replyToLoadPluginComponents() {

        // 1. Set up localization.
        $this->_localize();
    
        // 3. Admin pages
        if ( $this->_bIsAdmin ) {
            
            // 3.1. Create admin pages - just the example link in the submenu.
            // @todo check the option value and if the admin page option is true, load it.
            new AdminPageFrameworkLoader_AdminPage( 
                AdminPageFrameworkLoader_Registry::OptionKey,
                $this->_sFilePath   // caller script path
            );
                    
        }            
        
        // Modules should use this hook.
        do_action( 'admin_page_framework_loader_action_after_loading_plugin' );
        
    }

        
}