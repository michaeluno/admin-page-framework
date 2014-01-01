<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @extends			AdminPageFramework_FieldType_select
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_size extends AdminPageFramework_FieldType_select {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'size', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'is_multiple'	=> false,	
		'units'		=> null,	// do not define units here since this will be merged with the user defined field array.
		'attributes'	=> array(
			'size'	=>	array(
				'size'	=>	10,
				'maxlength'	=>	400,
				'min'	=>	'',
				'max'	=>	'',
			),
			'unit'	=>	array(
				'multiple'	=>	'',
				
				'size'	=> 1,
				'autofocusNew'	=> '',
				// 'form'	=> 		// this is still work in progress
				'multiple'	=> '',	// set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
				'required'	=> '',
			),
			'optgroup'	=> array(),
			'option'	=> array(),			
		),	
	);
	
	/**
	 * Defines the default units.
	 * 
	 * This goes to the 'units' element of the field definition array.
	 * 
	 * @since			3.0.0
	 */
	protected $aDefaultUnits = array(
		'px'	=> 'px',	// pixel
		'%'		=> '%',		// percentage
		'em'	=> 'em',	// font size
		'ex'	=> 'ex',	// font height
		'in'	=> 'in',	// inch
		'cm'	=> 'cm',	// centimetre
		'mm'	=> 'mm',	// millimetre
		'pt'	=> 'pt',	// point
		'pc'	=> 'pc',	// pica
	);
		
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
	 * @since			3.0.0			Reconstructed entirely which involves dropping unnecessary parameters and renaming keys in the field definition array.
	 */
	public function replyToGetField( $aField ) {
	
		/* 1. Initial set-up of the field definition array */
		$aField['units'] = isset( $aField['units'] ) 
			? $aField['units']
			: $this->aDefaultUnits;
	
		/* 2. Prepare attributes */
		
		/* 2-1. Base attributes */
		$aBaseAttributes = $aField['attributes'];
		unset( $aBaseAttributes['unit'], $aBaseAttributes['size'] ); 
		
		/* 2-2. Size attributes */		
		$aSizeAttributes = array(
			'type'	=>	'number',
			'id' =>	$aField['input_id'] . '_' . 'size',
			'name'	=>	$aField['field_name'] . '[size]',
			'value'	=>	isset( $aField['value']['size'] ) ? $aField['value']['size'] : '',
		) 
		+ $this->getFieldElementByKey( $aField['attributes'], 'size', $this->aDefaultKeys['attributes']['size'] )
		+ $aBaseAttributes;
		
		/* 2-3. Size label attributes */		
		$aSizeLabelAttributes = array(
			'for'	=>	$aSizeAttributes['id'],
			'class'	=>	$aSizeAttributes['disabled'] ? 'disabled' : '',
		);
		
		/* 2-4. Unit attributes */		
		$aUnitAttributes = array(
			'type'	=>	'select',
			'id'	=>	$aField['input_id']	. '_' . 'unit',
			'multiple'	=>	$aField['is_multiple']	? 'Multiple' : $aField['attributes']['unit']['multiple'],
			'value'	=>	isset( $aField['value']['unit'] ) ? $aField['value']['unit'] : '',
		)
		+ $this->getFieldElementByKey( $aField['attributes'], 'unit', $this->aDefaultKeys['attributes']['unit'] )
		+ $aBaseAttributes;
		$aUnitAttributes['name'] = empty( $aUnitAttributes['multiple'] ) ? "{$aField['field_name']}[unit]" : "{$aField['field_name']}[unit][]";
		
		/* 2-5. Unit label attributes */		
		$aUnitLabelAttributes = array(
			'for'	=>	$aUnitAttributes['id'],
			'class'	=>	$aUnitAttributes['disabled'] ? 'disabled' : '',
		);
		
		/* 3. Return the output */
		return
			$aField['before_field']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				/* The size (number) part */
				. "<label " . $this->generateAttributes( $aSizeLabelAttributes ) . ">"
					. $this->getFieldElementByKey( $aField['before_field'], 'size' )
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aSizeAttributes ) . " />"	// this method is defined in the base class
					. $this->getFieldElementByKey( $aField['after_input'], 'size' )
				. "</label>"
				/* The unit (select) part */
				. "<label " . $this->generateAttributes( $aUnitLabelAttributes ) . ">"
					. $this->getFieldElementByKey( $aField['before_field'], 'unit' )
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $aUnitAttributes ) . " >"
							. $this->_getOptionTags( $aUnitAttributes['id'], $aBaseAttributes, $aField['units'] )	// this method is defined in the select field type class
						. "</select>"
					. "</span>"
					. $this->getFieldElementByKey( $aField['after_input'], 'unit' )
				. "</label>"					
			. "</div>"
			. $aField['after_field']; 			
		
	}
	public function _replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
				
		$bSingle = ! is_array( $aField['label'] );
		$bIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $aField['units'] ) == 1 );
		$aSizeUnits = isset( $aField['units'] ) && is_array( $aField['units'] ) && $bIsSizeUnitForSingle 
			? $aField['units']
			: $_aDefaultKeys['units'];		
		
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<label for='{$tag_id}_{$sKey}'>"
						. $this->getCorrespondingArrayValue( $aField['before_input'], $sKey, $_aDefaultKeys['before_input'] ) 
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
							. ( ( $bMultipleOptions = $this->getCorrespondingArrayValue( $aField['is_multiple'], $sKey, $_aDefaultKeys['is_multiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $bSingle ? "'{$field_name}[unit]" : "'{$field_name}[{$sKey}][unit]" ) . ( $bMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $aField['vUnitSize'], $sKey, $_aDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$bSingle ? $aSizeUnits : $this->getCorrespondingArrayValue( $aField['units'], $sKey, $aSizeUnits ),
							$bSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $sKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'unit', 'px' ),
							$tag_id,
							$sKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$bMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $aField['after_input'], $sKey, $_aDefaultKeys['after_input'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);			


		
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