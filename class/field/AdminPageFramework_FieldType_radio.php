<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType_Base {
	
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
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $aField['label'] ) ) return;	
		
		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels =  $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label )  
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. $this->getRadioTags( $aField, $vValue, $label, $field_name, $tag_id, $sKey, $bSingle, $_aDefaultKeys )				
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-radio' id='{$tag_id}'>" 
				. implode( '', $aOutput )
			. "</div>";
		
	}
		/**
		 * A helper function for the <em>getRadioField()</em> method.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added the $aField, $field_name, $_aDefaultKeys, $tag_id, and $vValue parameter.
		 */ 
		private function getRadioTags( $aField, $vValue, $aLabels, $field_name, $tag_id, $sIterationID, $bSingle, $_aDefaultKeys ) {
			
			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) 
				$aOutput[] = 
					"<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
						. "<label for='{$tag_id}_{$sIterationID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$tag_id}_{$sIterationID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='radio' "
									. "value='{$sKey}' "
									. "name=" . ( ! $bSingle  ? "'{$field_name}[{$sIterationID}]' " : "'{$field_name}' " )
									. ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? 'Checked ' : '' )
									. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>";

			return implode( '', $aOutput );
		}

}
endif;