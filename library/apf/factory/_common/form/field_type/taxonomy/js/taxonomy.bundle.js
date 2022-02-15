/*! Admin Page Framework - Radio Field Type 0.0.2 */
(function($){

  var apfMain  = AdminPageFrameworkScriptFormMain;
  var apfTaxonomy = AdminPageFrameworkFieldTypeTaxonomy;

  $( document ).ready( function(){
    if ( 'undefined' === apfTaxonomy ) {
      return;
    }
    debugLog( '0.0.2', apfTaxonomy );

    /* For tabs */
    var enableAdminPageFrameworkTabbedBox = function ( nodeTabBoxContainer ) {
      $( nodeTabBoxContainer ).each( function () {

        $( this ).find( '.tab-box-tab' ).each( function ( i ) {

          if ( 0 === i ) {
            $( this ).addClass( 'active' );
          }

          $( this ).on( 'click', function ( e ) {

            // Prevents jumping to the anchor which moves the scroll bar.
            e.preventDefault();

            // Remove the active tab and set the clicked tab to be active.
            $( this ).siblings( 'li.active' ).removeClass( 'active' );
            $( this ).addClass( 'active' );

            // Find the element id and select the content element with it.
            var thisTab = $( this ).find( 'a' ).attr( 'href' );
            var activeContent = $( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' );
            activeContent.siblings().css( 'display', 'none' );

          } );

        } );
      } );
    };

    enableAdminPageFrameworkTabbedBox( $( '.tab-box-container' ) );

    $().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function ( oCloned, aModel ) {

          // Update attributes.
          oCloned.find( 'div, li.category-list' ).incrementAttribute(
            'id', // attribute name
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );
          oCloned.find( 'label' ).incrementAttribute(
            'for', // attribute name
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );
          oCloned.find( 'li.tab-box-tab a' ).incrementAttribute(
            'href', // attribute name
            aModel[ 'incremented_from' ], // index incremented from
            aModel[ 'id' ] // digit model
          );

          // Initialize
          enableAdminPageFrameworkTabbedBox( oCloned.find( '.tab-box-container' ) );

        },
      },
      apfTaxonomy.fieldTypeSlugs
    );

    
  });

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Taxonomy Field Type', ...msg );
  }

}(jQuery));