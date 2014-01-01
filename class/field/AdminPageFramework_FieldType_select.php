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
			'optgroup'	=> array(),
			'option'	=> array(),
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
			
		$aSelectAttributes = array(
			'id'	=>	$aField['input_id'],
			'multiple'	=>	$aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
		) + $aField['attributes']['select'];
		$aSelectAttributes['name'] = empty( $aSelectAttributes['multiple'] ) ? $aField['field_name'] : "{$aField['field_name']}[]";

		return
			$aField['before_field']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $aSelectAttributes ) . " >"
							. $this->_getOptionTags( $aField, $aField['label'] )
						. "</select>"
					. "</span>"
					. $aField['after_input']
				. "</label>"					
			. "</div>"
			. $aField['after_field']; 
			
		
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
		private function _getOptionTags( &$aField, $aLabel ) {
			
			$aOutput = array();
			$aValue = ( array ) $aField['attributes']['value'];

			foreach( $aLabel as $sKey => $asLabel ) {
				
				// For the optgroup tag,
				if ( is_array( $asLabel ) ) {	// optgroup
				
					$aAttributes = isset( $aField['attributes']['optgroup'][ $sKey ] ) && is_array( $aField['attributes']['optgroup'][ $sKey ] )
						? $aField['attributes']['optgroup'][ $sKey ] + $aField['attributes']['optgroup']
						: $aField['attributes']['optgroup'];
						
					$aOutput[] = 
						"<optgroup label='{$sKey}'" . $this->generateAttributes( $aAttributes ) . ">"
						. $this->_getOptionTags( $aField, $asLabel )
						. "</optgroup>";
					continue;
					
				}
				
				// For the option tag,
				$aValue = isset( $aField['attributes']['option'][ $sKey ]['value'] )
					? $aField['attributes']['option'][ $sKey ]['value']
					: $aValue;
				
				$aAttributes = array(
					'id'	=> $aField['input_id'] . '_' . $sKey,
					'value'	=> $sKey,
					'selected'	=> in_array( ( string ) $sKey, $aValue ) ? 'Selected' : '',
				) + ( isset( $aField['attributes']['option'][ $sKey ] ) && is_array( $aField['attributes']['option'][ $sKey ] )
					? $aField['attributes']['option'][ $sKey ] + $aField['attributes']['option']
					: $aField['attributes']['option']
				);

				$aOutput[] =
					"<option " . $this->generateAttributes( $aAttributes ) . " >"	
						. $asLabel
					. "</option>";
					
			}
			return implode( PHP_EOL, $aOutput );	
			
		}
		
}
endif;