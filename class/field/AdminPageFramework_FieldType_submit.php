<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_submit extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'submit', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'redirect_url'	=>	null,
		'href'	=>	null,
		'is_reset'	=>	null,		
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
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetField( $aField ) {
		
		$aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'submit' );

		$aInputAttributes = array(
			'value'	=>	( $sValue = $this->getInputFieldValueFromLabel( $aField ) ),
		) + $aField['attributes']
		+ array(
			'title'	=>	$sValue,
		);
		$aLabelAttributes = array(
			'style'	=>	$aField['label_min_width'] ? "min-width:{$aField['label_min_width']}px;" : null,
			'for'	=>	$aInputAttributes['id'],
			'class'	=>	$aInputAttributes['disabled'] ? 'disabled' : '',			
		);
		$aLabelContainerAttributes = array(
			'style'	=>	$aField['label_min_width'] ? "min-width:{$aField['label_min_width']}px;" : null,
			'class'	=>	'admin-page-framework-input-label-container admin-page-framework-input-button-container admin-page-framework-input-container',
		);
		
		return 
			$aField['before_label']
			. "<div " . $this->generateAttributes( $aLabelContainerAttributes ) . ">"
				. "<label " . $this->generateAttributes( $aLabelAttributes ) . ">"
					. $aField['before_input']
					. $this->_getEmbeddedHiddenInputFields( $aField )
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input']
				. "</label>"
			. "</div>"
			. $aField['after_label'];
		
	}
	
	/**
	 * Returns the output of hidden fields for the submit type that enables custom submit buttons.
	 * @since			3.0.0
	 */
	protected function _getEmbeddedHiddenInputFields( &$aField ) {

		return	
			"<input type='hidden' "
				. "name='__submit[{$aField['input_id']}][input_id]' "
					. "value='{$aField['input_id']}'"
			. "/>"
			. "<input type='hidden' "
				. "name='__submit[{$aField['input_id']}][field_id]' "
				. "value='{$aField['field_id']}'"
			. "/>"		
			. "<input type='hidden' "
				. "name='__submit[{$aField['input_id']}][name]' "
				. "value='{$aField['_field_name_flat']}'"
			. "/>" 						
			/* for the redirect_url key */
			. ( $aField['redirect_url']
				? "<input type='hidden' "
					. "name='__redirect[{$aField['input_id']}][url]' "
					. "value='{$aField['redirect_url']}'"
				. "/>" 
				. "<input type='hidden' "
					. "name='__redirect[{$aField['input_id']}][name]' "
					. "value='{$aField['_field_name_flat']}'"
				. "/>" 
				: "" 
			)
			/* for the href key */
			. ( $aField['href']
				? "<input type='hidden' "
					. "name='__link[{$aField['input_id']}][url]' "
					. "value='{$aField['href']}'"
				. "/>"
				. "<input type='hidden' "
					. "name='__link[{$aField['input_id']}][name]' "
					. "value='{$aField['_field_name_flat']}'"
				. "/>" 
				: "" 
			)
			/* for the is_reset key */
			. ( $aField['is_reset'] && ( ! ( $bResetConfirmed = $this->_checkConfirmationDisplayed( $aField['is_reset'], $aField['_field_name_flat'] ) ) )
				? "<input type='hidden' "
					. "name='__reset_confirm[{$aField['input_id']}][key]' "
					. "value='{$aField['_field_name_flat']}'"
				. "/>"
				. "<input type='hidden' "
					. "name='__reset_confirm[{$aField['input_id']}][name]' "
					. "value='{$aField['_field_name_flat']}'"
				. "/>" 
				: ""
			)
			. ( $aField['is_reset'] && $bResetConfirmed
				? "<input type='hidden' "
					. "name='__reset[{$aField['input_id']}][key]' "
					. "value='{$aField['is_reset']}'"
				. "/>"
				. "<input type='hidden' "
					. "name='__reset[{$aField['input_id']}][name]' "
					. "value='{$aField['_field_name_flat']}'"
				. "/>" 
				: ""
			);
		
	}
		
	
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the is_reset key is set.
		 * 
		 */
		private function _checkConfirmationDisplayed( $sResetKey, $sFlatFieldName ) {
				
			if ( ! $sResetKey ) return false;
			
			$bResetConfirmed =  get_transient( md5( "reset_confirm_" . $sFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $bResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $sFlatFieldName ) );
				
			return $bResetConfirmed;
			
		}

	/*
	 *	Shared Methods 
	 */

	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>label</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @remark			The submit, import, and export field types use this method.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
	 */ 
	protected function getInputFieldValueFromLabel( $aField ) {	
		
		if ( isset( $aField['value'] ) ) return $aField['value'];	// If the value key is explicitly set, use it.
		
		if ( isset( $aField['label'] ) ) return $aField['label'];	
		
		if ( isset( $aField['default'] ) ) return $aField['default'];	// If the default value is set,
		
	}
	
}
endif;