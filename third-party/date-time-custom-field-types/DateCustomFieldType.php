<?php
class DateCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'date_format'	 		=> 'yy/mm/dd',				// ( array or string ) This is for the date field type that specifies the date format.
			'max_length'			=> 400,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}	
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	// protected function getEnqueuingScripts() { 
		// return array(
		// );
	// }	

	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
			dirname( __FILE__ ) . '/css/jquery-ui-1.10.3.min.css',
		); 
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
		return "
		/* Date Picker */
		.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all {
			display: none;
		}		
		" . PHP_EOL;		
	}

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the geometry custom field type.
	 * 
	 */
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

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
								. "class='datepicker " . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
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
					. $this->getDatePickerEnablerScript( "{$tag_id}_{$sKey}", $this->getCorrespondingArrayValue( $aField['date_format'], $sKey, $_aDefaultKeys['date_format'] ) )
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
		 * A helper function for the above replyToGetField() method.
		 * 
		 */
		private function getDatePickerEnablerScript( $sID, $sDateFormat ) {
			return 
				"<script type='text/javascript' class='date-picker-enabler-script' data-id='{$sID}' data-date_format='{$sDateFormat}'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sID}' ).datepicker({
							dateFormat : '{$sDateFormat}'
						});
					});
				</script>";
		}

}