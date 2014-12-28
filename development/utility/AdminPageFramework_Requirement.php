<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Requirement' ) ) :
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
     * @since       3.4.6
     */ 
    public function __construct( array $aRequirements=array() ) {
        
        // Avoid undefined index warnings.
        $this->_aRequirements              = $aRequirements + $this->_aDefaultRequirements;    
        $this->_aRequirements['php']       = $this->_aRequirements['php'] + $this->_aDefaultRequirements['php'];
        $this->_aRequirements['wordpress'] = $this->_aRequirements['wordpress'] + $this->_aDefaultRequirements['wordpress'];
        $this->_aRequirements['mysql']     = $this->_aRequirements['mysql'] + $this->_aDefaultRequirements['mysql'];

    }
    
    /**
     * Returns a warning array by checking requirements.
     * 
     * If it is not empty, it means there is a missing requirement.
     * @since       3.4.6
     */
    public function get() {      
        
        // PHP
        if ( isset( $this->_aRequirements['php']['version'] ) && ! $this->_checkPHPVersion( $this->_aRequirements['php']['version'] ) ) {
            $_aWarnings[] = sprintf( $this->_aRequirements['php']['error'], $this->_aRequirements['php']['version'] );
        }

        // WordPress
        if ( isset( $this->_aRequirements['wordpress']['version'] ) && ! $this->_checkWordPressVersion( $this->_aRequirements['wordpress']['version'] ) ) {
            $_aWarnings[] = sprintf( $this->_aRequirements['wordpress']['error'], $this->_aRequirements['wordpress']['version'] );
        }
        
        // MySQL
        if ( isset( $this->_aRequirements['mysql']['version'] ) && ! $this->_checkMySQLVersion( $this->_aRequirements['mysql']['version'] ) ) {
            $_aWarnings[] = sprintf( $this->_aRequirements['mysql']['error'], $this->_aRequirements['mysql']['version'] );
        }        
        
        // Check the rest.
        $_aWarnings = array_merge(
            $_aWarnings,
            $this->_checkFunctions( $this->_aRequirements['functions'] ),
            $this->_checkClasses( $this->_aRequirements['classes'] ),
            $this->_checkConstants( $this->_aRequirements['constants'] ),
            $this->_checkFiles( $this->_aRequirements['files'] );
        );
        
        return array_filter( $_aWarnings ); // drop empty elements.

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
                : return true;
            
        }

        /**
         * Checks if the given classes exists.
         * @since       3.4.6
         */
        private function _checkClasses( $aClasses ) {
            return $this->_getWarningsByFunctionName( 'class_exists', $aClasses );
        }
        /**
         * Checks if the given functions exists
         * @since       3.4.6
         */
        private function _checkFunctions( $aFunctions ) {
            return $this->_getWarningsByFunctionName( 'function_exists', $aFunctions );
        }    
        /**
         * Checks if the given constants are defined.
         * @since       3.4.6
         */
        private function _checkConstants( $aConstants ) {
            return $this->_getWarningsByFunctionName( 'defined', $aConstants );
        }    
        /**
         * Checks if the given files exist.
         * @since       3.4.6
         */
        private function _checkFiles( $aFilePaths ) {
            return $this->_getWarningsByFunctionName( 'file_exists', $aFilePaths );
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
}
endif;