<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_size extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size_units'				=> array(	// the default unit size array.
				'px'	=> 'px',	// pixel
				'%'		=> '%',		// percentage
				'em'	=> 'em',	// font size
				'ex'	=> 'ex',	// font height
				'in'	=> 'in',	// inch
				'cm'	=> 'cm',	// centimetre
				'mm'	=> 'mm',	// millimetre
				'pt'	=> 'pt',	// point
				'pc'	=> 'pc',	// pica
			),
			'size'						=> 10,
			'vUnitSize'					=> 1,
			'vMaxLength'				=> 400,
			'vMin'						=> null,
			'vMax'						=> null,
			'vStep'						=> null,
			'vMultiple'					=> false,
			'vWidth'					=> '',
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
		"/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		" . PHP_EOL;
	}
	
	/**
	 * Returns the output of the field type.
	 *
	 * Returns the size input fields. This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
	 * Useful for theme developers.
	 * 
	 * @since			2.0.1
	 * @since			2.1.5			Moved from AdminPageFramework_InputField. Changed the name from getSizeField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
				
		$bSingle = ! is_array( $aField['label'] );
		$bIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $aField['size_units'] ) == 1 );
		$aSizeUnits = isset( $aField['size_units'] ) && is_array( $aField['size_units'] ) && $bIsSizeUnitForSingle 
			? $aField['size_units']
			: $_aDefaultKeys['size_units'];		
		
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<label for='{$sTagID}_{$sKey}'>"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. ( $sLabel 
							? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel ."</span>"
							: "" 
						)
						. "<input id='{$sTagID}_{$sKey}' "	// number field
							// . "style='text-align: right;'"
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
							. "type='number' "	// number
							. "name=" . ( $bSingle ? "'{$sFieldName}[size]' " : "'{$sFieldName}[{$sKey}][size]' " )
							. "value='" . ( $bSingle ? $this->getCorrespondingArrayValue( $vValue['size'], $sKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'size', '' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
							. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
							. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "					
						. "/>"
					. "</label>"
						. "<select id='{$sTagID}_{$sKey}' class='size-field-select'"	// select field
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "
							. ( ( $bMultipleOptions = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $bSingle ? "'{$sFieldName}[unit]" : "'{$sFieldName}[{$sKey}][unit]" ) . ( $bMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $aField['vUnitSize'], $sKey, $_aDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$bSingle ? $aSizeUnits : $this->getCorrespondingArrayValue( $aField['size_units'], $sKey, $aSizeUnits ),
							$bSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $sKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'unit', 'px' ),
							$sTagID,
							$sKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$bMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);			

		return "<div class='admin-page-framework-field-size' id='{$sTagID}'>" 
			. implode( '', $aOutput )
		. "</div>";
		
	}
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter. Moved from AdminPageFramwrodk_InputField.
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