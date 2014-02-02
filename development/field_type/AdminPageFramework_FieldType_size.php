<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @extends			AdminPageFramework_FieldType_select
 * @since			2.1.5
 * @internal
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
	public function _replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function _replyToGetScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return
		"/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		.admin-page-framework-field-size label {
			width: auto;			
		} 
		.form-table td fieldset .admin-page-framework-field-size label {
			display: inline;
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
	public function _replyToGetField( $aField ) {
	
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
			'name'	=>	$aField['_input_name'] . '[size]',
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
		$aUnitAttributes['name'] = empty( $aUnitAttributes['multiple'] ) ? "{$aField['_input_name']}[unit]" : "{$aField['_input_name']}[unit][]";
		
		/* 2-5. Unit label attributes */		
		$aUnitLabelAttributes = array(
			'for'	=>	$aUnitAttributes['id'],
			'class'	=>	$aUnitAttributes['disabled'] ? 'disabled' : '',
		);
		
		/* 3. Return the output */
		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				/* The size (number) part */
				. "<label " . $this->generateAttributes( $aSizeLabelAttributes ) . ">"
					. $this->getFieldElementByKey( $aField['before_label'], 'size' )
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aSizeAttributes ) . " />"	// this method is defined in the base class
					. $this->getFieldElementByKey( $aField['after_input'], 'size' )
				. "</label>"
				/* The unit (select) part */
				. "<label " . $this->generateAttributes( $aUnitLabelAttributes ) . ">"
					. $this->getFieldElementByKey( $aField['before_label'], 'unit' )
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $aUnitAttributes ) . " >"
							. $this->_getOptionTags( $aUnitAttributes['id'], $aBaseAttributes, $aField['units'] )	// this method is defined in the select field type class
						. "</select>"
					. "</span>"
					. $this->getFieldElementByKey( $aField['after_input'], 'unit' )
					. "<div class='repeatable-field-buttons'></div>"	// the repeatable field buttons will be replaced with this element.
				. "</label>"					
			. "</div>"
			. $aField['after_label']; 			
		
	}

}
endif;