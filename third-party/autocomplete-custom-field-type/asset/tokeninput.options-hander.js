/*
 * jQuery Plugin: Options Handler of Tokenizing Autocomplete Text Entry for Admin Page Framework.
 *
 */

(function ($) {
	$.fn.storeTokenInputOptions = function( sID, asOptions1, aOptions2 ) {
		if ( ! $.fn.aTokenInputOptions ) $.fn.aTokenInputOptions = {};
		$.fn.aTokenInputOptions[ sID ] = [ asOptions1, aOptions2 ];
	};	
	$.fn.getTokenInputOptions = function( sID ) {
		return ( typeof $.fn.aTokenInputOptions[ sID ] === undefined )
			? null
			: $.fn.aTokenInputOptions[ sID ];
	}
	
}(jQuery));
