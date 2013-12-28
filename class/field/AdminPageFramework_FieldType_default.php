<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_default' ) ) :
/**
 * Defines the default field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_default extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the default key-values of this field type. 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		// 'attributes'	=> array(
			// 'size'	=>	30,
			// 'maxlength'	=>	400,
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
	 * This one is triggered when the called field type is unknown. This does not insert the input tag but just renders the value stored in the $vValue variable.
	 * 
	 * @since			2.1.5				
	 * @since			3.0.0			Removed unnecessary elements as well as parameters.
	 */
	public function replyToGetField( $aField ) {
		return 
			"<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input_tag']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. $aField['value']
					. $aField['after_input_tag']
				. "</label>"
			. "</div>"
		;		
	}

}
endif;