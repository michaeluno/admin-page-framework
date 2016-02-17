<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 */

/**
 * Provides an abstract base to create a bootstrap class for Wordpress plugins.
 * 
 * @action      do      {hook prefix}_action_before_loading_plugin
 * @action      do      {hook prefix}_action_after_loading_plugin
 * @since       3.5.0
 * @package     AdminPageFramework
 * @subpackage  Utility
 */
abstract class AdminPageFramework_PluginBootstrap {
    
    /**
     * Stores the caller file path.
     */
    public $sFilePath;
    
    /**
     * Stores whether the script is loaded in the admin area.
     */
    public $bIsAdmin;
    
    /**
     * Stores the hook prefix.
     */
    public $sHookPrefix;
            
    /**
     * Sets up properties and hooks.
     * 
     * @param       string      $sPluginFilePath        The plugin file path.
     * @param       string      $sPluginHookPrefix      The plugin hook slug without underscore. This will be used to construct hook names.
     * @param       string      $sSetUpHook             The action hook name for the setUp callback. Default 'plugins_loaded'.
     * @param       string      $iPriority              The priority. Set a lower number to get loader earlier. Default: `10`.
     */
    public function __construct( $sPluginFilePath, $sPluginHookPrefix='', $sSetUpHook='plugins_loaded', $iPriority=10 ) {
        
        // Check if it has been loaded.
        if ( $this->_hasLoaded() ) {
            return;
        }
        
        // 1. Set up properties
        $this->sFilePath   = $sPluginFilePath;
        $this->bIsAdmin    = is_admin();
        $this->sHookPrefix = $sPluginHookPrefix;
        $this->sSetUpHook  = $sSetUpHook;
        $this->iPriority   = $iPriority;
        
        // 2. Call the (public) user constructor.
        $_bValid = $this->start();
        if ( false === $_bValid ) {
            return;
        }
        
        // 3. Define constants.
        $this->setConstants();
        
        // 4. Set global variables.
        $this->setGlobals();
            
        // 5. Set up auto-load classes.
        $this->_registerClasses();

        // 6. Set up activation hook.
        register_activation_hook( $this->sFilePath, array( $this, 'replyToPluginActivation' ) );
        
        // 7. Set up deactivation hook.
        register_deactivation_hook( $this->sFilePath, array( $this, 'replyToPluginDeactivation' ) );
                 
        // 8. Schedule to load plugin specific components.
        if ( ! $this->sSetUpHook || did_action( $this->sSetUpHook ) )  {
            $this->_replyToLoadPluginComponents();
        } else {
            add_action( $this->sSetUpHook, array( $this, '_replyToLoadPluginComponents' ), $this->iPriority );
        }

        // 9. Set up localization
        add_action( 'init', array( $this, 'setLocalization' ) );
        
        // 10. Call the (protected) user constructor.
        $this->construct();
        
        
    }
        
        /* 
         * Do not allow multiple instances of the extended class per page load. 
         * 
         * @remark      It does not use a static property but a static local variable so that it takes effect in each extended class.
         * @since       3.5.0
         */
        protected function _hasLoaded() {
            
            static $_bLoaded = false;
            if ( $_bLoaded ) {
                return true;
            }
            $_bLoaded = true;

            return false;
            
        }
        
        /**
         * Register classes to be auto-loaded.
         * 
         * @since       3.5.0
         */
        protected function _registerClasses() {
            
            // This class should be used in the framework bootstrap so disabling the auto-load option for performance.
            if ( ! class_exists( 'AdminPageFramework_RegisterClasses', false ) ) {
                return;
            }
            
            // Register classes
            new AdminPageFramework_RegisterClasses(
                $this->getScanningDirs(),   // scanning directory paths
                array(),                    // autoloader options
                $this->getClasses()         // pre-generated class list
            );
                    
        }

        /**
         * Loads the plugin specific components. 
         * 
         * @remark      All the necessary classes should have been already loaded.
         * @since       3.5.0
         */
        public function _replyToLoadPluginComponents() {

            if ( $this->sHookPrefix ) {
                do_action( "{$this->sHookPrefix}_action_before_loading_plugin" );
            }
        
            $this->setUp();
            
            // Modules should use this hook.
            if ( $this->sHookPrefix ) {
                do_action( "{$this->sHookPrefix}_action_after_loading_plugin" );
            }
            
        }
        
    /*
     * Shared Methods. Users override these methods in the extended class.
     */
    
    /**
     * Sets up constants.
     */
    public function setConstants() {}
    
    /**
     * Sets up global variables.
     */
    public function setGlobals() {}
    
    /**
     * Returns an array holding class names in the key and the file path to the value.
     * The returned array will be passed to the autoloader class.
     * @since       3.5.0
     * @return      array       An array holding PHP classes.
     */
    public function getClasses() {
        
        $_aClasses = array();
        
        // Example
        // include( dirname( $this->sFilePath ) . '/include/class-list.php' );

        return $_aClasses;
    }
    
    /**
     * Returns an array holding scanning directory paths.
     * @since       3.5.0
     * @return      array       An array holding directory paths.
     */
    public function getScanningDirs() {
        $_aDirs = array();

        return $_aDirs;
    }
    
    /**
     * The plugin activation callback method.
     */
    public function replyToPluginActivation() {}

    /**
     * The plugin deactivation callback method.
     * 
     * @since       3.5.0
     */
    public function replyToPluginDeactivation() {}
        
    /**
     * Load localization files.
     *
     * @since       3.5.0
     */
    public function setLocalization() {}
    
    /**
     * Loads plugin components.
     * 
     * Use this method to load the main plugin components such as post type, admin pages, event routines etc 
     * as this method is triggered with the 'plugins_loaded' hook which is triggered after all the plugins are loaded.
     * 
     * On the other hand, for extension plugins, use the construct() method below and hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     * This way, extension plugin can load their components after the main plugin components get loaded.
     * 
     * @since       3.5.0
     */
    public function setUp() {}
        
    /**
     * The protected user constructor method.
     * 
     * For extension plugins, use this method to hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     * 
     * @since       3.5.0
     * @access      protected       This is meant to be called within the class definition. For public access use the `start()` method.
     */
    protected function construct() {}
 
    /**
     * The public user constructor method.
     * 
     * @since       3.5.0
     * @access      public
     * @return      void|boolean     Return false to stop loading components.
     */
    public function start() {}
 
}
