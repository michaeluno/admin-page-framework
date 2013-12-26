<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 1,
			'vMultiple'				=> false,				// ( array or boolean ) This value indicates whether the select tag should have the multiple attribute or not.
			'vWidth'				=> '',
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
		$aLabels = $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label ) {
			
			$bMultiple = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] );
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
						. "<label for='{$tag_id}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<select id='{$tag_id}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "
									. ( $bMultiple ? "multiple='Multiple' " : '' )
									. "name=" . ( $bSingle ? "'{$field_name}" : "'{$field_name}[{$sKey}]" ) . ( $bMultiple ? "[]' " : "' " )
									. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
									. "size=" . ( $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) ) . " "
									. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
								. ">"
									. $this->getOptionTags( $label, $vValue, $tag_id, $sKey, $bSingle, $bMultiple )
								. "</select>"
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $tag_id parameter.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $tag_id, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$tag_id}_{$sIterationID}_{$sKey}' "
						. "value='{$sKey}' "
						. (	$bMultiple 
							? ( in_array( $sKey, $aValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $sLabel
					. "</option>";
			}
			return implode( '', $aOutput );
		}
	
}
endif;