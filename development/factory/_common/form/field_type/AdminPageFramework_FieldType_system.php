<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Defines the `system` field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**data** - (optional, array) an associative array that holds the data to display. The following items are embedded by default. To remove an item, pass an empty value with the key.
 *          <ul>
 *              <li>`Current Time` - the current time</li>
 *              <li>`Admin Page Framework` - information of used Admin Page Framework</li>
 *              <li>`WordPress` - information of installed WordPress</li>
 *              <li>`PHP` - information of installed PHP</li>
 *              <li>`Server` - information of the server</li>
 *              <li>`PHP Error Log` - PHP error log</li>
 *              <li>`MySQL` - MySQL</li>
 *              <li>`MySQL Error Log` - MySQL error log</li>
 *              <li>`Browser` - Browser</li>
 *          </ul>
 *     </li>
 *     <li>**print_type** - [3.3.6+] (optional, integer) Indicates how the data array should be displayed. 1: readable array representation. 2. the output of the print_r() function. Default: `1`.</li>
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'system_information',
 *      'type'          => 'system',
 *      'title'         => __( 'System Information', 'admin-page-framework-loader' ),
 *      'data'          => array(
 *          'Custom Data'           => __( 'Here you can insert your own custom data with the data argument.', 'admin-page-framework-loader' ),
 *
 *          // To remove items, set empty values
 *          'Current Time'          => '',
 *          'Admin Page Framework'  => '',
 *      ),
 *      'save'          => false,
 *  ),
 * </code>
 *
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/system.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       3.3.0
 */
class AdminPageFramework_FieldType_system extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     *
     * The slug is used for the type key in a field definition array.
     * <code>
     * $this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => 'system',        // <--- THIS PART
     *          'field_id'      => '...',
     *          'title'         => '...',
     *      )
     *  );
     * </code>
     * @var         array
     */
    public $aFieldTypeSlugs = array( 'system', );

    /**
     * Defines the default key-values of this field type.
     *
     * The keys are used for the field definition array.
     * <code>
     *     $this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => '...',
     *          'field_id'      => '...',
     *          'my_custom_key' => '...',    // <-- THIS PART
     *      )
     *  );
     * </code>
     * @var             array
     * @remark          `$_aDefaultKeys` holds shared default key-values defined in the base class.
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
            // 'onclick'       => 'this.focus();this.select()', // @deprecated 3.8.24
        ),
    );

    /**
     * The user constructor.
     *
     * Loaded at the end of the constructor.
     * @internal
     */
    protected function construct() {}

    /**
     * Loads the field type necessary components.
     *
     * This method is triggered when a field definition array that calls this field type is parsed.
     * @internal
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
     * @internal
     * @return      array
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
     * @return      array
     * @internal
     */
    protected function getEnqueuingStyles() {
        return array();
    }


    /**
     * Returns the field type specific JavaScript script.
     * @internal
     * @return      string
     */
    protected function getScripts() {
        return '';
    }

    /**
     * Returns IE specific CSS rules.
     *
     * @internal
     * @return      string
     */
    protected function getIEStyles() {
        return '';
    }

    /**
     * Returns the field type specific CSS rules.
     *
     * @internal
     * @return      string
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
     */
    /**
     * Returns the output of the field type.
     *
     * @internal
     * @param       array   $aField
     * @return      string
     */
    protected function getField( $aField ) {

        $_aInputAttributes             = $aField[ 'attributes' ];
        $_aInputAttributes[ 'class' ] .= ' system';
        unset( $_aInputAttributes[ 'value' ] );
        return
            $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . ( $aField[ 'label' ] && ! $aField[ 'repeatable' ]
                        ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                                . $aField[ 'label' ]
                            . "</span>"
                        : ""
                    )
                    . "<textarea " . $this->getAttributes( $_aInputAttributes ) . " >"
                        . esc_textarea( $this->___getSystemInformation( $aField[ 'value' ], $aField[ 'data' ], $aField[ 'print_type' ] ) )
                    . "</textarea>"
                    . $aField[ 'after_input' ]
                . "</label>"
            . "</div>"
            . $aField[ 'after_label' ];

    }
        /**
         * Returns the system information value for a textarea tag.
         *
         * @return      string       The human readable system information.
         * @param       array|string $asValue
         * @param       array|string $asCustomData
         * @param       integer      $iPrintType
         * @internal
         */
        private function ___getSystemInformation( $asValue=null, $asCustomData=null, $iPrintType=1 ) {

            if ( isset( $asValue ) ) {
                return $asValue;
            }

            $_aOutput   = array();
            foreach( $this->___getFormattedSystemInformation( $asCustomData ) as $_sSection => $_aInfo ) {
                $_aOutput[] = $this->_____getSystemInfoBySection( $_sSection, $_aInfo, $iPrintType );
            }
            return implode( PHP_EOL, $_aOutput );

        }
            /**
             * Returns the formatted system information array.
             * @since       3.5.3
             * @internal
             * @param       string|array $asCustomData
             * @return      array        The formatted system information array.
             */
            private function ___getFormattedSystemInformation( $asCustomData ) {

                $_aData = $this->getAsArray( $asCustomData );
                $_aData = $_aData + array(
                    'WordPress'             => $this->___getSiteInfoWithCache( ! isset( $_aData[ 'WordPress' ] ) ),
                    'PHP'                   => $this->_getPHPInfo( ! isset( $_aData[ 'PHP' ] ) ),
                    'PHP Error Log'         => $this->___getErrorLogByType( 'php', ! isset( $_aData[ 'PHP Error Log' ] ) ),
                    'MySQL'                 => isset( $_aData[ 'MySQL' ] )
                        ? null
                        : $this->getMySQLInfo(),    // defined in the utility class.
                    'MySQL Error Log'       => $this->___getErrorLogByType( 'mysql', ! isset( $_aData[ 'MySQL Error Log' ] ) ),
                    'Server'                => $this->___getWebServerInfo( ! isset( $_aData[ 'Server' ] ) ),
                    'Browser'               => $this->___getClientInfo( ! isset( $_aData[ 'Browser' ] ) ),
                    'Admin Page Framework'  => isset( $_aData[ 'Admin Page Framework' ] )
                        ? null
                        : AdminPageFramework_Registry::getInfo(),
                );

                // Dropping empty elements allows the user to remove a section by setting an empty section.
                return array_filter( $_aData );

            }
            /**
             * Returns the system information by section.
             * @since       3.5.3
             * @return      string      The system information by section.
             * @param       string      $sSectionName
             * @param       array       $aData
             * @param       integer     $iPrintType
             * @internal
             */
            private function _____getSystemInfoBySection( $sSectionName, $aData, $iPrintType ) {
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
             * @internal
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo parameter. This is to reduce conditional statement in the caller method.
             * @param       boolean     $bGenerateInfo
             * @return      string
             */
            private function ___getClientInfo( $bGenerateInfo=true ) {

                if ( ! $bGenerateInfo ) {
                    return '';
                }

                // Check the browscap value in the ini file first to prevent warnings from being populated
                $_aBrowser = @ini_get( 'browscap' )
                    ? get_browser( $_SERVER[ 'HTTP_USER_AGENT' ], true )
                    : array();
                unset( $_aBrowser[ 'browser_name_regex' ] );  // this element causes output to be blank
                return empty( $_aBrowser )
                    ? __( 'No browser information found.', 'admin-page-framework' )
                    : $_aBrowser;

            }

            /**
             * Returns a error log by type.
             *
             * @internal
             * @since       3.5.3
             * @return      string      The found error log.
             * @param       string      $sType          The error log type. Either 'php' or 'mysql' is accepted.
             * @param       boolean     $bGenerateInfo  Whether to generate a log. This is for the caller method to reduce a conditinal statement.
             */
            private function ___getErrorLogByType( $sType, $bGenerateInfo=true ) {

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
             * @internal
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             * @param       boolean     $bGenerateInfo
             * @return      array       The generated site information array.
             */
            private function ___getSiteInfoWithCache( $bGenerateInfo=true ) {

                if ( ! $bGenerateInfo || isset( self::$_aSiteInfo ) ) {
                    return self::$_aSiteInfo;
                }
                self::$_aSiteInfo = self::___getSiteInfo();
                return self::$_aSiteInfo;

            }
                /**
                 * Returns the WordPress site information.
                 *
                 * @internal
                 * @since       3.5.3
                 * @return      array       The WordPress site information.
                 */
                private function ___getSiteInfo() {
                    global $wpdb;
                    return array(
                        __( 'Version', 'admin-page-framework' )                     => $GLOBALS[ 'wp_version' ],
                        __( 'Language', 'admin-page-framework' )                    => $this->getSiteLanguage(),
                        __( 'Memory Limit', 'admin-page-framework' )                => $this->getReadableBytes( $this->getNumberOfReadableSize( WP_MEMORY_LIMIT ) ),
                        __( 'Multi-site', 'admin-page-framework' )                  => $this->getAOrB( is_multisite(), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ),
                        __( 'Permalink Structure', 'admin-page-framework' )         => get_option( 'permalink_structure' ),
                        __( 'Active Theme', 'admin-page-framework' )                => $this->___getActiveThemeName(),
                        __( 'Registered Post Statuses', 'admin-page-framework' )    => implode( ', ', get_post_stati() ),
                        'WP_DEBUG'                                                  => $this->getAOrB( $this->isDebugMode(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        'WP_DEBUG_LOG'                                              => $this->getAOrB( $this->isDebugLogEnabled(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        'WP_DEBUG_DISPLAY'                                          => $this->getAOrB( $this->isDebugDisplayEnabled(), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        __( 'Table Prefix', 'admin-page-framework' )                => $wpdb->prefix,
                        __( 'Table Prefix Length', 'admin-page-framework' )         => strlen( $wpdb->prefix ),
                        __( 'Table Prefix Status', 'admin-page-framework' )         => $this->getAOrB( strlen( $wpdb->prefix ) > 16, $this->oMsg->get( 'too_long' ), $this->oMsg->get( 'acceptable' ) ),
                        'wp_remote_post()'                                          => $this->___getWPRemotePostStatus(),
                        'wp_remote_get()'                                           => $this->___getWPRemoteGetStatus(),
                        __( 'Multibite String Extension', 'admin-page-framework' )  => $this->getAOrB( function_exists( 'mb_detect_encoding' ), $this->oMsg->get( 'enabled' ), $this->oMsg->get( 'disabled' ) ),
                        __( 'WP_CONTENT_DIR Writeable', 'admin-page-framework' )    => $this->getAOrB( is_writable( WP_CONTENT_DIR ), $this->oMsg->get( 'yes' ), $this->oMsg->get( 'no' ) ),
                        __( 'Active Plugins', 'admin-page-framework' )              => PHP_EOL . $this->___getActivePlugins(),
                        __( 'Network Active Plugins', 'admin-page-framework' )      => PHP_EOL . $this->___getNetworkActivePlugins(),
                        __( 'Constants', 'admin-page-framework' )                   => $this->___getDefinedConstants( 'user' ),
                    );
                }
                    /**
                     *
                     * @since       3.5.12
                     * @param       array|string|null $asCategories
                     * @param       array|string|null $asRemovingCategories
                     * @return      string|array
                     * @internal
                     */
                    private function ___getDefinedConstants( $asCategories=null, $asRemovingCategories=null ) {
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
                 * @internal
                 * @return      string
                 */
                private function ___getActiveThemeName() {

                    // If the WordPress version is less than 3.4,
                    if ( version_compare( $GLOBALS[ 'wp_version' ], '3.4', '<' ) ) {
                        $_aThemeData = get_theme_data( get_stylesheet_directory() . '/style.css' );
                        return $_aThemeData[ 'Name' ] . ' ' . $_aThemeData[ 'Version' ];
                    }

                    $_oThemeData = wp_get_theme();
                    return $_oThemeData->Name . ' ' . $_oThemeData->Version;

                }
                /**
                 * Returns a list of active plugins.
                 *
                 * @return      string
                 * @internal
                 */
                private function ___getActivePlugins() {

                    $_aPluginList       = array();
                    $_aActivePlugins    = get_option( 'active_plugins', array() );
                    foreach ( get_plugins() as $_sPluginPath => $_aPlugin ) {
                        if ( ! in_array( $_sPluginPath, $_aActivePlugins ) ) {
                            continue;
                        }
                        $_aPluginList[] = '    ' . $_aPlugin[ 'Name' ] . ': ' . $_aPlugin[ 'Version' ];
                    }
                    return implode( PHP_EOL, $_aPluginList );

                }
                /**
                 * Returns a list of network-activated plugins.
                 * @return      string
                 * @internal
                 */
                private function ___getNetworkActivePlugins() {

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
                        $_aPluginList[] = '    ' . $_aPlugin[ 'Name' ] . ' :' . $_aPlugin[ 'Version' ];
                    }
                    return implode( PHP_EOL, $_aPluginList );

                }

                /**
                 * Checks if the wp_remote_post() function is functioning.
                 *
                 * @return      string
                 * @internal
                 */
                private function ___getWPRemotePostStatus() {

                    $_vResponse = $this->getTransient( 'apf_rp_check' );
                    $_vResponse = false === $_vResponse
                        ? wp_remote_post(
                            // 'https://www.paypal.com/cgi-bin/webscr',
                            add_query_arg( $_GET, admin_url( $GLOBALS[ 'pagenow' ] ) ),
                            array(
                                'sslverify'     => false,
                                'timeout'       => 60,
                                'body'          => array( 'apf_remote_request_test' => '_testing', 'cmd' => '_notify-validate' ),
                            )
                        )
                        : $_vResponse;
                    $this->setTransient( 'apf_rp_check', $_vResponse, 60 );
                    return $this->getAOrB( $this->___isHttpRequestError( $_vResponse ), $this->oMsg->get( 'not_functional' ), $this->oMsg->get( 'functional' ) );

                }
                /**
                 * Checks if the wp_remote_post() function is functioning.
                 *
                 * @return      string
                 * @internal
                 */
                private function ___getWPRemoteGetStatus() {

                    $_aoResponse = $this->getTransient( 'apf_rg_check' );
                    $_aoResponse = false === $_aoResponse
                        ? wp_remote_get(
                            add_query_arg( $_GET + array( 'apf_remote_request_test' => '_testing' ), admin_url( $GLOBALS[ 'pagenow' ] ) ),
                            array(
                                'sslverify'     => false,
                                'timeout'       => 60,
                            )
                        )
                        : $_aoResponse;
                    $this->setTransient( 'apf_rg_check', $_aoResponse, 60 );
                    return $this->getAOrB( $this->___isHttpRequestError( $_aoResponse ), $this->oMsg->get( 'not_functional' ), $this->oMsg->get( 'functional' ) );

                }
                    /**
                     * Checks the HTTP request response has an error.
                     * @since       3.5.3
                     * @param       mixed   $aoResponse        
                     * @return      boolean
                     * @internal
                     */
                    private function ___isHttpRequestError( $aoResponse ) {
                        
                        if ( is_wp_error( $aoResponse ) ) {
                            return true;
                        }
                        if ( $aoResponse[ 'response'][ 'code' ] < 200 ) {
                            return true;
                        }
                        if ( $aoResponse[ 'response' ][ 'code' ] >= 300 ) {
                            return true;
                        }
                        return false;
                        
                    }

            /**
             * Caches the php information.
             * @since       3.4.6
             * @internal
             */
            static private $_aPHPInfo;

            /**
             * Returns the PHP information.
             *
             * @internal
             * @since       3.4.6
             * @since       3.5.3       Added the $bGenerateInfo parameter. This is to reduce conditional statement in the caller method.
             * @param       boolean     $bGenerateInfo
             * @return      array
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
                    __( 'Constants', 'admin-page-framework' )               => $this->___getDefinedConstants( null, 'user' )
                )
                ;

                return self::$_aPHPInfo;

            }

            /**
             * Returns the web server information.
             * @internal
             * @since       3.4.6
             * @since       3.5.3        Added the $bGenerateInfo paramter. This is to reduce conditional statment in the caller method.
             * @param       boolean      $bGenerateInfo
             * @return      array|string
             */
            private function ___getWebServerInfo( $bGenerateInfo=true ) {
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
