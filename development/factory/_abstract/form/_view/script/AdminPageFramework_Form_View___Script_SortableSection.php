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
 * @since           3.3.0       Extends `AdminPageFramework_Form_View___Script_Base`.
 * @since           3.6.0       Changed the name from `AdminPageFramework_Form_View___Script_Sortable`.
 * @package         AdminPageFramework
 * @subpackage      JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_SortableSection extends AdminPageFramework_Form_View___Script_SortableField {
    
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

        // For tabbed sections, enable the sort to the tabs.
        var _bIsTabbed      = _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' );
        var _bCollapsible   = 0 < _oTarget.children( '.admin-page-framework-section.is_subsection_collapsible' ).length;

        var _oTarget        = _bIsTabbed
            ? _oTarget.find( 'ul.admin-page-framework-section-tabs' )
            : _oTarget;

        _oTarget.unbind( 'sortupdate' );
        _oTarget.unbind( 'sortstop' );
        
        var _aSortableOptions = { 
                items: _bIsTabbed
                    ? '> li:not( .disabled )'
                    : '> div:not( .disabled, .admin-page-framework-collapsible-toggle-all-button-container )', 
                handle: _bCollapsible
                    ? '.admin-page-framework-section-caption'
                    : false,
                
                // @todo Figure out how to allow the user to highlight text in sortable elements.
                // cancel: '.admin-page-framework-section-description, .admin-page-framework-section-title'
                
            }
        var _oSortable  = _oTarget.sortable( _aSortableOptions );               
        
        if ( ! _bIsTabbed ) {
            
            _oSortable.bind( 'sortstop', function() {
                                    
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).first().show();
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).not( ':first' ).hide();
                
            } );            
            
        }            
    
    };
}( jQuery ));
JAVASCRIPTS;

    }
    
    /**
     * Stores the set container IDs to prevent multiple calls.
     * @since       3.6.0
     */
    static private $_aSetContainerIDsForSortableSections = array();
    /**
     * Returns the enabler script of sortable sections.
     * @since       3.6.0
     * @return      string
     */
    static public function getEnabler( $sContainerTagID, $aSettings, $oMsg ) {
        
        if ( empty( $aSettings ) ) { 
            return ''; 
        }
        if ( in_array( $sContainerTagID, self::$_aSetContainerIDsForSortableSections ) ) { 
            return ''; 
        }
        self::$_aSetContainerIDsForSortableSections[ $sContainerTagID ] = $sContainerTagID;        
        
        new self( $oMsg ); 
        
        // $aSettings              = $this->getAsArray( $aSettings );        
        $_sScript       = <<<JAVASCRIPTS
jQuery( document ).ready( function() {    
    jQuery( '#{$sContainerTagID}' ).enableAdminPageFrameworkSortableSections( '{$sContainerTagID}' ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-section-sortable-script'>" 
                . '/* <![CDATA[ */'
                . $_sScript 
                . '/* ]]> */'
            . "</script>";
        
    }    
    
}