<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_import extends AdminPageFramework_FieldType_submit {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'import', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'option_key'	=>	null,
		'format'		=>	'json',
		'is_merge'		=>	false,
		'attributes'	=> array(
			'file'	=>	array(
				'accept'	=>	'audio/*|video/*|image/*|MIME_type',
				'class'		=>	'import',
				'type'		=>	'file',
			),
			'submit'	=>	array(
			
				'class'	=>	'import button button-primary',
				'type'	=>	'submit',
			),
		),	
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
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetField( $aField ) {
		
		/* Set some required values */
		$aField['attributes']['name'] = "__import[submit][{$aField['field_id']}]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]" : '' );		
		$aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'import' );
		
		return parent::replyToGetField( $aField );		
	}	
	
	/**
	 * Returns the output of hidden fields for this field type that enables custom submit buttons.
	 * @since			3.0.0
	 */
	protected function _getExtraInputFields( &$aField ) {

		$aHiddenAttributes = array( 'type'	=>	'hidden', );		
		return	
			"<input " . $this->generateAttributes( 
				array(
					'name'	=>	"__import[{$aField['field_id']}][input_id]" . ( $aField['_is_multiple_fields']  ? "[{$aField['_index']}]" : '' ),
					'value'	=>	$aField['input_id'],
				) + $aHiddenAttributes
			) . "/>"
			. "<input " . $this->generateAttributes( 
				array(
					'name'	=>	"__import[{$aField['field_id']}][field_id]" . ( $aField['_is_multiple_fields']  ? "[{$aField['_index']}]" : '' ),
					'value'	=>	$aField['field_id'],
				) + $aHiddenAttributes
			) . "/>"
			. "<input " . $this->generateAttributes( 
				array(
					'name'	=>	"__import[{$aField['field_id']}][is_merge]" . ( $aField['_is_multiple_fields']  ? "[{$aField['_index']}]" : '' ),
					'value'	=>	$aField['is_merge'],
				) + $aHiddenAttributes
			) . "/>"	
			. "<input " . $this->generateAttributes( 
				array(
					'name'	=>	"__import[{$aField['field_id']}][option_key]" . ( $aField['_is_multiple_fields']  ? "[{$aField['_index']}]" : '' ),
					'value'	=>	$aField['option_key'],
				) + $aHiddenAttributes
			) . "/>"
			. "<input " . $this->generateAttributes( 
				array(
					'name'	=>	"__import[{$aField['field_id']}][format]" . ( $aField['_is_multiple_fields']  ? "[{$aField['_index']}]" : '' ),
					'value'	=>	$aField['format'],
				) + $aHiddenAttributes
			) . "/>"	
			. "<input " . $this->generateAttributes( 
				array(
					'id'	=>	"{$aField['input_id']}_file",
					'type'	=>	'file',
					'name'	=>	"__import[{$aField['field_id']}]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]" : '' ),
				) + $aField['attributes']['file']			
			) . " />";

	}
		
}
endif;