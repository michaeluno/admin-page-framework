/*
 * jQuery Plugin: Options Handler of Tokenizing Autocomplete Text Entry for Admin Page Framework.
 *
 */

(function ($) {
	$.fn.storeTokenInputOptions = function( sID, asOptions1, aOptions2 ) {
		if ( ! $.fn.aTokenInputOptions ) $.fn.aTokenInputOptions = {};
		sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
		$.fn.aTokenInputOptions[ sID ] = [ asOptions1, aOptions2 ];
	};	
	$.fn.getTokenInputOptions = function( sID ) {
		sID = sID.replace( /__\d+_/, '___' ); // remove the section index
		return ( 'undefined' === typeof $.fn.aTokenInputOptions[ sID ] )
			? null
			: $.fn.aTokenInputOptions[ sID ];
	}
	
}(jQuery));
