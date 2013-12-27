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
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	protected $aFieldTypeSlugs = array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'attributes'	=> array(
			'size'	=>	30,
			'maxlength'	=>	400,
			'class'	=>	'',	
		),	
	);

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"	/* Text Field Type */
			.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container {
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
	
	// public function replyToGetInputScripts() {
		// $aJSArray = json_encode( $this->aFieldTypeSlugs );
		// return "
			// jQuery( document ).ready( function(){
				// jQuery().registerAPFCallback( {				
					// added_repeatable_field: function( node, sFieldType, sID ) {
						// if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) {
							// return;
						// }
						// console.log( 'This is a text field type.' );
						// console.log( {$aJSArray} );
						// console.log( 'id : '  + sID );
						// console.log( 'type : '  + sFieldType );
						// console.log( 'type fron node: '  + node.data( 'type' ) );
					// }
				// });
			// });
		// ";		
	// }		
		
}
endif;