<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_file' ) ) :
/**
 * Defines the file field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_file extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vAcceptAttribute'				=> 'audio/*|video/*|image/*|MIME_type',
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
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
	 * Returns the output of the field type.
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
							. ( $sLabel && ! $aField['repeatable'] ?
								"<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input "
								. "id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
								. "type='{$aField['type']}' "	// file
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $aFields, $sKey ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
	}

}
endif;