<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_number' ) ) :
/**
 * Defines the number, and range field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_number extends AdminPageFramework_FieldType_Base {

	/**
	 * Defines the field type slugs used for this field type.
	 * 
	 */
	protected $aFieldTypeSlugs = array( 'number', 'range' );
	
	/**
	 * Returns the array of the field type specific default keys.
	 * @see			http://dev.w3.org/html5/markup/input.number.html
	 */
	protected function getDefaultKeys() { 
		return array(
			'attributes'			=> array(
				'size'	=> 30,
				'maxlength' => 400,
				'class' => '',	
				'min'	=> '',
				'max'	=> '',
				'step'  => '',
				'readonly' => '',
				'required' => '',
				'placeholder' => '',
				'list' => '',
				'autofocus' => '',
				'autocomplete' => '',
			),
		) + self::$_aDefaultKeys;	// $_aDefaultKeys is defined in the base class.		
	}
	
	
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function replyToGetInputField( $aField ) {

		$aAttributes = $aField['attributes'] + array(
			'id' => $aField['input_id'],
			'name' => $aField['field_name'],
			'value' => $aField['value'],
			'type' => $aField['type'],	// number
		);	
		return 
			"<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input_tag']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->getHTMLTagAttributesFromArray( $aAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input_tag']
				. "</label>"
			. "</div>"
		;
		
	}
	
	/**
	 * Returns the output of the number input field.
	 * 
	 * @since			2.1.5
	 */
	public function _replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {
		
		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$tag_id}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input id='{$tag_id}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, 30 ) . "' "
								. "type='{$aField['type']}' "
								. "name=" . ( is_array( $aFields ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
								. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
								. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
								. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);				
					
		return "<div class='admin-page-framework-field-number' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
		
	}
	
}
endif;