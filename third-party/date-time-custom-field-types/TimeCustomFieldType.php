<?php
class TimeCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'time_format'	 		=> 'H:mm',				// ( array or string ) This is for the date field type that specifies the date format.
			'vMaxLength'			=> 400,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {

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
		
	}	
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
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
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "	// text, password, etc.
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getTimePickerEnablerScript( "{$sTagID}_{$sKey}", $this->getCorrespondingArrayValue( $aField['time_format'], $sKey, $_aDefaultKeys['time_format'] ) )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-date' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
		/**
		 * A helper function for the above getDateField() method.
		 * 
		 */
		private function getTimePickerEnablerScript( $sID, $sTimeFormat ) {
			return 
				"<script type='text/javascript' class='time-picker-enabler-script' data-id='{$sID}' data-time_format='{$sTimeFormat}'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sID}' ).timepicker({
							timeFormat : '{$sTimeFormat}',
							showButtonPanel : false,
						});
					});
				</script>";
		}
	
}