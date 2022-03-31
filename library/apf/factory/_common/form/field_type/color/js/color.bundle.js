/*! Admin Page Framework - Color Field Type 1.0.0 */
(function ( $ ) {

  var apfMain = AdminPageFrameworkScriptFormMain;
  var apfColor = AdminPageFrameworkColorFieldType;

  var registerAdminPageFrameworkColorPickerField = function ( osSubjectInput, aOptions ) {

    var osTargetInput = 'string' === typeof osSubjectInput
      ? '#' + osSubjectInput
      : osSubjectInput;
    var sInputID = 'string' === typeof osSubjectInput
      ? osSubjectInput
      : osSubjectInput.attr( 'id' );

    // Only for the iris color picker.
    var _aDefaults = {
      defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input
      change: function ( event, ui ) {
        $( osTargetInput ).trigger( 'admin-page-framework_field_type_color_changed' );
        $( osTargetInput ).val( ui.color.toString() ).trigger( 'change' ); // [3.9.1+]
      }, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/
      clear: function ( event, ui ) {
        $( osTargetInput ).trigger( 'admin-page-framework_field_type_color_cleared' );
      }, // a callback to fire when the input is emptied or an invalid color
      hide: true, // hide the color picker controls on load
      palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further
    };
    // For options, @see https://automattic.github.io/Iris/
    var _aColorPickerOptions = $.extend( {}, _aDefaults, aOptions );

    'use strict';
    /* This if-statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if ( 'object' === typeof $.wp && 'function' === typeof $.wp.wpColorPicker ) {
      $( osTargetInput ).wpColorPicker( _aColorPickerOptions );
    } else {
      /* We use farbtastic if the WordPress color picker widget doesn't exist */
      $( '#color_' + sInputID ).farbtastic( osTargetInput );
    }
  }

  $( document ).ready( function () {

    debugLog( 'APF Color Field Type:', apfColor );

    // Initialization
    $( '.admin-page-framework-field-color-picker' ).each( function () {
      registerAdminPageFrameworkColorPickerField( $( this ).data( 'input_id' ) );
    } );

    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function ( oCloned, aModel ) {

          oCloned.find( 'input.input_color' ).each( function ( iIterationIndex ) {

            var _oNewColorInput = $( this );
            var _oIris = _oNewColorInput.closest( '.wp-picker-container' );
            // WP 3.5+
            if ( _oIris.length > 0 ) {
              // unbind the existing color picker script in case there is.
              _oNewColorInput = _oNewColorInput.clone();
            }
            var _sInputID = _oNewColorInput.attr( 'id' );

            // Reset the value of the color picker.
            var _sInputValue = _oNewColorInput.val()
              ? _oNewColorInput.val()
              : _oNewColorInput.attr( 'data-default' );
            var _sInputStyle = _sInputValue !== 'transparent' && _oNewColorInput.attr( 'style' )
              ? _oNewColorInput.attr( 'style' )
              : '';
            _oNewColorInput.val( _sInputValue ); // set the default value
            _oNewColorInput.attr( 'style', _sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )

            // Replace the old color picker elements with the new one.
            // WP 3.5+
            if ( _oIris.length > 0 ) {
              $( _oIris ).replaceWith( _oNewColorInput );
            }
            // WP 3.4.x -
            else {
              oCloned.find( '.colorpicker' )
                .replaceWith( '<div class="colorpicker" id="color_' + _sInputID + '"></div>' );
            }

            // Bind the color picker event.
            registerAdminPageFrameworkColorPickerField( _oNewColorInput );
            _oNewColorInput.trigger( 'change' );

          } );
        },
      },
      apfColor.fieldTypeSlugs
    );
  } );

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF (Color)', ...msg );
  }

}( jQuery ));