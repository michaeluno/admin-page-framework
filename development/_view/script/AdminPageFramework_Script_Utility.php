<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_Utility' ) ) :
/**
 * Provides JavaScript utility scripts.
 * 
 * @since			3.0.0			
 * @package			AdminPageFramework
 * @subpackage		JavaScript
 * @internal
 */
class AdminPageFramework_Script_Utility {

	static public function getjQueryPlugin() {
		
		return "( function( $ ) {
			$.fn.reverse = [].reverse;
		
			$.fn.formatPrintText = function() {
				var aArgs = arguments;
				return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
					return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
						? aArgs[ parseInt( number ) + 1 ]
						: match
				;});
			};
		}( jQuery ));";
		
	}

}
endif;