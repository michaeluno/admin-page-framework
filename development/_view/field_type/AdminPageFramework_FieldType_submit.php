<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @internal
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
		'reset'	=>	null,		
		'attributes'	=> array(
			'class'	=>	'button button-primary',
		),	
	);	

	/**
	 * Loads the field type necessary components.
	 */ 
	public function _replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function _replyToGetScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AdminPageFramework_FormField.
	 */
	public function _replyToGetField( $aField ) {
		
		$aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->__( 'submit' );

		$aInputAttributes = array(
			'type'	=>	'submit',	// must be set because child class including export will use this method; in that case the export type will be assigned which input tag does not support
			'value'	=>	( $sValue = $this->_getInputFieldValueFromLabel( $aField ) ),
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
				. $this->_getExtraFieldsBeforeLabel( $aField )	// this is for the import field type that cannot place file input tag inside the label tag.
				. "<label " . $this->generateAttributes( $aLabelAttributes ) . ">"
					. $aField['before_input']
					. $this->_getExtraInputFields( $aField )
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input']
				. "</label>"
			. "</div>"
			. $aField['after_label'];
		
	}
	
	/**
	 * Returns extra output for the field.
	 * 
	 * This is for the import field type that extends this class. The import field type cannot place the file input tag inside the label tag that causes a problem in FireFox.
	 * 
	 * @since			3.0.0
	 */
	protected function _getExtraFieldsBeforeLabel( &$aField ) {
		return '';		
	}
	
	/**
	 * Returns the output of hidden fields for this field type that enables custom submit buttons.
	 * @since			3.0.0
	 */
	protected function _getExtraInputFields( &$aField ) {

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
				. "value='{$aField['_input_name_flat']}'"
			. "/>" 						
			. "<input type='hidden' "
				. "name='__submit[{$aField['input_id']}][section_id]' "
				. "value='" . ( isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '' ) . "'"
			. "/>"			
			/* for the redirect_url key */
			. ( $aField['redirect_url']
				? "<input type='hidden' "
					. "name='__submit[{$aField['input_id']}][redirect_url]' "
					. "value='{$aField['redirect_url']}'"
				. "/>" 
				: "" 
			)
			/* for the href key */
			. ( $aField['href']
				? "<input type='hidden' "
					. "name='__submit[{$aField['input_id']}][link_url]' "
					. "value='{$aField['href']}'"
				. "/>"
				: "" 
			)
			/* for the 'reset' key */
			. ( $aField['reset'] && ( ! ( $bResetConfirmed = $this->_checkConfirmationDisplayed( $aField['reset'], $aField['_input_name_flat'] ) ) )
				? "<input type='hidden' "
					. "name='__submit[{$aField['input_id']}][is_reset]' "
					. "value='1'"
				. "/>"
				: ""
			)
			. ( $aField['reset'] && $bResetConfirmed
				? "<input type='hidden' "
					. "name='__submit[{$aField['input_id']}][reset_key]' "	
					. "value='{$aField['reset']}'"	// set the option array key to delete.
				. "/>"
				: ""
			);
		
	}
		
	
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the 'reset' key is set.
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
	protected function _getInputFieldValueFromLabel( $aField ) {	
		
		if ( isset( $aField['value'] ) && $aField['value'] != '' ) return $aField['value'];	// If the value key is explicitly set, use it. But the empty string will be ignored.
		
		if ( isset( $aField['label'] ) ) return $aField['label'];	
		
		if ( isset( $aField['default'] ) ) return $aField['default'];	// If the default value is set,
		
	}
	
}
endif;