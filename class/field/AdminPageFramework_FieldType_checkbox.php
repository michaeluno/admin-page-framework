<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_checkbox' ) ) :
/**
 * Defines the checkbox field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType_Base {
	
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
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		

		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$tag_id}_{$sKey}'>"	
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name=" .  ( is_array( $aField['label'] ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
								. "<input "
									. "id='{$tag_id}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// checkbox
									. "name=" . ( is_array( $aField['label'] ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, null ) == 1 ? "Checked " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>"
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-checkbox' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";	
	
	}

}
endif;