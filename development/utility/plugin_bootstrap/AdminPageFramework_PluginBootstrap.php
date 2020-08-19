<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 */

/**
 * Provides an abstract base to create a bootstrap class for Wordpress plugins.
 *
 * <h3>Usage</h3>
 * Extend the class and insert your own plugin routine in the `setUp()` method.
 * If you have classes you want them to be auto-loaded, override the `getClasses()` method and return an array holding a list of class files. The array should consist of keys of class names and the values of class file paths.
 * By overriding the `replyToPluginActivation()` method, you can write a handing routine for when the plugin gets activated.
 * To set localization files, override the `setLocalization()` method and insert your code in the method to set up translation files.
 *
 * There are other methods you can override. All the public methods are meant to be overridden. Check out the public methods below.
 *
 * <h3>Example</h3>
 * <code>
 *  final class AdminPageFrameworkLoader_Bootstrap extends AdminPageFramework_PluginBootstrap {
 *
 *
 *      // Register classes to be auto-loaded.
 *      public function getClasses() {
 *
 *          // Include the include lists. The including file reassigns the list(array) to the $_aClassFiles variable.
 *          $_aClassFiles   = array();
 *          include( dirname( $this->sFilePath ) . '/include/loader-class-list.php' );
 *          $this->_aClassFiles = $_aClassFiles;
 *          return $_aClassFiles;
 *
 *      }
 *
 *      // The plugin activation callback method.
 *      public function replyToPluginActivation() {
 *
 *          // Do plugin requirement checks and deactivate the plugin if necessary.
 *          $_oRequirementCheck = new AdminPageFramework_Requirement(
 *              AdminPageFrameworkLoader_Registry::$aRequirements,
 *              AdminPageFrameworkLoader_Registry::NAME
 *          );
 *
 *          if ( $_oRequirementCheck->check() ) {
 *              $_oRequirementCheck->deactivatePlugin(
 *                  $this->sFilePath,
 *                  __( 'Deactivating the plugin', 'admin-page-framework-loader' ),  // additional message
 *                  true    // is in the activation hook. This will exit the script.
 *              );
 *          }
 *
 *      }
 *
 *      // Set localization
 *      public function setLocalization() {
 *
 *          // This plugin does not have messages to be displayed in the front end.
 *          if ( ! $this->bIsAdmin ) {
 *              return;
 *          }
 *
 *          $_sPluginBaseNameDirName = dirname( plugin_basename( $this->sFilePath ) );
 *          load_plugin_textdomain(
 *              AdminPageFrameworkLoader_Registry::TEXT_DOMAIN,
 *              false,
 *              $_sPluginBaseNameDirName . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
 *          );
 *
 *          load_plugin_textdomain(
 *              'admin-page-framework',
 *              false,
 *              $_sPluginBaseNameDirName . '/' . AdminPageFrameworkLoader_Registry::TEXT_DOMAIN_PATH
 *          );
 *
 *      }
 *
 *      public function setUp() {
 *
 *          // Do the plugin task
 *
 *      }
 *
 *  }
 *  new AdminPageFrameworkLoader_Bootstrap( PLUGIN_MAIN_FILE_PATH );
 * </code>
 *
 * @action      do      {hook prefix}_action_before_loading_plugin
 * @action      do      {hook prefix}_action_after_loading_plugin
 * @since       3.5.0
 * @package     AdminPageFramework/Utility
 */
abstract class AdminPageFramework_PluginBootstrap {

    /**#@+
     * @internal
     */
    /**
     * Stores the caller file path.
     * @var     string
     */
    public $sFilePath;

    /**
     * Stores whether the script is loaded in the admin area.
     * @var     boolean
     */
    public $bIsAdmin;

    /**
     * Stores the hook prefix.
     * @var     string
     */
    public $sHookPrefix;
    /**#@-*/

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
         * @internal
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
         * @internal
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
         * @internal
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
     *
     * @return      void
     */
    public function setConstants() {}

    /**
     * Sets up global variables.
     *
     * @return      void
     */
    public function setGlobals() {}

    /**
     * Returns an array holding class names in the key and the file path to the value.
     * The returned array will be passed to the autoloader class.
     * @since       3.5.0
     * @return      array       An array holding PHP classes. The array must consist of keys of class names and values of the class file paths.
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
     *
     * @since       3.5.0
     * @return      void
     */
    public function replyToPluginActivation() {}

    /**
     * The plugin deactivation callback method.
     *
     * @since       3.5.0
     * @return      void
     */
    public function replyToPluginDeactivation() {}

    /**
     * Load localization files.
     *
     * @since       3.5.0
     * @return      void
     * @callback    action      init
     */
    public function setLocalization() {}

    /**
     * Loads plugin components.
     *
     * Use this method to load the main plugin components such as post type, admin pages, event routines etc
     * as this method is triggered with the 'plugins_loaded' hook which is triggered after all the plugins are loaded.
     *
     * On the other hand, for extension plugins, use the construct() method below and hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     * This way, the extension plugin can load their components after the main plugin components get loaded.
     *
     * @since       3.5.0
     * @return      void
     */
    public function setUp() {}

    /**
     * The protected user constructor method which is automatically called when the class is instantiated.
     *
     * For extension plugins, use this method to hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     *
     * @since       3.5.0
     * @access      protected       This is meant to be called within the class definition. For public access use the `start()` method.
     * @return      void
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
