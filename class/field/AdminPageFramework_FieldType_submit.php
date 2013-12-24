<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_submit extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(		
			'class_attribute'					=> 'button button-primary',
			'redirect_url'							=> null,
			'links'								=> null,
			'is_reset'							=> null,
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
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
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

		
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			$sRedirectURL = $this->getCorrespondingArrayValue( $aField['redirect_url'], $sKey, $_aDefaultKeys['redirect_url'] );
			$sLinkURL = $this->getCorrespondingArrayValue( $aField['links'], $sKey, $_aDefaultKeys['links'] );
			$sResetKey = $this->getCorrespondingArrayValue( $aField['is_reset'], $sKey, $_aDefaultKeys['is_reset'] );
			$bResetConfirmed = $this->checkConfirmationDisplayed( $sResetKey, $sFieldNameFlat ); 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][input_id]' "
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][field_id]' "
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][name]' "
						. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
					. "/>" 						
					// for the redirect_url key
					. ( $sRedirectURL 
						? "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sRedirectURL . "' "
						. "/>" 
						. "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}" : "'" )
						. "/>" 
						: "" 
					)
					// for the links key
					. ( $sLinkURL 
						? "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sLinkURL . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: "" 
					)
					// for the is_reset key
					. ( $sResetKey && ! $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sFieldNameFlat . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. ( $sResetKey && $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sResetKey . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "	// submit
							. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'submit' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-submit' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the is_reset key is set.
		 * 
		 */
		private function checkConfirmationDisplayed( $sResetKey, $sFlatFieldName ) {
				
			if ( ! $sResetKey ) return false;
			
			$bResetConfirmed =  get_transient( md5( "reset_confirm_" . $sFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $bResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $sFlatFieldName ) );
				
			return $bResetConfirmed;
			
		}

	/*
	 *	Shared Methods 
	 */
	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 * 
	 * @remark			Used by the import and submit field types.
	 * @since			2.0.0
	 * @since			2.1.5			Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_InputField.
	 */ 
	protected function getInputFieldNameFlat( $aField ) {	
	
		return isset( $aField['option_key'] ) // the meta box class does not use the option key
			? "{$aField['option_key']}|{$aField['page_slug']}|{$aField['section_id']}|{$aField['field_id']}"
			: $aField['field_id'];
		
	}			
	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>label</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @remark			The submit, import, and export field types use this method.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
	 */ 
	protected function getInputFieldValueFromLabel( $aField ) {	
		
		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		if ( isset( $aField['label'] ) ) return $aField['label'];
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}
	
}
endif;