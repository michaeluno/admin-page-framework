(function($) {

    // Initialization
    $( document ).ready( function() {
        $( '.admin-page-framework-sections.sortable-section' ).each( function(){
            $( this ).enableAdminPageFrameworkSortableSections();
        } );
    });

    $.fn.enableAdminPageFrameworkSortableSections = function( sSectionsContainerID ) {

        var _oTarget    = 'string' === typeof sSectionsContainerID
            ? $( '#' + sSectionsContainerID + '.sortable-section' )
            : $( this );

        // For tabbed sections, enable the sort to the tabs.
        var _bIsTabbed      = _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' );
        var _bCollapsible   = 0 < _oTarget.children( '.admin-page-framework-section.is_subsection_collapsible' ).length;

        _oTarget        = _bIsTabbed
            ? _oTarget.find( 'ul.admin-page-framework-section-tabs' )
            : _oTarget;

        _oTarget.off( 'sortupdate' );
        _oTarget.off( 'sortstop' );

        var _aSortableOptions = {
                items: _bIsTabbed
                    ? '> li:not( .disabled )'
                    : '> div:not( .disabled, .admin-page-framework-collapsible-toggle-all-button-container )',
                handle: _bCollapsible
                    ? '.admin-page-framework-section-caption'
                    : false,

                stop: function(e,ui) {

                    // Callback the registered callback functions.
                    jQuery( this ).trigger(
                        'admin-page-framework_stopped_sorting_sections',
                        []  // parameters for the callbacks
                    );

                },


                // @todo Figure out how to allow the user to highlight text in sortable elements.
                // cancel: '.admin-page-framework-section-description, .admin-page-framework-section-title'

            }
        var _oSortable  = _oTarget.sortable( _aSortableOptions );

        if ( ! _bIsTabbed ) {

            _oSortable.on( 'sortstop', function() {

                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).first().show();
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).not( ':first' ).hide();

            } );

        }

    };
}( jQuery ));