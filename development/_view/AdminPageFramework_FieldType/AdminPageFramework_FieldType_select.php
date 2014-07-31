<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'select', );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		'label'			=>	array(),
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
	public function _replyToGetField( $aField ) {
			
		$_aSelectAttributes = array(
			'id'		=>	$aField['input_id'],
			'multiple'	=>	$aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
		) + $aField['attributes']['select'];
		$_aSelectAttributes['name'] = empty( $_aSelectAttributes['multiple'] ) ? $aField['_input_name'] : "{$aField['_input_name']}[]";

		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $_aSelectAttributes ) . " >"
							. $this->_getOptionTags( $aField['input_id'], $aField['attributes'], $aField['label'] )
						. "</select>"
					. "</span>"
					. $aField['after_input']
					. "<div class='repeatable-field-buttons'></div>"	// the repeatable field buttons will be replaced with this element.
				. "</label>"					
			. "</div>"
			. $aField['after_label']; 		
		
	}
		/**
		 * Returns the option tags of the select field.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $tag_id parameter.
		 * @since			3.0.0			Reconstructed entirely.
		 * @remark			The scope is protected as the size unit type uses this.
		 * @internal
		 */ 	
		protected function _getOptionTags( $sInputID, &$aAttributes, $aLabel ) {
			
			$_aOutput = array();
			$_aValue = ( array ) $aAttributes['value'];

			foreach( $aLabel as $__sKey => $__asLabel ) {
				
				// For the optgroup tag,
				if ( is_array( $__asLabel ) ) {	// optgroup
				
					$_aOptGroupAttributes = isset( $aAttributes['optgroup'][ $__sKey ] ) && is_array( $aAttributes['optgroup'][ $__sKey ] )
						? $aAttributes['optgroup'][ $__sKey ] + $aAttributes['optgroup']
						: $aAttributes['optgroup'];
						
					$_aOutput[] = 
						"<optgroup label='{$__sKey}'" . $this->generateAttributes( $_aOptGroupAttributes ) . ">"
							. $this->_getOptionTags( $sInputID, $aAttributes, $__asLabel )
						. "</optgroup>";
					continue;
					
				}
				
				// For the option tag,
				$_aValue = isset( $aAttributes['option'][ $__sKey ]['value'] )
					? $aAttributes['option'][ $__sKey ]['value']
					: $_aValue;
				
				$_aOptionAttributes = array(
					'id'	=> $sInputID . '_' . $__sKey,
					'value'	=> $__sKey,
					'selected'	=> in_array( ( string ) $__sKey, $_aValue ) ? 'Selected' : '',
				) + ( isset( $aAttributes['option'][ $__sKey ] ) && is_array( $aAttributes['option'][ $__sKey ] )
					? $aAttributes['option'][ $__sKey ] + $aAttributes['option']
					: $aAttributes['option']
				);

				$_aOutput[] =
					"<option " . $this->generateAttributes( $_aOptionAttributes ) . " >"	
						. $__asLabel
					. "</option>";
					
			}
			return implode( PHP_EOL, $_aOutput );	
			
		}
		
}
endif;