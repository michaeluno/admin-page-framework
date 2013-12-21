<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_hidden' ) ) :
/**
 * Defines the hidden field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_hidden extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
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
	 * 
	 * @remark			The user needs to assign the value to either the default key or the vValue key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// hidden
									. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. "value='" . $sValue  . "' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"
							. "</div>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-hidden' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}

}
endif;