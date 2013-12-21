<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_select extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
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
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
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
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<select id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "
									. ( $bMultiple ? "multiple='Multiple' " : '' )
									. "name=" . ( $bSingle ? "'{$sFieldName}" : "'{$sFieldName}[{$sKey}]" ) . ( $bMultiple ? "[]' " : "' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. "size=" . ( $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) ) . " "
									. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
								. ">"
									. $this->getOptionTags( $label, $vValue, $sTagID, $sKey, $bSingle, $bMultiple )
								. "</select>"
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $sTagID, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
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