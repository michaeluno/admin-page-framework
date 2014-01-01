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
	 */
	public $aFieldTypeSlugs = array( 'number', 'range' );

	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
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
	);
	
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function replyToGetField( $aField ) {

		return 
			$aField['before_field']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aField['attributes'] ) . " />"	// this method is defined in the base class
					. $aField['after_input']
				. "</label>"
			. "</div>"
			. $aField['after_field'];
		
		
	}
		
}
endif;