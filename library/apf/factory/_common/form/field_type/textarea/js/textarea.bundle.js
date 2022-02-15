/*! Admin Page Framework - Textarea Field Type 0.0.3 */
(function($){

  var apfMain  = AdminPageFrameworkScriptFormMain;
  var apfTextArea = AdminPageFrameworkFieldTypeTextArea;

  $( document ).ready( function(){
    if ( 'undefined' === apfTextArea ) {
      debugLog( 'Textarea script data is not loaded.' );
      return;
    }
    debugLog( '0.0.3', apfTextArea );

    
    // Move the link tag into the bottom of the page
    $( 'link#editor-buttons-css' ).appendTo( '#wpwrap' );

    // [3.9.0+] By default, the <textarea> value is not updated.                                             
    if ( 'undefined' !== typeof tinymce && tinymce.majorVersion >= 4 && 'undefined' !== typeof tinymce.editors && tinymce.editors.length ) {
      $( 'input[type="hidden"][data-tinymce-textarea]' ).each( function () {
        var _textareaID = $( this ).attr( 'data-tinymce-textarea' );
        for ( var _i = 0; _i < tinymce.editors.length; _i++ ) {
          if ( _textareaID === tinymce.editors[ _i ].id ) {
            tinymce.get( _i ).on( 'change', function () {
              var _oThisElement = $( '#' + this.id );
              var _sContent     = this.getContent();
              _oThisElement.val( _sContent );
              _oThisElement.html( _sContent );
            } );
          }
        }
      } );
    }

    /**
     * Determines whether the callback is handleable or not.
     */
    var isEditorReady = function ( oField, sFieldType ) {

      if ( $.inArray( sFieldType, apfTextArea.fieldTypeSlugs ) <= -1 ) {
        return false
      }

      // If tinyMCE is not ready, return.
      if ( 'object' !== typeof tinyMCEPreInit ) {
        return;
      }

      return true;

    };

    /**
     * Removes the editor by the given textarea ID.
     */
    var removeEditor = function ( sTextAreaID ) {

      if ( 'object' !== typeof tinyMCEPreInit ) {
        return;
      }

      // Store the previous textarea value. $ has a bug that val() for <textarea> does not work for cloned element. @see: http://bugs.jquery.com/ticket/3016
      var oTextArea = $( '#' + sTextAreaID );
      var sTextAreaValue = oTextArea.val();

      // Delete the rich editor. Somehow this deletes the value of the textarea tag in some occasions.
      tinyMCE.execCommand( 'mceRemoveEditor', false, sTextAreaID );
      delete tinyMCEPreInit[ 'mceInit' ][ sTextAreaID ];
      delete tinyMCEPreInit[ 'qtInit' ][ sTextAreaID ];

      // Restore the previous textarea value
      oTextArea.val( sTextAreaValue );

    };

    /**
     * Updates the editor
     *
     * @param sTextAreaID
     * @param oTinyMCESettings
     * @param oQuickTagSettings
     */
    var updateEditor = function ( sTextAreaID, oTinyMCESettings, oQuickTagSettings ) {

      removeEditor( sTextAreaID );
      var aTMCSettings = $.extend(
        {},
        oTinyMCESettings,
        {
          selector: '#' + sTextAreaID,
          body_class: sTextAreaID,
          height: '100px',
          menubar: false,
          setup: function ( ed ) {    // see: http://www.tinymce.com/wiki.php/API3:event.tinymce.Editor.onChange
            // It seems for tinyMCE 4 or above the on() method must be used.
            if ( tinymce.majorVersion >= 4 ) {
              ed.on( 'change', function () {
                var _oThisElement = $( '#' + this.id );
                var _sContent     = this.getContent();
                _oThisElement.val( _sContent );
                _oThisElement.html( _sContent );
              } );
            } else {
              // For tinyMCE 3.x or below the onChange.add() method needs to be used.
              ed.onChange.add( function ( ed, l ) {
                debugLog( ed.id + ' : Editor contents was modified. Contents: ' + l.content );
                var _oThisElement = $( '#' + ed.id );
                var _sContent     = ed.getContent();
                _oThisElement.val( _sContent );
                _oThisElement.html( _sContent );
              } );
            }
          },
        }
      );
      var aQTSettings = $.extend( {}, oQuickTagSettings, { id: sTextAreaID } );

      // Store the settings.
      tinyMCEPreInit.mceInit[ sTextAreaID ] = aTMCSettings;
      tinyMCEPreInit.qtInit[ sTextAreaID ] = aQTSettings;
      QTags.instances[ aQTSettings.id ] = aQTSettings;

      // Enable quick tags
      quicktags( aQTSettings );   // does not work... See https://core.trac.wordpress.org/ticket/26183
      QTags._buttonsInit();

      window.tinymce.dom.Event.domLoaded = true;
      tinyMCE.init( aTMCSettings );
      $( this ).find( '.wp-editor-wrap' ).first().on( 'click.wp-editor', function () {
        if ( this.id ) {
          window.wpActiveEditor = this.id.slice( 3, -5 );
        }
      } );

    };

    /**
     * Updates editors found in the passed elements.
     *
     * Called when fields are sorted to redraw the TinyMCE editor.
     */
    var updateFoundEditors = function ( oElements ) {

      oElements.each( function ( iIndex ) {

        // If the textarea tag is not found, do nothing.
        var oTextAreas = $( this ).find( 'textarea.wp-editor-area' );
        if ( oTextAreas.length <= 0 ) {
          return true;
        }

        // Find the tinyMCE wrapper element
        var oWrap = $( this ).find( '.wp-editor-wrap' );
        if ( oWrap.length <= 0 ) {
          return true;
        }

        // Retrieve the TinyMCE and Quick Tags settings. The enabler script stores the original element id.
        var oSettings = $().getAdminPageFrameworkInputOptions( oWrap.attr( 'data-id' ) );

        var oTextArea = $( this ).find( 'textarea.wp-editor-area' ).first().show().removeAttr( 'aria-hidden' );
        var oEditorContainer = $( this ).find( '.wp-editor-container' ).first().clone().empty();
        var oToolBar = $( this ).find( '.wp-editor-tools' ).first().clone();

        // Replace the tinyMCE wrapper with the plain textarea tag element.
        oWrap.empty()
          .prepend( oEditorContainer.prepend( oTextArea.show() ) )
          .prepend( oToolBar );

        updateEditor( oTextArea.attr( 'id' ), oSettings[ 'TinyMCE' ], oSettings[ 'QuickTags' ] );

        // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
        $( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );

      } );

    };

    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function ( oCloned, aModel ) {

          // Return if not the type and there is no editor element.
          if ( ! isEditorReady( oCloned, aModel[ 'field_type' ] ) ) {
debugLog( 'the editor is not ready', aModel );
            return;
          }
          if ( oCloned.find( 'textarea.wp-editor-area' ).length <= 0 ) {
debugLog( 'not found .wp-editor-area' );
            return;
          }

          // Find the tinyMCE wrapper element
          var _oWrap = oCloned.find( '.wp-editor-wrap' );
          if ( _oWrap.length <= 0 ) {
debugLog( 'not found .wp-editor-wrap' );
            return;
          }
debugLog( 'repeat passed' );
          // TinyMCE and Quick Tags Settings - the enabler script stores the original element id.
          var _oSettings = $().getAdminPageFrameworkInputOptions( _oWrap.attr( 'data-id' ) );

          // Elements
          var _oField = oCloned.closest( '.admin-page-framework-field' );
          var _oTextArea = _oField.find( 'textarea.wp-editor-area' )
            .first()
            .clone() // Cloning is needed here as repeatable sections does not work with the original element for unknown reasons.
            .show()
            .removeAttr( 'aria-hidden' );
          var _oEditorContainer = _oField.find( '.wp-editor-container' ).first().clone().empty();
          var _oToolBar = _oField.find( '.wp-editor-tools' ).first().clone();

          // Clean values
          _oTextArea.val( '' );    // only delete the value of the directly copied one
          _oTextArea.empty();      // the above use of val( '' ) does not erase the value completely.

          // Replace the tinyMCE wrapper with the plain textarea tag element.
          _oWrap.empty()
            .prepend( _oEditorContainer.prepend( _oTextArea.show() ) )
            .prepend( _oToolBar );

          // Update the editor. For repeatable sections, remove the previously assigned editor.                        
          updateEditor(
            _oTextArea.attr( 'id' ),
            _oSettings[ 'TinyMCE' ],
            _oSettings[ 'QuickTags' ]
          );

          // Update the TinyMCE editor and the Quick Tags bar and their attributes.
          _oToolBar.find( 'a,div,button' ).incrementAttributes(
            [ 'id', 'data-wp-editor-id', 'data-editor' ], // attribute name
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );
          _oField.find( '.wp-editor-wrap a' ).incrementAttribute(
            'data-editor',
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );
          _oField.find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).incrementAttribute(
            'id',
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );

        },

        /**
         * The sortable field callback for the sort update event.
         *
         * On contrary to repeatable fields callbacks, the _fields_ container element object and its ID will be passed.
         *
         * @param oSortedFields
         * @param sFieldType
         * @param sFieldsTagID
         * @param iCallType
         */
        stopped_sorting_fields: function ( oSortedFields, sFieldType, sFieldsTagID, iCallType ) {

          if ( ! isEditorReady( oSortedFields, sFieldType ) ) {
            return;
          }

          // Update the editor.
          setTimeout( function () {
            var _oFields = oSortedFields.children( '.admin-page-framework-field' );
            updateFoundEditors( _oFields );
          }, 100 );

        },

        /**
         * Called when sortable sections stop sorting.
         */
        stopped_sorting_sections: function ( oSections ) {

          setTimeout( function () {
            var _oFields = $( oSections ).find( '.admin-page-framework-field' );
            updateFoundEditors( _oFields );
          }, 100 );

        },

        /**
         * The saved widget callback.
         *
         * It is called when a widget is saved.
         */
        saved_widget: function ( oWidget ) {

          // If tinyMCE is not ready, return.
          if ( 'object' !== typeof tinyMCEPreInit ) {
            return;
          }

          var _sWidgetInitialTextareaID;
          $( oWidget ).find( '.admin-page-framework-field' ).each( function ( iIndex ) {

            /* If the textarea tag is not found, do nothing  */
            var oTextAreas = $( this ).find( 'textarea.wp-editor-area' );
            if ( oTextAreas.length <= 0 ) {
              return true;
            }

            // Find the tinyMCE wrapper element
            var oWrap = $( this ).find( '.wp-editor-wrap' );
            if ( oWrap.length <= 0 ) {
              return true;
            }

            // Retrieve the TinyMCE and Quick Tags settings from the initial widget form element. The initial widget is the one from which the user drags.
            var oTextArea = $( this ).find( 'textarea.wp-editor-area' ).first(); // .show().removeAttr( 'aria-hidden' );
            var _sID = oTextArea.attr( 'id' );
            var _sInitialTextareaID = _sID.replace( /(widget-.+-)([0-9]+)(-)/i, '$1__i__$3' );
            _sWidgetInitialTextareaID = 'undefined' === typeof tinyMCEPreInit.mceInit[ _sInitialTextareaID ]
              ? _sWidgetInitialTextareaID
              : _sInitialTextareaID;
            if ( 'undefined' === typeof tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ] ) {
              return true;
            }

            updateEditor(
              oTextArea.attr( 'id' ),
              tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ],
              tinyMCEPreInit.qtInit[ _sWidgetInitialTextareaID ]
            );

            // Store the settings.
            $().storeAdminPageFrameworkInputOptions(
              oWrap.attr( 'data-id' ),
              {
                TinyMCE: tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ],
                QuickTags: tinyMCEPreInit.qtInit[ _sWidgetInitialTextareaID ]
              }
            );
          } );

        } // end of 'saved_widget'

      },
      apfTextArea.fieldTypeSlugs
    );	            
    
    // Initialize
    $( '.admin-page-framework-textarea-data-input' ).each( function(){
      var _sInputID = $( this ).data( 'tinymce-textarea' );

      // Store the textarea tag ID to be referred by the repeatable routines.
      $( '#wp-' + _sInputID + '-wrap' ).attr( 'data-id', _sInputID );    // store the id
      if ( 'object' !== typeof tinyMCEPreInit ) {
        return;
      }

      // Store the settings.
      $().storeAdminPageFrameworkInputOptions(
        _sInputID,
        {
          TinyMCE: tinyMCEPreInit.mceInit[ _sInputID ],
          QuickTags: tinyMCEPreInit.qtInit[ _sInputID ]
        }
      );
    } );
    
  });

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Textarea Field Type', ...msg );
  }

}( jQuery ) );