/*! Admin Page Framework - Form Script 1.0.0 */
/**
 * This script should be empty and provide the banner (header comment) for the concatenated bundled script (form.bundle.js).
 */
(function ( $ ) {

    /**
     * Increments a digit in the given string by the model.
     *
     * @since       3.6.0
     * @return      string
     */
    $.fn.incrementDigitByModel = function( sString, iIncrementFrom, sModel, sDigitKey ) {

        if ( 'string' !== typeof sString  ) {
            return sString;
        }

        sDigitKey = sDigitKey ? sDigitKey : '___i___';

        // Escape regex characters.
        sModel = sModel.replace(
            /[-[\]{}()*+?.,\\^$|#\s]/g, // Use the g modifier to apply the changes to all the matches.
            "\\\\$&"  // in a test script this was okay with double-backslashes "\\$&"
        );

        // Construct a regex needle pattern.
        var _oRegex = new RegExp( '^(.+?)(' + sDigitKey + ')(.*?)$', 'g' );
        sModel = sModel.replace( _oRegex, '($1)(\\\d+)($3.*?)' );

        _oRegex = new RegExp( sModel );
        sString = sString.replace( _oRegex, function ( sFullMatch, sMatch0, sMatch1, sMatch2 ) {
            iIncrementFrom = 'undefined' === typeof iIncrementFrom
                ? sMatch1
                : iIncrementFrom;

            return sMatch0 + ( Number( iIncrementFrom ) + 1 ) + sMatch2;
        } );
        return sString;
    };

    /**
     * Increments a digit of the given attribute value.
     * @sinec       3.6.0
     */
    $.fn.incrementAttributes = function( aAttributeNames, iIncrementFrom, sModel, sDigitKey ) {
        var _oThis = $( this );
        $.each( aAttributeNames, function( iOuterIndex, sAttributeName ) {
            _oThis.incrementAttribute( sAttributeName, iIncrementFrom, sModel, sDigitKey );
        });
    };

    /**
     * Increments a digit of the given attribute value.
     * @sinec       3.6.0
     */
    $.fn.incrementAttribute = function( sAttributeName, iIncrementFrom, sModel, sDigitKey ) {
        return this.attr( sAttributeName, function( iIndex, sValue ) {
            return $( this ).incrementDigitByModel( sValue, iIncrementFrom, sModel, sDigitKey );
        });
    };


    /**
     * Increments a digit of the given occurrence(nth/-nth) with the prefix of underscore in a specified attribute value.
     * if the biOccurrence is false, the last found one will be replaced.
     * @deprecated  3.6.0
     */
    $.fn.incrementIDAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateID( iIndex, sValue, 1, biOccurrence );
        });
    };
    /**
     * Increments a digit of the given occurrence(nth/-nth) enclosed in [] in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.incrementNameAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateName( iIndex, sValue, 1, biOccurrence );
        });
    };

    /**
     * Decrements a digit of the given occurrence(nth/-nth) with the prefix of underscore in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.decrementIDAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateID( iIndex, sValue, -1, biOccurrence );
        });
    };
    /**
     * Decrements a first/last found digit enclosed in [] in a specified attribute value.
     * @deprecated  3.6.0
     */
    $.fn.decrementNameAttribute = function( sAttribute, biOccurrence ) {
        return this.attr( sAttribute, function( iIndex, sValue ) {
            return updateName( iIndex, sValue, -1, biOccurrence );
        });
    };

    /**
     * Sets the current index to the ID attribute. Used for sortable fields.
     * @deprecated  3.6.0
     * */
    $.fn.setIndexIDAttribute = function( sAttribute, iIndex, biOccurrence ){
        return this.attr( sAttribute, function( i, sValue ) {
            return updateID( iIndex, sValue, 0, biOccurrence );
        });
    };
    /**
     * Sets the current index to the name attribute. Used for sortable fields.
     * @deprecated  3.6.0
     */
    $.fn.setIndexNameAttribute = function( sAttribute, iIndex, biOccurrence ){
        return this.attr( sAttribute, function( i, sValue ) {
            return updateName( iIndex, sValue, 0, biOccurrence );
        });
    };

    /* Local Function Literals */
    /**
     * Sanitizes the occurrence parameter value for backward compatibility.
     *
     * @since   3.1.7
     * @deprecated  3.6.0
     */
    var sanitizeOccurrence = function( biOccurrence ) {

        // If not defined, pass -1 for the last occurrence.
        if ( 'undefined' === typeof biOccurrence ) {
            return -1;
        }
        // If true, it used to mean the first occurrence.
        if ( true === biOccurrence ) {
            return 1;
        }
        // If false, it used to mean the last occurrence.
        if ( false === biOccurrence ) {
            return -1;
        }
        // 0 may have been used to mean false which meant the last occurrence.
        if ( 0 === biOccurrence ) {
            return -1;
        }
        // If it is an integer, that is good.
        if ( 'number' === typeof biOccurrence ) {
            return biOccurrence;
        }
        // Otherwise, the default value will be returned
        return -1;

    }
    /**
     * Returns the modified ID string based on the modification type.
     *
     * @since  3.0.0
     * @since  3.1.7    Made it possible to specify the occurrence to change.
     * @param  integer  iIndex              The element index
     * @param  string   sID                 The ID to modify, the subject string haystack.
     * @param  integer  iIncrementType      1: increment, 2: decrement, 3: no change
     * @param  mixed    biOccurrence        One based index of occurrence to apply the change. 1 is the first occurrence. -1 is the first from the last.
     * @deprecated  3.6.0
     */
    var updateID = function( iIndex, sID, iIncrementType, biOccurrence ) {

        if ( 'undefined' === typeof sID ) { return sID; }

        var _iCurrentOccurrence = 1;
        var _oNeedle            = new RegExp( '(.+?)__(\\\d+)(?=([_-]|$))', 'g' ); // triple escape - not sure why but on a separate test script, double escape was working
        var _oMatch             = sID.match( _oNeedle );
        var _iTotalMatch        = null !== _oMatch && _oMatch.hasOwnProperty( 'length' ) ? _oMatch.length : 0;
        if ( _iTotalMatch === 0 ) { return sID; }
        var _iOccurrence        = sanitizeOccurrence( biOccurrence );
        var _bIsBackwards       = _iOccurrence < 0;
        _iOccurrence = _bIsBackwards ? _iTotalMatch + 1 + _iOccurrence : _iOccurrence;
        return sID.replace( _oNeedle, function ( sFullMatch, sMatch0, sMatch1 ) {

            // If the iterated item is not at the specified occurrence, return the unmodified string.
            if ( _iCurrentOccurrence !== _iOccurrence ) {
                _iCurrentOccurrence++;
                return sFullMatch;
            }

            // At this point, the iteration is at the specified occurrence.
            var _sResult = '';
            switch ( iIncrementType ) {
                case 1:
                    _sResult = sMatch0 + '__' + ( Number( sMatch1 ) + 1 );
                    break;
                case -1:
                    _sResult = sMatch0 + '__' + ( Number( sMatch1 ) - 1 );
                    break;
                default:
                    _sResult = sMatch0 + '__' + ( iIndex );
                    break;
            }
            _iCurrentOccurrence++;
            return _sResult;

        });

    }
    /**
     * Returns the modified string for name attributes based on the modification type.
     *
     * @since  3.0.0
     * @since  3.1.7    Made it possible to specify the occurrence to change.
     * @param  integer  iIndex              The element index
     * @param  string   sName               The name attribute value to modify, the subject string haystack.
     * @param  integer  iIncrementType      1: increment, 2: decrement, 3: no change
     * @param  mixed    biOccurrence        One based index of occurrence to apply the change. 1 is the first occurrence. -1 is the first from the last.
     * @deprecated  3.6.0
     */
    var updateName = function( iIndex, sName, iIncrementType, biOccurrence ) {

        if ( 'undefined' === typeof sName ) { return sName; }

        var _iCurrentOccurrence = 1;
        var _oNeedle            = new RegExp( '(.+?)\\\[(\\\d+)(?=\\\])', 'g' );    // triple escape - not sure why but on a separate test script, double escape was working
        var _oMatch             = sName.match( _oNeedle );
        var _iTotalMatch        = null !== _oMatch && _oMatch.hasOwnProperty( 'length' ) ? _oMatch.length : 0;
        if ( _iTotalMatch === 0 ) { return sName; }
        var _iOccurrence        = sanitizeOccurrence( biOccurrence );
        var _bIsBackwards       = _iOccurrence < 0;
        _iOccurrence = _bIsBackwards ? _iTotalMatch + 1 + _iOccurrence : _iOccurrence;
        return sName.replace( _oNeedle, function ( sFullMatch, sMatch0, sMatch1 ) {


            // If the iterated item is not at the specified occurrence, return the unmodified string.
            if ( _iCurrentOccurrence !== _iOccurrence ) {
                _iCurrentOccurrence++;
                return sFullMatch;
            }

            // At this point, the iteration is at the specified occurrence.
            var _sResult = '';
            switch ( iIncrementType ) {
                case 1:
                    _sResult = sMatch0 + '[' + ( Number( sMatch1 ) + 1 );
                    break;
                case -1:
                    _sResult = sMatch0 + '[' + ( Number( sMatch1 ) - 1 );
                    break;
                default:
                    _sResult = sMatch0 + '[' + ( iIndex );
                    break;
            }
            _iCurrentOccurrence++;
            return _sResult;

        });
    }

}( jQuery ));
(function ( $ ) {

    /**
     * Checks all the checkboxes in siblings.
     */
    $.fn.selectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .prop( 'checked', true )
            .trigger( 'change' );   // 3.8.8+
    }
    /**
     * Unchecks all the checkboxes in siblings.
     */
    $.fn.deselectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .prop( 'checked', false )
            .trigger( 'change' );   // 3.8.8+
    }

}( jQuery ));
( function( $ ) {

    var _removeAdminPageFrameworkLoadingOutputs = function() {

        jQuery( '.admin-page-framework-form-loading' ).remove();
        jQuery( '.admin-page-framework-form-js-on' )
            .hide()
            .css( 'visibility', 'visible' )
            .fadeIn( 200 )
            .removeClass( '.admin-page-framework-form-js-on' );

    }

    /**
     * When some plugins or themes have JavaScript errors and the script execution gets stopped,
     * remove the style that shows "Loading...".
     */
    var _oneerror = window.onerror;
    window.onerror = function(){

        // We need to show the form.
        _removeAdminPageFrameworkLoadingOutputs();

        // Restore the original
        window.onerror = _oneerror;

        // If the original object is a function, execute it;
        // otherwise, discontinue the script execution and show the error message in the console.
        return "function" === typeof _oneerror
            ? _oneerror()
            : false;

    }

    /**
     * Rendering forms is heavy and unformatted layouts will be hidden with a script embedded in the head tag.
     * Now when the document is ready, restore that visibility state so that the form will appear.
     */
    jQuery( document ).ready( function() {
        _removeAdminPageFrameworkLoadingOutputs();
    });

    /**
     * Gets triggered when a widget of the framework is saved.
     * @since    3.7.0
     */
    $( document ).on( 'admin-page-framework_saved_widget', function( event, oWidget ){
        jQuery( '.admin-page-framework-form-loading' ).remove();
    });

}( jQuery ));
(function ( $ ) {

    $.fn.aAdminPageFrameworkInputOptions = {};

    $.fn.storeAdminPageFrameworkInputOptions = function( sID, vOptions ) {
        sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
        $.fn.aAdminPageFrameworkInputOptions[ sID ] = vOptions;
    };
    $.fn.getAdminPageFrameworkInputOptions = function( sID ) {
        sID = sID.replace( /__\d+_/, '___' ); // remove the section index
        return ( 'undefined' === typeof $.fn.aAdminPageFrameworkInputOptions[ sID ] )
            ? null
            : $.fn.aAdminPageFrameworkInputOptions[ sID ];
    }

}( jQuery ));
(function ( $ ) {

    // Callback containers.
    $.fn.aAdminPageFrameworkAddRepeatableFieldCallbacks        = [];
    $.fn.aAdminPageFrameworkRepeatFieldCallbacks               = [];    // 3.8.8+
    $.fn.aAdminPageFrameworkRemoveRepeatableFieldCallbacks     = [];
    $.fn.aAdminPageFrameworkSortedFieldsCallbacks              = [];
    $.fn.aAdminPageFrameworkStoppedSortingFieldsCallbacks      = [];
    $.fn.aAdminPageFrameworkAddedWidgetCallbacks               = [];
    $.fn.aAdminPageFrameworkStoppedSortingSectionsCallbacks    = [];    // 3.8.0+

    /**
     * Gets triggered when the + (add) button of a repeatable field is pressed.
     */
    $( document ).on( 'admin-page-framework_added_repeatable_field', function( oEvent, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ){
        var _oThisNode = jQuery( oEvent.target );
        $.each( $.fn.aAdminPageFrameworkAddRepeatableFieldCallbacks, function( iIndex, aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ]; // '_nested', 'inline_mixed' are built-in

            // 2 here is reserved for built-in field types.
            if ( 2 < _aFieldTypes.length && -1 === $.inArray( sFieldType, _aFieldTypes ) ) {
                return true; // continue
            }
            if ( 'function' !== typeof _hfCallback ) {
                return true; // continue
            }
            // Show console warnings for a deprecated method.
            if ( -1 === $.inArray( sFieldType, [ '_nested', 'inline_mixed' ] ) ) {
                console.warn( 'Admin Page Framework (' + sFieldType + ' field type): The `added_repeatable_field` callback argument for the `registerAdminPageFrameworkCallbacks` method is deprecated. Use `repeated_field` instead.' );
            }
            _hfCallback( _oThisNode, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex );
        });


    });
    /**
     * Another way to handle repeatable fields.
     *
     * Unlike the `admin-page-framework_added_repeatable_field` event, this does not call the callback function
     * if it does not match the field type. This means `_nested` and `inline_mixed` will not trigger the callback.
     *
     * @since       3.8.8
     * @param       oEvent              The jQuery event object.
     * @param       iCallType           0: repeated field, 1: repeated section.
     * @param       oModelContainer     The container that has data of model strings to generate incremented IDs and names.
     */
    $( document ).on( 'admin-page-framework_repeated_field', function( oEvent, iCallType, oModelContainer ){

        var _oThis     = jQuery( oEvent.target );
        var sFieldType = $( oEvent.target ).data( 'type' );
        var _aModel    = {};
        // var _aModel    = oModelContainer.data();
        _aModel[ 'call_type' ]      = iCallType;
        _aModel[ 'field_type' ]     = sFieldType;
        _aModel[ 'model_element' ]  = oModelContainer;
        _aModel[ 'added_element' ]  = _oThis;
        switch( iCallType ) {

            // Repeatable sections (calling a belonging field)
            case 1:
                _aModel[ 'incremented_from' ] = Number( oModelContainer.attr( 'data-largest_index' ) );
                _aModel[ 'index' ]            = _aModel[ 'incremented_from' ] + 1;
                _aModel[ 'id' ]               = oModelContainer.attr( 'data-section_id_model' );
                _aModel[ 'name' ]             = oModelContainer.attr( 'data-section_name_model' );
                _aModel[ 'flat_name' ]        = oModelContainer.attr( 'data-flat_section_name_model' );
                _aModel[ 'address' ]          = oModelContainer.attr( 'data-section_address_model' );
                break;

            // Repeatable fields
            default:
            case 0:
            case 2:
                _aModel[ 'incremented_from' ] = Number( oModelContainer.attr( 'data-largest_index' ) - 1 );
                _aModel[ 'index' ]            = _aModel[ 'incremented_from' ] + 1;
                _aModel[ 'id' ]               = oModelContainer.attr( 'data-field_tag_id_model' );
                _aModel[ 'name' ]             = oModelContainer.attr( 'data-field_name_model' );
                _aModel[ 'flat_name' ]        = oModelContainer.attr( 'data-field_name_flat_model' );
                _aModel[ 'address' ]          = oModelContainer.attr( 'data-field_address_model' );
                break;

        }

        $.each( $.fn.aAdminPageFrameworkRepeatFieldCallbacks, function( iIndex, aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ]; // '_nested', 'inline_mixed' are built-in
            if ( -1 !== $.inArray( sFieldType, [ '_nested', 'inline_mixed' ] ) ) {
                return true;    // continue
            }
            if ( -1 === $.inArray( sFieldType, _aFieldTypes ) ) {
                return true;    // continue
            }
            if ( 'function' !== typeof _hfCallback ) {
                return true;    // continue
            }
            _hfCallback( _oThis, _aModel );
        } );

    } );
    /**
     * Gets triggered when sorting sections stops.
     * @since       3.8.0
     */
    $( document ).on( 'admin-page-framework_stopped_sorting_sections', function( oEvent ){

        var _oThisNode = jQuery( oEvent.target );
        $.each( $.fn.aAdminPageFrameworkStoppedSortingSectionsCallbacks, function( iIndex, aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ];
            if ( 'function' !== typeof _hfCallback ) {
                return true;    // continue
            }
            _hfCallback( _oThisNode );
        });

    });

    /**
     * Supposed to get triggered when a repeatable field remove button is pressed.
     * @remark      Currently not used.
     */
    /* $( document ).on( 'admin-page-framework_removed_field', function( oEvent, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ){
        var _oThisNode = jQuery( oEvent.target );
        $.each( $.fn.aAdminPageFrameworkRemoveRepeatableFieldCallbacks, function( iIndex, aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ];
            if ( 2 < _aFieldTypes.length && -1 === $.inArray( sFieldType, _aFieldTypes ) ) {
                return true; // continue
            }
            if ( 'function' !== typeof _hfCallback ) {
                return true;    // continue
            }
            _hfCallback( _oThisNode, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex );
        });
    });   */

    /**
     * Gets triggered when a sortable field is dropped and the sort event occurred.
     */
    $.fn.callBackSortedFields = function( sFieldType, sID, iCallType ) {
        var oThisNode = this;
        $.fn.aAdminPageFrameworkSortedFieldsCallbacks.forEach( function( aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ]; // '_nested', 'inline_mixed' are bult-in
            if ( 2 < _aFieldTypes.length && -1 === $.inArray( sFieldType, _aFieldTypes ) ) {
                return true; // continue
            }
            if ( 'function' === typeof _hfCallback ) {
                _hfCallback( oThisNode, sFieldType, sID, iCallType );
            }
        });
    };

    /**
     * Gets triggered when sorting fields stopped.
     * @since   3.1.6
     */
    $.fn.callBackStoppedSortingFields = function( sFieldType, sID, iCallType ) {
        var oThisNode = this;
        $.fn.aAdminPageFrameworkStoppedSortingFieldsCallbacks.forEach( function( aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ]; // '_nested', 'inline_mixed' are built-in
            if ( 2 < _aFieldTypes.length && -1 === $.inArray( sFieldType, _aFieldTypes ) ) {
                return true; // continue
            }
            if ( 'function' === typeof _hfCallback ) {
                _hfCallback( oThisNode, sFieldType, sID, iCallType );
            }
        });
    };

    /**
     * Gets triggered when a widget of the framework is saved.
     * @since    3.2.0
     */
    $( document ).on( 'admin-page-framework_saved_widget', function( event, oWidget ){
        $.each( $.fn.aAdminPageFrameworkAddedWidgetCallbacks, function( iIndex, aCallback ) {
            var _hfCallback  = aCallback[ 0 ];
            var _aFieldTypes = aCallback[ 1 ];
            if ( 'function' !== typeof _hfCallback ) {
                return true;    // continue
            }
            _hfCallback( oWidget );
        });
    });

    /**
     * Registers callbacks. This will be called in each field type definition class.
     *
     * @since       unknown
     * @since       3.6.0       Changed the name from `registerAPFCallback()`.
     */
    $.fn.registerAdminPageFrameworkCallbacks = function( oCallbacks, aFieldTypeSlugs ) {

        // This is the easiest way to have default options.
        var oCallbacks = $.extend(
            {
                // The user specifies the settings with the following options.
                added_repeatable_field      : null, // @deprecated 3.8.8
                repeated_field              : null, // 3.8.8+
                removed_repeatable_field    : null, // @deprecated 3.6.0
                sorted_fields               : null,
                stopped_sorting_fields      : null,
                saved_widget                : null,
                stopped_sorting_sections    : null, // 3.8.0+
            },
            oCallbacks
        );
        var aFieldTypeSlugs = 'undefined' === typeof aFieldTypeSlugs
            ? []
            : aFieldTypeSlugs;
        aFieldTypeSlugs.push( '_nested', 'inline_mixed' );    // 3.8.0+

        // Store the callback functions
        $.fn.aAdminPageFrameworkAddRepeatableFieldCallbacks.push(
            [ oCallbacks.added_repeatable_field, aFieldTypeSlugs ]
        );

        $.fn.aAdminPageFrameworkRepeatFieldCallbacks.push(  // 3.8.8+
            [ oCallbacks.repeated_field, aFieldTypeSlugs ]
        );
        $.fn.aAdminPageFrameworkRemoveRepeatableFieldCallbacks.push(
            [ oCallbacks.removed_repeatable_field, aFieldTypeSlugs ]
        );
        $.fn.aAdminPageFrameworkSortedFieldsCallbacks.push(
            [ oCallbacks.sorted_fields, aFieldTypeSlugs ]
        );
        $.fn.aAdminPageFrameworkStoppedSortingFieldsCallbacks.push(
            [ oCallbacks.stopped_sorting_fields, aFieldTypeSlugs ]
        );
        $.fn.aAdminPageFrameworkAddedWidgetCallbacks.push(
            [ oCallbacks.saved_widget, aFieldTypeSlugs ]
        );

        // 3.8.0
        $.fn.aAdminPageFrameworkStoppedSortingSectionsCallbacks.push(
            [ oCallbacks.stopped_sorting_sections, aFieldTypeSlugs ]
        );

    };
    /**
     * An alias of the `registerAdminPageFrameworkCalbacks()` method.
     * @remark      Kept for backward compatibility. There are some custom field types which call the old method name.
     * @deprecated
     */
    $.fn.registerAPFCallback = function( oCallbacks, aFieldTypeSlugs ) {
        $.fn.registerAdminPageFrameworkCallbacks( oCallbacks, aFieldTypeSlugs );
    }

}( jQuery ));
(function ( $ ) {

    /**
     * The passed data from PHP.
     * @var AdminPageFrameworkScriptFormMain
     */
    var translation = AdminPageFrameworkScriptFormMain;

    /**
     * Bind field-repeating events to repeatable buttons for individual fields.
     * @remark      This method can be called from a fields container or a cloned field container.
     */
    $.fn.updateAdminPageFrameworkRepeatableFields = function( aSettings ) {

        var nodeThis            = this;
        // @todo check if this find() may be appropriate to determine the fields container when there are nested fields.
        var _sFieldsContainerID = nodeThis.find( '.repeatable-field-add-button' ).first().data( 'id' );
        var _oFieldsContainer   = $( '#' + _sFieldsContainerID );

        /* Store the fields specific options */
        var _aOptions = $.extend({
            // These are the defaults.
            max: 0,
            min: 0,
            fadein: 500,
            fadeout: 500,
            disabled: false,    // 3.8.13+
			      preserve_values: 0, // 3.8.19+
        }, aSettings );
        if ( ! _oFieldsContainer.data( 'repeatable' ) ) {
            _oFieldsContainer.data( 'repeatable', _aOptions );
        }

        /* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer to the options */
        var _oRepeatableButtons = $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' )
            .filter( function() {
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
            });
        _oRepeatableButtons.attr( 'data-max', _aOptions[ 'max' ] );
        _oRepeatableButtons.attr( 'data-min', _aOptions[ 'min' ] );
        _oRepeatableButtons.attr( 'data-fadein', _aOptions[ 'fadein' ] );
        _oRepeatableButtons.attr( 'data-fadeout', _aOptions[ 'fadeout' ] );
        _oRepeatableButtons.attr( 'data-preserve_values', _aOptions[ 'preserve_values' ] );

        /**
         * The Add button behavior - if the tag id is given, multiple buttons will be selected.
         * Otherwise, a field node is given and a single button will be selected.
         */
        var _oRepeatableAddButtons = $( nodeThis ).find( '.repeatable-field-add-button' );

        _oRepeatableAddButtons.off( 'click' );
        _oRepeatableAddButtons.on( 'click', function() {

            // 3.8.13+
            if ( $( this ).parent().data( 'disabled' ) ) {
                var _aDisabled = $( this ).parent().data( 'disabled' );
                tb_show( _aDisabled[ 'caption' ], $( this ).attr( 'href' ) );
                return false;
            }

            $( this ).addAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });

        /* The Remove button behavior */
        var _oRepeatableRemoveButton = $( nodeThis ).find( '.repeatable-field-remove-button' );

        _oRepeatableRemoveButton.off( 'click' );
        _oRepeatableRemoveButton.on( 'click', function() {

            $( this ).removeAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });

        /* If the number of fields is less than the set minimum value, add fields. */
        var _sFieldID           = _oRepeatableAddButtons.first().closest( '.admin-page-framework-field' ).attr( 'id' );
        var _nCurrentFieldCount = $( '#' + _sFieldsContainerID ).find( '.admin-page-framework-field' )
            .filter( function() {
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
            })
            .length;
        if ( _aOptions[ 'min' ] > 0 && _nCurrentFieldCount > 0 ) {
            if ( ( _aOptions[ 'min' ] - _nCurrentFieldCount ) > 0 ) {
                $( '#' + _sFieldID ).addAdminPageFrameworkRepeatableField( _sFieldID );
            }
        }

    };

    /**
     * Adds a repeatable field.
     *
     * This method is called when the user presses the + repeatable button.
     */
    $.fn.addAdminPageFrameworkRepeatableField = function( sFieldContainerID ) {

        if ( 'undefined' === typeof sFieldContainerID ) {
            sFieldContainerID = $( this ).closest( '.admin-page-framework-field' ).attr( 'id' );
        }

        var nodeFieldContainer  = $( '#' + sFieldContainerID );
        var nodeNewField        = nodeFieldContainer.clone(); // clone without bind events.
        var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
        var _sFieldsContainerID = nodeFieldsContainer.attr( 'id' );

        var _aOptions = nodeFieldsContainer.data( 'repeatable' );

        // If the set maximum number of fields already exists, do not add.
        if ( ! _aOptions ) {
            var _nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                .filter( function() {
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
                })
                .first();
            _aOptions = {
                max: _nodeButtonContainer.attr( 'data-max' ), // These are the defaults.
                min: _nodeButtonContainer.attr( 'data-min' ),
                fadein: _nodeButtonContainer.attr( 'data-fadein' ),
                fadeout: _nodeButtonContainer.attr( 'data-fadeout' ),
                preserve_values: _nodeButtonContainer.attr( 'data-preserve_values' ), // 3.8.19
            };
        }

        var _iFadein  = _aOptions[ 'fadein' ];
        var _iFadeout = _aOptions[ 'fadeout' ];

        // Show a warning message if the user tries to add more fields than the number of allowed fields.
        var sMaxNumberOfFields  = _aOptions[ 'max' ];
        var _oInnerFields       = nodeFieldsContainer.find( '.admin-page-framework-field' )
                                    .filter( function() {
                                        return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
                                    });
        var _oRepeatableButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                                    .filter( function() {
                                        return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
                                    });
        if ( sMaxNumberOfFields != 0 && _oInnerFields.length >= sMaxNumberOfFields ) {
            var nodeLastRepeaterButtons = _oRepeatableButtons.last();
            var sMessage                = $( this ).formatPrintText( translation.messages.cannotAddMore, sMaxNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + _sFieldsContainerID + '\" >' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;
        }

        // Empty values.
        if ( ! _aOptions[ 'preserve_values' ] ) {
            nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value
            nodeNewField.find( 'input[type=checkbox]' ).prop( 'checked', false ); // uncheck checkboxes.
        }
        nodeNewField.find( '.repeatable-error' ).remove(); // remove error messages.

        // Add the cloned new field element.
        if ( _iFadein ) {
            nodeNewField
                .hide()
                .insertAfter( nodeFieldContainer )
                .delay( 100 )
                .fadeIn( _iFadein );
        } else {
            nodeNewField.insertAfter( nodeFieldContainer );
        }

        // 3.6.0+ Increment name and id attributes of the newly cloned field.
        _incrementFieldAttributes( nodeNewField, nodeFieldsContainer );

        /**
         * Rebind the click event to the + and - buttons - important to update AFTER inserting the clone to the document node since the update method needs to count the fields.
         * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'.
         */
        nodeNewField.updateAdminPageFrameworkRepeatableFields();

        // It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone.
        nodeFieldContainer.find( 'input[type=radio][checked=checked]' )
            .prop( 'checked', true )
            .attr( 'checked', 'checked' );

        // Call back the registered functions.

        // @deprecated 3.8.8 Kept for backward compatibility as some custom field types rely on this method.
        nodeNewField.trigger(
            'admin-page-framework_added_repeatable_field',
            [
                nodeNewField.data( 'type' ), // field type slug
                nodeNewField.attr( 'id' ),   // element tag id
                0, // call type // call type, 0 : repeatable fields, 1: repeatable sections, 2: nested repeatable fields.
                0, // section index - @todo find the section index
                0  // field index - @todo find the field index
            ]
        );

        // 3.8.8+ _nested and inline_mixed field types have nested fields.
        // @todo check if this is okay as this applies to all inner fields including nested ones.
        $( nodeNewField ).find( '.admin-page-framework-field' ).addBack().trigger(
            'admin-page-framework_repeated_field',
            [
                0, // call type, 0 : repeatable fields, 1: repeatable sections
                jQuery( nodeNewField ).closest( '.admin-page-framework-fields' )    // model container
            ]
        );

        // If more than one fields are created, show the Remove button.
        var nodeRemoveButtons = nodeFieldsContainer
            .find( '.repeatable-field-remove-button' )
            .filter( function() {
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;
            })
        if ( nodeRemoveButtons.length > 1 ) {
            nodeRemoveButtons.css( 'visibility', 'visible' );
        }

        // Display/hide delimiters.
        nodeFieldsContainer.children( '.admin-page-framework-field' ).children( '.delimiter' ).show().last().hide();

        // Return the newly created element. The media uploader needs this
        return nodeNewField;

    };

        /**
         * Increments digits in field attributes.
         * @since       3.8.0
         */
        var _incrementFieldAttributes = function( oElement, oFieldsContainer ) {

            var _iFieldCount            = Number( oFieldsContainer.attr( 'data-largest_index' ) );
            var _iIncrementedFieldCount = _iFieldCount + 1;
            oFieldsContainer.attr( 'data-largest_index', _iIncrementedFieldCount );

            var _sFieldTagIDModel    = oFieldsContainer.attr( 'data-field_tag_id_model' );
            var _sFieldNameModel     = oFieldsContainer.attr( 'data-field_name_model' );
            var _sFieldFlatNameModel = oFieldsContainer.attr( 'data-field_name_flat_model' );
            var _sFieldAddressModel  = oFieldsContainer.attr( 'data-field_address_model' );

            oElement.incrementAttribute(
                'id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );
            oElement.find( 'label' ).incrementAttribute(
                'for', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );
            oElement.find( 'input,textarea,select,option' ).incrementAttribute(
                'id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );
            oElement.find( 'input,textarea,select' ).incrementAttribute(
                'name', // attribute name
                _iFieldCount, // increment from
                _sFieldNameModel // digit model
            );

            // Update the hidden input elements that contain field names for nested elements.
            oElement.find( 'input[type=hidden].element-address' ).incrementAttributes(
                [ 'name', 'value', 'data-field_address_model' ], // attribute names - these elements contain id values in the 'name' attribute.
                _iFieldCount,
                _sFieldAddressModel // digit model - this is
            );

            // For checkbox, select, and radio input types
            oElement.find( 'input[type=radio][data-id],input[type=checkbox][data-id],select[data-id]' ).incrementAttribute(
                'data-id', // attribute name
                _iFieldCount, // increment from
                _sFieldTagIDModel // digit model
            );

            // 3.8 For nested repeatable fields
            oElement.find( '.admin-page-framework-field,.admin-page-framework-fields,.admin-page-framework-fieldset' ).incrementAttributes(
                [ 'id', 'data-field_tag_id_model', 'data-field_id' ],
                _iFieldCount,
                _sFieldTagIDModel
            );
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_model' ],
                _iFieldCount,
                _sFieldNameModel
            );
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_flat', 'data-field_name_flat_model' ],
                _iFieldCount,
                _sFieldFlatNameModel
            );
            oElement.find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_address', 'data-field_address_model' ],
                _iFieldCount,
                _sFieldAddressModel
            );

        }


    /**
     * Removes a repeatable field.
      This method is called when the user presses the - repeatable button.
     */
    $.fn.removeAdminPageFrameworkRepeatableField = function() {

        /* Need to remove the element: the field container */
        var nodeFieldContainer  = $( this ).closest( '.admin-page-framework-field' );
        var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
        var _sFieldsContainerID = nodeFieldsContainer.attr( 'id' );
        var _aOptions = nodeFieldsContainer.data( 'repeatable' );

        /* If the set minimum number of fields already exists, do not remove */
        var sMinNumberOfFields  = _aOptions
            ? _aOptions[ 'min' ]
            : 0;
        var _oInnerFields        = nodeFieldsContainer.find( '.admin-page-framework-field' )
            .filter( function() {
                return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
            });
        if ( sMinNumberOfFields != 0 && _oInnerFields.length <= sMinNumberOfFields ) {
            var _oRepeatableButtons     = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' )
                .filter( function() {
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
                });
            var nodeLastRepeaterButtons = _oRepeatableButtons.last();
            var sMessage                = $( this ).formatPrintText( translation.messages.cannotRemoveMore, sMinNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + _sFieldsContainerID + '\">' + sMessage + '</span>' );
            var _repeatableErrors       = nodeFieldsContainer.find( '#repeatable-error-' + _sFieldsContainerID )
                .filter( function() {
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;  // Avoid dealing with nested field's elements.
                });
            if ( _repeatableErrors.length > 0 ) {
                _repeatableErrors.replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            var _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;
        }

        /* Remove the field */
        var _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
        nodeFieldContainer.fadeOut( _iFadeout, function() {
            $( this ).remove();
            var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove-button' )
                .filter( function() {
                    return $( this ).closest( '.admin-page-framework-fields' ).attr( 'id' ) === _sFieldsContainerID;
                });
            if ( 1 === nodeRemoveButtons.length ) {
                nodeRemoveButtons.css( 'visibility', 'hidden' );
            }
        } );

    };

}( jQuery ));
( function( $ ) {

  $( document ).ready( function() {
    // the parent element of the ul tag; The ul element holds li tags of titles.
    $( '.admin-page-framework-section-tabs-contents' ).createTabs();
  });

  $.fn.createTabs = function( asOptions ) {

    var _bIsRefresh = ( typeof asOptions === 'string' && asOptions === 'refresh' );
    if ( typeof asOptions === 'object' ) {
        var aOptions = $.extend(
            {},
            asOptions
        );
    }

    var _sURLHash = 'undefined' !== typeof window.location.hash
        ? window.location.hash
        : '';

    this.children( 'ul' ).each( function () {

        // First, check if the url has a hash that exists in this tab group.
        // Consider the possibility that multiple tab groups are in one page.
        var _bSetActive = false;
        $( this ).children( 'li' ).each( function( i ) {
            var sTabContentID = $( this ).children( 'a' ).attr( 'href' );
            if ( '' !== _sURLHash && sTabContentID === _sURLHash ) {
                _bSetActive = true;
                return false;
            }
        });

        // Second iteration
        $( this ).children( 'li' ).each( function( i ) {

            var sTabContentID = $( this ).children( 'a' ).attr( 'href' );

            // If the url hash is set, compare the content id with it. If it matches, activate it.
            if ( '' !== _sURLHash && sTabContentID === _sURLHash ) {
                $( this ).addClass( 'active' );
            }

            if ( ! _bIsRefresh && ! _bSetActive ) {
                $( this ).addClass( 'active' );
                _bSetActive = true;
            }

            if ( $( this ).hasClass( 'active' ) ) {
                $( sTabContentID ).show();
            } else {
                $( sTabContentID ).css( 'display', 'none' );
            }

            $( this ).addClass( 'nav-tab' );
            $( this ).children( 'a' ).addClass( 'anchor' );

            $( this ).off( 'click' ); // for refreshing
            $( this ).on( 'click', function( e ){

                e.preventDefault(); // Prevents jumping to the anchor which moves the scroll bar.

                // Remove the active tab and set the clicked tab to be active.
                $( this ).siblings( 'li.active' ).removeClass( 'active' );
                $( this ).addClass( 'active' );

                // Find the element id and select the content element with it.
                var sTabContentID = $( this ).find( 'a' ).attr( 'href' );
                var _oActiveContent = $( this ).parent().parent().find( sTabContentID ).css( 'display', 'block' );
                _oActiveContent.siblings( ':not( ul )' ).css( 'display', 'none' );

            });

        });

    });

  };


}( jQuery ));
(function($) {

    // Initialize
    $( document ).ready( function() {
        $( '.admin-page-framework-fields.sortable' ).each( function() {
            $( this ).enableAdminPageFrameworkSortableFields();
        } );
    });

    $.fn.enableAdminPageFrameworkSortableFields = function( sFieldsContainerID ) {

        var _oTarget    = 'string' === typeof sFieldsContainerID
            ? $( '#' + sFieldsContainerID + '.sortable' )
            : $( this );

        _oTarget.off( 'sortupdate' );
        _oTarget.off( 'sortstop' );
        var _oSortable  = _oTarget.sortable(
            // the options for the sortable plugin
            {
                items: '> div:not( .disabled )',
            }
        );

        // Callback the registered functions.
        _oSortable.on( 'sortstop', function() {
            $( this ).callBackStoppedSortingFields(
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );
        });
        _oSortable.on( 'sortupdate', function() {
            $( this ).callBackSortedFields(
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );
        });

    };
}( jQuery ));
(function($) {

    // Initialization
    $( document ).ready( function() {
        $( '.admin-page-framework-sections.sortable-section' ).each( function(){
            $( this ).enableAdminPageFrameworkSortableSections();
        } );
    });

    $.fn.enableAdminPageFrameworkSortableSections = function( sSectionsContainerID ) {

        var _oTarget    = 'string' === typeof sSectionsContainerID
            ? $( '#' + sSectionsContainerID + '.sortable-section' )
            : $( this );

        // For tabbed sections, enable the sort to the tabs.
        var _bIsTabbed      = _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' );
        var _bCollapsible   = 0 < _oTarget.children( '.admin-page-framework-section.is_subsection_collapsible' ).length;

        _oTarget        = _bIsTabbed
            ? _oTarget.find( 'ul.admin-page-framework-section-tabs' )
            : _oTarget;

        _oTarget.off( 'sortupdate' );
        _oTarget.off( 'sortstop' );

        var _aSortableOptions = {
                items: _bIsTabbed
                    ? '> li:not( .disabled )'
                    : '> div:not( .disabled, .admin-page-framework-collapsible-toggle-all-button-container )',
                handle: _bCollapsible
                    ? '.admin-page-framework-section-caption'
                    : false,

                stop: function(e,ui) {

                    // Callback the registered callback functions.
                    jQuery( this ).trigger(
                        'admin-page-framework_stopped_sorting_sections',
                        []  // parameters for the callbacks
                    );

                },


                // @todo Figure out how to allow the user to highlight text in sortable elements.
                // cancel: '.admin-page-framework-section-description, .admin-page-framework-section-title'

            }
        var _oSortable  = _oTarget.sortable( _aSortableOptions );

        if ( ! _bIsTabbed ) {

            _oSortable.on( 'sortstop', function() {

                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).first().show();
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).not( ':first' ).hide();

            } );

        }

    };
}( jQuery ));
( function( $ ) {
    $.fn.reverse = [].reverse;

    $.fn.formatPrintText = function() {
        var aArgs = arguments;
        return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
            return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
                ? aArgs[ parseInt( number ) + 1 ]
                : match;
        });
    };
}( jQuery ));
(function ( $ ) {

    $( document ).ready( function() {

        $( document ).ajaxComplete( function( event, XMLHttpRequest, ajaxOptions ) {

            // Determine which ajax request this is (we're after \"save-widget\")
            var _aRequest   = {}, _iIndex, _aSplit, _oWidget;
            var _aPairs     = 'string' === typeof ajaxOptions.data
                ? ajaxOptions.data.split( '&' )
                : {};
            for( _iIndex in _aPairs ) {
                _aSplit = _aPairs[ _iIndex ].split( '=' );
                _aRequest[ decodeURIComponent( _aSplit[ 0 ] ) ] = decodeURIComponent( _aSplit[ 1 ] );
            }
            // Only proceed if this was a widget-save request
            if( _aRequest.action && ( 'save-widget' === _aRequest.action ) ) {

                // Locate the widget block
                _oWidget = $( 'input.widget-id[value=\"' + _aRequest['widget-id'] + '\"]' ).parents( '.widget' );

                // Check if it is the framework widget.
                if ( $( _oWidget ).find( '.admin-page-framework-sectionset' ).length <= 0 ) {
                    return;
                }

                // Trigger manual save, if this was the save request and if we didn't get the form html response (the wp bug)
                if( ! XMLHttpRequest.responseText )  {
                    wpWidgets.save( _oWidget, 0, 1, 0 );
                    return;
                }

                // We got an response, this could be either our request above, or a correct widget-save call, so fire an event on which we can hook our js.
                $( document ).trigger(
                    'admin-page-framework_saved_widget',
                    _oWidget
                );

            }
        });

    });

}( jQuery ));