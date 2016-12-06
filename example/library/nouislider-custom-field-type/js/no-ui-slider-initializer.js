(function ($) {
    jQuery( document ).ready( function(){

        /**
         * Initialize no ui slider with the given slider container node.
         *
         * @since       0.0.1
         * @param       DOM Node    oNode       The slider target DOM node object.
         * @param       boolean     bUpdate     Whether to update options.
         */
        var initializeNoUISlider = function( oNode, bUpdate ) {

            var _oSliderTarget = jQuery( oNode );
            var _aOptions      = _oSliderTarget.data();

            if ( bUpdate ) {
                oNode.noUiSlider.updateOptions( _aOptions );
                return;
            }

            var _bAllowEmpty   = _oSliderTarget.data( 'allow_empty' );

            noUiSlider.create( oNode, _aOptions );

            /**
             * When the slider is updated, update the text input value as well.
             */
            // If the slider is manually moved, update the input regardless it is empty or not.
            oNode.noUiSlider.on( 'change', function( values, handle ) {
                var _nValue = values[ handle ];
                _nValue     = parseFloat( _nValue ).toFixed( _aOptions.round );
                var _oInput = jQuery( '#' + jQuery( oNode ).attr( 'data-id-' + handle ) );
                _oInput .val( _nValue );
            });
            // If the user types an empty value and `allow_empty` is enabled, leave it empty.
            oNode.noUiSlider.on( 'update', function( values, handle ) {
                var _nValue = values[ handle ];
                _nValue     = parseFloat( _nValue ).toFixed( _aOptions.round );
                var _oInput = jQuery( '#' + jQuery( oNode ).attr( 'data-id-' + handle ) );
                if ( _bAllowEmpty && '' === _oInput.val() ) {
                    return;
                }
                _oInput .val( _nValue );

            });

        };

        /**
         * Initialize no ui sliders by the given input node.
         *
         * @param       nodeThis        The input dom node to parse.
         */
        var _initializeNoUISliders = function( nodeThis ) {

            var _oTargetSlider = jQuery( nodeThis ).closest( '.admin-page-framework-field' )
                .children( '.no-ui-sliders' )
                .first();

            // Set the input ID to the target slider element.
            var _iIndex = jQuery( nodeThis ).data( 'key' );  // should be an integer that is an index set to the `start` argument
            _iIndex = _iIndex ? _iIndex : 0;
            _oTargetSlider.attr( 'data-id-' + _iIndex, jQuery( nodeThis ).attr( 'id' ) );

            // Initialise only if the number of handles matches the parsing index,
            if ( jQuery( nodeThis ).data( 'handles' ) !== ( _iIndex + 1 ) ) {
                return true; // skip
            }

            initializeNoUISlider(
                jQuery( nodeThis ).closest( '.admin-page-framework-field' )
                    .children( '.no-ui-sliders' )
                    .first().get( 0 )
            );

        };

        /**
         * Initialize toggle elements. Note that a pair of inputs (min and max) are parsed for each field.
         * So skip one of them.
         */
        jQuery( 'input[data-type=no_ui_slider]' ).each( function () {
            _initializeNoUISliders( this );
        } );

        /**
         * Interactive input elements.
         * @since       0.0.4
         */
        jQuery( 'input[data-type=no_ui_slider][data-interactive=1]' ).on( 'change', function(){

            var _oTargetSlider    = jQuery( this ).closest( '.admin-page-framework-field' )
                .children( '.no-ui-sliders' )
                .first();
            var _nodeTargetSlider = _oTargetSlider.get( 0 );
            var _bCanSliderUpdate = _oTargetSlider.data( 'can_exceed_min' ) || _oTargetSlider.data( 'can_exceed_max' );
            var _bAllowEmpty      = _oTargetSlider.data( 'allow_empty' );

            // Retrieve all the inputs associated with the available handles.
            var _aRawValues       = _getInputValues( this );
            var _aValues          = _getInputValuesSanitized( _aRawValues, this );

            // Set the values to the slider.
            if ( _bCanSliderUpdate ) {
                // There is a possibility that the slider needs to be updated.
                if ( _bAllowEmpty ) {
                    _setSliderValuesWithRangeAndAllowEmpty( _nodeTargetSlider, _aRawValues );
                } else {
                    _setSliderValuesWithRange( _nodeTargetSlider, _aValues );
                }
            }

            /**
             * Update the slider handle positions and associated input values.
             */
            _nodeTargetSlider.noUiSlider.set( _aValues );

            // Overwrite the text input values.
            if ( _bAllowEmpty ) {
                _setEmptyInputValues( this, _aRawValues );
            }

        } );

            function _setEmptyInputValues( nodeInput, aValues ) {

                jQuery( nodeInput ).closest( '.admin-page-framework-field' ).children( '.admin-page-framework-input-label-container' )
                    .find( 'input[data-type=no_ui_slider]' )
                    .each( function( _iIndex ){
                        if ( undefined === aValues[ _iIndex ] ) {
                            return true;    // continue
                        }
                        if ( '' !== aValues[ _iIndex ] ) {
                            return true;
                        }
                        this.value = aValues[ _iIndex ];
                    } );
            }

            function _getInputValuesSanitized( aValues, nodeInput ) {

                var _aValues = [];
                var _aRange  = jQuery( nodeInput ).closest( '.admin-page-framework-field' ).children( '.no-ui-sliders' )
                    .first()
                    .data( 'range' );

                var _iLength = aValues.length;
                jQuery.each( aValues, function( _iIndex, _nsValue ){
                    if ( '' === _nsValue ) {
                        // If it is the first item, set the min value
                        if ( 0 === _iIndex ) {
                            _nsValue = _aRange[ 'min' ];
                        }
                        // If it is the last item, set the max value
                        if ( ( _iLength - 1 )=== _iIndex ) {
                            _nsValue = _aRange[ 'max' ];
                        }
                    }
                    _aValues[ _iIndex ] = _nsValue;
                } );
                return _aValues;
            }
            /**
             * Retrieves all the input values associated with handles.
             * @param nodeInput
             * @private
             * @return  array
             */
            function _getInputValues( nodeInput ) {
                var _aValues = [];
                jQuery( nodeInput ).closest( '.admin-page-framework-field' ).children( '.admin-page-framework-input-label-container' )
                    .find( 'input[data-type=no_ui_slider][data-interactive=1]' )
                    .each( function(){
                        var _nsValue = '' === jQuery.trim( this.value ) ? '' : this.value;
                        _aValues.push( _nsValue );
                    } );
                return _aValues;
            }

            /**
             *
             * @param nodeTargetSlider
             * @param aValues
             * @private
             * @return  array       input values.
             */
            function _setSliderValuesWithRange( nodeTargetSlider, aValues ) {

                var _oTargetSlider    = jQuery( nodeTargetSlider );
                var _aRange           = _oTargetSlider.data( 'range' );
                var _bCanExceedMin    = _oTargetSlider.data( 'can_exceed_min' );
                var _bCanExceedMax    = _oTargetSlider.data( 'can_exceed_max' );
                var _nMin             = parseFloat( aValues[ 0 ] );
                var _nMax             = parseFloat( aValues[ aValues.length - 1 ] );
                var _bIsMinExceeded   = parseFloat( _aRange[ 'min' ] ) > _nMin;
                var _bIsMaxExceeded   = parseFloat( _aRange[ 'max' ] ) < _nMax;
                var _bUpdateSlider    = ( _bIsMinExceeded && _bCanExceedMin ) || ( _bIsMaxExceeded && _bCanExceedMax );
                if ( ! _bUpdateSlider ) {
                    return;
                }
                if ( _bIsMinExceeded && _bCanExceedMin ) {
                    _aRange[ 'min' ] = _nMin;
                }
                if ( _bIsMaxExceeded && _bCanExceedMax ) {
                    _aRange[ 'max' ] = _nMax;
                }

                // Update the slider's range
                _oTargetSlider.data( 'range', _aRange );
                initializeNoUISlider( nodeTargetSlider, true );    // update

            }
            /**
             * @remark Note that an empty value `` represents no limit.
             * @param nodeTargetSlider
             * @param aValues
             * @private
             * @return  void
             */
            function _setSliderValuesWithRangeAndAllowEmpty( nodeTargetSlider, aValues ) {

                var _oTargetSlider    = jQuery( nodeTargetSlider );
                var _aRange           = _oTargetSlider.data( 'range' );
                var _bCanExceedMin    = _oTargetSlider.data( 'can_exceed_min' );
                var _bCanExceedMax    = _oTargetSlider.data( 'can_exceed_max' );
                var _bIsMinEmpty      = '' === jQuery.trim( aValues[ 0 ] );
                var _bIsMaxEmpty      = '' === jQuery.trim( aValues[ aValues.length - 1 ] );
                var _nMin             = parseFloat( aValues[ 0 ] );
                var _nMax             = parseFloat( aValues[ aValues.length - 1 ] );
                var _bIsMinExceeded   = _bIsMinEmpty || ( parseFloat( _aRange[ 'min' ] ) > _nMin );
                var _bIsMaxExceeded   = _bIsMaxEmpty || ( parseFloat( _aRange[ 'max' ] ) < _nMax );
                var _bUpdateSlider    = ( _bIsMinExceeded && _bCanExceedMin ) || ( _bIsMaxExceeded && _bCanExceedMax );
                if ( ! _bUpdateSlider ) {
                    return;
                }
                if ( _bIsMinExceeded && _bCanExceedMin ) {
                    _aRange[ 'min' ] = _bIsMinEmpty
                        ? _aRange[ 'min' ]
                        : _nMin;
                }
                if ( _bIsMaxExceeded && _bCanExceedMax ) {
                    _aRange[ 'max' ] = _bIsMaxEmpty
                        ? _aRange[ 'max' ]
                        : _nMax;
                }

                // Update the slider'a range.
                _oTargetSlider.data( 'range', _aRange );
                initializeNoUISlider( nodeTargetSlider, true );

            }

        /**
         * Register callbacks.
         */
        jQuery().registerAdminPageFrameworkCallbacks(
            {
                /**
                 * Called when a field of this field type gets repeated.
                 */
                repeated_field: function( oCloned, aModel ) {

                    oCloned.children( '.no-ui-sliders' ).empty();

                    // Initialize the event bindings.
                    oCloned.find( 'input[data-type=no_ui_slider]' ).each( function () {
                        _initializeNoUISliders( this );
                    });

                }
            },
            [ 'no_ui_slider' ]    // subject field type slugs
        );

    });
})(jQuery);
