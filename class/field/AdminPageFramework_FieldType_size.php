<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_size extends AdminPageFramework_FieldType_Base {
	
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
			'max_length'				=> 400,
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
	public function replyToGetScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
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
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
				
		$bSingle = ! is_array( $aField['label'] );
		$bIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $aField['size_units'] ) == 1 );
		$aSizeUnits = isset( $aField['size_units'] ) && is_array( $aField['size_units'] ) && $bIsSizeUnitForSingle 
			? $aField['size_units']
			: $_aDefaultKeys['size_units'];		
		
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<label for='{$tag_id}_{$sKey}'>"
						. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
						. ( $sLabel 
							? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel ."</span>"
							: "" 
						)
						. "<input id='{$tag_id}_{$sKey}' "	// number field
							// . "style='text-align: right;'"
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
							. "type='number' "	// number
							. "name=" . ( $bSingle ? "'{$field_name}[size]' " : "'{$field_name}[{$sKey}][size]' " )
							. "value='" . ( $bSingle ? $this->getCorrespondingArrayValue( $vValue['size'], $sKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'size', '' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
							. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
							. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
							. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "					
						. "/>"
					. "</label>"
						. "<select id='{$tag_id}_{$sKey}' class='size-field-select'"	// select field
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "
							. ( ( $bMultipleOptions = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $bSingle ? "'{$field_name}[unit]" : "'{$field_name}[{$sKey}][unit]" ) . ( $bMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $aField['vUnitSize'], $sKey, $_aDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$bSingle ? $aSizeUnits : $this->getCorrespondingArrayValue( $aField['size_units'], $sKey, $aSizeUnits ),
							$bSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $sKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'unit', 'px' ),
							$tag_id,
							$sKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$bMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);			

		return "<div class='admin-page-framework-field-size' id='{$tag_id}'>" 
			. implode( '', $aOutput )
		. "</div>";
		
	}
		/**
		 * A helper function for the above replyToGetField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $tag_id parameter. Moved from AdminPageFramwrodk_InputField.
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