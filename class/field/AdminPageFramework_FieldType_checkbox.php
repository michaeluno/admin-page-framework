<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_checkbox' ) ) :
/**
 * Defines the checkbox field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'checkbox' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		// 'label'			=> array(),
		// 'attributes'	=> array(
		// ),
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
		return "/* Checkbox field type */
			.admin-page-framework-field input[type='checkbox'] {
				margin-right: 0.5em;
			}			
			.admin-page-framework-field-checkbox .admin-page-framework-input-label-container {
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

		$aOutput = array();
		$asValue = $aField['attributes']['value'];
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) {
			
			$aInputAttributes = array(
				'id' => $aField['input_id'] . '_' . $sKey,
				'checked'	=> $this->getCorrespondingArrayValue( $asValue, $sKey, null ) == 1 ? 'checked' : '',
				'value' => 1,	// must be always 1 for the checkbox type; the actual saved value will be reflected with the above 'checked' attribute.
				'name'	=> is_array( $aField['label'] ) ? "{$aField['attributes']['name']}[{$sKey}]" : $aField['attributes']['name'],
			) 
			+ $this->getFieldElementByKey( $aField['attributes'], $sKey, $aField['attributes'] )
			+ $aField['attributes'];
		
			$aLabelAttributes = array(
				'for'	=>	$aInputAttributes['id'],
				'class'	=>	$aInputAttributes['disabled'] ? 'disabled' : '',
			);
			
			$aOutput[] =
				$this->getFieldElementByKey( $aField['before_field'], $sKey )
				. "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width: {$aField['label_min_width']}px;'>"
					. "<label " . $this->generateAttributes( $aLabelAttributes ) . ">"
						. $this->getFieldElementByKey( $aField['before_input'], $sKey )
						. "<span class='admin-page-framework-input-container'>"
							. "<input type='hidden' name='{$aInputAttributes['name']}' value='0' />"	// the unchecked value must be set prior to the checkbox input field.
							. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class	
						. "</span>"
						. "<span class='admin-page-framework-input-label-string'>"
							. $sLabel
						. "</span>"
						. $this->getFieldElementByKey( $aField['after_input'], $sKey )
					. "</label>"					
				. "</div>"
				. $this->getFieldElementByKey( $aField['after_field'], $sKey );
				
		}	
		return implode( PHP_EOL, $aOutput );
		
	}	
	
}
endif;