<?php
class DialCustomFieldType extends AdminPageFramework_CustomFieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSize'					=> 10,
			'vMaxLength'			=> 400,
			'vDataAttribute'		=> array(),		// the array holds the data for the data-{...} attribute of the input tag.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		
	}	
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			dirname( __FILE__ ) . '/js/jquery.knob.js',
		);
	}	

	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
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
		return "
			.admin-page-framework-field-dial .admin-page-framework-input-label-container {
				padding-right: 1em;
				padding-bottom: 2em;
			}
			.admin-page-framework-field-dial .admin-page-framework-input-label-string {
				vertical-align: top;
			}
		
		";		
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
								. "class='knob " . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
								. $this->getDataAttributes( $arrField, $strKey, $arrDefaultKeys )
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDialEnablerScript( "{$strTagID}_{$strKey}" )					
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);

		return "<div class='admin-page-framework-field-dial' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
			
	}	
	
	/**
	 * A helper function for the above method.
	 */
	private function getDataAttributes( $arrField, $strKey, $arrDefaultKeys ) {
		
		$arrDataAttribute = isset( $arrField['vDataAttribute'][ $strKey ] )
			? $this->uniteArrays( ( array ) $arrField['vDataAttribute'][ $strKey ], $arrDefaultKeys['vDataAttribute'] )
			: $arrField['vDataAttribute'];
			
		return $this->convertArrayToDataAttributes( $arrDataAttribute );
		
	}
	
	/**
	 * Generates the data-{...} attributes from the given array.
	 * 
	 */
	private function convertArrayToDataAttributes( $arrAssociativeArray ) {
				
		$arrDataAttributes = array();
		foreach( $arrAssociativeArray as $strKey => $strValue ) 
			if ( isset( $strValue ) )
				$arrDataAttributes[] = "data-{$strKey}='{$strValue}'";
			
		return implode( ' ', $arrDataAttributes );
		
	}
	
	private function getDialEnablerScript( $strInputID ) {
			return 
				"<script type='text/javascript' class='dial-enabler-script'>
					jQuery( document ).ready( function() {
						jQuery( '#{$strInputID}' ).knob();
					});
				</script>";		
		
	}
	
}