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
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'select' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		'is_multiple'	=> '',
		'attributes'	=> array(
			'select'	=> array(
				'size'	=> 1,
				'autofocusNew'	=> '',
				// 'form'	=> 		// this is still work in progress
				'multiple'	=> '',	// set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
				'required'	=> '',		
			),
			'option'	=> array(
			
			),
		),
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
		return "/* Select Field Type */
			.admin-page-framework-field-select .admin-page-framework-input-label-container {
				vertical-align: top; 
			}
			.admin-page-framework-field-select .admin-page-framework-input-label-container {
				padding-right: 1em;
			}
		";
	}
	
	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function replyToGetField( $aField ) {
			
		$aField['attributes']['select'] = array(
			'id'	=>	$aField['input_id'],
			'multiple'	=>	$aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
		) + $aField['attributes']['select'];
// var_dump( $aField );
		return $aField['before_input_tag']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				. "<label for='{$aField['input_id']}'>"
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->getHTMLTagAttributesFromArray( $aField['attributes']['select'] ) . " >"
							. $this->_getOptionTags( $aField )
						. "</select>"
					. "</span>"
				. "</label>"					
			. "</div>"
			. $aField['after_input_tag'];
		
	}
		/**
		 * Returns the option tags of the select field.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $tag_id parameter.
		 * @since			3.0.0			Reconstructed entirely.
		 * @internal
		 */ 	
		private function _getOptionTags( &$aField ) {
			
			$aOutput = array();
			$aValue = ( array ) $aField['attributes']['value'];
			foreach( $aField['label'] as $sKey => $sLabel ) {
				
				$aValue = isset( $aField['attributes']['option'][ $sKey ]['value'] )
					? $aField['attributes']['option'][ $sKey ]['value']
					: $aValue;
				
				$aAttributes = array(
					'id'	=> $aField['input_id'] . '_' . $sKey,
					'value'	=> $sKey,
					'selected'	=> in_array( $sKey, $aValue ) ? 'Selected' : '',
				) + $aField['attributes']['option'];
				
				$aOutput[] =
					"<option " . $this->getHTMLTagAttributesFromArray( $aAttributes ) . " >"	
						. $sLabel
					. "</option>";
					
			}
			return implode( PHP_EOL, $aOutput );	
			
		}
		
	public function _replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	

		if ( ! is_array( $aField['label'] ) ) return;	

		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels = $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label ) {
			
			$bMultiple = $this->getCorrespondingArrayValue( $aField['is_multiple'], $sKey, $_aDefaultKeys['is_multiple'] );
			$aOutput[] = 
				"<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
					. "<label for='{$tag_id}_{$sKey}'>"
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
					. "</label>"
				. "</div>";
				
		}
	}		
		/**
		 * A helper function for the above replyToGetField() methods.
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