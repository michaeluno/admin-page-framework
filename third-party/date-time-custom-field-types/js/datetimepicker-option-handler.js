/*
 * jQuery Plugin: Options Handler of the DateTime picker jQuery plugin for Admin Page Framework.
 *
 */

(function ($) {
	$.fn.setDateTimePickerOptions = function( sID, aOptions1 ) {
		if ( ! $.fn.aDateTimePickerOptions ) $.fn.aDateTimePickerOptions = {};
		var sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
		$.fn.aDateTimePickerOptions[ sID ] = aOptions1;
	};	
	$.fn.getDateTimePickerOptions = function( sID ) {
		var sID = sID.replace( /__\d+_/, '___' ); // remove the section index
		return ( typeof $.fn.aDateTimePickerOptions[ sID ] === undefined )
			? null
			: $.fn.aDateTimePickerOptions[ sID ];
	}
	
}(jQuery));
