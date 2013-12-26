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
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'number', 'range' ) as $sTextTypeSlug ) 
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();
		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vMin'				=> null,
			'vMax'				=> null,
			'vStep'				=> null,
			'size'				=> 30,
			'max_length'		=> 400,
		);	
	}
	
	/**
	 * Returns the output of the number input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {
		
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