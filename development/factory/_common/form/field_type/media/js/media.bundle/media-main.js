(function ( $ ) {

  debugLog( '0.1.0', apfMedia );

  var setAdminPageFrameworkMediaUploader;
  
  $( document ).ready( function () {

    // For WordPress 3.4.x or below
    if ( ! apfMedia.hasMediaUploader ) {
      /**
       * Bind/rebinds the thickbox script the given selector element.
       * The bMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
       */
      setAdminPageFrameworkMediaUploader = function ( sInputID, bMultiple, fExternalSource ) {
        var _oSelectMedia = $( '#select_media_' + sInputID );
        _oSelectMedia.off( 'click' ); // for repeatable fields
        _oSelectMedia.on( 'click', function () {
          var sPressedID = $( this ).attr( 'id' );
          window.sInputID = sPressedID.substring( 13 ); // remove the select_media_ prefix and set a property to pass it to the editor callback method.
          window.original_send_to_editor = window.send_to_editor;
          window.send_to_editor = hfAdminPageFrameworkSendToEditorMedia;
          var fExternalSource = $( this ).attr( 'data-enable_external_source' );
          tb_show( apfMedia.label.uploadFile, 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer=' + apfMedia.referer + '&amp;button_label=' + apfMedia.label.useThisFile + '&amp;type=image&amp;TB_iframe=true', false );
          return false; // do not click the button after the script by returning false.
        } );
      }
      var hfAdminPageFrameworkSendToEditorMedia = function ( sRawHTML, param ) {
        var sHTML = '<div>' + sRawHTML + '</div>'; // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
        var src = $( 'a', sHTML ).attr( 'href' );
        var classes = $( 'a', sHTML ).attr( 'class' );
        var id = (classes) ? classes.replace( /(.*?)wp-image-/, '' ) : ''; // attachment ID    
    
        // If the user wants to save relevant attributes, set them.
        var sInputID = window.sInputID;
        $( '#' + sInputID ).val( src ); // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
        $( '#' + sInputID + '_id' ).val( id );

        window.send_to_editor = window.original_send_to_editor; // restore the original send_to_editor
        tb_remove(); // close the thickbox
      }
    } else {
      setAdminPageFrameworkMediaUploader = function ( sInputID, bMultiple, fExternalSource ) {

        var _bEscaped = false;
        var _oMediaUploader;

        var _oSelectMediaButton = $( '#select_media_' + sInputID );
        _oSelectMediaButton.off( 'click' ); // for repeatable fields
        _oSelectMediaButton.on( 'click', function ( e ) {

          // Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
          var sInputID = $( this ).attr( 'id' ).substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.
          var jInput   = $( '#' + sInputID );

          window.wpActiveEditor = null;
          e.preventDefault();

          // If the uploader object has already been created, reopen the dialog
          if ( 'object' === typeof _oMediaUploader ) {
            _oMediaUploader.open();
            return;
          }

          // Store the original select object in a global variable
          oAdminPageFrameworkOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;

          // Assign a custom select object.
          wp.media.view.MediaFrame.Select = fExternalSource ? getAdminPageFrameworkCustomMediaUploaderSelectObject() : oAdminPageFrameworkOriginalMediaUploaderSelectObject;
          _oMediaUploader = wp.media( {
            title: fExternalSource
              ? apfMedia.label.insertFromURL
              : apfMedia.label.uploadFile,
            button: {
              text: apfMedia.label.useThisFile
            },
            library: {
              type: jInput.data( 'mime_types' ).length ? jInput.data( 'mime_types' ) : [],
            },
            multiple: bMultiple, // Set this to true to allow multiple files to be selected
            metadata: {},
          } );

          // When the uploader window closes,
          _oMediaUploader.on( 'escape', function () {
            _bEscaped = true;
            return false;
          } );
          _oMediaUploader.on( 'close', function () {

            var state = _oMediaUploader.state();

            // Check if it's an external URL
            if ( typeof (state.props) != 'undefined' && typeof (state.props.attributes) != 'undefined' ) {

              // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
              var _oMedia = {}, _sKey;
              for ( _sKey in state.props.attributes ) {
                _oMedia[ _sKey ] = state.props.attributes[ _sKey ];
              }

            }

            // If the image variable is not defined at this point, it's an attachment, not an external URL.
            if ( typeof (_oMedia) !== 'undefined' ) {
              setMediaPreviewElementWithDelay( sInputID, _oMedia );
            } else {

              var _oNewField;
              _oMediaUploader.state().get( 'selection' ).each( function ( oAttachment, iIndex ) {

                var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                  ? oAttachment.attributes
                  : {};

                if ( 0 === iIndex ) {
                  // place first attachment in field
                  setMediaPreviewElementWithDelay( sInputID, _oAttributes );
                  return true;
                }

                var _oFieldContainer = 'undefined' === typeof _oNewField
                  ? $( '#' + sInputID ).closest( '.admin-page-framework-field' )
                  : _oNewField;
                _oNewField = $( this ).addAdminPageFrameworkRepeatableField( _oFieldContainer.attr( 'id' ) );
                var sInputIDOfNewField = _oNewField.find( 'input' ).attr( 'id' );
                setMediaPreviewElementWithDelay( sInputIDOfNewField, _oAttributes );

              } );

            }

            // Restore the original select object.
            wp.media.view.MediaFrame.Select = oAdminPageFrameworkOriginalMediaUploaderSelectObject;

          } );

          // Open the uploader dialog
          _oMediaUploader.open();
          return false;
        } );

        var setMediaPreviewElementWithDelay = function ( sInputID, oImage, iMilliSeconds ) {
          iMilliSeconds = 'undefined' === typeof iMilliSeconds ? 100 : iMilliSeconds;
          setTimeout( function () {
            if ( ! _bEscaped ) {
              setMediaPreviewElement( sInputID, oImage );
            }
            _bEscaped = false;
          }, iMilliSeconds );
        }

      }
    }

    /**
     * Sets the preview element.
     *
     * @since 3.2.0 Changed the scope to global.
     * @since 3.9.0 Changed the scope to private.
     */
    function setMediaPreviewElement( sInputID, oSelectedFile ) {

      // If the user want the attributes to be saved, set them in the input tags.
      $( '#' + sInputID ).val( oSelectedFile.url ); // the url field is mandatory so  it does not have the suffix.
      $( '#' + sInputID + '_id' ).val( oSelectedFile.id );
      $( '#' + sInputID + '_caption' ).val( $( '<div/>' ).text( oSelectedFile.caption ).html() );
      $( '#' + sInputID + '_description' ).val( $( '<div/>' ).text( oSelectedFile.description ).html() );

    }

    $( document ).ready( function () {

      // Initialize
      $( 'a.select_media.button' ).each( function() {
        setAdminPageFrameworkMediaUploader( $( this ).data( 'input_id' ), $( this ).data( 'repeatable' ), $( this ).data( 'enable_external_source' ) );
      } );
      $( 'a.remove_media.button' ).on( 'click', function() {
        setMediaPreviewElement( $( this ).data( 'input_id' ), {} );
        return false; // do not click
      } );

      // Repeatable handling
      $().registerAdminPageFrameworkCallbacks( {
          /**
           * Called when a field of this field type gets repeated.
           */
          repeated_field: function ( oCloned, aModel ) {

            // Update attributes.
            oCloned.find( '.select_media.button, .remove_media.button' ).incrementAttributes(
              [ 'id', 'data-input_id' ], // attribute name
              aModel[ 'incremented_from' ], // index incremented from
              aModel[ 'id' ] // digit model
            );

            // Bind the event.
            var _oMediaInput = oCloned.find( '.media-field input' );
            if ( _oMediaInput.length <= 0 ) {
              return true;
            }
            setAdminPageFrameworkMediaUploader(
              _oMediaInput.attr( 'id' ),
              true,
              oCloned.find( '.select_media' ).attr( 'data-enable_external_source' )
            );
            $( '#remove_media_' + _oMediaInput.attr( 'id' ) ).on( 'click', function() {
              setMediaPreviewElement( $( this ).data( 'input_id' ), {} );
              return false; // do not click
            } );

          },
        },
        apfMedia.fieldTypeSlugs
      );
    } );

  } );  // $( document ).ready  

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Media Field Type:', ...msg );
  }


}( jQuery ));