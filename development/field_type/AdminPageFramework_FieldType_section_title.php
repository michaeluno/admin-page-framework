<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_section_title' ) ) :
/**
 * Defines the section_tab field type.
 * 
 * When a field is defined with this field type, the section title will be replaced with this field. This is used for repeatable tabbed sections.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FieldType_section_title extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'section_title', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'hidden'	=>	 true,
		'attributes'	=> array(
			'size'	=>	10,
			'maxlength'	=>	100,
		),	
	);

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return "";
		return "/* Section Tab Field Type */
				.admin-page-framework-field-section_tab .admin-page-framework-field .admin-page-framework-input-label-container {
					vertical-align: top; 
				}
			" . PHP_EOL;		
	}	
	
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function _replyToGetField( $aField ) {

		return 
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aField['attributes'] ) . " />"	// this method is defined in the base class
					. $aField['after_input']
					. "<div class='repeatable-field-buttons'></div>"	// the repeatable field buttons will be replaced with this element.
				. "</label>"
			. "</div>"
			. $aField['after_label'];
		
	}
			
}
endif;