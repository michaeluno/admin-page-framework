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
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['strFieldName'];
		$sTagID = $aField['strTagID'];
		$sFieldClassSelector = $aField['strFieldClassSelector'];
		$aDefaultKeys = $aFieldDefinition['arrDefaultKeys'];	
		
		$aFields = $aField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['vLabel'];		
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['vLabelMinWidth'], $sKey, $aDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='knob " . $this->getCorrespondingArrayValue( $aField['vClassAttribute'], $sKey, $aDefaultKeys['vClassAttribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['vSize'], $sKey, $aDefaultKeys['vSize'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $aDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. $this->getDataAttributes( $aField, $sKey, $aDefaultKeys )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDialEnablerScript( "{$sTagID}_{$sKey}" )					
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['vDelimiter'], $sKey, $aDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);

		return "<div class='admin-page-framework-field-dial' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
			
	}	
	
	/**
	 * A helper function for the above method.
	 */
	private function getDataAttributes( $aField, $sKey, $aDefaultKeys ) {
		
		$aDataAttribute = isset( $aField['vDataAttribute'][ $sKey ] )
			? $this->uniteArrays( ( array ) $aField['vDataAttribute'][ $sKey ], $aDefaultKeys['vDataAttribute'] )
			: $aField['vDataAttribute'];
			
		return $this->convertArrayToDataAttributes( $aDataAttribute );
		
	}
	
	/**
	 * Generates the data-{...} attributes from the given array.
	 * 
	 */
	private function convertArrayToDataAttributes( $aAssociativeArray ) {
				
		$aDataAttributes = array();
		foreach( $aAssociativeArray as $sKey => $sValue ) 
			if ( isset( $sValue ) )
				$aDataAttributes[] = "data-{$sKey}='{$sValue}'";
			
		return implode( ' ', $aDataAttributes );
		
	}
	
	private function getDialEnablerScript( $sInputID ) {
			return 
				"<script type='text/javascript' class='dial-enabler-script'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sInputID}' ).knob();
					});
				</script>";		
		
	}
	
}