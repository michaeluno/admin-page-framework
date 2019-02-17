<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2019 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'Select2CustomFieldType' ) ) :
/**
 * A filed of the `select2` field type lets the user select items from a predefined list by typing the item name and the items possibly be fetched with AJAX.
 *
 * This class defines the `select2` field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**is_multiple** - (optional, boolean) if this is set to true, the `multiple` attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
 *     <li>**options** - (optional, array) The options argument passed to the `select2` method. For detaks, see https://select2.github.io/options.html
 *     </li>
 *     <li>**callback** - (optional, array)
 *          <ul>
 *              <li>**search** - (optional, callable) Set a callback function that is triggered in the background when the user type something in the select input field expecting a list of suggested items will be displayed.
 *  - `$aQueries` - (array) an array holding the following arguments.
 *     - `q` - (string) the queried characters.
 *     - `page` - (string) the pagination number. When the result has too many items, it can be paginated.
 *     - `field_id` - (string) the field ID that calls the query.
 *     - `section_id` - (string) the section ID that calls the query.
 *  - `$aFieldset` - (array) the field definition array.
 *
 *  The callback method is expected to return an array with the following structure:
 * <code>
 *  array(
 *      'results' => array(
 *          array(
 *              'id'    => 224, //the value saved in the database.
 *              'text'  => 'The title of this item.'   //The text displayed in the drop-down list.
 *          ),
 *          array(
 *              'id'    => 567,
 *              'text'  => 'The title of this item.'
 *          ),
 *          ... continues ...
 *      ),
 *      'pagination' => array(  // can be omitted
 *          'more'  => true,    // (boolean) or false - whether the next paginated item exists or not.
 *      )
 *  )
 * </code>
 *              </li>
 *              <li>**new_tag** - (optional, callable) Set a callback function that is called when the user creates a new tag.
  *  - `$aQueries` - (array) an array holding the following arguments.
 *     - `tag` - (string) the tag name.
 *     - `field_id` - (string) the field ID that calls the query.
 *     - `section_id` - (string) the section ID that calls the query.
 *  - `$aFieldset` - (array) the field definition array.
 *  If this callback is set, the `options` -> `tags` argument will be automatically enabled.
 *
 *  The callback method is expected to return an array with the following structure:
 * <code>
 *  array(
 *      'id'    => 78,              // the value which will be stored in the database.
 *      'text'  => 'Tag Name',   // the tag name gets displayed in the field.
 *      'note'  => 'A console message.' // (optional) A note displayed in the browser console.
 *      'error' => 'An error massage.',  // (optional) if this is set, an error message will be displayed.
 *  )
 * </code>
 *              </li>
 *          </ul>
 *     </li>
 * </ul>
 *
 * @since       3.8.7
 * @version     0.0.4
 * @supports    IE8 or above. (uses JSON object)
 * @requires    Admin Page Framework 3.8.14
 */
class Select2CustomFieldType extends AdminPageFramework_FieldType_select {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'select2', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(

        'label'             => array(),
        'is_multiple'       => false,
        'attributes'        => array(
            'select'    => array(
                'size'          => 1,
                'autofocusNew'  => null,
                'multiple'      => null,    // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'required'      => null,
            ),
            'optgroup'  => array(),
            'option'    => array(),
        ),

        /**
         * @see     https://select2.github.io/options.html
         */
        'options'   => array(
            'width' => 'auto',
            // 'maximum-selection-length' => 2,
        ),

        // If a callback is set, the select list will be generated dynamically with Ajax.
        // The callback function must return an array of select list.
        'callback'  => array(
            'search' => null,
            'new_tag' => null,
        ),

    );

    protected function construct() {}

    /**
     * Loads the field type necessary components.
     */
    public function setUp() {}


    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'src'           => $this->isDebugMode()
                    ? dirname( __FILE__ ) . '/select2/js/select2.full.js'
                    : dirname( __FILE__ ) . '/select2/js/select2.full.min.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery' )
            ),
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            $this->isDebugMode()
                ? dirname( __FILE__ ) . '/select2/css/select2.css'
                : dirname( __FILE__ ) . '/select2/css/select2.min.css',
        );
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {

        $_sAjaxURL = admin_url( 'admin-ajax.php' );
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return
"jQuery( document ).ready( function(){
    
    /**
     * Shows an error message that disappears in given milliseconds.
     */
    _showDecayingError = function( oNode, sMessage, iMilliseconds ) {
        
        iMilliseconds = 'undefined' === typeof iMilliseconds ? 4000 : parseInt( iMilliseconds );
        var _oError = jQuery( '<div class=\"error notice is-dismissible\"><p>' + sMessage + '</p></div>' );
        _oError.appendTo( jQuery( oNode ) )
            .delay( iMilliseconds ).fadeOut( 'slow' );
        setTimeout( function() {
            _oError.remove();
        }, iMilliseconds*1 + 2000 );
    
    }            
            
    /**
     * Checks whether a given string is of a pending item.
     * 
     * A pending item here refers to a string in a form `__string__`.
     * This is a custom ID for pending items set in the `createTag` callback function of the `ajax` select2 argument.
     * 
     * @return      boolean
     */
    var _isItemPending = function( isIndex ) {
        var _bHasSuffix = isIndex.lastIndexOf( '__', 0) === 0;
        var _bHasPrefix = isIndex.indexOf( '__' ) === 0;
        return ( _bHasSuffix && _bHasPrefix );
    }            
    
    /**
     * Search the index (key) in a plain object.
     */
    var _getIndexByValue = function( sSearch, oObject ) {
        var _nsiResult = null;
        jQuery.each( oObject, function( isKey, value ) {
            if ( value === sSearch ) {
                _nsiResult = isKey;
                return false;
            }
        } );
        return _nsiResult;
    }            
    
    var _getNumberOfValues = function( sSearch, oObject ){
        
        var _iCount = 0;
        jQuery.each( oObject, function( isKey, value ) {
            if ( value === sSearch ) {
                _iCount++;
            }
        } );
        return _iCount;
        
    }
    
    /**
     * Returns an array with the key of an ID and the value of a name of option tag elements.
     */
    var _getSelectedNames = function( oSelectNode ) {
        var _aSelection = jQuery( oSelectNode ).val();
        var _aSelectedNames = {};
        jQuery.each( _aSelection, function( iIndex, isValue ){
            _aSelectedNames[ isValue ] = jQuery( oSelectNode ).find( 'option[value=\"' + isValue + '\"]').text();
        } );           
        return _aSelectedNames;
    }        

    /**
     * @deprecated      Doesn't work
     */
    var eliminateDuplicates = function(arr) {
        var i,
        len=arr.length,
        out=[],
        obj={};

        for (i=0;i<len;i++) {
            obj[arr[i]]=0;
        }
        for (i in obj) {
            out.push(i);
        }
        return out;
    }        

    /**
     * Initialize no ui slider with the given slider container node.
     * 
     * @since       3.8.7
     * @param       oNode       The target select tag DOM node object.
     */
    var _initializeSelect2 = function( oNode ) {
                        
        var _oSelect2Target = jQuery( oNode );       

        /**
         * Construct options.
         */
        var _aOptions       = _oSelect2Target.data();
        if ( _aOptions[ 'search_callback' ] ) {
                     
            _aOptions = jQuery.extend(
                {           // defaults
                    minimumInputLength: 2,
                    ajax: {
                        delay: 250,                            
                        cache: true,
                    },
                },                        
                _aOptions,  // user inputs
                {       
                    ajax: {
                        url: '{$_sAjaxURL}',
                        dataType: 'json',
                        type: 'POST',   // as `page` query key conflicts with page slug, do not use `GET`.
                        data: function (params) {                    
                            params.page = params.page || 1;
                            return {
                                // Query Parameters
                                action:             'dummy_select2_field_type_action',
                                q:                  params.term, // search term
                                page:               params.page, // pagination number
                                doing_select2_ajax: true, // ensure it is called from here
                                field_id:           _oSelect2Target.data( 'field_id' ), // will be checked in the background                                        
                                section_id:         _oSelect2Target.data( 'section_id' ), // will be checked in the background                                        
                            };
                        },
                    },
                } // overriding values                                
            ); // end of extend
                                
        }
        
        if ( _aOptions[ 'new_tag_callback' ] ) {
            
            /**
             * Called right before finishing creating a new tag.
             * To cancel, return void.
             * 
             * When called from an AJAX search result, the `page` will be available.
             * When the user hit the token separater key such as `,`, the property will be missing.
             */            
            _aOptions[ 'createTag' ] = function( obj ) {
                
                /**
                 * Sanitize user inputs.
                 * Must trim the word because `word` and `word ` will create the same tag `word`.
                 */
                var _sTerm = jQuery.trim( obj.term );
                
                /**
                 * Check duplicates.
                 */
                var _bFoundDuplicates = false;
                _oSelect2Target.find( 'option:selected' ).each( function( iIndex, value ){
                    if ( jQuery( this ).text() === _sTerm ) {
                        _bFoundDuplicates = true;
                        return false; // break
                    }
                } );
                if ( _bFoundDuplicates ) {
                    // If the user selects a tag from a suggester list, do not add a tag .
                    if ( obj.page ) {
                        return;
                    }
                    // If the user pressed the token separator, show the tag first and remove it in the `select2:select` event.
                    // Otherwise, the input gets stuck.
                    return {
                        id:   '__' + _sTerm + '__',   // for a temporary id
                        text: _sTerm,
                        isDuplicate: true,
                        disabled: true,
                    };
                }
                
                /**
                 * Performs a new tag AJAX request.
                 */
                jQuery.ajax( {
                    type: 'POST',
                    url: '{$_sAjaxURL}',                     
                    data: {
                        action: 'dummy_select2_field_type_action',
                        tag: _sTerm,
                        doing_select2_ajax: true, // ensure it is called from here
                        field_id:           _oSelect2Target.data( 'field_id' ), // will be checked in the background                                        
                        section_id:         _oSelect2Target.data( 'section_id' ), // will be checked in the background                                                                            
                    },
                    error: function() {
                        _showDecayingError( _oSelect2Target.parent().get( 0 ), 'Ajax request failed' );
                    },
                    success: function( data ) {                    

                        if ( data.error ) {
                            _showDecayingError( _oSelect2Target.parent().get( 0 ), data.error );
                            return;
                        }
                        if ( data.note ) {
                            console.log( 'APF Select2 Field Type: ' + data.note );
                        }
                        
                        // First, release the lock so that the values will be avaiable.
                        var _oOptionTags = _oSelect2Target.find( 'option[value=\"' + '__' + data.text + '__' + '\"]' );
                            _oOptionTags.removeAttr( 'disabled' );
                            
                        /**
                         * Retrieve the selected IDs. 
                         * 
                         * `_oSelect2Target.val()` also does the job but it is not updated realtime. 
                         * For accurate results, parse items each.
                         */
                        var _aSelectedValues = [];
                        _oSelect2Target.find( 'option:selected' ).each( function( iIndex ){
                            _aSelectedValues.push( jQuery( this ).val() );
                        } );
                                    
                        // Replace the temporarily set tag name with the value of ID.
                        var _isIndex = _getIndexByValue( '__' + data.text + '__', _aSelectedValues );
                        if ( null !== _isIndex ) {
                            _aSelectedValues[ _isIndex ] = data.id.toString();
                                                                                
                            // Add HTML option to select field
                            jQuery( '<option value=\"' + data.id + '\">' + data.text + '</option>' )
                               .appendTo( _oSelect2Target );
                                                       
                            _oSelect2Target.val( _aSelectedValues ).trigger( 'change' );
                            
                        }

                    },
                    dataType: 'json',
                });                                       
                 
                return {
                    
                    text: _sTerm,
                    
                    // for a temporary id, adding the prefix and suffix of `__` to make it distinctive 
                    // so that it will be obvious that is pending to be validated.
                    id:   '__' + _sTerm + '__',  
                    
                    // Flag a new tag to be referred from a callback
                    isNewFlag: true,
                    
                    // Not setting `disable` here but in the `select2:select` event 
                    // because this will disable the selection on UI as well.
                    // disabled: false,  
                    
                };
            };                                  
            
        }
        
        /**
         * Adjust field element width.
         * 
         * When the drop-down list width is set, if the parent container element widths are small,
         * the width on drop-down list does take effect.
         */
        if ( _aOptions[ 'width' ] && 'auto' !== _aOptions[ 'width' ] ) {
            var _oFieldContainer = _oSelect2Target.closest( '.admin-page-framework-field-select2' );
            _oFieldContainer.css( 'width', _aOptions[ 'width' ] );
            _oFieldContainer.children( '.admin-page-framework-select-label' )
                .css( 'width', '100%' );
            _oFieldContainer.children( '.admin-page-framework-select-label' )
                .children( 'label' )
                .children( '.admin-page-framework-input-container' )
                .css( 'width', '100%' );
            _aOptions[ 'width' ] = '100%';
        }
        
        /**
         * Initialization
         */
        _oSelect2Target.select2( _aOptions );

        /**
         * Ajax handling.
         * 
         * For Ajax based fields, the selected text and their associated ids must be stored.
         * Otherwise, in the next page load, the text(label) in the drop-down list cannnot be displayed.
         */
        if ( _aOptions[ 'search_callback' ] ) {
            
            /**
             * Set inital values.
             */
            var _oInputForEncoded = _oSelect2Target.closest( '.admin-page-framework-field' )
                .children( 'input[data-encoded]' ).first();
            var _sData = _oInputForEncoded.val();
            if ( _sData ) {                       
                jQuery.each( jQuery.parseJSON( _sData ), function( iIndex, aItem ){
                    var _oOptionTag = jQuery( '<option selected>' + aItem[ 'text' ] + '</option>' )
                        .val( aItem[ 'id' ] );
                    _oSelect2Target.append( _oOptionTag );
                } );
            }

            /**
             * When the user selects an item, set a JSON encoded string to a hidden input with the key of `encoded`.
             * 
             * Unselect items with the value of `__string__` as these are pending for update via Ajax.
             * And if the user saves the form with these items, the saved values messes up with IDs and dummy index.
             */
            _oSelect2Target.on( 'change', function( event ){
                
                /**
                 * Construct the data to store as JSON. Get the values (id and text) of each option.
                 * jQuery( this ) will be the `<select>` element.
                 */
                var _aText   = [];
                var _aValues = [];
                jQuery( this ).find( 'option:selected' ).each( function( index ){
                    
                    var _sID   = jQuery( this ).val();
                    var _sText = jQuery( this ).text();
                    
                    // Ignore pending items.
                    if ( _isItemPending( _sID ) ) {
                        return true;
                    }       
                    
                    // Check duplicated items,
                    if ( -1 !== jQuery.inArray( _sText, _aText ) ) {
                        jQuery( this ).removeAttr( 'selected' );
                        return true;
                    }
                    
                    _aText.push( _sText );
                    _aValues.push( {
                        id: _sID,
                        text: _sText,    // the label
                    } );        
                    
                } );
                
                // Set the encoded value.
                jQuery( this ).closest( '.admin-page-framework-field' )
                    .children( 'input[data-encoded]' ).first()
                    .val( JSON.stringify( _aValues ) );
                
            } );
        }

        if ( _aOptions[ 'new_tag_callback' ] ) {
        
            /**
             * Handles removing duplicate tags.
             */
            _oSelect2Target.on( 'select2:select', function( event ) {
                
                // Check the flag inserted in the `createTag` callback.
                if ( event.params.data.isDuplicate ) {                
                    jQuery( this ).find( 'option[value=\"' + '__' + event.params.data.text + '__' + '\"]' )
                        .removeAttr( 'selected' );
                    return;
                }      
                
                /**
                 * Temporarily disable the subject tag. 
                 * 
                 * So when the form is submitted, pending items won't be sent.
                 */
                jQuery( this ).find( 'option[value=\"' + '__' + event.params.data.text + '__' + '\"]' )
                    .attr( 'disabled', 'disabled' );                
                
            } );            
            
        }

    }

    /**
     * Initialize toggle elements. Note that a pair of inputs (min and max) are parsed for each field.
     * So skip one of them.
     */
    jQuery( 'select[data-type=select2]' ).each( function () {
        _initializeSelect2( this );
    });
    

    jQuery().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                        
            oCloned.find( '.select2-container' ).remove();
                     
            oCloned.find( 'select[data-type=select2]' ).each( function () {
                _initializeSelect2( this );
            });              
            
        },
    },
    [ 'select2' ]    // subject field type slugs
    );

});";
    }

    /**
     * Returns the field type specific CSS rules.
     */
    protected function getStyles() {
        return "
.admin-page-framework-field-select2 .select2-container {     
    min-width: 200px;
    width: auto;
}

.admin-page-framework-field-select2 {
    max-width: 96%;
}

/* Make a room for repeatable buttons */
.repeatable .admin-page-framework-field-select2 > .admin-page-framework-select-label > label > .admin-page-framework-input-container {
    max-width: 84%;
}

        ";
    }

    /**
     * Returns the output of the field type.
     *
     * @return      string
     */
    public function getField( $aField ) {

        $_sInputForEncodedValue = '';
        if ( is_callable( $this->getElement( $aField, array( 'callback', 'search' ) ) ) ) {
            $_sInputForEncodedValue = $this->_getChildInputByKey( 'encoded', $aField );
            $aField[ 'attributes' ] = $this->_getAttributesUpdatedForAJAX( $aField );
        }

        $_aOptions = $this->_getSelect2OptionsFormatted( $aField[ 'options' ], $aField );

        $aField[ 'attributes' ][ 'select' ] = array(
            'data-type'       => 'select2',
            'data-field_id'   => $aField[ 'field_id' ],   // checked in the background with the `doOnFieldRegistration()` method using AJAX.
            'data-section_id' => $aField[ 'section_id' ], // checked in the background with the `doOnFieldRegistration()` method using AJAX.
        )   + $this->getDataAttributeArray( $_aOptions )
            + $this->getElementAsArray( $aField, array( 'attributes', 'select', ) );

        return parent::getField( $aField ) // the select field
            . $_sInputForEncodedValue;     // a nested input that stores an encoded selection value.

    }

        /**
         * @return      string
         */
        private function _getChildInputByKey( $sKey, $aField ) {

            $_aAttributes = array(
                'name'              => $aField[ 'attributes' ][ 'name' ] . '[' . $sKey . ']',
                'id'                => $aField[ 'attributes' ][ 'id' ] . '_' . $sKey,
                'data-' . $sKey     => true,
                'type'              => 'hidden',
                'value'             => ( string ) $this->getElement( $aField, array( 'value', $sKey ), '' ),
                'style'             => 'width: 100%',   // for debugging
            );
            return "<input " . $this->getAttributes( $_aAttributes ) . " />";

        }

        /**
         * For AJAX enabled fields, the stored field data structure becomes different.
         *
         * Nested elements of `encoded` and `value` will be added. The selection IDs will be stored in the `value`.
         * The `encoded` element will store the text and id of the user's selection.
         *
         * @return      array
         */
        private function _getAttributesUpdatedForAJAX( $aField ) {

            $_aAttributes = $aField[ 'attributes' ];
            $_aAttributes[ 'name' ] = $_aAttributes[ 'name' ] . "[value]";
            $_aAttributes[ 'id' ]   = $_aAttributes[ 'id' ] . "_value";
            return $_aAttributes;

        }


        /**
         * @return          array
         */
        private function _getSelect2OptionsFormatted( $aOptions, $aField ) {

            // Format camel-cased key names.
            foreach( $aOptions as $_sKey => $_mValue ) {

                if( ! preg_match( '/([a-zA-Z])(?=[A-Z])/', $_sKey ) ) {
                    continue;
                }

                $_sDashed = $this->_getCamelCaseToDashed( $_sKey );
                $aOptions[ $_sDashed ] = $_mValue;
                unset( $aOptions[ $_sKey ] );

            }

            $aOptions[ 'search_callback' ]    = is_callable(
                $this->getElement( $aField, array( 'callback', 'search' ) )
            );
            $_bNewTagCallbackCallable = is_callable(
                $this->getElement( $aField, array( 'callback', 'new_tag' ) )
            );
            $aOptions[ 'new_tag_callback' ] = $_bNewTagCallbackCallable;
            if ( $_bNewTagCallbackCallable ) {
                $aOptions[ 'tags' ] = true;
            }
            return $aOptions;

        }
            /**
             * @return      string
             */
            private function _getCamelCaseToDashed( $sString ) {
                return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $sString ) );
            }


    /**
     * Callks back the callback function if it is set.
     *
     * Called when the field type is registered.
     */
    protected function doOnFieldRegistration( $aFieldset ) {

        $_aQueries = $_REQUEST;
        if ( ! $this->_shouldProceedToAjaxRequest( $_aQueries, $aFieldset ) ) {
            return;
        }
        unset( $_aQueries[ 'doing_select2_ajax' ] );

        $_asCallable = $this->_getAjaxCallback( $_aQueries, $aFieldset );
        if ( ! is_callable( $_asCallable ) ) {
            return;
        }

        // Will exist in the function.
        wp_send_json(
            call_user_func_array(
                $_asCallable,   // callable
                array(
                    $_aQueries, // param 1
                    $aFieldset  // param 2
                )
            )
        );

    }
        /**
         * @return      boolean|callable        False when a callback is not found. Otherwise, the found callable.
         */
        private function _getAjaxCallback( $aRequest, $aFieldset ) {

            if ( isset( $aRequest[ 'q' ] ) ) {
                return $this->getElement( $aFieldset, array( 'callback', 'search' ), false );
            }
            if ( isset( $aRequest[ 'tag' ] ) ) {
                return $this->getElement( $aFieldset, array( 'callback', 'new_tag' ), false );
            }
            return false;

        }

        /**
         * @return      boolean
         */
        private function _shouldProceedToAjaxRequest( $aRequest, $aFieldset ) {

            if (
                ! isset(
                    $aRequest[ 'doing_select2_ajax' ],
                    $aRequest[ 'field_id' ],
                    $aRequest[ 'section_id' ]
                )
            ) {
                return false;
            }
            if ( $aFieldset[ 'field_id' ] !== $aRequest[ 'field_id' ] ) {
                return false;
            }
            // @deprecated 0.0.4 Bug fix - this is for nested repeated fields to process properly
            // in meta boxes, if the section id is '_default', fieldset may not have that key
//            if ( $aFieldset[ 'section_id' ] !== $aRequest[ 'section_id' ] ) {
//                return false;
//            }

            return true;

        }

}
endif;
