<?php
if ( ! class_exists( 'AdminPageFramework_InputFieldType_textarea' ) ) :
/**
 * Defines the textarea field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_textarea extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'rows'					=> 4,
			'cols'					=> 80,
			'vRich'					=> false,
			'vMaxLength'			=> 400,
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
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
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
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. ( ! empty( $aRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
								? wp_editor( 
									$this->getCorrespondingArrayValue( $vValue, $sKey, null ), 
									"{$sTagID}_{$sKey}",  
									$this->uniteArrays( 
										( array ) $aRichEditorSettings,
										array(
											'wpautop' => true, // use wpautop?
											'media_buttons' => true, // show insert/upload button(s)
											'textarea_name' => is_array( $aFields ) ? "{$sFieldName}[{$sKey}]" : $sFieldName , // set the textarea name to something different, square brackets [] can be used here
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
								) . $this->getScriptForRichEditor( "{$sTagID}_{$sKey}" )
								: "<textarea id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
									. "rows='" . $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ) . "' "
									. "cols='" . $this->getCorrespondingArrayValue( $aField['cols'], $sKey, $_aDefaultKeys['cols'] ) . "' "
									. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
									. "type='{$aField['type']}' "
									. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. ">"
									. $this->getCorrespondingArrayValue( $vValue, $sKey, null )
								. "</textarea>"
							)
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$sTagID}'>" 
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