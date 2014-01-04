<?php
class DateTimeCustomFieldType extends AdminPageFramework_FieldType {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'date_time', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'date_format'	=>	'yy/mm/dd',
		'time_format'	=> 'H:mm',
		'attributes'	=>	array(
			'size'	=>	10,
			'maxlength'	=>	400,
		),	
	);
	
	/**
	 * Loads the field type necessary components.
	 */ 
	public function setUp() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}	

	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			array( 'src'	=> dirname( __FILE__ ) . '/js/jquery-ui-timepicker-addon.min.js', 'dependencies'	=> array( 'jquery-ui-datepicker' ) ),
			array( 'src'	=> dirname( __FILE__ ) . '/js/jquery-ui-sliderAccess.js', 'dependencies'	=> array( 'jquery-ui-datepicker' ) ),
		);
	}	
	
	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
			dirname( __FILE__ ) . '/css/jquery-ui-1.10.3.min.css',
			dirname( __FILE__ ) . '/css/jquery-ui-timepicker-addon.min.css',
		); 
	}	
		
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 

		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		/*	The below function will be triggered when a new repeatable field is added. */
		return "
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not this field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

						/* If the input tag is not found, do nothing  */
						var nodeNewDateTimePickerInput = node.find( 'input.datetime_picker' );
						if ( nodeNewDateTimePickerInput.length <= 0 ) return;

						/* Bind the date picker script */
						nodeNewDateTimePickerInput.removeClass( 'hasDatepicker' );
						nodeNewDateTimePickerInput.datetimepicker({
							timeFormat : nodeNewDateTimePickerInput.data( 'time_format' ),
							dateFormat : nodeNewDateTimePickerInput.data( 'date_format' ),
							showButtonPanel : false,
						});						
						
					}
				});
			});		
		
		" . PHP_EOL;
		
	} 
	

	/**
	 * Returns IE specific CSS rules.
	 */
	protected function getIEStyles() { return ''; }

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	protected function getStyles() { return ""; }	
		
	/**
	 * Returns the output of this field type.
	 */
	protected function getField( $aField ) { 
			
		$aInputAttributes = array(
			'type'	=>	'text',
			'data-date_format'	=> $aField['date_format'],
			'data-time_format'	=> $aField['time_format'],
		) + $aField['attributes'];
		$aInputAttributes['class']	.= ' datetime_picker';
		return 
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input']
				. "</label>"
			. "</div>"
			. $this->getDateTimePickerEnablerScript( $aField['input_id'], $aField['date_format'], $aField['time_format'] )
			. $aField['after_label'];
		
	}	

		/**
		 * A helper function for the above getDateField() method.
		 * 
		 */
		private function getDateTimePickerEnablerScript( $sInputID, $sDateFormat, $sTimeFormat ) {
			return 
				// "<script type='text/javascript' class='date-time-picker-enabler-script' data-id='{$sID}' data-time_format='{$sTimeFormat}'>
				"<script type='text/javascript' class='date-time-picker-enabler-script'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sInputID}' ).datetimepicker({
							timeFormat : '{$sTimeFormat}',
							dateFormat : '{$sDateFormat}',
							showButtonPanel : false,
						});
					});
				</script>";
		}
	
}