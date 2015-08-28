<?php
class AdminPageFramework_Script_SortableSection extends AdminPageFramework_Script_SortableField {
    static public function getScript() {
        return <<<JAVASCRIPTS
(function($) {
    $.fn.enableAdminPageFrameworkSortableSections = function( sSectionsContainerID ) {

        var _oTarget    = 'string' === typeof sSectionsContainerID 
            ? $( '#' + sSectionsContainerID + '.sortable-section' )
            : this;

        // For tabbed sections, enable the sort to the tabs.
        var _bIsTabbed  = _oTarget.hasClass( 'admin-page-framework-section-tabs-contents' );
        
        var _oTarget    = _bIsTabbed
            ? _oTarget.find( 'ul.admin-page-framework-section-tabs' )
            : _oTarget;

        _oTarget.unbind( 'sortupdate' );
        _oTarget.unbind( 'sortstop' );
        
        if ( _bIsTabbed ) {
            
            var _oSortable  = _oTarget.sortable(
                { items: '> li:not( .disabled )', }
            );      
            
        } else {
                
            var _oSortable  = _oTarget.sortable(
                { items: '> div:not( .disabled, .admin-page-framework-collapsible-toggle-all-button-container )', } // the options for the sortable plugin
            );
            _oSortable.bind( 'sortstop', function() {
                                    
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).first().show();
                jQuery( this ).find( 'caption > .admin-page-framework-section-title:not(.admin-page-framework-collapsible-sections-title,.admin-page-framework-collapsible-section-title)' ).not( ':first' ).hide();
                
            } );            
            
        }            
    
    };
}( jQuery ));
JAVASCRIPTS;
        
    }
    static private $_aSetContainerIDsForSortableSections = array();
    static public function getEnabler($sContainerTagID, $aSettings, $oMsg) {
        if (empty($aSettings)) {
            return '';
        }
        if (in_array($sContainerTagID, self::$_aSetContainerIDsForSortableSections)) {
            return '';
        }
        self::$_aSetContainerIDsForSortableSections[$sContainerTagID] = $sContainerTagID;
        new self($oMsg);
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {    
    jQuery( '#{$sContainerTagID}' ).enableAdminPageFrameworkSortableSections( '{$sContainerTagID}' ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-section-sortable-script'>" . $_sScript . "</script>";
    }
}