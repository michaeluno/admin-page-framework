<?php
class DialCustomFieldType extends AdminPageFramework_CustomFieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'vMaxLength'			=> 400,
			'data_attribute'		=> array(),		// the array holds the data for the data-{...} attribute of the input tag.
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
								. "class='knob " . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. $this->getDataAttributes( $aField, $sKey, $_aDefaultKeys )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDialEnablerScript( "{$sTagID}_{$sKey}" )					
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
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
	private function getDataAttributes( $aField, $sKey, $_aDefaultKeys ) {
		
		$aDataAttribute = isset( $aField['data_attribute'][ $sKey ] )
			? $this->uniteArrays( ( array ) $aField['data_attribute'][ $sKey ], $_aDefaultKeys['data_attribute'] )
			: $aField['data_attribute'];
			
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