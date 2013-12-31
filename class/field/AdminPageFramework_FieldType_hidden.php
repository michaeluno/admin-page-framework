<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_hidden' ) ) :
/**
 * Defines the hidden field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_hidden extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'hidden' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array();
	
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
	 * @remark			The user needs to assign the value to either the default key or the vValue key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 * @since			3.0.0				Removed unnecessary elements including the parameters.
	 */
	public function replyToGetField( $aField ) {

		return 
			$aField['before_field']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input_tag']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->getHTMLTagAttributesFromArray( $aField['attributes'] ) . " />"	// this method is defined in the base class
					. $aField['after_input_tag']
				. "</label>"
			. "</div>"
			. $aField['after_field'];
		
	}
	
}
endif;