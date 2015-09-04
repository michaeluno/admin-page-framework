<?php
class AdminPageFramework_Script_Utility extends AdminPageFramework_Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {
    $.fn.reverse = [].reverse;

    $.fn.formatPrintText = function() {
        var aArgs = arguments;     
        return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
            return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
                ? aArgs[ parseInt( number ) + 1 ]
                : match;
        });
    };
}( jQuery ));
JAVASCRIPTS;
        
    }
}