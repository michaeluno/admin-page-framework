(function ( $ ) {

  $( document ).ready( function () {


    $( '.button-select-path2' ).on( 'click', initializeJSTree );
    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function ( oCloned, aModel ) {

          // Increment element IDs.
          oCloned.find( '.select_path, .jstree-path-modal, .path2-field-options' ).incrementAttributes(
            [ 'id', 'data-id', 'href', 'data-input_id' ], // attribute name
            aModel[ 'incremented_from' ], // increment from
            aModel[ 'id' ] // digit model
          );
          // Initialize the event bindings.
          oCloned.find( '.button-select-path2' ).on( 'click', initializeJSTree );

        },
      },
      [ 'path2' ]  // subject field type slugs
    );

  } );

  function initializeJSTree() {

    var _sInputID = $( this ).data( 'input_id' );
    tb_show(
      AdminPageFrameworkPath2FieldType.label.selectPath,  // modal window title
      '#TB_inline?width=640&inlineId=path_selector_' + _sInputID  // open the modal with an inline content
    );

    function _setInputValueAndCloseModal( sSelector, sValue ) {
      $( sSelector ).val( sValue );
      tb_remove();
    }

    var _oNodeTree = $( '#TB_ajaxContent .path2-node-tree' );
    var _aPath2Options = $( '#TB_ajaxContent .path2-field-options' ).data();
    var _oButtonPanel = $( '<div class="container-path2-modal-select-button">'
      + '<div class="media-toolbar-secondary"></div>'
      + '<div class="media-toolbar-primary search-form"><button type="button" class="button media-button button-primary button-small">' + AdminPageFrameworkPath2FieldType.label.select + '</button></div>'
      + '</div>' );
    $( '#TB_ajaxContent' ).after( _oButtonPanel );
    _oButtonPanel.on( 'click', 'button', function () {
      var _aSelected = _oNodeTree.jstree( 'get_selected' );
      _setInputValueAndCloseModal( '#' + _sInputID, _aSelected[ 0 ] );
    } );
    _oNodeTree
      .on( 'click', '.jstree-anchor', function ( e ) {
        $( this ).jstree( true ).toggle_node( e.target );
      } )
      .on( 'dblclick.jstree', function ( event, data ) {
        var _nodeLi = $( event.target ).closest( 'li' );
        var _tree = $( this ).jstree( true );
        var _node = _tree.get_node( _nodeLi.attr( 'id' ) );
        if ( 'file' === _node.type ) {
          _setInputValueAndCloseModal( '#' + _sInputID, _node.id );
        }
      } )
      .jstree( {
        core: {
          dblclick_toggle: false,
          themes: {
            responsive: false,
            variant: 'small',
            stripes: true
          },
          data: function ( node, cb ) {
            $.ajax( {
              type: 'post',
              dataType: 'json',
              url: AdminPageFrameworkPath2FieldType.ajaxURL,
              data: {
                id: node.id,
                action: 'apf_path2_field_type-admin-page-framework',
                'admin-page-framework_path2_field_type': 1,
                nonce: AdminPageFrameworkPath2FieldType.nonce,
                options: _aPath2Options
              },
              success: function ( response ) {
                cb( response );
              },
              error: function ( response ) {
                cb( [] );
              },
            } ); // ajax
          }
        },
        types: {
          default: {
            icon: 'folder',
          },
          file: {
            valid_children: [],
            icon: 'file'
          }
        },
        plugins: [ 'sort', 'unique', 'types' ]
      } );
  }

  /**
   * Removes the set values to the input tags.
   * Gets called when a remove button is clicked.
   * @since   3.9.0
   */
  removeInputValuesForPath2 = function ( oElem ) {
    $( oElem ).closest( '.admin-page-framework-field' ).find( '.path2-field input' ).val( '' );
  }

})( jQuery );