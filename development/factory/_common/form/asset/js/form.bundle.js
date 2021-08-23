/*! Admin Page Framework - Form Main 1.2.4 */
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
            "\\$&"  // when this script was directory echoed in PHP, backslashes need to be escaped like "\\\\$&"
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

    // Initialize repeatable buttons
    $( document ).ready( function() {
        $( '.admin-page-framework-fields.repeatable' ).each( function() {
            var _buttonModel = $( this ).siblings( '.repeatable-field-buttons-model' );
            if ( ! _buttonModel.length ) {
                return true;
            }
            var _aSettings           = _buttonModel.data();
            var _buttonContainer     = _buttonModel.children( '.admin-page-framework-repeatable-field-buttons' ).first();
            _buttonModel.remove();
            var _buttonsNestedFields = $( _buttonContainer );
            var _buttonsSmall        = $( _buttonContainer ).clone();
            _buttonsNestedFields.find( '.repeatable-field-button' ).addClass( 'button-large' );
            _buttonsSmall.find( '.repeatable-field-button' ).addClass( 'button-small' );

            // For unnested fields
            var _childFields = $( this ).find( '> .admin-page-framework-field.without-child-fields' );
            var _oButtonPlaceHolders = _childFields.find( '.repeatable-field-buttons' );
            /* If the button place-holder is set in the field type definition, replace it with the created output */
            if ( _oButtonPlaceHolders.length > 0 ) {
                _oButtonPlaceHolders.replaceWith( _buttonsSmall );
            }
            /* Otherwise, insert the button element at the beginning of the field tag */
            else {
                /**
                 * Check whether the button container already exists for WordPress 3.5.1 or below and then add buttons.
                 */
                if ( ! _childFields.find( '.admin-page-framework-repeatable-field-buttons' ).length ) {
                    _childFields.prepend( _buttonsSmall );
                }
            }

            /**
             * For nested fields, add buttons to the fields tag.
             */
            $( this ).find( '> .admin-page-framework-field.with-child-fields' ).prepend( _buttonsNestedFields );
                /**
                 * Add buttons to the fields tag.
                 */
                // $( this ).find( '.admin-page-framework-field.with-nested-fields' ).prepend( _buttonsNestedFields );

                /**
                 * Support for inline mixed fields.
                 * @todo not sure why this is commented out. Remove this if it's okay.
                 */
                // $( this ).find( '.admin-page-framework-field.with-mixed-fields' ).prepend( _buttonsNestedFields );




            $( this ).updateAdminPageFrameworkRepeatableFields( _aSettings ); // Update the fields

        } );

    });    

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
        var _iFadeout = 500;
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
            _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;
        }

        /* Remove the field */
        _iFadeout = _aOptions ? _aOptions[ 'fadeout' ] : 500;
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

        $( '.admin-page-framework-sections.repeatable-section' ).each( function( index ){

            var _buttonModel    = $( this ).children( '.repeatable-section-buttons-model' ).first();
            var _repeatOptions  = _buttonModel.data();
            _buttonModel = _buttonModel.find( '.admin-page-framework-repeatable-section-buttons' ).first().detach();

            // Add buttons
            $( this ).find( '.admin-page-framework-section-caption' ).each( function(){

                $( this ).show();
                var _oButtons = $( _buttonModel[0].outerHTML );
                if ( $( this ).children( '.admin-page-framework-collapsible-section-title' ).children( 'fieldset' ).length > 0 ) {
                    _oButtons.addClass( 'section_title_field_sibling' );
                }
                var _oCollapsibleSectionTitle = $( this ).find( '.admin-page-framework-collapsible-section-title' );
                if ( _oCollapsibleSectionTitle.length ) {
                    _oButtons.find( '.repeatable-section-button' ).removeClass( 'button-large' );
                    _oCollapsibleSectionTitle.append( _oButtons );
                } else {
                    $( this ).prepend( _oButtons );
                }

            } );

            // Update the fields
            $( this ).updateAdminPageFrameworkRepeatableSections( _repeatOptions );

        } );

    });

    /**
     * The passed data from PHP.
     * @var AdminPageFrameworkScriptFormMain
     */
    var translation = AdminPageFrameworkScriptFormMain;

    /**
     *
     * @remark      This method can be from a sections container or a cloned section container.
     * @since       unknown
     * @since       3.6.0       Changed the name from `updateAPFRepeatableSections`.
     * @todo        Change the selector name 'repeatable-section-add-button' to something else to avoid apf version conflict.
     */
    $.fn.updateAdminPageFrameworkRepeatableSections = function( aSettings ) {

        var _oThis                = this;
        var _sSectionsContainerID = _oThis.find( '.repeatable-section-add-button' ).first().closest( '.admin-page-framework-sections' ).attr( 'id' );

        // Store the sections specific options in an array.
        if ( ! $.fn.aAdminPageFrameworkRepeatableSectionsOptions ) {
            $.fn.aAdminPageFrameworkRepeatableSectionsOptions = [];
        }
        if ( ! $.fn.aAdminPageFrameworkRepeatableSectionsOptions.hasOwnProperty( _sSectionsContainerID ) ) {
            $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ _sSectionsContainerID ] = $.extend(
                {
                    max: 0, // These are the defaults.
                    min: 0,
                    fadein: 500,
                    fadeout: 500,
                    disabled: 0,
                    preserve_values: 0
                },
                aSettings
            );
        }
        var _aOptions = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ _sSectionsContainerID ];

        // The Add button behavior - if the tag id is given, multiple buttons will be selected.
        // Otherwise, a section node is given and single button will be selected.
        $( _oThis ).find( '.repeatable-section-add-button' ).on( 'click', function() {

            // 3.8.13+
            if ( $( this ).parent().data( 'disabled' ) ) {
                var _aDisabled = $( this ).parent().data( 'disabled' );
                tb_show( _aDisabled[ 'caption' ], $( this ).attr( 'href' ) );
                return false;
            }

            $( this ).addAdminPageFrameworkRepeatableSection();
            return false; // will not click after that
        });

        // The Remove button behavior
        $( _oThis ).find( '.repeatable-section-remove-button' ).on( 'click', function() {
            $( this ).removeAdminPageFrameworkRepeatableSection();
            return false; // will not click after that
        });

        // If the number of sections is less than the set minimum value, add sections.
        var _sSectionID           = _oThis.find( '.repeatable-section-add-button' ).first().closest( '.admin-page-framework-section' ).attr( 'id' );
        var _nCurrentSectionCount = jQuery( '#' + _sSectionsContainerID ).find( '.admin-page-framework-section' ).length;
        if ( _aOptions[ 'min' ] > 0 && _nCurrentSectionCount > 0 ) {
            if ( ( _aOptions[ 'min' ] - _nCurrentSectionCount ) > 0 ) {
                $( '#' + _sSectionID ).addAdminPageFrameworkRepeatableSection( _sSectionID );
            }
        }

    };

    /**
     * Adds a repeatable section.
     *
     * @remark      Gets triggered when the user presses the repeatable `+` section button.
     */
    $.fn.addAdminPageFrameworkRepeatableSection = function( sSectionContainerID ) {

        // Local variables
        if ( 'undefined' === typeof sSectionContainerID ) {
            sSectionContainerID = $( this ).closest( '.admin-page-framework-section' ).attr( 'id' );
        }
        var nodeSectionContainer    = $( '#' + sSectionContainerID );
        var nodeNewSection          = nodeSectionContainer.clone(); // clone without bind events.
        var nodeSectionsContainer   = nodeSectionContainer.closest( '.admin-page-framework-sections' );
        var sSectionsContainerID    = nodeSectionsContainer.attr( 'id' );
        var nodeTabsContainer       = $( this ).closest( '.admin-page-framework-section-tabs-contents' )
            .children( '.admin-page-framework-section-tabs' )
            .first();

        var _iSectionIndex          = nodeSectionsContainer.attr( 'data-largest_index' );

        var _iFadein                = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadein' ];
        var _iFadeout               = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadeout' ];
        var _bPreserveValues        = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'preserve_values' ];

        // If the set maximum number of sections already exists, do not add.
        var _sMaxNumberOfSections   = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'max' ];
        if ( _sMaxNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length >= _sMaxNumberOfSections ) {
            var _nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var _sMessage                = $( this ).formatPrintText( translation.messages.cannotAddMoreSections, _sMaxNumberOfSections );
            var _nodeMessage             = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + _sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 ) {
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( _nodeMessage );
            } else {
                _nodeLastRepeaterButtons.before( _nodeMessage );
            }
            _nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;
        }

        // Empty the values.
        if ( ! _bPreserveValues ) {
            nodeNewSection.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );
        }
        nodeNewSection.find( '.repeatable-section-error' ).remove(); // remove error messages.

        // If this is not for tabbed sections, do not show the title.
        var _sSectionTabSlug = nodeNewSection.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
        if ( ! _sSectionTabSlug || _sSectionTabSlug === '_default' ) {
            nodeNewSection.find( '.admin-page-framework-section-title' ).not( '.admin-page-framework-collapsible-section-title' ).hide();
        }
        // Bind the click event to the collapsible section(s) bar. If a collapsible section is not added, the jQuery plugin is not added.
        if( 'function' === typeof nodeNewSection.enableAdminPageFrameworkCollapsibleButton ){
            nodeNewSection.find( '.admin-page-framework-collapsible-sections-title, .admin-page-framework-collapsible-section-title' ).enableAdminPageFrameworkCollapsibleButton();
        }

        // Add the cloned new field element.
        nodeNewSection.hide().insertAfter( nodeSectionContainer );
        /// For non tabbed sections, show it.
        if ( ! nodeTabsContainer.length || nodeSectionContainer.hasClass( 'is_subsection_collapsible' ) ) {
            nodeNewSection.delay( 100 ).fadeIn( _iFadein );
        }

        // 3.6.0+ Increment the id and name attributes of the newly cloned section.
        _incrementAttributes( nodeNewSection, _iSectionIndex, nodeSectionsContainer );

        // It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone.
        nodeSectionContainer.find( 'input[type=radio][checked=checked]' ).prop( 'checked', true );

        // Iterate each field one by one.
        $( nodeNewSection ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {

            // Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields.
            // @todo examine whether this is needed any longer.
            $( this ).updateAdminPageFrameworkRepeatableFields();

            // Callback the registered callback functions.

            // @deprecated 3.8.8 Kept for backward compatibility.
            $( this ).trigger(
                'admin-page-framework_added_repeatable_field',
                [
                    $( this ).data( 'type' ), // field type slug
                    $( this ).attr( 'id' ), // element tag id
                    1, // call type, 0: repeatable fields, 1: repeatable sections, (not implemented yet - 2: parent fields, 3: parent sections)
                    _iSectionIndex,
                    iFieldIndex
                ]
            );

            // 3.8.8
            $( this ).trigger(
                'admin-page-framework_repeated_field',
                [
                    1, // call type, 0: repeatable fields, 1: repeatable sections,
                    jQuery( nodeNewSection ).closest( '.admin-page-framework-sections' )    // model container
                ]
            );

        });

        // Rebind the click event to the repeatable sections buttons - important to update AFTER inserting the clone to the document node since the update method need to count sections.
        // Also do this after updating the attributes since the script needs to check the last added id for repeatable section options such as 'min'.
        nodeNewSection.updateAdminPageFrameworkRepeatableSections();

        // Rebind sortable fields - iterate sortable fields containers.
        nodeNewSection.find( '.admin-page-framework-fields.sortable' ).each( function() {
            $( this ).enableAdminPageFrameworkSortableFields();
        });

        // For tabbed sections - add the title tab list.
        if ( nodeTabsContainer.length > 0 && ! nodeSectionContainer.hasClass( 'is_subsection_collapsible' ) ) {

            // The clicked (copy source) section tab.
            var nodeTab     = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );
            var nodeNewTab  = nodeTab.clone();

            nodeNewTab.removeClass( 'active' );
            if ( ! _bPreserveValues ) {
                nodeNewTab.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value
            }

            // Add the cloned new field tab.
            nodeNewTab
                .hide()
                .insertAfter( nodeTab )
                .delay( 10 )
                .fadeIn( _iFadein );

            _incrementAttributes( nodeNewTab, _iSectionIndex, nodeSectionsContainer );

            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );

        }

        // Increment the largest index attribute.
        nodeSectionsContainer.attr( 'data-largest_index', Number( _iSectionIndex ) + 1 );

        // If more than one sections are created, show the Remove button.
        var _nodeRemoveButtons =  nodeSectionsContainer.find( '.repeatable-section-remove-button' );
        if ( _nodeRemoveButtons.length > 1 ) {
            _nodeRemoveButtons.show();
        }

        // Return the newly created element.
        return nodeNewSection;

    };
        /**
         *
         */
        var _incrementAttributes = function( oElement, iSectionsCount, oSectionsContainer ) {

            var _sSectionIDModel        = oSectionsContainer.attr( 'data-section_id_model' );
            var _sSectionNameModel      = oSectionsContainer.attr( 'data-section_name_model' );
            var _sSectionFlatNameModel  = oSectionsContainer.attr( 'data-flat_section_name_model' );

            $( oElement ).incrementAttribute(
                'id', // attribute name
                iSectionsCount, // increment from
                _sSectionIDModel // digit model
            );
            $( oElement ).find( 'tr.admin-page-framework-fieldrow, .admin-page-framework-fieldset, .admin-page-framework-fields, .admin-page-framework-field, table.form-table, input,textarea,select,option' )
                .incrementAttribute(
                    'id',
                    iSectionsCount,
                    _sSectionIDModel
                );

            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttribute(
                'data-field_tag_id_model',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_model' ],
                iSectionsCount,
                _sSectionNameModel
            );
            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttributes(
                [ 'data-field_name_flat', 'data-field_name_flat_model', 'data-field_address', 'data-field_address_model' ],
                iSectionsCount,
                _sSectionFlatNameModel
            );

            // For checkbox, select, and radio input types
            $( oElement ).find( 'input[type=radio][data-id],input[type=checkbox][data-id],select[data-id]' ).incrementAttribute(
                'data-id', // attribute name
                iSectionsCount, // increment from
                _sSectionIDModel // digit model
            );

        // @todo this may be able to be removed
            $( oElement ).find( '.admin-page-framework-fieldset' ).incrementAttribute(
                'data-field_id',
                iSectionsCount,
                _sSectionIDModel
            );

            // holds the fields container ID referred by the repeater field script.
            $( oElement ).find( '.repeatable-field-add-button' ).incrementAttribute(
                'data-id',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( 'label' ).incrementAttribute(
                'for',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( 'input:not(.element-address),textarea,select' ).incrementAttribute(
                'name',
                iSectionsCount,
                _sSectionNameModel
            );

            // Section Tabs
            $( oElement ).find( 'a.anchor' ).incrementAttribute(
                'href', // attribute names - this elements contains id values in the 'name' attribute.
                iSectionsCount,
                _sSectionIDModel // digit model - this is
            );

            // Update the hidden input elements that contain dynamic field names for nested elements.
            $( oElement ).find( 'input[type=hidden].element-address' ).incrementAttributes(
                [ 'name', 'value', 'data-field_address_model' ], // attribute names - this elements contains id values in the 'name' attribute.
                iSectionsCount,
                _sSectionFlatNameModel // digit model - this is
            );

        }

    /**
     * Removes a repeatable section.
     * @remark  Triggered when the user presses the repeatable `-` section button.
     */
    $.fn.removeAdminPageFrameworkRepeatableSection = function() {

        // Local variables - preparing to remove the sections container element.
        var nodeSectionContainer    = $( this ).closest( '.admin-page-framework-section' );
        var sSectionContainerID     = nodeSectionContainer.attr( 'id' );
        var nodeSectionsContainer   = $( this ).closest( '.admin-page-framework-sections' );
        var sSectionsContainerID    = nodeSectionsContainer.attr( 'id' );
        var nodeTabsContainer       = $( this ).closest( '.admin-page-framework-section-tabs-contents' )
            .children( '.admin-page-framework-section-tabs' )
            .first();
        var nodeTabs                = nodeTabsContainer.children( '.admin-page-framework-section-tab' );

        var _iSectionIndex          = nodeSectionsContainer.attr( 'data-largest_index' );

        var _iFadein                = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadein' ];
        var _iFadeout               = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadeout' ];

        // If the set minimum number of sections already exists, do not remove.
        var _sMinNumberOfSections = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'min' ];
        if ( _sMinNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length <= _sMinNumberOfSections ) {
            var _nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var _sMessage                = $( this ).formatPrintText( translation.messages.cannotRemoveMoreSections, _sMinNumberOfSections );
            var _nodeMessage             = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + _sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 ) {
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( _nodeMessage );
            } else {
                _nodeLastRepeaterButtons.before( _nodeMessage );
            }
            _nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;
        }

        /**
         * Call the registered callback functions
         *
         * @since 3.0.0
         * @since 3.1.6 Changed it to do after removing the element.
         */
        var _oNextAllSections           = nodeSectionContainer.nextAll();
        var _bIsSubsectionCollapsible   = nodeSectionContainer.hasClass( 'is_subsection_collapsible' );

        // Remove the section
        // nodeSectionContainer.remove(); // @deprecated    3.6.0
        nodeSectionContainer.fadeOut( _iFadeout, function() {

            $( this ).remove();

            // Count the remaining Remove buttons and if it is one, disable the visibility of it.
            var _nodeRemoveButtons = nodeSectionsContainer.find( '.repeatable-section-remove-button' );
            if ( 1 === _nodeRemoveButtons.length ) {
                _nodeRemoveButtons.css( 'display', 'none' );

                // Also, if this is not for tabbed sections, do show the title.
                var _sSectionTabSlug = nodeSectionsContainer.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
                if ( ! _sSectionTabSlug || '_default' === _sSectionTabSlug ) {
                    nodeSectionsContainer.find( '.admin-page-framework-section-title' ).first().show();
                }
            }

        } );


        // Decrement the names and ids of the next following siblings.
        _oNextAllSections.each( function( _iIterationIndex ) {

            // @todo set the section index
            var _iSectionIndex = _iIterationIndex;

            // Call the registered callback functions.
            // @deprecated  3.6.0
            // $( this ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {
                // $( this ).callBackRemoveRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1, _iSectionIndex, iFieldIndex );
            // });
        });

        // For tabbed sections - remove the title tab list.
        if ( nodeTabsContainer.length > 0 && nodeTabs.length > 1 && ! _bIsSubsectionCollapsible ) {
            var _oSelectionTab = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );

            if ( _oSelectionTab.prev().length ) {
                _oSelectionTab.prev().addClass( 'active' );
            } else {
                _oSelectionTab.next().addClass( 'active' );
            }

            _oSelectionTab.fadeOut( _iFadeout, function() {
                $( this ).delay( 100 ).remove();
            } );
            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );

        }

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
/**
 * Extends the core wp-pointer tooltip jQuery widget to add additional features.
 */
(function($){

	var zindex = 9999;

  // Extend wp.pointer jQuery widget
  $.widget( 'admin-page-framework.pointerTooltip', $.wp.pointer, {
   /**
    * Overrides the reposition() method.
    * The show() method is replaced with fadeIn().
    */
    reposition: function() {
      var position;

      if ( this.options.disabled ) {
        return;
      }

      position = this._processPosition( this.options.position );

      // Reposition pointer.
      this.pointer.css({
        top: 0,
        left: 0,
        zIndex: zindex++ // Increment the z-index so that it shows above other opened pointers.
      });
      this.pointer.fadeIn( this.options.fadeIn );
          var _optionsPosition = $.extend(
            {
              of: this.element,
              collision: 'fit none'
            },
            position
          );
      this.pointer.position( _optionsPosition ); // The object comes before this.options.position so the user can override position.of.
      this.repoint();
    },
    _create: function() {
      this._super();
      if ( this.options.pointerHeight ) {
        this.pointer
          .css({
            height: this.options.pointerHeight+'px'
          });
      }
    },
    get: function() {
      return this.pointer;
    }

  } );

}(jQuery));
/**
 * Displays tips for fields in a tooltip.
 *
 * It provides the $[ 'admin-page-framework-tooltip' ]( options ) jQuery plugin method.
 * The name uses hyphens for the user's text domain to be replaced. So the compiled framework script will be entirely ported with without the keyword of `admin-page-framework`.
 *
 * To use the method,
 * ```
 * $( '.my-tooltip' )[ 'admin-page-framework-form-tooltip' ]( 'Hi, this is a tooltip content!' );
 * ```
 * Or have a text message enclosed in an element with a class `admin-page-framework-form-tooltip-content`.
 * ```
 *    <span class="my-tooltip dashicons dashicons-editor-help">
 *      <span class="admin-page-framework-form-tooltip-content">
 *        Some text
 *      </span>
 *    </span>
 * ```
 * Then, call it like
 * ```
 * $( '.my-tooltip' )[ 'admin-page-framework-form-tooltip' ]();
 * ```
 * Or
 * ```
 *    <span class="my-tooltip dashicons dashicons-editor-help" data-tooltip-content="Hello"></span>
 * ```
 *
 * If it the script is loaded, elements with the .admin-page-framework-from-tooltip class will be automatically parsed.
 * So not to call the `[ 'admin-page-framework-form-tooltip' ]()` method, just create an element with the selector
 * and it should automatically have a tooltip.
 *
 *
 * When the framework file is compiled, replace the keyword `admin-page-framework` with your text domain.
 */
(function($){

  // Initialize
  $( document ).ready( function() {
    $( '.admin-page-framework-form-tooltip' )[ 'admin-page-framework-form-tooltip' ]();
  } );

  $.fn[ 'admin-page-framework-form-tooltip' ] = function( options ) {
    if ( 'string' === typeof options ) {
      options = {
        content: isHTML( options ) ? options : "<span>" + options + "</span>"
      }
    }
    initialize( this, options )
  };

  function initialize( target, options ) {

    var _this = $( target );
    var _content = $( target ).attr( 'data-tooltip-content' );
    _content = _content ? _content : undefined;

    // Format options
    options = 'undefined' === typeof options ? {} : options;
    options = $.extend( {}, {
      pointerClass: 'admin-page-framework-form-tooltip-balloon',
      width: $( target ).data( 'width' ) || options.width || 340,
      shown: false,        // initial visibility
      content: _content,
      oneOff: false,
      // whether to close the tooltip automatically when the mouse leaves. do not turn on when the oneOff is on, it will disappear immediately
      autoClose: true,
      noArrow: false,
    }, options );
    options.pointerClass += options.noArrow ? ' no-arrow' : '';

    // Disable the CSS default tooltip
    $( _this ).removeClass( 'no-js' );

    if ( options.shown ) {
      handleTooltip( target, options );
    }

    var _pointerTooltip = $( _this );
    if ( ! options.oneOff ) {
      _pointerTooltip.on( 'mouseover touchend', options, handleTooltipCallback );
    }

  }

  function handleTooltipCallback( event ) {
    handleTooltip( this, event.data );
  }
  function handleTooltip( self, options ) {

    var _body      = $( 'body' );
    var _width     = options.width;
    var _content   = 'undefined' !== typeof options.content
      ? $( isHTML( options.content ) ? options.content : "<span>" + options.content + "</span>" )
      : $( self ).find( '.admin-page-framework-form-tooltip-content' ).clone();
    var _offscreen  = $( self ).offset().left + $( self ).width() + _width > _body.offset().left + _body.width();

    // Open the tooltip
    var _options    = $.extend( true, {}, options, {
      pointerWidth: _width,
      content: function() {
        return _content.html();
      },
      position: {
        edge: _offscreen ? 'top' : 'left',
        align: _offscreen ? 'center' : 'left',
        within: _offscreen ? _body : $( self ).closest( '.admin-page-framework-field, .admin-page-framework-fieldrow, .admin-page-framework-section' ),
      },
      buttons: function() {},
      close: function() {},
    }, options );
    _options.pointerClass += _offscreen ? ' offscreen' : '';

    debugLog( 'options', _options );
    $( self ).pointerTooltip( _options )
      .pointerTooltip( 'open' );

    // Handle toolitip closing
    var _self    = self;
    if ( options.autoClose ) {
      handleAutoClose( _self, _content );
    } else {
      setTimeout( function() {
        handleCloseOnEmptySpace( _self, _content, options );
      }, 200 );
    }
    /// For mobile devices
    setTimeout( function() {
      handleCloseOnMobile( _self, _content );
    }, 200 );

  }

  function handleCloseOnEmptySpace( self, content, options ) {

    var _class = options.pointerClass.split( ' ' )[ 0 ];
    var _emptySpace = $( 'body' ).not( '.' + _class );
    _emptySpace.on( 'click', ':not(.' + _class + ')', _closeTooltipOnEmptySpace );
    function _closeTooltipOnEmptySpace( event ) {
      if ( $( this ).closest( '.' + _class ).length ) {
        return;
      }
      _emptySpace.off( 'click', _closeTooltipOnEmptySpace );
      $( self ).pointerTooltip( 'close' );
      content.remove();
    }
  }

  function handleCloseOnMobile( self, content ) {
    var _body = $( 'body' );
    _body.on( 'touchstart', _closeTooltipMobile );
    function _closeTooltipMobile( event ) {
      _body.off( 'touchstart', _closeTooltipMobile );
      $( self ).pointerTooltip( 'close' );
      content.remove();
    }
  }
  function handleAutoClose( self, content ) {
      /// For non-mobile devices
      $( self ).add( '.admin-page-framework-form-tooltip-balloon' ).on( 'mouseleave', function( event ){
        var _selfMouseLeave = this;
        // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
        var _timeoutId = setTimeout(function(){
          $( self ).pointerTooltip( 'close' );
          content.remove();
          $( self ).off( 'mouseleave' );
          $( _selfMouseLeave ).off( 'mouseleave' );
          $( self ).off( 'mouseenter' );
          $( _selfMouseLeave ).off( 'mouseenter' );
        }, 1000 );
        $( self ).data( 'timeoutId', _timeoutId );

      } );
      $( self ).add( '.admin-page-framework-form-tooltip-balloon' ).on( 'mouseenter', function(){
        clearTimeout( $( self ).data('timeoutId' ) );
      });
  }

  function isHTML(str) {
    var a = document.createElement('div');
    a.innerHTML = str;
    for (var c = a.childNodes, i = c.length; i--; ) {
      if ( 1 === c[ i ].nodeType) {
        return true;
      }
    }
    return false;
  }

  function debugLog( ...message ) {
    if ( ! parseInt( AdminPageFrameworkScriptFormMain.debugMode ) ) {
      return;
    }
    console.log( '[APF]', ...message );
  }

}(jQuery));
( function( $ ) {

    /**
     * Checks whether an element is off screen or not.
     *
     * ```
     * // returns all elements that are offscreen
     * $(':offscreen');
     *
     * // boolean returned if element is offscreen
     * $('div').is(':offscreen');
     * ```
     *
     * @see https://stackoverflow.com/a/8897628
     * @param el
     * @returns {boolean}
     */
    $.expr.filters.offscreen = function(el) {
      var rect = el.getBoundingClientRect();
      return (
               (rect.x + rect.width) < 0 
                 || (rect.y + rect.height) < 0
                 || (rect.x > window.innerWidth || rect.y > window.innerHeight)
             );
    };    
    
    $.fn.reverse = [].reverse;

    $.fn.formatPrintText = function() {
        var aArgs = arguments;
        return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
            return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
                ? aArgs[ parseInt( number ) + 1 ]
                : match;
        });
    };

    /**
     * Compare two software version numbers (e.g. 1.7.1)
     * Returns:
     *
     *  0 if they're identical
     *  negative if v1 < v2
     *  positive if v1 > v2
     *  Nan if they in the wrong format
     *
     *  E.g.:
     *
     *  assert(version_number_compare("1.7.1", "1.6.10") > 0);
     *  assert(version_number_compare("1.7.1", "1.7.10") < 0);
     *
     *  "Unit tests": http://jsfiddle.net/ripper234/Xv9WL/28/
     *
     *  Taken from http://stackoverflow.com/a/6832721/11236
     *  @since 3.9.0
     *  @see   https://stackoverflow.com/a/6832721
     */
    $.fn.compareVersionNumbers = function( v1, v2 ){
        var v1parts = v1.split('.');
        var v2parts = v2.split('.');

        // First, validate both numbers are true version numbers
        function validateParts(parts) {
            for (var i = 0; i < parts.length; ++i) {
                if (!isPositiveInteger(parts[i])) {
                    return false;
                }
            }
            return true;
        }
        if (!validateParts(v1parts) || !validateParts(v2parts)) {
            return NaN;
        }

        for (var i = 0; i < v1parts.length; ++i) {
            if (v2parts.length === i) {
                return 1;
            }

            if (v1parts[i] === v2parts[i]) {
                continue;
            }
            if (v1parts[i] > v2parts[i]) {
                return 1;
            }
            return -1;
        }

        if (v1parts.length !== v2parts.length) {
            return -1;
        }

        return 0;

        function isPositiveInteger(x) {
            // http://stackoverflow.com/a/1019526/11236
            return /^\d+$/.test(x);
        }
    }

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