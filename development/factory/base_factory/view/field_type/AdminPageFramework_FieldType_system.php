<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the 'system' field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.3.0
 * @internal
 */
class AdminPageFramework_FieldType_system extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     *     $this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => 'system',        // <--- THIS PART
     *          'field_id'      => '...',
     *          'title'         => '...',
     *      )
     *  );
     */
    public $aFieldTypeSlugs = array( 'system', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     *     $this->addSettingFields(
     *      array(
     *          'section_id'    => '...',    
     *          'type'          => '...',
     *          'field_id'      => '...',
     *          'my_custom_key' => '...',    // <-- THIS PART
     *      )
     *  );
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'data'          =>  array(),        // [3.2.0+] Stores the data to be displayed
        'print_type'    =>  1,              // [3.4.6+] 1: readable representation of array. 2: print_r()
        'attributes'    =>    array(
            'rows'          => 60,
            'autofocus'     => null,
            'disabled'      => null,
            'formNew'       => null,
            'maxlength'     => null,
            'placeholder'   => null,
            'readonly'      => 'readonly',
            'required'      => null,
            'wrap'          => null,  
            'style'         => null,
            'onclick'       => 'this.focus();this.select()',
        ),    
    );

    /**
     * The user constructor.
     * 
     * Loaded at the end of the constructor.
     */
    protected function construct() {}
        
    /**
     * Loads the field type necessary components.
     * 
     * This method is triggered when a field definition array that calls this field type is parsed. 
     */ 
    protected function setUp() {}    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     * 
     * The returning array should be composed with all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
     *     <li>**attributes** - (optional, array) [3.3.0+] attribute argument array. `array( 'async' => '', 'data-id' => '...' )`</li>
     * </ul>     
     */
    protected function getEnqueuingScripts() { 
        return array();
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
     * </ul>
     */
    protected function getEnqueuingStyles() { 
        return array();
    }            


    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 
        return '';
    }

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
.admin-page-framework-field-system {
    width: 100%;
}
.admin-page-framework-field-system .admin-page-framework-input-label-container {
    width: 100%;
}
.admin-page-framework-field-system textarea {
    background-color: #f9f9f9; 
    width: 97%; 
    outline: 0; 
    font-family: Consolas, Monaco, monospace;
    white-space: pre;
    word-wrap: normal;
    overflow-x: scroll;            
}
CSSRULES;
        
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 

        $_aInputAttributes           = $aField['attributes'];
        $_aInputAttributes['class'] .= ' system';
        unset( $_aInputAttributes['value'] );
        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<textarea " . $this->getAttributes( $_aInputAttributes ) . " >"    
                        . esc_textarea( $this->_getSystemInfomation( $aField['value'], $aField['data'], $aField['print_type'] ) )
                    . "</textarea>"
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }    
        /**
         * Returns the system information value for a textarea tag.
         * 
         * @return      string      The human readable system information.
         */
        private function _getSystemInfomation( $asValue=null, $asCustomData=null, $iPrintType=1 ) {

            if ( isset( $asValue ) ) {
                return $asValue;
            }

            $_aOutput   = array();
            foreach( $this->_getFormattedSystemInformation( $asCustomData ) as $_sSection => $_aInfo ) {
                $_aOutput[] = $this->_getSystemInfoBySection( $_sSection, $_aInfo, $iPrintType );
            }
            return implode( PHP_EOL, $_aOutput );
            
        }
            /**
             * Returns the formatted system information array.
             * @since       3.5.3
             * @internal
             * @return      array       the formatted system information array.
             */
            private function _getFormattedSystemInformation( $asCustomData ) {
                
                $_aData = $this->getAsArray( $asCustomData );
                $_aData = $_aData + array(
                    'Admin Page Framework'  => isset( $_aData['Admin Page Framework'] )
                        ? null
                        : AdminPageFramework_Registry::getInfo(),
                    'WordPress'             => $this->_getSiteInfoWithCache( ! isset( $_aData['WordPress'] ) ),
                    'PHP'                   => $this->_getPHPInfo( ! isset( $_aData['PHP'] ) ),
                    'PHP Error Log'         => $this->_getErrorLogByType( 'php', ! isset( $_aData['PHP Error Log'] ) ),
                    'MySQL'                 => isset( $_aData['MySQL'] )
                        ? null
                        : $this->getMySQLInfo(),    // defined in the utility class.
                    'MySQL Error Log'       => $this->_getErrorLogByType( 'mysql', ! isset( $_aData['MySQL Error Log'] ) ),
                    'Server'                => $this->_getWebServerInfo( ! isset( $_aData['Server'] ) ),
                    'Browser'               => $this->_getClientInfo( ! isset( $_aData['Browser'] ) ),
                );
                
                // Dropping empty elements allows the user to remove a section by setting an empty section.
                return array_filter( $_aData );
                
            }        
            /**
             * Returns the system information by section.
             * @since       3.5.3
             * @return      string      The system information by section.
             */
            private function _getSystemInfoBySection( $sSectionName, $aData, $iPrintType ) {
                switch ( $iPrintType ) {
                    default:
                    case 1: // use the framework readable representation of arrays.
                        return $this->getReadableArrayContents( $sSectionName, $aData, 32 ) . PHP_EOL;
                    case 2: // use print_r()
                        return "[{$sSectionName}]" . PHP_EOL
                            . print_r( $aData, true ) . PHP_EOL;
                }                      
            }
            /**
             * Returns a client information
             * 
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             */
            private function _getClientInfo( $bGenerateInfo=true ) {
                 
                if ( ! $bGenerateInfo ) {
                    return '';
                }
                 
                // Check the browscap value in the ini file first to prevent warnings from being populated
                $_aBrowser = @ini_get( 'browscap' ) 
                    ? get_browser( $_SERVER['HTTP_USER_AGENT'], true )
                    : array();
                unset( $_aBrowser['browser_name_regex'] );  // this element causes output to be blank
                return empty( $_aBrowser ) 
                    ? __( 'No browser information found.', 'admin-page-framework' )
                    : $_aBrowser;
                   
            }
            
            /**
             * Returns a error log by type.
             * 
             * @since       3.5.3
             * @return      string      The found error log.
             * @param       string      $sType          The error log type. Either 'php' or 'mysql' is accepted.
             * @param       boolean     $bGenerateInfo  Whether to generate a log. This is for the caller method to reduce a conditinal statement.
             */
            private function _getErrorLogByType( $sType, $bGenerateInfo=true ) {

                if ( ! $bGenerateInfo ) {
                    return '';
                }
                switch ( $sType ) {
                    default:
                    case 'php':
                        $_sLog = $this->getPHPErrorLog( 200 );
                        break;
                    case 'mysql':
                        $_sLog = $this->getMySQLErrorLog( 200 );
                        break;
                }
                return empty( $_sLog )
                    ? $this->oMsg->get( 'no_log_found' )
                    : $_sLog;            
                
            }

            /**
             * Caches the WordPress installed site information.
             */
            static private $_aSiteInfo;
            /**
             * Returns the Wordpress installed site. 
             * 
             * Uses a cache if stored in a previous call.
             * 
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             * @return      array      The generated site information array.
             */
            private function _getSiteInfoWithCache( $bGenerateInfo=true ) {
                
                if ( ! $bGenerateInfo || isset( self::$_aSiteInfo ) ) {
                    return self::$_aSiteInfo;
                }
                self::$_aSiteInfo = self::_getSiteInfo();
                return self::$_aSiteInfo;
                
            }
                /**
                 * Returns the WordPress site information.
                 * @internal
                 * @since       3.5.3
                 * @return      array       The WordPress site information.
                 */
                private function _getSiteInfo() {
                    global $wpdb;
                    return array(
                        __( 'Version', 'admin-page-framework' )                     => $GLOBALS[ 'wp_version' ],
                        __( 'Language', 'admin-page-framework' )                    => $this->getSiteLanguage(),
                        __( 'Memory Limit', 'admin-page-framework' )                => $this->getReadableBytes( $this->getNumberOfReadableSize( WP_MEMORY_LIMIT ) ),
                        __( 'Multi-site', 'admin-page-framework' )                  => $this->getAOrB( is_multisite(), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ), 
                        __( 'Permalink Structure', 'admin-page-framework' )         => get_option( 'permalink_structure' ), 
                        __( 'Active Theme', 'admin-page-framework' )                => $this->_getActiveThemeName(),
                        __( 'Registered Post Statuses', 'admin-page-framework' )    => implode( ', ', get_post_stati() ),
                        'WP_DEBUG'                                                  => $this->getAOrB( $this->isDebugModeEnabled(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        'WP_DEBUG_LOG'                                              => $this->getAOrB( $this->isDebugLogEnabled(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        'WP_DEBUG_DISPLAY'                                          => $this->getAOrB( $this->isDebugDisplayEnabled(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        __( 'Table Prefix', 'admin-page-framework' )                => $wpdb->prefix,
                        __( 'Table Prefix Length', 'admin-page-framework' )         => strlen( $wpdb->prefix ),
                        __( 'Table Prefix Status', 'admin-page-framework' )         => $this->getAOrB( strlen( $wpdb->prefix ) > 16, $this->oMsg->get( 'too_long' ), $this->oMsg->get( 'acceptable' ) ),
                        'wp_remote_post()'                                          => $this->_getWPRemotePostStatus(),
                        'wp_remote_get()'                                           => $this->_getWPRemoteGetStatus(),
                        __( 'Multibite String Extension', 'admin-page-framework' )  => $this->getAOrB( function_exists( 'mb_detect_encoding' ), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        __( 'WP_CONTENT_DIR Writeable', 'admin-page-framework' )    => $this->getAOrB( is_writable( WP_CONTENT_DIR ), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ), 
                        __( 'Active Plugins', 'admin-page-framework' )              => PHP_EOL . $this->_getActivePlugins(),
                        __( 'Network Active Plugins', 'admin-page-framework' )      => PHP_EOL . $this->_getNetworkActivePlugins(),
                        __( 'Constants', 'admin-page-framework' )                   => $this->_getDefinedConstants( 'user' ),
                    );                        
                }
                    /**
                     * 
                     * @since       3.5.12
                     * @return      stirng|array
                     */
                    private function _getDefinedConstants( $asCategories=null, $asRemovingCategories=null ) {
                        $_asConstants = $this->getDefinedConstants( $asCategories, $asRemovingCategories );
                        if ( ! is_array( $_asConstants ) ) {
                            return $_asConstants;
                        }
                        if ( isset( $_asConstants[ 'user' ] ) ) {
                            $_asConstants[ 'user' ] = array(
                                'AUTH_KEY'              => '__masked__',
                                'SECURE_AUTH_KEY'       => '__masked__',
                                'LOGGED_IN_KEY'         => '__masked__',
                                'NONCE_KEY'             => '__masked__',
                                'AUTH_SALT'             => '__masked__',
                                'SECURE_AUTH_SALT'      => '__masked__',
                                'LOGGED_IN_SALT'        => '__masked__',
                                'NONCE_SALT'            => '__masked__',
                                'COOKIEHASH'            => '__masked__',
                                'USER_COOKIE'           => '__masked__',
                                'PASS_COOKIE'           => '__masked__',
                                'AUTH_COOKIE'           => '__masked__',
                                'SECURE_AUTH_COOKIE'    => '__masked__',
                                'LOGGED_IN_COOKIE'      => '__masked__',
                                'TEST_COOKIE'           => '__masked__',      
                                'DB_USER'               => '__masked__',      
                                'DB_PASSWORD'           => '__masked__',      
                                'DB_HOST'               => '__masked__',      
                            ) + $_asConstants[ 'user' ];
                        }
                        return $_asConstants;
                    }
                
                /**
                 * Returns the active theme name.
                 */
                private function _getActiveThemeName() {
                    
                    // If the WordPress version is less than 3.4,
                    if ( version_compare( $GLOBALS['wp_version'], '3.4', '<' ) ) {
                        $_aThemeData = get_theme_data( get_stylesheet_directory() . '/style.css' );
                        return $_aThemeData['Name'] . ' ' . $_aThemeData['Version'];
                    } 
                    
                    $_oThemeData = wp_get_theme();
                    return $_oThemeData->Name . ' ' . $_oThemeData->Version;
                    
                }   
                /**
                 * Returns a list of active plugins.
                 * 
                 * @return      string
                 */
                private function _getActivePlugins() {
                
                    $_aPluginList       = array();
                    $_aActivePlugins    = get_option( 'active_plugins', array() );
                    foreach ( get_plugins() as $_sPluginPath => $_aPlugin ) {
                        if ( ! in_array( $_sPluginPath, $_aActivePlugins ) ) {
                            continue;
                        }
                        $_aPluginList[] = '    ' . $_aPlugin['Name'] . ': ' . $_aPlugin['Version'];
                    }
                    return implode( PHP_EOL, $_aPluginList );
                    
                } 
                /**
                 * Returns a list of network-activated plugins.
                 * @return      string
                 */
                private function _getNetworkActivePlugins() {
                    
                    if ( ! is_multisite() ) {
                        return '';
                    }
                    $_aPluginList       = array();
                    $_aActivePlugins    = get_site_option( 'active_sitewide_plugins', array() );
                    foreach ( wp_get_active_network_plugins() as $_sPluginPath ) {
                        if ( ! array_key_exists( plugin_basename( $_sPluginPath ), $_aActivePlugins ) ) {
                            continue;
                        }
                        $_aPlugin       = get_plugin_data( $_sPluginPath );
                        $_aPluginList[] = '    ' . $_aPlugin['Name'] . ' :' . $_aPlugin['Version'];
                    }
                    return implode( PHP_EOL, $_aPluginList );
                    
                }
                
                /**
                 * Checks if the wp_remote_post() function is functioning.
                 * 
                 * @return      string
                 */
                private function _getWPRemotePostStatus() {
                    
                    $_vResponse = $this->getTransient( 'apf_rp_check' );
                    $_vResponse = false === $_vResponse
                        ? wp_remote_post( 
                            // 'https://www.paypal.com/cgi-bin/webscr', 
                            add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) ),
                            array(
                                'sslverify'     => false,
                                'timeout'       => 60,
                                'body'          => array( 'apf_remote_request_test' => '_testing', 'cmd' => '_notify-validate' ),
                            )
                        )
                        : $_vResponse;
                    $this->setTransient( 'apf_rp_check', $_vResponse, 60 );
                    return $this->getAOrB( $this->_isHttpRequestError( $_vResponse ), $this->oMsg->get( 'not_functional' ), $this->oMsg->get( 'functional' ) );
                        
                }   
                /**
                 * Checks if the wp_remote_post() function is functioning.
                 * 
                 * @return      string
                 */
                private function _getWPRemoteGetStatus() {
                    
                    $_vResponse = $this->getTransient( 'apf_rg_check' );
                    $_vResponse = false === $_vResponse
                        ? wp_remote_get( 
                            add_query_arg( $_GET + array( 'apf_remote_request_test' => '_testing' ), admin_url( $GLOBALS['pagenow'] ) ),
                            array(
                                'sslverify'     => false,
                                'timeout'       => 60,
                            )
                        )
                        : $_vResponse;
                    $this->setTransient( 'apf_rg_check', $_vResponse, 60 );
                    return $this->getAOrB( $this->_isHttpRequestError( $_vResponse ), $this->oMsg->get( 'not_functional' ), $this->oMsg->get( 'functional' ) );
                    
                }       
                    /**
                     * Checks the HTTP request response has an error.
                     * @since       3.5.3
                     * @return      bool
                     */
                    private function _isHttpRequestError( $mResponse ) {
                        
                        // if ( ! is_wp_error( $_vResponse ) && $_vResponse['response']['code'] >= 200 && $_vResponse['response']['code'] < 300 ) {
                        //  echo 'no error' .
                        // }
                        if ( is_wp_error( $mResponse ) ) {
                            return true;
                        }
                        if ( $mResponse['response']['code'] < 200 ) {
                            return true;
                        }
                        if ( $mResponse['response']['code'] >= 300 ) {
                            return true;
                        }
                        return false;
                    }
                    
            /**
             * Caches the php information.
             * @since       3.4.6
             */
            static private $_aPHPInfo;
            
            /**
             * Returns the PHP information.
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             */
            private function _getPHPInfo( $bGenerateInfo=true ) {
                
                if ( ! $bGenerateInfo || isset( self::$_aPHPInfo ) ) {
                    return self::$_aPHPInfo;
                }
                
                $_oErrorReporting   = new AdminPageFramework_ErrorReporting;
                self::$_aPHPInfo = array(
                    __( 'Version', 'admin-page-framework' )                 => phpversion(),
                    __( 'Safe Mode', 'admin-page-framework' )               => $this->getAOrB( @ini_get( 'safe_mode' ), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ), 
                    __( 'Memory Limit', 'admin-page-framework' )            => @ini_get( 'memory_limit' ),
                    __( 'Upload Max Size', 'admin-page-framework' )         => @ini_get( 'upload_max_filesize' ),
                    __( 'Post Max Size', 'admin-page-framework' )           => @ini_get( 'post_max_size' ),
                    __( 'Upload Max File Size', 'admin-page-framework' )    => @ini_get( 'upload_max_filesize' ),
                    __( 'Max Execution Time', 'admin-page-framework' )      => @ini_get( 'max_execution_time' ),
                    __( 'Max Input Vars', 'admin-page-framework' )          => @ini_get( 'max_input_vars' ),
                    __( 'Argument Separator', 'admin-page-framework' )      => @ini_get( 'arg_separator.output' ),
                    __( 'Allow URL File Open', 'admin-page-framework' )     => $this->getAOrB( @ini_get( 'allow_url_fopen' ),    $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ),
                    __( 'Display Errors', 'admin-page-framework' )          => $this->getAOrB( @ini_get( 'display_errors' ),     $this->oMsg->get( 'on' ), $this->oMsg->get( 'off' ) ),
                    __( 'Log Errors', 'admin-page-framework' )              => $this->getAOrB( @ini_get( 'log_errors' ),         $this->oMsg->get( 'on' ), $this->oMsg->get( 'off' ) ),
                    __( 'Error log location', 'admin-page-framework' )      => @ini_get( 'error_log' ),
                    __( 'Error Reporting Level', 'admin-page-framework' )   => $_oErrorReporting->getErrorLevel(),
                    __( 'FSOCKOPEN', 'admin-page-framework' )               => $this->getAOrB( function_exists( 'fsockopen' ),   $this->oMsg->get( 'supported' ), $this->oMsg->get( 'not_supported' ) ),
                    __( 'cURL', 'admin-page-framework' )                    => $this->getAOrB( function_exists( 'curl_init' ),   $this->oMsg->get( 'supported' ), $this->oMsg->get( 'not_supported' ) ),
                    __( 'SOAP', 'admin-page-framework' )                    => $this->getAOrB( class_exists( 'SoapClient' ),     $this->oMsg->get( 'supported' ), $this->oMsg->get( 'not_supported' ) ),
                    __( 'SUHOSIN', 'admin-page-framework' )                 => $this->getAOrB( extension_loaded( 'suhosin' ),    $this->oMsg->get( 'supported' ), $this->oMsg->get( 'not_supported' ) ),
                    'ini_set()'                                             => $this->getAOrB( function_exists( 'ini_set' ),     $this->oMsg->get( 'supported' ), $this->oMsg->get( 'not_supported' ) ),
                ) 
                + $this->getPHPInfo()
                + array( 
                    __( 'Constants', 'admin-page-framework' )               => $this->_getDefinedConstants( null, 'user' )
                )
                ;
                
                return self::$_aPHPInfo;
                
            }
                      
            /**
             * Returns the web server information.
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             */                      
            private function _getWebServerInfo( $bGenerateInfo=true ) {
                        
                return $bGenerateInfo 
                    ? array(
                        __( 'Web Server', 'admin-page-framework' )                  => $_SERVER['SERVER_SOFTWARE'],
                        'SSL'                                                       => $this->getAOrB( is_ssl(), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ),
                        __( 'Session', 'admin-page-framework' )                     => $this->getAOrB( isset( $_SESSION ), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        __( 'Session Name', 'admin-page-framework' )                => esc_html( @ini_get( 'session.name' ) ),
                        __( 'Session Cookie Path', 'admin-page-framework' )         => esc_html( @ini_get( 'session.cookie_path' ) ),
                        __( 'Session Save Path', 'admin-page-framework' )           => esc_html( @ini_get( 'session.save_path' ) ),
                        __( 'Session Use Cookies', 'admin-page-framework' )         => $this->getAOrB( @ini_get( 'session.use_cookies' ), $this->oMsg->get( 'on' ), $this->oMsg->get( 'off' ) ),
                        __( 'Session Use Only Cookies', 'admin-page-framework' )    => $this->getAOrB( @ini_get( 'session.use_only_cookies' ), $this->oMsg->get( 'on' ), $this->oMsg->get( 'off' ) ),
                    ) + $_SERVER
                    : '';
                
            }
    
 
}