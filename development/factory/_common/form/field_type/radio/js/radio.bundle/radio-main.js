(function($){

  var apfMain  = AdminPageFrameworkScriptFormMain;
  var apfRadio = AdminPageFrameworkFieldTypeRadio;

  $( document ).ready( function(){
    if ( 'undefined' === apfRadio ) {
      return;
    }
    debugLog( apfRadio );

    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
          oCloned.find( 'input[type=radio]' )
            .off( 'change' )
            .on( 'change', function( e ) {
          
            // Uncheck the other radio buttons
            // prop( 'checked', ... ) does not seem to take effect so use .attr( 'checked' ) also.
            // removeAttr( 'checked' ) causes JQMIGRATE warnings for its deprecation.
            $( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' )
              .prop( 'checked', false )
              .attr( 'checked', false );

            // Make sure the clicked item is checked
            $( this )
              .prop( 'checked', true )
              .attr( 'checked', 'checked' );
          });                      
        },
      },
      apfRadio.fieldTypeSlugs
    );

  });

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF (radio)', ...msg );
  }

}(jQuery));