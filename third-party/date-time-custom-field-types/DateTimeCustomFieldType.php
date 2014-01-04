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
	 * Loads the field type necessary components.
	 */ 
/* 	public function _replyToFieldLoader() {

		wp_enqueue_script( 'jquery-ui-datepicker' );
	
		wp_enqueue_script(
			'jquery-ui-timepicker',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/jquery-ui-timepicker-addon.min.js' ),
			array( 'jquery-ui-datepicker' )	// dependency
		);
		wp_enqueue_script(
			'jquery-ui-sliderAccess',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/jquery-ui-sliderAccess.js' ),
			array( 'jquery-ui-timepicker' ) // dependency
		);		
		
	}	 */
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 
return "";
		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		/*	The below function will be triggered when a new repeatable field is added. */
		return "
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

						/* If the input tag is not found, do nothing  */
						var nodeNewDatePickerInput = node.find( 'input.datepicker' );
						if ( nodeNewDatePickerInput.length <= 0 ) return;

						/* Bind the date picker script */
						nodeNewDatePickerInput.removeClass( 'hasDatepicker' );
						nodeNewDatePickerInput.datepicker({
							dateFormat : nodeNewDatePickerInput.data( 'date_format' ),
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
	 * Returns the output of the field type.
	 * 
	 */
	public function _replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$tag_id}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['before_input'], $sKey, $_aDefaultKeys['before_input'] ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$tag_id}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
								. "type='text' "	// text, password, etc.
								. "name=" . ( is_array( $aFields ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['after_input'], $sKey, $_aDefaultKeys['after_input'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDateTimePickerEnablerScript( 
						"{$tag_id}_{$sKey}", 
						$this->getCorrespondingArrayValue( $aField['date_format'], $sKey, $_aDefaultKeys['date_format'] ),
						$this->getCorrespondingArrayValue( $aField['time_format'], $sKey, $_aDefaultKeys['time_format'] ) 
					)
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-date' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
		/**
		 * A helper function for the above getDateField() method.
		 * 
		 */
		private function getDateTimePickerEnablerScript( $sID, $sDateFormat, $sTimeFormat ) {
			return 
				// "<script type='text/javascript' class='date-time-picker-enabler-script' data-id='{$sID}' data-time_format='{$sTimeFormat}'>
				"<script type='text/javascript' class='date-time-picker-enabler-script'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sID}' ).datetimepicker({
							timeFormat : '{$sTimeFormat}',
							dateFormat : '{$sDateFormat}',
							showButtonPanel : false,
						});
					});
				</script>";
		}
	
}