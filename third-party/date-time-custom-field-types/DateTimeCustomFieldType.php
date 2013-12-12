<?php
class DateTimeCustomFieldType extends AdminPageFramework_CustomFieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSize'					=> 20,
			'vDateFormat'	 		=> 'yy/mm/dd',
			'vTimeFormat'	 		=> 'H:mm',
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
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];		
		
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. ( $strLabel && ! $arrField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: "" 
							)
							. "<input id='{$strTagID}_{$strKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "	// text, password, etc.
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDateTimePickerEnablerScript( 
						"{$strTagID}_{$strKey}", 
						$this->getCorrespondingArrayValue( $arrField['vDateFormat'], $strKey, $arrDefaultKeys['vDateFormat'] ),
						$this->getCorrespondingArrayValue( $arrField['vTimeFormat'], $strKey, $arrDefaultKeys['vTimeFormat'] ) 
					)
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-date' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}
		/**
		 * A helper function for the above getDateField() method.
		 * 
		 */
		private function getDateTimePickerEnablerScript( $strID, $strDateFormat, $strTimeFormat ) {
			return 
				"<script type='text/javascript' class='date-time-picker-enabler-script' data-id='{$strID}' data-time_format='{$strTimeFormat}'>
					jQuery( document ).ready( function() {
						jQuery( '#{$strID}' ).datetimepicker({
							timeFormat : '{$strTimeFormat}',
							dateFormat : '{$strDateFormat}',
							showButtonPanel : false,
						});

					});
				</script>";
		}
	
}