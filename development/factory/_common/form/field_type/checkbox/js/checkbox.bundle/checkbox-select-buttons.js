(function($){

  var apfMain     = AdminPageFrameworkScriptFormMain;
  var apfCheckBox = AdminPageFrameworkFieldTypeCheckbox;

  $( document ).ready( function(){
    if ( 'undefined' === apfCheckBox ) {
      return;
    }

    // Add the buttons.
    $( document ).ready( function(){

      var _body = $( 'body' );
      var _checkboxAllButton = $( apfCheckBox.selectors.selectAll );
      _checkboxAllButton.each( function(){
        var _oButton = $( '<div class="select_all_button_container"><a class="select_all_button button button-small">'
          + $( this ).data( 'select_all_button' )
          + '</a></div>' );
        $( this ).before( _oButton );
      });

      _body.on( 'click', '.select_all_button_container', function() {
        $( this ).selectAllAdminPageFrameworkCheckboxes();
        return false;
      } );

      var _checkboxNoneButton = $( apfCheckBox.selectors.selectNone );
      _checkboxNoneButton.each( function(){
        var _oButton = $( '<div class="select_none_button_container"><a class="select_all_button button button-small">'
          + $( this ).data( 'select_none_button' )
          + '</a></div>' );
        $( this ).before( _oButton );
      });

      _body.on( 'click', '.select_none_button_container', function() {
        $( this ).deselectAllAdminPageFrameworkCheckboxes();
        return false;
      } );
    });

  });

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF (checkbox, select buttons)', ...msg );
  }

}(jQuery));