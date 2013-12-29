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
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetField( $aField ) {

		$aOutput = array();
		$asValue = $aField['attributes']['value'];
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) {
			$aAttributes = array(
				'id' => $aField['input_id'] . '_' . $sKey,
				'checked'	=> $this->getCorrespondingArrayValue( $asValue, $sKey, null ) == 1 ? 'checked' : '',
				'value' => 1,	// must be always 1 for the checkbox type
				'name'	=> is_array( $aField['label'] ) ? "{$aField['attributes']['name']}[{$sKey}]" : $aField['attributes']['name'],
			) + $aField['attributes'];
			$aOutput[] =
				$aField['before_input_tag']
				. "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: {$aField['label_min_width']}px;'>"
					. "<label for='{$aAttributes['id']}'>"
						. "<span class='admin-page-framework-input-container'>"
							. "<input type='hidden' name='{$aAttributes['name']}' value='0' />"	// the unchecked value must be set prior to the checkbox input field.
							. "<input " . $this->getHTMLTagAttributesFromArray( $aAttributes ) . " />"	// this method is defined in the base class	
						. "</span>"
						. "<span class='admin-page-framework-input-label-string'>"
							. $sLabel
						. "</span>"
					. "</label>"					
				. "</div>"
				. $aField['after_input_tag'];
		}	
		return implode( PHP_EOL, $aOutput );
	}	
	
}
endif;