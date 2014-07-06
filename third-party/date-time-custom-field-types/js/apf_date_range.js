/*
 * jQuery Plugin for the date_range custom field type of Admin Page Framework.
 *
 */

(function ($) {

	$.fn.apf_date_range = function( sInputID_To, aFromOptions, aToOptions ) {
		this.apf_date_time_range_abstract( sInputID_To, aFromOptions, aToOptions, 'date' );
	};	
	$.fn.apf_date_time_range = function( sInputID_To, aFromOptions, aToOptions ) {
		this.apf_date_time_range_abstract( sInputID_To, aFromOptions, aToOptions, 'datetime' );
	};
	$.fn.apf_time_range = function( sInputID_To, aFromOptions, aToOptions ) {		
		// this.apf_date_time_range_abstract( sInputID_To, aFromOptions, aToOptions, 'time' );
		this.apf_time_range_abstract( sInputID_To, aFromOptions, aToOptions );
	};	

	/**
	 * Bind the jQuery time/date/datetime picker plugin to the given element.
	 * 
	 * Call the method from the input element of the starting date and pass the ending date's input ID to the first parameter.
	 * 
	 * @param 	string	sInputID_to		The input id of the text input field(tag) of the ending time(date).
	 * @param	array	aFromOptions	The options array(object) passed to the starting time(date) input field.
	 * @param	array	aToOptions		The options array(object) passed to the ending time(date) input field.
	 * @param	string	sType			Indicates either of 'time', 'datetime', or 'date'.
	 */
	$.fn.apf_time_range_abstract = function( sInputID_To, aFromOptions, aToOptions ) {
			
		var sType = 'time';
		var oStartDateInput = this;
		var oEndDateInput = $( '#' + sInputID_To );	
		var _aFromOptions = $.extend( true, [], aFromOptions );	// copy it to store for later use (repeatable fields)
		var _aToOptions = $.extend( true, [], aToOptions );	// copy it to store for later use (repeatable fields)
		var _sMethodName = sType + 'picker';
		
		// Format the options
		$.extend( true, aFromOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oEndDateInput.val() ) {
					var _sStartDate = oStartDateInput[ _sMethodName ]( 'getDate' );
					var _sEndDate = oEndDateInput[ _sMethodName ]( 'getDate' );
					if ( _sStartDate > _sEndDate ) {
						var _sHours = _sStartDate.getHours();
						var _sMinutes = _sStartDate.getMinutes();							
						oEndDateInput[ _sMethodName ]( 'setTime', _sHours + ':' + _sMinutes );
					}
				}
			},
			onSelect: function ( selectedDateTime ){

				var _sHours = oStartDateInput[ 'datepicker' ]( 'getDate' ).getHours();
				var _sMinutes = oStartDateInput[ 'datepicker' ]( 'getDate' ).getMinutes();
				oEndDateInput[ _sMethodName ]( 'option', 'minTime', _sHours + ':' + _sMinutes );
				
			},						
		});		 			
		$.extend( true, aToOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oStartDateInput.val() ) {
					var _sStartDate = oStartDateInput[ _sMethodName ]( 'getDate' );
					var _sEndDate = oEndDateInput[ _sMethodName ]( 'getDate' );
					if ( _sStartDate > _sEndDate ) {						
						var _sHours = _sEndDate.getHours();
						var _sMinutes = _sEndDate.getMinutes();					
						oStartDateInput[ _sMethodName ]( 'setTime', _sHours + ':' + _sMinutes );
					}
				}
			},
			onSelect: function( selectedDateTime ) {
				
				var _sHours = oEndDateInput[ 'datepicker' ]( 'getDate' ).getHours();
				var _sMinutes = oEndDateInput[ 'datepicker' ]( 'getDate' ).getMinutes();				
				oStartDateInput[ _sMethodName ]( 'option', 'maxTime', _sHours + ':' + _sMinutes );
				
			},		
		});		
		
		// Bind the 'From'(start date/time) and 'To'(end date/time) input with the datepicker plugin.
		oStartDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oStartDateInput[ _sMethodName ]( aFromOptions );
		oEndDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oEndDateInput[ _sMethodName ]( aToOptions );
		
		// Store the options for repeatable fields
		var sOptionID = this.closest( '.admin-page-framework-sections' ).attr( 'id' ) 
			+ '_' 
			+ this.closest( '.admin-page-framework-fields' ).attr( 'id' );
		this.setDateTimePickerOptions( sOptionID + '_from', _aFromOptions );		
		this.setDateTimePickerOptions( sOptionID + '_to', _aToOptions );
		
	};	
	
	/**
	 * Bind the jQuery time/date/datetime picker plugin to the given element.
	 * 
	 * Call the method from the input element of the starting date and pass the ending date's input ID to the first parameter.
	 * 
	 * @param 	string	sInputID_to		The input id of the text input field(tag) of the ending time(date).
	 * @param	array	aFromOptions	The options array(object) passed to the starting time(date) input field.
	 * @param	array	aToOptions		The options array(object) passed to the ending time(date) input field.
	 * @param	string	sType			Indicates either of 'time', 'datetime', or 'date'.
	 */
	$.fn.apf_date_time_range_abstract = function( sInputID_To, aFromOptions, aToOptions, sType ) {
		
		var oStartDateInput = this;
		var oEndDateInput = $( '#' + sInputID_To );	
		var _aFromOptions = $.extend( true, [], aFromOptions );	// copy it to store for later use (repeatable fields)
		var _aToOptions = $.extend( true, [], aToOptions );	// copy it to store for later use (repeatable fields)
		var _sMethodName = sType + 'picker';
		
		// var _sTypeSlug = _getTypeSlug( sType );	// can be Time, Date or DateTime
		
		// Format the options
		$.extend( true, aFromOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oEndDateInput.val() ) {
					var _sStartDate = oStartDateInput[ _sMethodName ]( 'getDate' );
					var _sEndDate = oEndDateInput[ _sMethodName ]( 'getDate' );
					if ( _sStartDate > _sEndDate ) {
						oEndDateInput[ _sMethodName ]( 'setDate', _sStartDate );
					}
				}
			},
			onSelect: function ( selectedDateTime ){
				oEndDateInput[ _sMethodName ]( 'option', 'minDate', oStartDateInput[ _sMethodName ]( 'getDate' ) );
				// oEndDateInput[ _sMethodName ]( 'option', 'minDateTime', oStartDateInput[ _sMethodName ]( 'getDate' ) );	// does not work
			},						
		});		 			
		$.extend( true, aToOptions, {	// recursive merge
			onClose: function( dateText, inst ) {
				if ( '' != oStartDateInput.val() ) {
					var _sStartDate = oStartDateInput[ _sMethodName ]( 'getDate' );
					var _sEndDate = oEndDateInput[ _sMethodName ]( 'getDate' );
					if ( _sStartDate > _sEndDate ) {
						oStartDateInput[ _sMethodName ]( 'setDate', _sEndDate );
					}
				}
			},
			onSelect: function( selectedDateTime ) {
				oStartDateInput[ _sMethodName ]( 'option', 'maxDate', oEndDateInput[ _sMethodName ]( 'getDate' ) );
				// oStartDateInput[ _sMethodName ]( 'option', 'maxDateTime', oEndDateInput[ _sMethodName ]( 'getDate' ) ); // does not work
			},		
		});		

		// Bind the 'From'(start date/time) and 'To'(end date/time) input with the datepicker plugin.
		oStartDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oStartDateInput[ _sMethodName ]( aFromOptions );
		oEndDateInput.removeClass( 'hasDatepicker' );	// for repeatable fields
		oEndDateInput[ _sMethodName ]( aToOptions );
		
		// Store the options for repeatable fields
		var sOptionID = this.closest( '.admin-page-framework-sections' ).attr( 'id' ) 
			+ '_' 
			+ this.closest( '.admin-page-framework-fields' ).attr( 'id' );
		this.setDateTimePickerOptions( sOptionID + '_from', _aFromOptions );		
		this.setDateTimePickerOptions( sOptionID + '_to', _aToOptions );
		
	};		
	
	/**
	 * Returns the type slug from the given type.
	 * 
	 * This is used to set the options for jQuery datetimepicker plugin cush as minDateTime or maxDate in the callback functions.
	 * Those options vary depending on the type.
	 * 
	 * The type can be either of the 'time', 'datetime', or 'date'.
	 * 
	 * @deprecated
	 */
	var _getTypeSlug = function( sType ) {
		
		switch( sType ) {
			case 'time':
				return 'Time';
			break;
			case 'datetime':
				return 'DateTime';
			break;
			case 'date':
			default:
				return 'Date';
			break;
		} 		
		
	};
	
	
}(jQuery));