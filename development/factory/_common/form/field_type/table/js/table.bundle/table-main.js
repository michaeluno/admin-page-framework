( function( $ ){

  var apfMain  = AdminPageFrameworkScriptFormMain;
  var apfTable = AdminPageFrameworkFieldTypeTable;

  function getDataValueFormatted( v ) {
    if ( 'true' === v || true === v ) {
      return true;
    }
    if ( 'false' === v || false === v ) {
      return false;
    }
    v = parseInt( v );
    return isNaN( v ) ? false : v;
  }

  $( document ).ready( function(){
    if ( 'undefined' === apfTable ) {
      return;
    }
    debugLog( '0.0.1', apfTable );

    $( '.admin-page-framework-field-table .accordion-container' ).each( function() {

      var _biActive = getDataValueFormatted( $( this ).data( 'active' ) );
      var _options = $.extend(
        {},
        $( this ).data(),
        {
          collapsible: true,
          active: true === _biActive ? 0 : _biActive, // for some reasons true does not work so give a zero-base index to specify which item to open. 0 means the first item to open.
          heightStyle: 'content', // without this, when the browser width changes, the height is not adjusted properly
        }
      );

      $( this ).accordion( _options );
      var _sIconClass = _biActive
        ? "dashicons-arrow-up"
        : "dashicons-arrow-down";
      $( this ).find( '.accordion-title' )
        .append( '<div class="buttons"><span class="icon-collapsible dashicons ' + _sIconClass + '"></span></div>' );

    } );

  }); // document ready

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Table Field Type', ...msg );
  }

}( jQuery ) );