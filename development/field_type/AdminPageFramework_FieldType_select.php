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
	public $aFieldTypeSlugs = array( 'select' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		'label'	=>	array(),
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
			
		$aSelectAttributes = array(
			'id'	=>	$aField['input_id'],
			'multiple'	=>	$aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
		) + $aField['attributes']['select'];
		$aSelectAttributes['name'] = empty( $aSelectAttributes['multiple'] ) ? $aField['_input_name'] : "{$aField['_input_name']}[]";

		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $aSelectAttributes ) . " >"
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
			
			$aOutput = array();
			$aValue = ( array ) $aAttributes['value'];

			foreach( $aLabel as $sKey => $asLabel ) {
				
				// For the optgroup tag,
				if ( is_array( $asLabel ) ) {	// optgroup
				
					$aOptGroupAttributes = isset( $aAttributes['optgroup'][ $sKey ] ) && is_array( $aAttributes['optgroup'][ $sKey ] )
						? $aAttributes['optgroup'][ $sKey ] + $aAttributes['optgroup']
						: $aAttributes['optgroup'];
						
					$aOutput[] = 
						"<optgroup label='{$sKey}'" . $this->generateAttributes( $aOptGroupAttributes ) . ">"
						. $this->_getOptionTags( $sInputID, $aAttributes, $asLabel )
						. "</optgroup>";
					continue;
					
				}
				
				// For the option tag,
				$aValue = isset( $aAttributes['option'][ $sKey ]['value'] )
					? $aAttributes['option'][ $sKey ]['value']
					: $aValue;
				
				$aOptionAttributes = array(
					'id'	=> $sInputID . '_' . $sKey,
					'value'	=> $sKey,
					'selected'	=> in_array( ( string ) $sKey, $aValue ) ? 'Selected' : '',
				) + ( isset( $aAttributes['option'][ $sKey ] ) && is_array( $aAttributes['option'][ $sKey ] )
					? $aAttributes['option'][ $sKey ] + $aAttributes['option']
					: $aAttributes['option']
				);

				$aOutput[] =
					"<option " . $this->generateAttributes( $aOptionAttributes ) . " >"	
						. $asLabel
					. "</option>";
					
			}
			return implode( PHP_EOL, $aOutput );	
			
		}
		
}
endif;