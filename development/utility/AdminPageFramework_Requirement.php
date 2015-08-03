<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Checks if the site has given requirements.
 * 
 * @since       3.4.6
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal    
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
     * To disable checking on particular item, sent non-array value to the element.
     * 
     * $aRequirement = array(
     *  'mysql' => '',  // <-- mysql will be skipped.
     *  'php' ...
     * )
     * 
     * @since       3.4.6
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
         */
        private function _checkPHPVersion( $sPHPVersion ) {
            return version_compare( phpversion(), $sPHPVersion, ">=" );
        }
        
        /**
         * Checks if the given version is greater than or equal to the installed WordPress verison.
         * @since       3.4.6
         */
        private function _checkWordPressVersion( $sWordPressVersion ) {
            return version_compare( $GLOBALS['wp_version'], $sWordPressVersion, ">=" );
        }
        
        /**
         * Checks if the given version is greater than or equal to the installed MySQL version.
         * @since       3.4.6
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
         */
        private function _checkClasses( $aClasses ) {
            return empty( $aClasses )
                ? array()
                : $this->_getWarningsByFunctionName( 'class_exists', $aClasses );
        }
        /**
         * Checks if the given functions exists
         * @since       3.4.6
         */
        private function _checkFunctions( $aFunctions ) {
            return empty( $aFunctions )
                ? array()
                : $this->_getWarningsByFunctionName( 'function_exists', $aFunctions );
        }    
        /**
         * Checks if the given constants are defined.
         * @since       3.4.6
         */
        private function _checkConstants( $aConstants ) {
            return empty( $aConstants )
                ? array()
                : $this->_getWarningsByFunctionName( 'defined', $aConstants );
        }    
        /**
         * Checks if the given files exist.
         * @since       3.4.6
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
     */
    public function setAdminNotices() {
        add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
    }
        /**
         * Prints warnings.
         * @since       3.4.6
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
     */
    public function deactivatePlugin( $sPluginFilePath, $sMessage='', $bIsOnActivation=false ) {
        
        add_action( 'admin_notices', array( $this, '_replyToPrintAdminNotices' ) );
        $this->aWarnings[] = '<strong>' . $sMessage . '</strong>';
        if ( ! function_exists( 'deactivate_plugins' ) ) {
            if ( ! @include( ABSPATH . '/wp-admin/includes/plugin.php' ) ) {
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