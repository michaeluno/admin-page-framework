<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_export' ) ) :
/**
 * Defines the export field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_export extends AdminPageFramework_FieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'export_data'					=> null,	// ( array or string or object ) This is for the export field type. 			
			'export_format'					=> 'array',	// ( array or string )	for the export field type. Do not set a default value here. Currently array, json, and text are supported.
			'export_file_name'				=> null,	// ( array or string )	for the export field type. Do not set a default value here.
			'class_attribute'				=> 'button button-primary',	// ( array or string )	
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
			// : $aField['label'];		
				
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		
		// If vValue is not an array and the export data set, set the transient. ( it means single )
		if ( isset( $aField['export_data'] ) && ! is_array( $vValue ) )
			set_transient( md5( "{$aField['class_name']}_{$aField['field_id']}" ), $aField['export_data'], 60*2 );	// 2 minutes.
		
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			
			$sExportFormat = $this->getCorrespondingArrayValue( $aField['export_format'], $sKey, $_aDefaultKeys['export_format'] );
			
			// If it's one of the multiple export buttons and the export data is explictly set for the element, store it as transient in the option table.
			$bIsDataSet = false;
			if ( isset( $vValue[ $sKey ] ) && isset( $aField['export_data'][ $sKey ] ) ) {
				set_transient( md5( "{$aField['class_name']}_{$aField['field_id']}_{$sKey}" ), $aField['export_data'][ $sKey ], 60*2 );	// 2 minutes.
				$bIsDataSet = true;
			}
			
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$tag_id}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"					
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][file_name]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['export_file_name'], $sKey, $this->generateExportFileName( $aField['option_key'], $sExportFormat ) )
					. "' />"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $sExportFormat
					. "' />"				
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][transient]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . ( $bIsDataSet ? 1 : 0 )
					. "' />"				
					. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
						. "<input "
							. "id='{$tag_id}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the export button is a custom submit button.
							// . "name=" . ( is_array( $aField['label'] ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
							. "name='__export[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'export_options' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
									
		}
					
		return "<div class='admin-page-framework-field-export' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
	
		/**
		 * A helper function for the above method.
		 * 
		 * @remark			Currently only array, text or json is supported.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_InputField class.
		 */ 
		private function generateExportFileName( $sOptionKey, $sExportFormat='text' ) {
				
			switch ( trim( strtolower( $sExportFormat ) ) ) {
				case 'text':	// for plain text.
					$sExt = "txt";
					break;
				case 'json':	// for json.
					$sExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$sExt = "txt";
					break;
			}		
				
			return $sOptionKey . '_' . date("Ymd") . '.' . $sExt;
			
		}

}
endif;