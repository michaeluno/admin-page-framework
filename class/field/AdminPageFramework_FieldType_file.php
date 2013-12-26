<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_file' ) ) :
/**
 * Defines the file field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_file extends AdminPageFramework_FieldType_Base {
	
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
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
							. ( $sLabel && ! $aField['repeatable'] ?
								"<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input "
								. "id='{$tag_id}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
								. "type='{$aField['type']}' "	// file
								. "name=" . ( is_array( $aFields ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
								. "value='" . $this->getCorrespondingArrayValue( $aFields, $sKey ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
	}

}
endif;