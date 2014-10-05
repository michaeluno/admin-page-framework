<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_system' ) ) :
/**
 * Defines the 'system' field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.2.2
 * @internal
 */
class AdminPageFramework_FieldType_system extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     *     $this->addSettingFields(
            array(
                'section_id'    => '...',
                'type'          => 'system',        // <--- THIS PART
                'field_id'      => '...',
                'title'         => '...',
            )
        );
     */
    public $aFieldTypeSlugs = array( 'system', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     *     $this->addSettingFields(
            array(
                'section_id'    => '...',    
                'type'          => '...',
                'field_id'      => '...',
                'my_custom_key' => '...',    // <-- THIS PART
            )
        );
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'data'          =>  array(),        // [3.2.0+] Stores the data to be displayed
        'attributes'    =>    array(
            // 'size'    =>    10,
            // 'cols'          =>  60,
            'rows'          =>  60,
            'autofocus'     => '',
            'disabled'      => '',
            'formNew'       => '',
            'maxlength'     => '',
            'placeholder'   => '',
            'readonly'      => 'ReadOnly',
            'required'      => '',
            'wrap'          => '',  
            'style'         => '',
            'onclick'       => 'this.focus();this.select()',
        ),    
    );

    /**
     * The user constructor.
     * 
     * Loaded at the end of the constructor.
     */
    public function construct() {        
    }
        
    /**
     * Loads the field type necessary components.
     * 
     * This method is triggered when a field definition array that calls this field type is parsed. 
     */ 
    public function setUp() {}    

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
        return "
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
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) { return; }

                        /* If the input tag is not found, do nothing  */
                        var nodeNewAutoComplete = oCopiedNode.find( 'input.autocomplete' );
                        if ( nodeNewAutoComplete.length <= 0 ) { return; }

                    },                    
                });
            });        
        
        " . PHP_EOL;
        
    }

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "
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
        ";
        
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 

        $aInputAttributes = array(
            'type'    =>    'textarea',
        ) + $aField['attributes'];
        $aInputAttributes['class']    .= ' system';
        unset( $aInputAttributes['value'] );
        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<textarea " . $this->generateAttributes( $aInputAttributes ) . " >"    
                        . esc_textarea( $this->_getSystemInfomation( $aField['value'], $aField['data'] ) )
                    . "</textarea>"
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }    
        private function _getSystemInfomation( $asValue=null, $asCustomData=null ) {
            
            static $_aSystemInfo;
            
            global $wpdb;
            
            $_oErrorReporting   = new AdminPageFramework_ErrorReporting;
            $_aSystemInfo       = isset( $_aSystemInfo )
                ? $_aSystemInfo
                : array(
                    'Admin Page Framework'  => array(
                        __( 'Version', 'admin-page-framework' )                 => class_exists( 'AdminPageFramework_Registry' ) ? AdminPageFramework_Registry::Version : '',
                        __( 'Minified', 'admin-page-framework' )                => $this->_getYesOrNo( class_exists( 'AdminPageFramework_Registry' ) && AdminPageFramework_Registry::$bIsMinifiedVersion ),
                    ),
                    'WordPress'             => array(
                        __( 'Version', 'admin-page-framework' )                 => $GLOBALS['wp_version'],
                        __( 'Language', 'admin-page-framework' )                => ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ),
                        __( 'Memory Limit', 'admin-page-framework' )            => $this->_convertBytesToHR( $this->_convertToNumber( WP_MEMORY_LIMIT ) ),
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
                        __( 'WP_CONTENT_DIR Writable', 'admin-page-framework' ) => $this->_getYesOrNo( is_writable( WP_CONTENT_DIR ) ),
                        __( 'Active Plugins', 'admin-page-framework' )          => PHP_EOL . $this->_getActivePlugins(),
                        __( 'Network Active Plugins', 'admin-page-framework' )  => PHP_EOL . $this->_getNetworkActivePlugins(),

                    ),
                    'PHP'                   => array(
                        __( 'Version', 'admin-page-framework' )                 => phpversion(),
                        __( 'Safe Mode', 'admin-page-framework' )               => $this->_getYesOrNo( ini_get( 'safe_mode' ) ),
                        __( 'Memory Limit', 'admin-page-framework' )            => ini_get( 'memory_limit' ),
                        __( 'Upload Max Size', 'admin-page-framework' )         => ini_get( 'upload_max_filesize' ),
                        __( 'Post Max Size', 'admin-page-framework' )           => ini_get( 'post_max_size' ),
                        __( 'Upload Max File Size', 'admin-page-framework' )    => ini_get( 'upload_max_filesize' ),
                        __( 'Max Execution Time', 'admin-page-framework' )      => ini_get( 'max_execution_time' ),
                        __( 'Max Input Vars', 'admin-page-framework' )          => ini_get( 'max_input_vars' ),
                        __( 'Argument Separator', 'admin-page-framework' )      => ini_get( 'arg_separator.output' ),
                        __( 'Allow URL File Open', 'admin-page-framework' )     => $this->_getYesOrNo( ini_get( 'allow_url_fopen' ) ),
                        __( 'Display Errors', 'admin-page-framework' )          => $this->_getOnOrOff( ini_get( 'display_errors' ) ),
                        __( 'Log Errors', 'admin-page-framework' )              => $this->_getOnOrOff( ini_get( 'log_errors' ) ),
                        __( 'Error log location', 'admin-page-framework' )      => ini_get( 'error_log' ),
                        __( 'Error Reporting Level', 'admin-page-framweork' )   => $_oErrorReporting->getErrorLevel(),
                        __( 'FSOCKOPEN', 'admin-page-framework' )               => $this->_getSupportedOrNot( function_exists( 'fsockopen' ) ),
                        __( 'cURL', 'admin-page-framework' )                    => $this->_getSupportedOrNot( function_exists( 'curl_init' ) ),
                        __( 'SOAP', 'admin-page-framework' )                    => $this->_getSupportedOrNot( class_exists( 'SoapClient' ) ),
                        __( 'SUHOSIN', 'admin-page-framework' )                 => $this->_getSupportedOrNot( extension_loaded( 'suhosin' ) ),
                        'ini_set()'                                             => $this->_getSupportedOrNot( function_exists( 'ini_set' ) ),                    
                    
                    ),
                    'MySQL'               => array(
                        __( 'Version', 'admin-page-framework' )                     => $this->_getMySQLVersion(),
                        __( 'Max Allowed Packet', 'admin-page-framework' )          => $this->_getMaxAllowedPacket(),
                        
                    ),      
                    'Server'              => array(
                        __( 'Web Server', 'admin-page-framework' )                  => $_SERVER['SERVER_SOFTWARE'],
                        'SSL'                                                       => $this->_getYesOrNo( is_ssl() ),
                        __( 'Session', 'admin-page-framework' )                     => $this->_getEnabledOrDisabled( isset( $_SESSION ) ),
                        __( 'Session Name', 'admin-page-framework' )                => esc_html( ini_get( 'session.name' ) ),
                        __( 'Session Cookie Path', 'admin-page-framework' )         => esc_html( ini_get( 'session.cookie_path' ) ),
                        __( 'Session Save Path', 'admin-page-framework' )           => esc_html( ini_get( 'session.save_path' ) ),
                        __( 'Session Use Cookies', 'admin-page-framework' )         => $this->_getOnOrOff( ini_get( 'session.use_cookies' ) ),
                        __( 'Session Use Only Cookies', 'admin-page-framework' )    => $this->_getOnOrOff( ini_get( 'session.use_only_cookies' ) ),                                    
                    ),
                    'Browser'             => @get_browser( null, true ),
                );

            $_aData     = $this->getAsArray( $asCustomData );
            $_aOutput   = array();
            foreach( $_aData + $_aSystemInfo as $_sSection => $_aInfo ) {

                // Skipping an empty element allows the user to remove a section by passing an empty section.
                if ( empty( $_aInfo ) ) { continue; }
            
                $_aOutput[] = $this->getReadableArrayContents( $_sSection, $_aInfo, 32 ) . PHP_EOL;
                
            }
            return implode( PHP_EOL, $_aOutput );
            
        }
        
            private function _getMySQLVersion() {
                global $wpdb;
                return $wpdb->use_mysqli
                    ? @mysqli_get_server_info( $wpdb->dbh )
                    : @mysql_get_server_info();

            }
            
            private function _getMaxAllowedPacket() {
                
                global $wpdb;

                $_aRow = $wpdb->get_row( 'SELECT @@global.max_allowed_packet', 'ARRAY_A' );
                return isset( $_aRow[ '@@global.max_allowed_packet' ] )
                    ? $this->_convertBytesToHR( $_aRow[ '@@global.max_allowed_packet' ] )
                    : '';
                    
            }
            
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
            
            private function _convertToNumber( $nSize ) {
                
                $_nReturn     = substr( $nSize, 0, -1 );
                switch( strtoupper( substr( $nSize, -1 ) ) ) {
                    case 'P':
                        $_nReturn *= 1024;
                    case 'T':
                        $_nReturn *= 1024;
                    case 'G':
                        $_nReturn *= 1024;
                    case 'M':
                        $_nReturn *= 1024;
                    case 'K':
                        $_nReturn *= 1024;
                }
                return $_nReturn;
                
            }      
            private function _convertBytesToHR( $nBytes ) {
                $_aUnits    = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
                $_nLog      = log( $nBytes, 1024 );
                $_iPower    = ( int ) $_nLog;
                $_iSize     = pow( 1024, $_nLog - $_iPower );
                return $_iSize . $_aUnits[ $_iPower ];
            }
            
            private function _getActiveThemeName() {
                
                // If the WordPress version is less than 3.4,
                if ( version_compare( $GLOBALS['wp_version'], '3.4', '<' ) ) {                      
                    $_aThemeData = get_theme_data( get_stylesheet_directory() . '/style.css' );
                    return $_aThemeData['Name'] . ' ' . $_aThemeData['Version'];
                } 
                
                $_oThemeData = wp_get_theme();
                return $_oThemeData->Name . ' ' . $_oThemeData->Version;
                
            }
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
            
            private function _getWPRemotePostStatus() {
                
                $_vResponse = $this->getTransient( 'apf_rp_check' );
                $_vResponse = false === $_vResponse
                    ? wp_remote_post( 
                        'https://www.paypal.com/cgi-bin/webscr', 
                        array(
                            'sslverify'     => false,
                            'timeout'       => 60,
                            'body'          => array( 'cmd' => '_notify-validate' ),
                        )
                    )
                    : $_vResponse;
                $this->setTransient( 'apf_rp_check', $_vResponse, 60 );
                return $this->_getFunctionalOrNot( ! is_wp_error( $_vResponse ) && $_vResponse['response']['code'] >= 200 && $_vResponse['response']['code'] < 300 ) ;
                
            }  
        
}
endif;