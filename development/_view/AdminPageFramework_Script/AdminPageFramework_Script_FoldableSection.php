<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_CollapsibleSection' ) ) :
/**
 * Provides JavaScript utility scripts.
 * 
 * @since       3.3.4
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_CollapsibleSection extends AdminPageFramework_Script_Base {

    /**
     * Returns the script.
     * 
     * @since       3.3.4
     */
    static public function getScript() {
        
        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];        
   
        return <<<JAVASCRIPTS
( function( $ ) {
}( jQuery ));
JAVASCRIPTS;
    }
}
endif;