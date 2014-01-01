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
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'textarea' );

	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'rich'				=> false,
		'attributes'			=> array(		
			'autofocus' => '',
			'cols'	=> 60,
			'disabled' => '',
			'formNew' => '',
			'maxlength' => '',
			'placeholder' => '',
			'readonly' => '',
			'required' => '',
			'rows' => 4,
			'wrap' => '',			
		),
	);
		
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
		return "/* Textarea Field Type */
			.admin-page-framework-field-textarea .admin-page-framework-input-label-string {
				vertical-align: top;
				margin-top: 2px;
			}		
			/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}
			.admin-page-framework-field-textarea.admin-page-framework-field .admin-page-framework-input-label-container {
				vertical-align: top; 
			} 
			
		" . PHP_EOL;		
	}	
		
	/**
	 * Returns the output of the textarea input field.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed redundant elements including parameters.
	 */
	public function replyToGetField( $aField ) {

		return 
			"<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. ( ! empty( $aField['rich'] ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
							? wp_editor( 
								$aField['value'],
								$aField['attributes']['id'],  
								$this->uniteArrays( 
									( array ) $aField['rich'],
									array(
										'wpautop' => true, // use wpautop?
										'media_buttons' => true, // show insert/upload button(s)
										'textarea_name' => $aField['attributes']['name'],
										'textarea_rows' => $aField['attributes']['rows'],
										'tabindex' => '',
										'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
										'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
										'editor_class' => $aField['attributes']['class'], // add extra class(es) to the editor textarea
										'teeny' => false, // output the minimal editor config used in Press This
										'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
										'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
										'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
									)
								)
							) . $this->_getScriptForRichEditor( $aField['attributes']['id'] )
							: "<textarea " . $this->generateAttributes( $aField['attributes'] ) . " >"	// this method is defined in the base class
									. $aField['value']
								. "</textarea>"
					)
					. $aField['after_input']
				. "</label>"
			. "</div>"
		;
		
	}
	
		/**
		 * Provides the JavaScript script that hides the rich editor until the document gets loaded and places into the right position.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */	
		private function _getScriptForRichEditor( $sIDSelector ) {

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