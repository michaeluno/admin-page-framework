<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Performs checks for given requirements whether the site can satisfy them.
 *
 * <h2>Usage</h2>
 * Set requirements to the first parameter and perform the `check()` method to get the number of warnings.
 *
 * <h2>Example</h2>
 * <code>
 *  $_oRequirementCheck = new AdminPageFramework_Requirement(
 *      array(
 *          'php' => array(
 *              'version'   => '5.2.4',
 *              'error'     => 'The plugin requires the PHP version %1$s or higher.',
 *          ),
 *          'wordpress'         => array(
 *              'version'   => '3.4',
 *              'error'     => 'The plugin requires the WordPress version %1$s or higher.',
 *          ),
 *          'mysql'             => array(
 *              'version'   => '5.0',
 *              'error'     => 'The plugin requires the MySQL version %1$s or higher.',
 *          ),
 *      ),
 *      'My Plugin Name'
 *  );
 *
 *  if ( $_oRequirementCheck->check() ) {
 *      $_oRequirementCheck->deactivatePlugin(
 *          $this->sFilePath,   // the plugin main file path
 *          __( 'Deactivating the plugin', 'admin-page-framework-loader' ),  // additional message
 *          true    // is in the activation hook. This will exit the script.
 *      );
 *  }
 * </code>
 *
 * @since       3.4.6
 * @package     AdminPageFramework/Utility
 */
class AdminPageFramework_Requirement {

    /**
     * Stores the criteria of the requirements.
     *
     * @since       3.4.6
     */
    private $_aRequirements = array();

    /**
     * Stores warning messages of insufficient items.
     * @since       3.4.6
     */
    public $aWarnings = array();

    /**
     * The default criteria and their error messages.
     *
     * @since       3.4.6
     */
    private $_aDefaultRequirements = array(
        'php' => array(
            'version'   => '5.2.4',
            'error'     => 'The plugin requires the PHP version %1$s or higher.',
        ),
        'wordpress'         => array(
            'version'   => '3.3',
            'error'     => 'The plugin requires the WordPress version %1$s or higher.',
        ),
        'mysql'             => array(
            'version'   => '5.0',
            'error'     => 'The plugin requires the MySQL version %1$s or higher.',
        ),
        'functions'     => array(
            // e.g. 'mblang' => 'The plugin requires the mbstring extension.',
        ),
        'classes'       => array(
            // e.g. 'DOMDocument' => 'The plugin requires the DOMXML extension.',
        ),
        'constants'     => array(
            // e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
            // e.g. 'APSPATH' => 'The script cannot be loaded directly.',
        ),
        'files'         =>    array(
            // e.g. 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.',

        ),
    );

    /**
     * Sets up properties.
     *
     * To disable checking on particular item, set an empty value to the element or simply omit it.
     *
     * <code>
     * $aRequirement = array(
     *  'mysql' => '',  // <-- mysql will be skipped.
     *  'php' ...
     *  // 'wordpress' will be omitted
     * )
     * </code>
     *
     * @since       3.4.6
     * @param       array       $aRequirements      An array holding a requirement definition.
     * <ul>
     *      <li>`php` - (array) An array holding requirement information.
     *           <ul>
     *               <li><code>version</code> - (string) a version number to be required. e.g. `1.5.0`, `15.6RC01`</li>
     *               <li><code>error</code> - (string) an error message to display to the user. Use a placeholder (`%1$s`) for the version set with the `version` argument to be embedded. e.g. `The plugin requires the PHP version %1$s or higher.`</li>
     *           </ul>
     *      </li>
     *      <li>`wordpress` - (array) An array holding requirement information.
     *           <ul>
     *               <li><code>version</code> - (string) a version number to be required. e.g. `1.5.0`, `15.6RC01`</li>
     *               <li><code>error</code> - (string) an error message to display to the user. Use a placeholder (`%1$s`) for the version set with the `version` argument to be embedded. e.g. `The plugin requires the WordPress version %1$s or higher.`</li>
     *           </ul>
     *      </li>
     *      <li>`mysql` - (array) An array holding requirement information.
     *           <ul>
     *               <li><code>version</code> - (string) a version number to be required. e.g. `1.5.0`, `15.6RC01`</li>
     *               <li><code>error</code> - (string) an error message to display to the user. Use a placeholder (`%1$s`) for the version set with the `version` argument to be embedded. e.g. `The plugin requires the MySQL version %1$s or higher.`</li>
     *           </ul>
     *      </li>
     *      <li><code>functions</code> - (array) An array holding required function names in the keys and the message in the values. e.g. `array( 'my_custom_func_in_other_script' => 'The function %1$s is missing. Please install the other script.' )`</li>
     *      <li><code>classes</code> - (array) An array holding required class names in the keys and the message in the values. e.g. `array( 'DOMDocument' => 'The plugin requires the DOMXML extension.' )`</li>
     *      <li><code>constants</code> - (array) An array holding required constants in the keys and the message in the values. e.g. `array( 'APSPATH' => 'The script cannot be loaded directly.' )`</li>
     *      <li><code>files</code> - (array) An array holding required file paths in the keys and the message in the values. e.g. `array( 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.' )`</li>
     * </ul>
     * @param       string      $sScriptName        The script name.
     */
    public function __construct( array $aRequirements=array(), $sScriptName='' ) {

        // Avoid undefined index warnings.
        $aRequirements          = $aRequirements + $this->_aDefaultRequirements;
        $aRequirements          = array_filter( $aRequirements, 'is_array' );
        foreach( array( 'php', 'mysql', 'wordpress' ) as $_iIndex => $_sName ) {
            if ( isset( $aRequirements[ $_sName ] ) ) {
                $aRequirements[ $_sName ] = $aRequirements[ $_sName ] + $this->_aDefaultRequirements[ $_sName ];
            }
        }
        $this->_aRequirements   = $aRequirements;

        // Store the script name for admin notices.
        $this->_sScriptName     = $sScriptName;

    }

    /**
     * Performs checks.
     *
     * If it is not empty, it means there is a missing requirement.
     *
     * @since       3.4.6
     * @return      integer         The number of warnings.
     */
    public function check() {

        $_aWarnings = array();

        // PHP, WordPress, MySQL
        $_aWarnings[] = $this->_getWarningByType( 'php' );
        $_aWarnings[] = $this->_getWarningByType( 'wordpress' );
        $_aWarnings[] = $this->_getWarningByType( 'mysql' );

        // Ensure necessary array elements.
        $this->_aRequirements = $this->_aRequirements + array(
            'functions' => array(),
            'classes'   => array(),
            'constants' => array(),
            'files'     => array(),
        );

        // Check the rest.
        $_aWarnings = array_merge(
            $_aWarnings,
            $this->_checkFunctions( $this->_aRequirements['functions'] ),
            $this->_checkClasses( $this->_aRequirements['classes'] ),
            $this->_checkConstants( $this->_aRequirements['constants'] ),
            $this->_checkFiles( $this->_aRequirements['files'] )
        );

        $this->aWarnings = array_filter( $_aWarnings ); // drop empty elements.
        return count( $this->aWarnings );

    }
        /**
         * Returns a php warning if present.
         * @since       3.5.3
         * @internal
         * @return      string      The warning.
         */
        private function _getWarningByType( $sType ) {
            if ( ! isset( $this->_aRequirements[ $sType ][ 'version' ] ) ) {
                return '';
            }
            if ( $this->_checkPHPVersion( $this->_aRequirements[ $sType ][ 'version' ] ) ) {
                return '';
            }
            return sprintf(
                $this->_aRequirements[ $sType ][ 'error' ],
                $this->_aRequirements[ $sType ][ 'version' ]
            );
        }

        /**
         * Checks if the given version is greater than or equal to the installed PHP version.
         *
         * @return      boolean     True if the given version is greater or equal to the current version. Otherwise, false.
         * @since       3.4.6
         * @internal
         */
        private function _checkPHPVersion( $sPHPVersion ) {
            return version_compare( phpversion(), $sPHPVersion, ">=" );
        }

        /**
         * Checks if the given version is greater than or equal to the installed WordPress verison.
         * @since       3.4.6
         * @internal
         */
        private function _checkWordPressVersion( $sWordPressVersion ) {
            return version_compare( $GLOBALS['wp_version'], $sWordPressVersion, ">=" );
        }

        /**
         * Checks if the given version is greater than or equal to the installed MySQL version.
         * @since       3.4.6
         * @internal
         */
        private function _checkMySQLVersion( $sMySQLVersion ) {

            global $wpdb;
            $_sInstalledMySQLVersion = isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli
                ? @mysqli_get_server_info( $wpdb->dbh )
                : @mysql_get_server_info();

            return $_sInstalledMySQLVersion
                ? version_compare( $_sInstalledMySQLVersion, $sMySQLVersion, ">=" )
                : true;

        }

        /**
         * Checks if the given classes exists.
         * @since       3.4.6
         * @internal
         */
        private function _checkClasses( $aClasses ) {
            return empty( $aClasses )
                ? array()
                : $this->_getWarningsByFunctionName( 'class_exists', $aClasses );
        }
        /**
         * Checks if the given functions exists
         * @since       3.4.6
         * @internal
         */
        private function _checkFunctions( $aFunctions ) {
            return empty( $aFunctions )
                ? array()
                : $this->_getWarningsByFunctionName( 'function_exists', $aFunctions );
        }
        /**
         * Checks if the given constants are defined.
         * @since       3.4.6
         * @internal
         */
        private function _checkConstants( $aConstants ) {
            return empty( $aConstants )
                ? array()
                : $this->_getWarningsByFunctionName( 'defined', $aConstants );
        }
        /**
         * Checks if the given files exist.
         * @since       3.4.6
         * @internal
         */
        private function _checkFiles( $aFilePaths ) {
            return empty( $aFilePaths )
                ? array()
                : $this->_getWarningsByFunctionName( 'file_exists', $aFilePaths );
        }
            /**
             * Performs the given function to get warnings.
             *
             * if it returns non true (false), it stores the subject warning and returns the array holding the warnings.
             *
             * @since       3.4.6
             * @return      array           The warning array.
             * @internal
             */
            private function _getWarningsByFunctionName( $sFuncName, $aSubjects ) {
                $_aWarnings = array();
                foreach( $aSubjects as $_sSubject => $_sWarning ) {
                    if ( ! call_user_func_array( $sFuncName, array( $_sSubject ) ) ) {
                        $_aWarnings[] = sprintf( $_sWarning, $_sSubject );
                    }
                }
                return $_aWarnings;
            }

    /**
     * Sets up admin notices to display warnings.
     *
     * @since       3.4.6
     * @internal
     */
    public function setAdminNotices() {
        add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
    }
        /**
         * Prints warnings.
         * @since       3.4.6
         * @internal
         */
        public function _replyToPrintAdminNotices() {

            $_aWarnings     = array_unique( $this->aWarnings );
            if ( empty( $_aWarnings ) ) {
                return;
            }
            echo "<div class='error notice is-dismissible'>"
                    . "<p>"
                        . $this->_getWarnings()
                    . "</p>"
                . "</div>";

        }
            /**
             * Returns the warnings.
             *
             * @since        3.4.6
             * @internal
             */
            private function _getWarnings() {

                $_aWarnings     = array_unique( $this->aWarnings );
                if ( empty( $_aWarnings ) ) {
                    return '';
                }
                $_sScripTitle   = $this->_sScriptName
                    ?  "<strong>" . $this->_sScriptName . "</strong>:&nbsp;"
                    : '';
                return $_sScripTitle
                   . implode( '<br />', $_aWarnings );

            }

    /**
     * Deactivates the plugin.
     * @since       3.4.6
     * @param       string      $sPluginFilePath    A plugin main file path.
     * @param       string      $sMessage           A message to be displayed to the user.
     * @param       boolean     $bIsOnActivation    Whether it is called upon plugin activation hook.
     * @return      void
     */
    public function deactivatePlugin( $sPluginFilePath, $sMessage='', $bIsOnActivation=false ) {

        add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
        $this->aWarnings[] = '<strong>' . $sMessage . '</strong>';
        if ( ! function_exists( 'deactivate_plugins' ) ) {
            if ( ! @include( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
                return;
            }
        }
        deactivate_plugins( $sPluginFilePath );

        // If it is in the activation hook, WordPress cannot display or add admin notices. So the script needs to exit.
        // Before that, we can display messages to the user.
        if ( $bIsOnActivation ) {

            $_sPluginListingPage = add_query_arg( array(), $GLOBALS['pagenow'] );
            wp_die( $this->_getWarnings() . "<p><a href='$_sPluginListingPage'>Go back</a>.</p>" );

        }

    }
}
