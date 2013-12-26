<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_import extends AdminPageFramework_FieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'class_attribute'					=> 'import button button-primary',	// ( array or string )	
			'vAcceptAttribute'					=> 'audio/*|video/*|image/*|MIME_type',
			'class_attributeUpload'				=> 'import',
			'vImportOptionKey'					=> null,	// ( array or string )	for the import field type. The default value is the set option key for the framework.
			'vImportFormat'						=> 'array',	// ( array or string )	for the import field type.
			'vMerge'							=> false,	// ( array or boolean ) [2.1.5+] for the import field
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
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
	
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$field_nameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$tag_id}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][do_merge]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vMerge'], $sKey, $_aDefaultKeys['vMerge'] ) . "' "
					. "/>"							
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][import_option_key]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportOptionKey'], $sKey, $aField['option_key'] )
					. "' />"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportFormat'], $sKey, $_aDefaultKeys['vImportFormat'] )	// array, text, or json.
					. "' />"			
					. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$tag_id}_{$sKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attributeUpload'], $sKey, $_aDefaultKeys['class_attributeUpload'] ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
							. "type='file' "	// upload field. the file type will be stored in $_FILE
							. "name='__import[{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$tag_id}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
	
}
endif;