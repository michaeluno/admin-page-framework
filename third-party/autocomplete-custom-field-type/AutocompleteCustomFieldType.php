<?php
if ( ! class_exists( 'AutoCompleteCustomFieldType' ) ) :
class AutoCompleteCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     *     $this->addSettingFields(
            array(
                'section_id'    => '...',
                'type'          => 'autocomplete',        // <--- THIS PART
                'field_id'      => '...',
                'title'         => '...',
            )
        );
     */
    public $aFieldTypeSlugs = array( 'autocomplete', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     *  <code>$this->addSettingFields(
     *       array(
     *          'section_id'    => '...',    
     *          'type'          => '...',
     *          'field_id'      => '...',
     *          'my_custom_key' => '...',    // <-- THIS PART
     *      )
     *  );</code>
     * @remark  $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'settings'      => null,    // will be set in the constructor.
        'settings2'     => null,    // will be set in the constructor.
        'attributes'    => array(
            'size'      => 10,
            'maxlength' => 400,
        ),    
    );

    /**
     * User constructor.
     * 
     * Loaded at the end of the constructor.
     */
    protected function construct() {

        $_aGet = $_GET;
        unset( $_aGet['post_type'], $_aGet['request'], $_aGet['page'], $_aGet['tab'], $_aGet['settings-updated'] );
        $this->aDefaultKeys['settings']  = $this->getQueryAdminURL( array( 'request' => 'autocomplete', 'post_type' => 'post' ) + $_aGet );
        $this->aDefaultKeys['settings2'] = array(
            'hintText'    => __( 'Type the title of posts.', 'admin-page-framework-demo' ),
        );

        /*
         * If the request key is set in the url and it yields 'autocomplete', return a JSON output and exit.
         */        
        if ( isset( $_GET['request'] ) && 'autocomplete' === $_GET['request'] ) {
            if ( did_action( 'init' ) ) {
                $this->_replyToReturnAutoCompleteRequest();
            } else {            
                add_action( 'init', array( $this, '_replyToReturnAutoCompleteRequest' ) );
            }
        }

    }
    
    /**
     * Responds to the request.
     */
    public function _replyToReturnAutoCompleteRequest() {

        if ( ! $this->_isLoggedIn() ) { exit; }
        if ( ! isset( $_GET['q'] ) ) { exit; }

        $_aGet = $_GET;
        unset( $_aGet['request'], $_aGet['page'], $_aGet['tab'], $_aGet['settings-updated'] );

        $_aData = array();
        $_sType = isset( $_GET['type'] ) ? $_GET['type'] : '';
        switch ( $_sType ) {
            default:
            case 'post':
                $_aData = $this->_searchPosts( $_aGet );
                break;
            case 'user':
                $_aData = $this->_searchUsers( $_aGet );
                break;
        }
     
        exit( json_encode( $_aData ) );
        
    }
        /**
         * Searches users by the given criteria.
         */
        private function _searchUsers( array $aGet=array() ) {
            
            $_aArgs = $aGet + array(
                'number'    => 100,    // the maximum number to return.
            );
            
            // Set the callback to modify the database query string.
            add_action( 'pre_user_query', array( $this, '_replyToModifyMySQLWhereClauseToSearchUsers' ) );
            
            $_oResults = new WP_User_Query( $_aArgs );

            // Format the data
            $_aData = array();
            foreach( $_oResults->results as $_iIndex => $_oUser ) {
                $_aData[ $_iIndex ] = array(
                    'id'    => $_oUser->ID,
                    'name'  => $_oUser->data->display_name,
                );
            }            
            return $_aData;
            
        }
            /**
             * Modifies the WordPress database query.
             */
            public function _replyToModifyMySQLWhereClauseToSearchUsers( $oWPQuery ) {
                
                global $wpdb;
                if ( $oWPQuery->get( 'q' ) ) {
                    $_sSearchTerm = $oWPQuery->get( 'q' );
                    $oWPQuery->query_where .= " AND " . $wpdb->users . ".display_name LIKE '%" . esc_sql( $wpdb->esc_like( $_sSearchTerm ) ) . "%'";
                }    
                                
            }            
        /**
         * Searches posts by the given criteria.
         */
        private function _searchPosts( array $aGet=array() ) {
            
            $_aArgs = $aGet + array(
                'post_type'      => 'post',        
                'post_status'    => 'publish, private',
                'orderby'        => 'title', 
                'order'          => 'ASC',
                'posts_per_page' => 100,    
            );
            if ( isset( $_aArgs['post_types'] ) ) {
                $_aArgs['post_type'] = preg_split( "/[,]\s*/", trim( ( string ) $_aArgs['post_types'] ), 0, PREG_SPLIT_NO_EMPTY );
            }    
            $_aArgs['post_status']    = preg_split( "/[,]\s*/", trim( ( string ) $_aArgs['post_status'] ), 0, PREG_SPLIT_NO_EMPTY );
            $_aArgs['q'] = $_GET['q'] ;

            // Set the callback to modify the database query string.
            add_filter( 'posts_where', array( $this, '_replyToModifyMySQLWhereClauseToSearchPosts' ), 10, 2 );
            $_oResults = new WP_Query( $_aArgs );                    

            // Format the data
            $_aData = array();
            foreach( $_oResults->posts as $__iIndex => $__oPost ) {
                $_aData[ $__iIndex ] = array(
                    'id'    => $__oPost->ID,
                    'name'  => $__oPost->post_title,
                );
            }            
            return $_aData;
            
        }
            /**
             * Modifies the WordPress database query.
             */
            public function _replyToModifyMySQLWhereClauseToSearchPosts( $sWhere, $oWPQuery ) {

                global $wpdb;
                if ( $oWPQuery->get( 'q' ) ) {
                    $_sSearchTerm = $oWPQuery->get( 'q' );
                    $sWhere .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql( $wpdb->esc_like( $_sSearchTerm ) ) . "%'";
                }               
                return $sWhere;
                
            }            
        /**
         * Checks whether the user is logged-in.
         */
        private function _isLoggedIn() {
            
            if ( ! is_multisite() ) {                
                if ( ! function_exists( 'is_user_logged_in' ) ) {
                    include( ABSPATH . "wp-includes/pluggable.php" );             
                }    
                return is_user_logged_in();
            }
            
            // For multi-sites
            return is_user_member_of_blog( get_current_user_id(), get_current_blog_id() );

        }
    
    
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
     * </ul>     
     */
    protected function getEnqueuingScripts() { 
        return array(
            array(     // if you need to set a dependency, pass as a custom argument array. 
                'src'           => dirname( __FILE__ ) . '/asset/jquery.tokeninput.js',     // path or url
                'dependencies'  => array( 'jquery' ) 
            ),
            dirname( __FILE__ ) . '/asset/tokeninput.options-hander.js',    // a string value of the target path or url will work as well.
        );
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
        return array(
            dirname( __FILE__ ) . '/asset/token-input.css',
            dirname( __FILE__ ) . '/asset/token-input-facebook.css',
            dirname( __FILE__ ) . '/asset/token-input-mac.css',        
            dirname( __FILE__ ) . '/asset/token-input-admin_page_framework.css',        
        );
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
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

                        /* If the input tag is not found, do nothing  */
                        var nodeNewAutoComplete = oCopiedNode.find( 'input.autocomplete' );
                        if ( nodeNewAutoComplete.length <= 0 ) return;
                        
                        /* Remove unnecessary elements */
                        oCopiedNode.find( 'ul.token-input-list' ).remove();
                        
                        /* Bind the autocomplete script */
                        var sFieldsID = oCopiedNode.closest( '.admin-page-framework-fields' ).attr( 'id' );
                        var sOptionID = oCopiedNode.closest( '.admin-page-framework-sections' ).attr( 'id' ) + '_' + oCopiedNode.closest( '.admin-page-framework-fields' ).attr( 'id' );    // sections id + _ + fields id 
                        var aOptions = jQuery( '#' + nodeNewAutoComplete.attr( 'id' ) ).getTokenInputOptions( sOptionID );
                        aOptions = jQuery.isArray( aOptions ) ? aOptions : [ [], [] ];
                        
                        jQuery( nodeNewAutoComplete ).tokenInput( 
                            aOptions[0], 
                            jQuery.extend( true, aOptions[1], {
                                onAdd: function ( item ) {
                                    jQuery( nodeNewAutoComplete ).attr( 'value', JSON.stringify( jQuery( nodeNewAutoComplete ).tokenInput( 'get' ) ) );
                                },
                                onDelete: function ( item ) {
                                    jQuery( nodeNewAutoComplete ).attr( 'value', JSON.stringify( jQuery( nodeNewAutoComplete ).tokenInput( 'get' ) ) );
                                },
                            })
                        );
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
        .admin-page-framework-field-autocomplete {
            width: 100%;
        }
        .admin-page-framework-field-autocomplete .admin-page-framework-input-label-container {
            min-width: 200px;
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
            'type'    => 'text',
        ) + $aField['attributes'];
        $aInputAttributes['class'] .= ' autocomplete';
        $aInputAttributes['value']  = $this->_setPrepopulate( $aField['settings'], $aField['settings2'], $aInputAttributes['value'] );
            
        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>"    // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"
            . $this->getAutocompletEnablerScript( $aField['input_id'], $aField['settings'], $aField['settings2'], $aInputAttributes['value'] )
            . $aField['after_label'];
        
    }    
        /**
         * If the user sets the prePopulate option, this method compose a sting JSON value of the pre-populated array. 
         */
        private function _setPrepopulate( $asParam1, $aParam2, $sValue ) {

            if ( '[]' == $sValue  ) {
                return $sValue;
            }
        
            // If the value is json encoded string, do nothing.
            $_aoValue = json_decode( $sValue );
            if ( ! empty( $_aoValue ) ) {    
                return $sValue;
            }
                    
            if ( isset( $asParam1['prePopulate'] ) && is_array( $asParam1['prePopulate'] ) ) {
                return json_encode( $asParam1['prePopulate'] );
            }
            if ( isset( $aParam2['prePopulate'] ) && is_array( $aParam2['prePopulate'] ) ) {
                return json_encode( $aParam2['prePopulate'] );
            }
            
            return $sValue;
            
        }
        
        private function getAutocompletEnablerScript( $sInputID, $asParam1, $aParam2, $sValue='' ) {
            
            $sParam1 = $this->_formatSettings( $asParam1, $sValue );
            $sParam2 = $this->_formatSettings( $aParam2, $sValue );
            return 
"<script type='text/javascript' class='autocomplete-enabler-script'>
    jQuery( document ).ready( function() {
        var _sJSONValue     = jQuery( '#{$sInputID}' ).attr( 'value' );
        var _oSavedValues    = _sJSONValue ? jQuery.parseJSON( _sJSONValue ) : '';
        jQuery( '#{$sInputID}' ).tokenInput( 
            {$sParam1}, 
            jQuery.extend( true, {$sParam2}, {
                onAdd: function ( item ) {
                    jQuery( '#{$sInputID}' ).attr( 'value', JSON.stringify( jQuery( '#{$sInputID}' ).tokenInput( 'get' ) ) );
                },
                onDelete: function ( item ) {
                    jQuery( '#{$sInputID}' ).attr( 'value', JSON.stringify( jQuery( '#{$sInputID}' ).tokenInput( 'get' ) ) );
                },
            })
        );
        jQuery( _oSavedValues ).each( function ( iIndex, value ) {
            jQuery( '#{$sInputID}' ).tokenInput( 'add', value );
        }); 
        var sOptionID = jQuery( '#{$sInputID}' ).closest( '.admin-page-framework-sections' ).attr( 'id' ) + '_' + jQuery( '#{$sInputID}' ).closest( '.admin-page-framework-fields' ).attr( 'id' );
        jQuery( '#{$sInputID}' ).storeTokenInputOptions( sOptionID, {$sParam1}, {$sParam2} );

    });
</script>";        
        }
        
        /**
         * 
         * @return            string            The json encoded string.
         */
        private function _formatSettings( $asParams, $sValue='' ) {

            if ( ! is_array( $asParams ) ) {
                return "'" . ( string ) $asParams . "'";
            }
                
            $aParams = $asParams;
            
            // the 'prePopulate' option should only be set when the value is not set; otherwise, the jQuery script causes an unknown error.
            if ( isset( $aParams['prePopulate'] ) && $sValue ) {
                unset( $aParams['prePopulate'] );
            }
            
            return json_encode( ( array ) $aParams );
            
        }
    
}
endif;