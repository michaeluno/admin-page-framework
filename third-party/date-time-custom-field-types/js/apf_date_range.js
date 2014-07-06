/*
 * jQuery Plugin for the date_range custom field type of Admin Page Framework.
 *
 * Usage: Call the method from the input element of the starting date and pass the ending date's input ID to the first parameter.
 */

(function ($) {
	$.fn.apf_date_range = function( sInputID_To, aFromOptions, aToOptions ) {
	
		// var sInputID_From = this.attr( 'id' );
		// var oStartDateInput = $( '#' + sInputID_From );	
		var oStartDateInput = this;
		var oEndDateInput = $( '#' + sInputID_To );	
		var _aFromOptions = $.extend( true, [], aFromOptions );	// copy it to store for later use (repeatable fields)
		var _aToOptions = $.extend( true, [], aToOptions );	// copy it to store for later use (repeatable fields)
			
		// Format the options
		$.extend( true, aFromOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oEndDateInput.val() ) {
					var _sStartDate = oStartDateInput.datepicker( 'getDate' );
					var _sEndDate = oEndDateInput.datepicker( 'getDate' );
					if ( _sStartDate > _sEndDate ) {
						oEndDateInput.datepicker( 'setDate', _sStartDate );
					}
				}
			},
			onSelect: function ( selectedDateTime ){
				oEndDateInput.datepicker( 'option', 'minDate', oStartDateInput.datepicker('getDate') );
			},						
		});		 			
		$.extend( true, aToOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oStartDateInput.val() ) {
					var _sStartDate = oStartDateInput.datepicker( 'getDate' );
					var _sEndDate = oEndDateInput.datepicker( 'getDate' );
					if ( _sStartDate > _sEndDate ) {
						oStartDateInput.datepicker( 'setDate', _sEndDate );
					}
				}
			},
			onSelect: function( selectedDateTime ) {
				oStartDateInput.datepicker( 'option', 'maxDate', oEndDateInput.datepicker( 'getDate' ) );
			},		
		});		
		
		// Bind the 'From'(start date) input with the datepicker plugin.
		oStartDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oStartDateInput.datepicker( aFromOptions );
		oEndDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oEndDateInput.datepicker( aToOptions );

		// Store the options for repeatable fields
		var sOptionID = this.closest( '.admin-page-framework-sections' ).attr( 'id' ) 
			+ '_' 
			+ this.closest( '.admin-page-framework-fields' ).attr( 'id' );
		this.setDateTimePickerOptions( sOptionID + '_from', _aFromOptions );		
		this.setDateTimePickerOptions( sOptionID + '_to', _aToOptions );		
		
	};	
}(jQuery));