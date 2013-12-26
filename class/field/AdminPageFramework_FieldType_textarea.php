<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_textarea' ) ) :
/**
 * Defines the textarea field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_textarea extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'rows'					=> 4,
			'cols'					=> 80,
			'vRich'					=> false,
			'max_length'			=> 400,
		);	
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"	/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}		
		" . PHP_EOL;		
	}	
		
	/**
	 * Returns the output of the textarea input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];			
		$bSingle = ! is_array( $aFields );
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) {
			
			$aRichEditorSettings = $bSingle
				? $aField['vRich']
				: $this->getCorrespondingArrayValue( $aField['vRich'], $sKey, null );
				
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$tag_id}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. ( ! empty( $aRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
								? wp_editor( 
									$this->getCorrespondingArrayValue( $vValue, $sKey, null ), 
									"{$tag_id}_{$sKey}",  
									$this->uniteArrays( 
										( array ) $aRichEditorSettings,
										array(
											'wpautop' => true, // use wpautop?
											'media_buttons' => true, // show insert/upload button(s)
											'textarea_name' => is_array( $aFields ) ? "{$field_name}[{$sKey}]" : $field_name , // set the textarea name to something different, square brackets [] can be used here
											'textarea_rows' => $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ),
											'tabindex' => '',
											'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
											'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
											'editor_class' => $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ), // add extra class(es) to the editor textarea
											'teeny' => false, // output the minimal editor config used in Press This
											'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
											'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
											'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
										)
									)
								) . $this->getScriptForRichEditor( "{$tag_id}_{$sKey}" )
								: "<textarea id='{$tag_id}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
									. "rows='" . $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ) . "' "
									. "cols='" . $this->getCorrespondingArrayValue( $aField['cols'], $sKey, $_aDefaultKeys['cols'] ) . "' "
									. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
									. "type='{$aField['type']}' "
									. "name=" . ( is_array( $aFields ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
									. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
								. ">"
									. $this->getCorrespondingArrayValue( $vValue, $sKey, null )
								. "</textarea>"
							)
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		

	}	
		/**
		 * A helper function for the above getTextAreaField() method.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */	
		private function getScriptForRichEditor( $sIDSelector ) {

			// id: wp-sample_rich_textarea_0-wrap
			return "<script type='text/javascript'>
				jQuery( '#wp-{$sIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$sIDSelector}-wrap' ).appendTo( '#field-{$sIDSelector}' );
					jQuery( '#wp-{$sIDSelector}-wrap' ).show();
				})
			</script>";		
			
		}	
	
}
endif;