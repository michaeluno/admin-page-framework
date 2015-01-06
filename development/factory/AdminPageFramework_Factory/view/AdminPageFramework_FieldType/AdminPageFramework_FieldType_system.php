<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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

        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        /*    
         * The below function will be triggered when a new repeatable field is added. 
         * 
         * Use the registerAPFCallback method to register a callback.
         * Available callbacks are:
         *     added_repeatable_field - triggered when a repeatable field gets repeated. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     removed_repeatable_field - triggered when a repeatable field gets removed. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     sorted_fields - triggered when a sortable field gets sorted. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         * */
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery().registerAPFCallback( {                
    
        /**
         * The repeatable field callback.
         * 
         * @param    object    oCopiedNode
         * @param    string    the field type slug
         * @param    string    the field container tag ID
         * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */
        added_repeatable_field: function( oCopiedNode, sFieldType, sFieldTagID, iCallType ) {

            /* If it is not this field type, do nothing. */
            if ( jQuery.inArray( sFieldType, $aJSArray ) <= -1 ) { return; }

            /* If the input tag is not found, do nothing  */
            var nodeNewAutoComplete = oCopiedNode.find( 'input.autocomplete' );
            if ( nodeNewAutoComplete.length <= 0 ) { return; }

        },                    
    });
});        
JAVASCRIPTS;
        
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
                    . "<textarea " . $this->generateAttributes( $_aInputAttributes ) . " >"    
                        . esc_textarea( $this->_getSystemInfomation( $aField['value'], $aField['data'], $aField['print_type'] ) )
                    . "</textarea>"
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }    
        /**
         * Returns the system information value for a textarea tag.
         */
        private function _getSystemInfomation( $asValue=null, $asCustomData=null, $iPrintType=1 ) {

            global $wpdb;
            
            $_aData = $this->getAsArray( $asCustomData );
            $_aData = $_aData + array(
                'Admin Page Framework'  => isset( $_aData['Admin Page Framework'] )
                    ? null
                    : AdminPageFramework_Registry::getInfo(),
                'WordPress'             => isset( $_aData['WordPress'] )
                    ? null
                    : $this->_getSiteInfo(),
                'PHP'                   => isset( $_aData['PHP'] )
                    ? null
                    : $this->_getPHPInfo(),
                'PHP Error Log'         => isset( $_aData['PHP Error Log'] )
                    ? null
                    : $this->_getPHPErrorLog(),
                'MySQL'                 => isset( $_aData['MySQL'] )
                    ? null
                    : $this->getMySQLInfo(),
                'MySQL Error Log'       => isset( $_aData['MySQL Error Log'] ) 
                    ? null
                    : $this->_getMySQLErrorLog(),
                'Server'                => isset( $_aData['Server'] )
                    ? null
                    : $this->_getWebServerInfo(),
                'Browser'               => isset( $_aData['Browser'] )
                    ? null
                    : $this->_getClientInfo(),

            );
            
            $_aOutput   = array();
            foreach( $_aData as $_sSection => $_aInfo ) {

                // Skipping an empty element allows the user to remove a section by setting an empty section.
                if ( empty( $_aInfo ) ) { continue; }
            
                switch ( $iPrintType ) {
                    default:
                    case 1: // use the framework readable representation of arrays.
                        $_aOutput[] = $this->getReadableArrayContents( $_sSection, $_aInfo, 32 ) . PHP_EOL;
                        break;
                    case 2: // use print_r()
                        $_aOutput[] = "[{$_sSection}]" . PHP_EOL
                            . print_r( $_aInfo, true ) . PHP_EOL;
                        break;
                }
                
            }
            return implode( PHP_EOL, $_aOutput );
            
        }
            /**
             * Returns a client information
             * 
             * @since       3.4.6
             */
            private function _getClientInfo() {
                 
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
             * Returns a PHP error log.
             * 
             * @since       3.4.6
             */
            private function _getPHPErrorLog() {

                $_sLog = $this->getPHPErrorLog( 200 );
                return empty( $_sLog )
                    ? __( 'No log found.', 'admin-page-framework' )
                    : $_sLog;
            
            }
            
            /**
             * Returns a MySQL error log.
             * 
             * @since       3.4.6
             */
            private function _getMySQLErrorLog() {
                
                $_sLog = $this->getMySQLErrorLog( 200 );
                return empty( $_sLog )
                    ? __( 'No log found.', 'admin-page-framework' )
                    : $_sLog;
                    
            }
            /**
             * Caches the WordPress installed site information.
             */
            static private $_aSiteInfo;
            /**
             * Returns the Wordpress installed site.
             * since        3.4.6
             */
            private function _getSiteInfo() {
                
                if ( isset( self::$_aSiteInfo ) ) {
                    return self::$_aSiteInfo;
                }
                
                global $wpdb;
                
                self::$_aSiteInfo = array(
                    __( 'Version', 'admin-page-framework' )                 => $GLOBALS['wp_version'],
                    __( 'Language', 'admin-page-framework' )                => ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ),
                    __( 'Memory Limit', 'admin-page-framework' )            => $this->getReadableBytes( $this->getNumberOfReadableSize( WP_MEMORY_LIMIT ) ),
                    __( 'Multi-site', 'admin-page-framework' )              => $this->_getYesOrNo( is_multisite() ), 
                    __( 'Permalink Structure', 'admin-page-framework' )     => get_option( 'permalink_structure' ), 
                    __( 'Active Theme', 'admin-page-framework' )            => $this->_getActiveThemeName(),
                    __( 'Registered Post Statuses', 'admin-page-framework' ) => implode( ', ', get_post_stati() ),
                    'WP_DEBUG'                                              => $this->_getEnabledOrDisabled( defined( 'WP_DEBUG' ) && WP_DEBUG ),
                    'WP_DEBUG_LOG'                                            => $this->_getEnabledOrDisabled( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ),
                    'WP_DEBUG_DISPLAY'                                        => $this->_getEnabledOrDisabled( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ),
                    __( 'Table Prefix', 'admin-page-framework' )            => $wpdb->prefix,
                    __( 'Table Prefix Length', 'admin-page-framework' )     => strlen( $wpdb->prefix ),
                    __( 'Table Prefix Status', 'admin-page-framework' )     => strlen( $wpdb->prefix ) >16 ? __( 'Too Long', 'admin-page-framework' ) : __( 'Acceptable', 'admin-page-frmework' ),
                    'wp_remote_post()'                                      => $this->_getWPRemotePostStatus(),
                    'wp_remote_get()'                                       => $this->_getWPRemoteGetStatus(),
                    __( 'WP_CONTENT_DIR Writeable', 'admin-page-framework' ) => $this->_getYesOrNo( is_writable( WP_CONTENT_DIR ) ),
                    __( 'Active Plugins', 'admin-page-framework' )          => PHP_EOL . $this->_getActivePlugins(),
                    __( 'Network Active Plugins', 'admin-page-framework' )  => PHP_EOL . $this->_getNetworkActivePlugins(),
                    __( 'Constants', 'admin-page-framework' )               => $this->getDefinedConstants( 'user' ),
                );          
                return self::$_aSiteInfo;
                
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
                    return $this->_getFunctionalOrNot( ! is_wp_error( $_vResponse ) && $_vResponse['response']['code'] >= 200 && $_vResponse['response']['code'] < 300 ) ;
                    
                }   
                /**
                 * Checks if the wp_remote_post() function is functioning.
                 * 
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
                    return $this->_getFunctionalOrNot( ! is_wp_error( $_vResponse ) && $_vResponse['response']['code'] >= 200 && $_vResponse['response']['code'] < 300 ) ;
                    
                }                  
            /**
             * Caches the php information.
             * @since       3.4.6
             */
            static private $_aPHPInfo;
            
            /**
             * Returns the PHP information.
             * @since       3.4.6
             */
            private function _getPHPInfo() {
                
                if ( isset( self::$_aPHPInfo ) ) {
                    return self::$_aPHPInfo;
                }
                
                $_oErrorReporting   = new AdminPageFramework_ErrorReporting;
                self::$_aPHPInfo = array(
                    __( 'Version', 'admin-page-framework' )                 => phpversion(),
                    __( 'Safe Mode', 'admin-page-framework' )               => $this->_getYesOrNo( @ini_get( 'safe_mode' ) ),
                    __( 'Memory Limit', 'admin-page-framework' )            => @ini_get( 'memory_limit' ),
                    __( 'Upload Max Size', 'admin-page-framework' )         => @ini_get( 'upload_max_filesize' ),
                    __( 'Post Max Size', 'admin-page-framework' )           => @ini_get( 'post_max_size' ),
                    __( 'Upload Max File Size', 'admin-page-framework' )    => @ini_get( 'upload_max_filesize' ),
                    __( 'Max Execution Time', 'admin-page-framework' )      => @ini_get( 'max_execution_time' ),
                    __( 'Max Input Vars', 'admin-page-framework' )          => @ini_get( 'max_input_vars' ),
                    __( 'Argument Separator', 'admin-page-framework' )      => @ini_get( 'arg_separator.output' ),
                    __( 'Allow URL File Open', 'admin-page-framework' )     => $this->_getYesOrNo( @ini_get( 'allow_url_fopen' ) ),
                    __( 'Display Errors', 'admin-page-framework' )          => $this->_getOnOrOff( @ini_get( 'display_errors' ) ),
                    __( 'Log Errors', 'admin-page-framework' )              => $this->_getOnOrOff( @ini_get( 'log_errors' ) ),
                    __( 'Error log location', 'admin-page-framework' )      => @ini_get( 'error_log' ),
                    __( 'Error Reporting Level', 'admin-page-framework' )   => $_oErrorReporting->getErrorLevel(),
                    __( 'FSOCKOPEN', 'admin-page-framework' )               => $this->_getSupportedOrNot( function_exists( 'fsockopen' ) ),
                    __( 'cURL', 'admin-page-framework' )                    => $this->_getSupportedOrNot( function_exists( 'curl_init' ) ),
                    __( 'SOAP', 'admin-page-framework' )                    => $this->_getSupportedOrNot( class_exists( 'SoapClient' ) ),
                    __( 'SUHOSIN', 'admin-page-framework' )                 => $this->_getSupportedOrNot( extension_loaded( 'suhosin' ) ),
                    'ini_set()'                                             => $this->_getSupportedOrNot( function_exists( 'ini_set' ) ),
                ) 
                + $this->getPHPInfo()
                + array( 
                    __( 'Constants', 'admin-page-framework' )               => $this->getDefinedConstants( null, 'user' )
                )
                ;
                
                return self::$_aPHPInfo;
                
            }
                      
            /**
             * Returns the web server information.
             * @since       3.4.6
             */                      
            private function _getWebServerInfo() {
                
                return array(
                    __( 'Web Server', 'admin-page-framework' )                  => $_SERVER['SERVER_SOFTWARE'],
                    'SSL'                                                       => $this->_getYesOrNo( is_ssl() ),
                    __( 'Session', 'admin-page-framework' )                     => $this->_getEnabledOrDisabled( isset( $_SESSION ) ),
                    __( 'Session Name', 'admin-page-framework' )                => esc_html( @ini_get( 'session.name' ) ),
                    __( 'Session Cookie Path', 'admin-page-framework' )         => esc_html( @ini_get( 'session.cookie_path' ) ),
                    __( 'Session Save Path', 'admin-page-framework' )           => esc_html( @ini_get( 'session.save_path' ) ),
                    __( 'Session Use Cookies', 'admin-page-framework' )         => $this->_getOnOrOff( @ini_get( 'session.use_cookies' ) ),
                    __( 'Session Use Only Cookies', 'admin-page-framework' )    => $this->_getOnOrOff( @ini_get( 'session.use_only_cookies' ) ),                                    
                ) + $_SERVER;                
                
            }
                    

    /**
     * Methods for labels.
     */
    private function _getYesOrNo( $bBoolean ) {
        return $bBoolean 
            ? __( 'Yes', 'admin-page-framework' )
            : __( 'No', 'admin-page-framework' );
    }
    private function _getEnabledOrDisabled( $bBoolean ) {
        return $bBoolean 
            ? __( 'Enabled', 'admin-page-framework' )
            : __( 'Disabled', 'admin-page-framework' );
    }
    private function _getOnOrOff( $bBoolean ) {
        return $bBoolean
            ? __( 'On', 'admin-page-framework' )
            : __( 'Off', 'admin-page-framework' );
    }
    private function _getSupportedOrNot( $bBoolean ) {
        return $bBoolean
            ? __( 'Supported', 'admin-page-framework' )
            : __( 'Not supported', 'admin-page-framework' );                
    }
    private function _getFunctionalOrNot( $bBoolean ) {
        return $bBoolean
            ? __( 'Functional', 'admin-page-framework' )
            : __( 'Not functional', 'admin-page-framework' );                                
    }
 
}