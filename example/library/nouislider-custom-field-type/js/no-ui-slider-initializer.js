(function ($) {
    jQuery( document ).ready( function(){

        /**
         * Initialize no ui slider with the given slider container node.
         *
         * @since       3.8.6
         * @param       DOM Node    oNode       The slider target DOM node object.
         * @param       boolean     bUpdate     Whether to update options.
         */
        var initializeNoUISlider = function( oNode, bUpdate ) {

            var _oSliderTarget = jQuery( oNode );
            var _aOptions = _oSliderTarget.data();

            if ( ! bUpdate ) {
                noUiSlider.create( oNode, _aOptions );
                oNode.noUiSlider.on( 'update', function( values, handle ) {
                    var _nValue = values[ handle ];
                    _nValue = parseFloat( _nValue ).toFixed( _aOptions.round );
                    jQuery( '#' + jQuery( oNode ).attr( 'data-id-' + handle ) )
                        .val( _nValue );
                });
                return;
            }
            oNode.noUiSlider.updateOptions( _aOptions );

        }

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

        }

        /**
         * Initialize toggle elements. Note that a pair of inputs (min and max) are parsed for each field.
         * So skip one of them.
         */
        jQuery( 'input[data-type=no_ui_slider]' ).each( function () {
            _initializeNoUISliders( this );
        } );

        /**
         * Interactive input elements.
         * @since       3.8.13
         */
        jQuery( 'input[data-type=no_ui_slider][data-interactive=1]' ).on( 'change', function(){

            // Retrieve all the inputs associated with the available handles.
            var _aValues = [];
            jQuery( this ).closest( '.admin-page-framework-field' ).children( '.admin-page-framework-input-label-container' )
                .find( 'input[data-type=no_ui_slider][data-interactive=1]' )
                .each( function(){
                    _aValues.push( this.value );
                } );

            // Set the values to the slider.
            var _oTargetSlider = jQuery( this ).closest( '.admin-page-framework-field' )
                .children( '.no-ui-sliders' )
                .first();
            var _nodeTargetSlider = _oTargetSlider.get( 0 );

            // Check if the range cen be expanded.
            var _aRange           = _oTargetSlider.data( 'range' );
            var _bCanExceedMin    = _oTargetSlider.data( 'can_exceed_min' );
            var _bCanExceedMax    = _oTargetSlider.data( 'can_exceed_max' );
            var _bIsMinExceeded   = parseFloat( _aRange[ 'min' ] ) > parseFloat( _aValues[ 0 ] );
            var _bIsMaxExceeded   = parseFloat( _aRange[ 'max' ] ) < parseFloat( _aValues[ _aValues.length - 1 ] );
            var _bUpdateSlider    = ( _bIsMinExceeded && _bCanExceedMin ) || ( _bIsMaxExceeded && _bCanExceedMax );
            if ( _bIsMinExceeded && _bCanExceedMin ) {
                _aRange[ 'min' ] = parseFloat( _aValues[ 0 ] );
            }
            if ( _bIsMaxExceeded && _bCanExceedMax ) {
                _aRange[ 'max' ] = parseFloat( _aValues[ _aValues.length - 1 ] );
            }
            if ( _bUpdateSlider ) {
                _oTargetSlider.data( 'range', _aRange );
                initializeNoUISlider( _nodeTargetSlider, true );    // update
            }
            _nodeTargetSlider.noUiSlider.set( _aValues );

        } );

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

                },
            },
            [ 'no_ui_slider' ]    // subject field type slugs
        );

    });
})(jQuery);
