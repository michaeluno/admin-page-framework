<?php
class AdminPageFramework_Form_View___Script_Form extends AdminPageFramework_Form_View___Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {
    
    /**
     * Renderisn forms is heavy and unformatted layouts will be hidden with a script embedded in the head tag.
     * Now when the document is ready, restore that visibility state so that the form will appear.
     */
    jQuery( document ).ready( function() {
        jQuery( '.admin-page-framework-form-js-on' )
            .hide()
            .css( 'visibility', 'visible' )
            .fadeIn( 320 )
            ;
    });    

}( jQuery ));
JAVASCRIPTS;
        
    }
    static private $_bLoadedTabEnablerScript = false;
}