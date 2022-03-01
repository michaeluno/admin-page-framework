<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * The main class of the framework to be extended to create admin pages and forms.
 *
 * <p>The user defines their own class by extending this class with the `setUp()` method overridden to define admin pages.</p>
 *
 * @abstract
 * @since       2.0.0
 * @extends     AdminPageFramework_Controller
 * @package     AdminPageFramework/Factory/AdminPage
 * @remark      Most of the internal methods are prefixed with the underscore like `_getSomething()` and callback methods are prefixed with `_reply`.
 * The methods for the users are public and do not have those prefixes.
 */
abstract class AdminPageFramework extends AdminPageFramework_Controller {

    /**
     * Defines the class object structure type.
     *
     * This is used to create a property object as well as to define the form element structure.
     *
     * @since       3.0.0
     * @since       3.7.0       Changed the name from `$_sFieldsType`.
     * @since       3.7.12      Moved from `AdminPageFramework_Model_Form`. Removed the static scope.
     * @internal
     */
    protected $_sStructureType = 'admin_page';

    /**
     * Registers necessary callbacks and sets up internal components including properties.
     *
     * <h4>Example</h4>
     * <code>
     * new MyAdminPageClass( 'my_custom_option_key', __FILE__ );
     * </code>
     *
     * @access      public
     * @since       2.0.0
     * @see         http://codex.wordpress.org/Roles_and_Capabilities
     * @see         http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains
     * @param       array|integer|string    $aisOptionKey    (optional) specifies the option key name to store in the options table. If this is not set, the instantiated class name will be used as default.
     * [3.5.9+] If an integer is given, a transient will be used. If an array of option key arguments is given, the argument values will be set to the framework properties.
     * - type - either `options_table` or `transient`.
     * - key - the option key or the transient key
     * - duration  - when the option type is transient, this value will be used for the time span to store the value in the database.
     * `
     * array(
     *      'type' => 'options_table',
     *      'key'  => 'my_admin_options',
     * )
     * `
     * `
     * array(
     *      'type' => 'transient',
     *      'key' => $sTransientKeyDefinedSomewhereInYourProgram,
     *      'duration' => 60*60*24*2  // two days
     * )
     * `
     * @param       string                  $sCallerPath    (optional) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
     * @param       string                  $sCapability    (optional) sets the overall access level to the admin pages created by the framework. The used capabilities are listed <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. The capability can be set per page, tab, setting section, setting field. Default: `manage_options`
     * @param       string                  $sTextDomain    (optional) the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains" target="_blank">text domain</a> used for the framework's system messages. Default: admin-page-framework.
     */
    public function __construct( $isOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ){

        if ( ! $this->_isInstantiatable() ) {
            return;
        }

        parent::__construct(
            $isOptionKey,
            $this->_getCallerPath( $sCallerPath ),
            $sCapability,
            $sTextDomain
        );

    }
        /**
         * Returns the script caller path.
         * @remark      It is important to find the caller script path here when separating the library into multiple files.
         * @return      null|string
         * @since       3.8.2
         */
        private function _getCallerPath( $sCallerPath ) {

            if ( $sCallerPath  ) {
                return trim( $sCallerPath );
            }

            if ( ! is_admin() ) {
                return null;
            }

            if ( ! isset( $GLOBALS[ 'pagenow' ] ) ) {
                return null;
            }

            return 'plugins.php' === $GLOBALS[ 'pagenow' ] || isset( $_GET[ 'page' ] )     // sanitization unnecessary
                ? AdminPageFramework_Utility::getCallerScriptPath( __FILE__ ) // not using $oUtil as this method is caller earlier than the base constructor.
                : null;

        }

}
