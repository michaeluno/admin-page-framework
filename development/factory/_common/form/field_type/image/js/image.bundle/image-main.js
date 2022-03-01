(function ( $ ) {

  var setAdminPageFrameworkImageUploader;

  /** global: apfImage */
  debugLog( '0.0.2', apfImage );

  $( document ).ready( function () {

    // For WordPress 3.4.x or below
    if ( ! apfImage.hasMediaUploader ) {
      /**
       * Bind/rebinds the thickbox script the given selector element.
       * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
       */
      setAdminPageFrameworkImageUploader = function ( sInputID, fMultiple, fExternalSource ) {
        var _oSelectImage = $( '#select_image_' + sInputID );
        _oSelectImage.off( 'click' ); // for repeatable fields
        _oSelectImage.on( 'click', function () {
          var sPressedID = $( this ).attr( 'id' );
          window.sInputID = sPressedID.substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.
          window.original_send_to_editor = window.send_to_editor;
          window.send_to_editor = hfAdminPageFrameworkSendToEditorImage;
          var fExternalSource = $( this ).attr( 'data-enable_external_source' );
          tb_show( apfImage.label.uploadImage, 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label=' + apfImage.label.useThisImage + '&amp;type=image&amp;TB_iframe=true', false );
          return false; // do not click the button after the script by returning false.
        } );
      }
      var hfAdminPageFrameworkSendToEditorImage = function ( sRawHTML ) {

        var sHTML = '<div>' + sRawHTML + '</div>'; // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
        var src = $( 'img', sHTML ).attr( 'src' );
        var alt = $( 'img', sHTML ).attr( 'alt' );
        var title = $( 'img', sHTML ).attr( 'title' );
        var width = $( 'img', sHTML ).attr( 'width' );
        var height = $( 'img', sHTML ).attr( 'height' );
        var classes = $( 'img', sHTML ).attr( 'class' );
        var id = (classes) ? classes.replace( /(.*?)wp-image-/, '' ) : ''; // attachment ID
        var sCaption = sRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
          .replace( /<a.*?>(.*?)<\/a>/m, '' );
        var align = sRawHTML.replace( /^.*?\[\w+.*?\salign=(['"])(.*?)['"]\s.+$/mg, '$2' );
        var link = $( sHTML ).find( 'a:first' ).attr( 'href' );

        // Escape the strings of some of the attributes.
        sCaption = $( '<div/>' ).text( sCaption ).html();
        var sAlt = $( '<div/>' ).text( alt ).html();
        title = $( '<div/>' ).text( title ).html();

        // If the user wants to save relevant attributes, set them.
        var sInputID = window.sInputID; // window.sInputID should be assigned when the thickbox is opened.

        $( '#' + sInputID ).val( src ); // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
        $( '#' + sInputID + '_id' ).val( id );
        $( '#' + sInputID + '_width' ).val( width );
        $( '#' + sInputID + '_height' ).val( height );
        $( '#' + sInputID + '_caption' ).val( sCaption );
        $( '#' + sInputID + '_alt' ).val( sAlt );
        $( '#' + sInputID + '_title' ).val( title );
        $( '#' + sInputID + '_align' ).val( align );
        $( '#' + sInputID + '_link' ).val( link );

        // Update the preview
        var _oImagePreview = $( '#image_preview_' + sInputID ); 
        _oImagePreview.attr( 'alt', alt );
        _oImagePreview.attr( 'title', title );
        _oImagePreview.attr( 'data-classes', classes );
        _oImagePreview.attr( 'data-id', id );
        _oImagePreview.attr( 'src', src ); // updates the preview image
        $( '#image_preview_container_' + sInputID ).css( 'display', '' ); // updates the visibility
        _oImagePreview.show() // updates the visibility

        // restore the original send_to_editor
        window.send_to_editor = window.original_send_to_editor;

        // close the thickbox
        tb_remove();

      }

    } else {

      /**
       * Binds/rebinds the uploader button script to the specified element with the given ID.
       */
      setAdminPageFrameworkImageUploader = function ( sInputID, fMultiple, fExternalSource ) {

        var _bEscaped = false; // indicates whether the frame is escaped/cancelled.
        var _oCustomImageUploader;

        // The input element.
        var _oInput = $( '#' + sInputID + '[data-show_preview="1"]' );
        _oInput.off( 'change' ); // for repeatable fields
        _oInput.on( 'change', function ( e ) {
          var _sImageURL = $( this ).val();
          // Check if it is a valid image url.
          $( '<img>', {
            src: _sImageURL,
            error: function () {
            },
            load: function () {
              // if valid,  set the preview.
              setImagePreviewElement(
                sInputID,
                {
                  url: _sImageURL
                }
              );
            }
          } );
        } );

        // The Select button element.
        var _oSelectImage = $( '#select_image_' + sInputID );
        _oSelectImage.off( 'click' ); // for repeatable fields
        _oSelectImage.on( 'click', function ( e ) {

          // Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
          var sInputID = $( this ).attr( 'id' ).substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.

          window.wpActiveEditor = null;
          e.preventDefault();

          // If the uploader object has already been created, reopen the dialog
          if ( 'object' === typeof _oCustomImageUploader ) {
            _oCustomImageUploader.open();
            return;
          }

          // Store the original select object in a global variable
          oAdminPageFrameworkOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;

          // Assign a custom select object
          wp.media.view.MediaFrame.Select = fExternalSource ? getAdminPageFrameworkCustomMediaUploaderSelectObject() : oAdminPageFrameworkOriginalImageUploaderSelectObject;
          _oCustomImageUploader = wp.media( {
            id: sInputID,
            title: fExternalSource ? apfImage.label.insertFromURL : apfImage.label.uploadImage,
            button: {
              text: apfImage.label.useThisImage
            },
            type: 'image',
            library: { type: 'image' },
            multiple: fMultiple,  // Set this to true to allow multiple files to be selected
            metadata: {},
          } );

          // When the uploader window closes,
          _oCustomImageUploader.on( 'escape', function () {
            _bEscaped = true;
            return false;
          } );
          _oCustomImageUploader.on( 'close', function () {

            var state = _oCustomImageUploader.state();
            // Check if it's an external URL
            if ( typeof (state.props) != 'undefined' && typeof (state.props.attributes) != 'undefined' ) {

              // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
              var _oImage = {}, _sKey;
              for ( _sKey in state.props.attributes ) {
                _oImage[ _sKey ] = state.props.attributes[ _sKey ];
              }

            }

            // If the _oImage variable is not defined at this point, it's an attachment, not an external URL.
            if ( typeof (_oImage) !== 'undefined' ) {
              setImagePreviewElementWithDelay( sInputID, _oImage );

            } else {

              var _oNewField;
              _oCustomImageUploader.state().get( 'selection' ).each( function ( oAttachment, iIndex ) {

                var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                  ? oAttachment.attributes
                  : {};

                if ( 0 === iIndex ) {
                  // place first attachment in the field
                  setImagePreviewElementWithDelay( sInputID, _oAttributes );
                  return true;
                }

                var _oFieldContainer = 'undefined' === typeof _oNewField
                  ? $( '#' + sInputID ).closest( '.admin-page-framework-field' )
                  : _oNewField;
                _oNewField = $( this ).addAdminPageFrameworkRepeatableField( _oFieldContainer.attr( 'id' ) );
                var sInputIDOfNewField = _oNewField.find( 'input' ).attr( 'id' );
                setImagePreviewElementWithDelay( sInputIDOfNewField, _oAttributes );

              } );

            }

            // Restore the original select object.
            wp.media.view.MediaFrame.Select = oAdminPageFrameworkOriginalImageUploaderSelectObject;

            return false;
          } );  // on close

          // Open the uploader dialog
          _oCustomImageUploader.open();
          return false;

        } );

        function setImagePreviewElementWithDelay( sInputID, oImage, iMilliSeconds ) {
          iMilliSeconds = 'undefined' === typeof iMilliSeconds ? 100 : iMilliSeconds;
          setTimeout( function () {
            if ( ! _bEscaped ) {
              setImagePreviewElement( sInputID, oImage );
            }
            _bEscaped = false;
          }, iMilliSeconds );
        }

      }
    }

    /**
     * Sets the preview element.
     *
     * @since   3.2.0   Changed the scope to global.
     * @since   3.9.0   Changed the scope to private.
     */
    function setImagePreviewElement( sInputID, oImage ) {

      oImage = $.extend(
        true,   // recursive
        {
          caption: '',
          alt: '',
          title: '',
          url: '',
          id: '',
          width: '',
          height: '',
          align: '',
          link: '',
        },
        oImage
      );

      // Escape the strings of some of the attributes.
      var _sCaption = $( '<div/>' ).text( oImage.caption ).html();
      var _sAlt = $( '<div/>' ).text( oImage.alt ).html();
      var _sTitle = $( '<div/>' ).text( oImage.title ).html();

      // If the user wants the attributes to be saved, set them in the input tags.
      $( 'input#' + sInputID ).val( oImage.url ); // the url field is mandatory so it does not have the suffix.
      $( 'input#' + sInputID + '_id' ).val( oImage.id );
      $( 'input#' + sInputID + '_width' ).val( oImage.width );
      $( 'input#' + sInputID + '_height' ).val( oImage.height );
      $( 'input#' + sInputID + '_caption' ).val( _sCaption );
      $( 'input#' + sInputID + '_alt' ).val( _sAlt );
      $( 'input#' + sInputID + '_title' ).val( _sTitle );
      $( 'input#' + sInputID + '_align' ).val( oImage.align );
      $( 'input#' + sInputID + '_link' ).val( oImage.link );

      // Update up the preview
      var _oImagePreview = $( '#image_preview_' + sInputID );
      _oImagePreview.attr( 'data-id', oImage.id );
      _oImagePreview.attr( 'data-width', oImage.width );
      _oImagePreview.attr( 'data-height', oImage.height );
      _oImagePreview.attr( 'data-caption', _sCaption );
      _oImagePreview.attr( 'alt', _sAlt );
      _oImagePreview.attr( 'title', _sTitle );
      _oImagePreview.attr( 'src', oImage.url );
      if ( oImage.url ) {
        $( '#image_preview_container_' + sInputID ).show();
      } else {
        $( '#image_preview_container_' + sInputID ).hide();
      }

    }
    
    $().registerAdminPageFrameworkCallbacks( {

        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function ( oCloned, aModel ) {

          // Remove the value of the cloned preview element - check the value for repeatable sections.
          if ( 1 !== aModel[ 'call_type' ] || ! oCloned.find( 'input' ).first().val() ) { // if it's not for repeatable sections
            oCloned.find( '.image_preview' ).hide(); // for the image field type, hide the preview element
            oCloned.find( '.image_preview img' ).attr( 'src', '' ); // for the image field type, empty the src property for the image uploader field
          }

          // Increment element IDs.
          oCloned.find( '.image_preview, .image_preview img, .select_image.button, .remove_image.button' ).incrementAttributes(
            [ 'id', 'data-input_id' ], // attribute name
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );

          // Bind the event.
          var _oFieldContainer = oCloned.closest( '.admin-page-framework-field' );
          var _oSelectButton = _oFieldContainer.find( '.select_image' );
          var _oImageInput = _oFieldContainer.find( '.image-field input' );
          if ( _oImageInput.length <= 0 ) {
            return true;
          }

          setAdminPageFrameworkImageUploader(
            _oImageInput.attr( 'id' ),
            true,
            _oSelectButton.attr( 'data-enable_external_source' )
          );
          // Remove buttons
          $( '#remove_image_' + _oImageInput.attr( 'id' ) + '.remove_image.button' ).on( 'click', function ( event ) {
            setImagePreviewElement( $( this ).data( 'input_id' ), {} );
            return false;
          } );

        },
      },
      apfImage.fieldTypeSlugs
    );

    // Upload buttons
    $( '.select_image.button' ).each( function() {
      setAdminPageFrameworkImageUploader( $( this ).data( 'input_id' ), $( this ).data( 'repeatable' ), $( this ).data( 'enable_external_source' ) );
    } );
    // Remove buttons
    $( '.remove_image.button' ).on( 'click', function ( event ) {
      setImagePreviewElement( $( this ).data( 'input_id' ), {} );
      return false;
    } );
    
  } );  // $( document ).ready  
  
  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Image Field Type', ...msg );
  }

}( jQuery ));