<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_text' ) ) :
/**
 * Defines the text field type.
 * 
 * Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defeined.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_text extends AdminPageFramework_FieldType_Base {

	protected $sFieldTypeSlug = 'text';

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', ) as $sTextTypeSlug )
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();

		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'attributes'			=> array(
				'size'	=> 30,
				'maxlength' => 400,
				'class' => '',	
			),
		) + self::$_aDefaultKeys;	// $_aDefaultKeys is defined in the base class.
	}
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function replyToGetInputField( $aField ) {

		$aAttributes = $aField['attributes'] + array(
			'id' => $aField['input_id'],
			'name' => $aField['field_name'],
			'value' => $aField['value'],
			'type' => $aField['type'],	// text, password, etc.
		);	
		return 
			"<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input_tag']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->getHTMLTagAttributesFromArray( $aAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input_tag']
				. "</label>"
			. "</div>"
		;
		
	}
	
	public function replyToGetInputScripts() {
		return "
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sID ) {
						console.log( 'id : '  + sID );
						console.log( 'type : '  + sFieldType );
						console.log( 'type fron node: '  + node.data( 'type' ) );
					}
				});
			});
		";		
	}		
		
}
endif;