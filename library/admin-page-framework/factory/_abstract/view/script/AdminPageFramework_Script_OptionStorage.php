<?php
class AdminPageFramework_Script_OptionStorage extends AdminPageFramework_Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
(function ( $ ) {
            
    $.fn.aAdminPageFrameworkInputOptions = {}; 
                            
    $.fn.storeAdminPageFrameworkInputOptions = function( sID, vOptions ) {
        var sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
        $.fn.aAdminPageFrameworkInputOptions[ sID ] = vOptions;
    };	
    $.fn.getAdminPageFrameworkInputOptions = function( sID ) {
        var sID = sID.replace( /__\d+_/, '___' ); // remove the section index
        return ( 'undefined' === typeof $.fn.aAdminPageFrameworkInputOptions[ sID ] )
            ? null
            : $.fn.aAdminPageFrameworkInputOptions[ sID ];
    }

}( jQuery ));
JAVASCRIPTS;
        
    }
}