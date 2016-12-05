(function ($) {
    jQuery( document ).ready( function(){

        /**
         * Initialize no ui slider with the given slider container node.
         *
         * @since 3.8.6
         * @param       oNode       The slider target DOM node object.
         */
        var initializeNoUISlider = function( oNode ) {

            var _oSliderTarget = jQuery( oNode );
            var _aOptions = _oSliderTarget.data();

            noUiSlider.create( oNode, _aOptions );
            oNode.noUiSlider.on( 'update', function( values, handle ) {
                var _nValue = values[ handle ];
                _nValue= parseFloat( _nValue ).toFixed( _aOptions.round );
                jQuery( '#' + jQuery( oNode ).attr( 'data-id-' + handle )  )
                    .val( _nValue );
            });

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
            _nodeTargetSlider.noUiSlider.set( _aValues );

        } );

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
