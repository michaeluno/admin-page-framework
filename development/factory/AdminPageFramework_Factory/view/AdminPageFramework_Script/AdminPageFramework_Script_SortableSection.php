<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for the sortable method.
 * 
 * @since           3.0.0     
 * @since           3.3.0       Extends `AdminPageFramework_Script_Base`.
 * @since           3.6.0       Changed the name from `AdminPageFramework_Script_Sortable`.
 * @package         AdminPageFramework
 * @subpackage      JavaScript
 * @internal
 */
class AdminPageFramework_Script_SortableSection extends AdminPageFramework_Script_SortableField {
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.6.0
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */        
    static public function getScript( /* $oMsg */ ) {

        // Uncomment these lines when parameters need to be accessed.
        // $_aParams   = func_get_args() + array( null );
        // $_oMsg      = $_aParams[ 0 ];            

        return <<<JAVASCRIPTS
(function($) {
    $.fn.enableAdminPageFrameworkSortableSections = function( sSectionsContainerID ) {

        var _oTarget    = 'string' === typeof sSectionsContainerID 
            ? $( '#' + sSectionsContainerID + '.sortable-section' )
            : this;
console.log( 'sortable: ' + sSectionsContainerID );
console.log( 'has class: ' + _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' ) );
        // For tabbed sections, enable the sort to the tabs.
        var _bIsTabbed  = _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' );
        
        var _oTarget    = _bIsTabbed
            ? _oTarget.find( 'ul.admin-page-framework-section-tabs' )
            : _oTarget;

        _oTarget.unbind( 'sortupdate' );
        _oTarget.unbind( 'sortstop' );
        
console.log( 'target: ' + _oTarget.attr( 'id' ) );
        if ( _bIsTabbed ) {
            
            var _oSortable  = _oTarget.sortable(
                { items: '> li:not( .disabled )', }
            );
            // @todo change the order of section contents.
            _oSortable.bind( 'sortstop', function() {
                                                    
            } );            
            
        } else {
                
            var _oSortable  = _oTarget.sortable(
                { items: '> div:not( .disabled )', } // the options for the sortable plugin
            );
            _oSortable.bind( 'sortstop', function() {
                                    
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).first().show();
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).not( ':first' ).hide();
                
            } );            
            
        }


/*         _oSortable.bind( 'sortstop', function() {
         
            // Callback the registered functions 
            $( this ).callBackStoppedSortingFields( 
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );  
            
        });
        _oSortable.bind( 'sortupdate', function() {

            // Callback the registered functions.
            $( this ).callBackSortedFields( 
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );
            
        });   */               
    
    };
}( jQuery ));
JAVASCRIPTS;

    }
}