(function($){

  var apfMain     = AdminPageFrameworkScriptFormMain;
  var apfCheckBox = AdminPageFrameworkFieldTypeCheckbox;

  $( document ).ready( function(){
    if ( 'undefined' === apfCheckBox ) {
      return;
    }
    debugLog( apfCheckBox );

    handleCheckedAttributeUpdate( 'input[type="checkbox"]' );

    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
          handleCheckedAttributeUpdate( oCloned.find( 'input[type="checkbox"]' ) );
        },
      },
      apfCheckBox.fieldTypeSlugs
    );

  });

  /**
   * This is important to send form data through JavaScript.
   * If the attributes are not updated, somehow JavaScript does not catch the checked states of checkboxes.
   * @param subject
   */
  function handleCheckedAttributeUpdate( subject ) {
    $( subject ).on( 'change', function() {
        $( this ).prop( 'checked', $( this ).is( ':checked' ) );
        $( this ).attr( 'checked', $( this ).is( ':checked' ) );
    } );
  }

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF (checkbox, attributes)', ...msg );
  }

}(jQuery));