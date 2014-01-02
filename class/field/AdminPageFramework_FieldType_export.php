<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_export' ) ) :
/**
 * Defines the export field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_export extends AdminPageFramework_FieldType_submit {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'export', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'data'	=>	null,	// ( array or string or object ) This is for the export field type. Do not set a value here.		
		'format'	=>	'array',	// ( string )	for the export field type. Do not set a default value here. Currently array, json, and text are supported.
		'file_name'	=>	null,	// ( string )	for the export field type. Do not set a default value here.	
		'attributes'	=> array(
			'class'	=>	'button button-primary',
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
			
		/* Set the transient data to export - If the value is not an array and the export data is set. */
		if ( isset( $aField['data'] ) ) {
			$sTransient = $aField['_is_multiple_fields']
				? md5( "{$aField['class_name']}_{$aField['field_id']}_{$aField['_index']}" )
				: md5( "{$aField['class_name']}_{$aField['field_id']}" );
			set_transient( $sTransient, $aField['data'], 60*2 );	// 2 minutes.
		}
		
		/* Set some required values */
		$aField['attributes']['name'] = "__export[submit][{$aField['field_id']}]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]" : '' );
		$aField['file_name'] = $aField['file_name'] ? $aField['file_name'] : $this->_generateExportFileName( $aField['option_key'] ? $aField['option_key'] : $aField['class_name'], $aField['format'] );
		$aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'export' );
		
		return parent::replyToGetFIeld( $aField );
		
	}
	
	/**
	 * Returns the output of hidden fields for this field type that enables custom submit buttons.
	 * @since			3.0.0
	 */
	protected function _getEmbeddedHiddenInputFields( &$aField ) {

		return
			"<input type='hidden' "	// embed the field id and input id
				. "name='__export[{$aField['field_id']}][input_id]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]' " : "' " )
				. "value='{$aField['input_id']}' "
			. "/>"
			. "<input type='hidden' "
				. "name='__export[{$aField['field_id']}][field_id]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]' " : "' " )
				. "value='{$aField['field_id']}' "
			. "/>"					
			. "<input type='hidden' "
				. "name='__export[{$aField['field_id']}][file_name]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]' " : "' " )
				. "value='{$aField['file_name']}' " 
			. "/>"
			. "<input type='hidden' "
				. "name='__export[{$aField['field_id']}][format]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]' " : "' " )
				. "value='{$aField['format']}' "
			. "/>"				
			. "<input type='hidden' "
				. "name='__export[{$aField['field_id']}][transient]" . ( $aField['_is_multiple_fields'] ? "[{$aField['_index']}]' " : "' " )
				. "value='" . ( isset( $aField['data'] ) ) . "'"
			. "/>";
					
	}
			
		/**
		 * Generates a file name for the exporting data.
		 * 
		 * A helper function for the above method.
		 * 
		 * @remark			Currently only array, text or json is supported.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_InputField class.
		 */ 
		private function _generateExportFileName( $sOptionKey, $sExportFormat='json' ) {
				
			switch ( trim( strtolower( $sExportFormat ) ) ) {
				case 'text':	// for plain text.
					$sExt = "txt";
					break;
				case 'json':	// for json.
					$sExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$sExt = "txt";
					break;
			}		
				
			return $sOptionKey . '_' . date("Ymd") . '.' . $sExt;
			
		}

}
endif;