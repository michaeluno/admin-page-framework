<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_import extends AdminPageFramework_InputFieldType_submit {
	
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
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
	
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$sTagID}_{$sKey}' "
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
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$sTagID}_{$sKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attributeUpload'], $sKey, $_aDefaultKeys['class_attributeUpload'] ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
							. "type='file' "	// upload field. the file type will be stored in $_FILE
							. "name='__import[{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
	
}
endif;