<?php
if ( ! class_exists( 'LinkCustomFieldType' ) ) :
class LinkCustomFieldType extends AdminPageFramework_FieldType {

	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'link', );

	/**
	 * Defines the default key-values of this field type settings. 
	 * 
	 * @remark          $_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(

		'type'          => 'text',
		'attributes'    =>  array(
			'size'      =>  40,
			'maxlength' =>  400,
		),

	);

	/**
	 * Loads the field type necessary components.
	 */ 
	public function setUp() {
		
		wp_enqueue_script( 'wplink' );
		if ( ! class_exists( '_WP_Editors' ) ) {			
			require( ABSPATH . WPINC . '/class-wp-editor.php' );		
		}		
		add_action( 'admin_print_footer_scripts', array( '_WP_Editors', 'editor_js' ), 50 );
		add_action( 'admin_print_footer_scripts', array( '_WP_Editors', 'enqueue_scripts' ), 1 );		
		add_action( 'admin_print_footer_scripts', array( '_WP_Editors', 'wp_link_dialog' ) );				
		
	}  
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			//array( 'src' => get_template_directory_uri().'/js/admin/link.js', 'dependencies' => array( 'jquery' ) ),
		);
	}

	protected function getEnqueuingStyles() {
		return array(
			includes_url( 'css/editor.min.css' ),
		);
	}
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 
		
		add_action( 'admin_footer', array( $this, '_replyToAddLinkModalQueryPlugin' ) );
		$_aJSArray = json_encode( $this->aFieldTypeSlugs );
		return "jQuery( document ).ready( function(){	
            jQuery( '#wp-link-submit' ).on( 'click', function( event ) {
                
                if ( ! sInputID_LinkModal ) return;
                var linkAtts = wpLink.getAttrs();
                jQuery( '#' + sInputID_LinkModal ).val( linkAtts.href );
                jQuery( '#' + sInputID_LinkModal + '_title' ).val( linkAtts.title );
                jQuery( '#' + sInputID_LinkModal + '_target' ).val( linkAtts.target );
                wpLink.textarea = jQuery( 'body' );
                wpLink.close();
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
                event.stopPropagation();
                sInputID_LinkModal = '';
                return false;
                
            });					
            jQuery( '#wp-link-close' ).on( 'click', function( event ) {
                wpLink.textarea = jQuery( 'body' );
                wpLink.close();
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
                event.stopPropagation();
                return false;
            });

            jQuery().registerAPFCallback( {				
            
                /**
                 * The repeatable field callback.
                 * 
                 * When a repeat event occurs and a field is copied, this method will be triggered.
                 * 
                 * @param	object	oCopied		the copied node object.
                 * @param	string	sFieldType	the field type slug
                 * @param	string	sFieldTagID	the field container tag ID
                 * @param	integer	iCallType	the caller type. 1 : repeatable sections. 0 : repeatable fields.
                 */
                added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {		
                
                    /* If it is not this field type, do nothing. */
                    if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) {
                        return;
                    }

                    /* If the input tag is not found, do nothing  */
                    var oLinkModalInput = oCopied.find( 'input.link_modal_dialog' );
                    if ( oLinkModalInput.length <= 0 ) {
                        return;
                    }

                    // Find the 'Select Link' button and update its id (it is copied so the id is still the same as the original one of the clone.)
                    var oLinkModalSelectButton = oCopied.find( '.select_link' );
                    
                    // Now attach the event.
                    oLinkModalSelectButton.link_modal_dialog();
                    
                }
                
            });		
            
        });";

	}	
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	protected function getStyles() { 
		return "/* Link Custom Field Type */
			a.select_link.button {
				vertical-align: middle;
			}
			input.link_modal_dialog {
				margin-right: 0.5em;
				vertical-align: middle;
			}
			@media screen and (max-width: 782px) {
				input.link_modal_dialog {
					margin: 0.5em 0.5em 0.5em 0;
				}
			}			
		" . PHP_EOL;
	 }	
	
	/**
	 * Returns the output of the field type.
	 */
	public function getField( $aField ) {

		$aInputAttributes = array(
			'type'  =>  'text',
			'name'  =>  $aField['attributes']['name'].'[url]',
			'value' =>  isset( $aField['attributes']['value']['url'] ) ? $aField['attributes']['value']['url'] : '',
		) + $aField['attributes'];
		$aInputAttributes['class']	.= ' link_modal_dialog';
		
		return 
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<div class='repeatable-field-buttons'></div>"    // the repeatable field buttons will be replaced with this element.
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
					. "<a href='#' id='select_{$aField['input_id']}' class='select_link button button-small' >" . __( 'Select Link', 'admin-page-framework' ) . "</a>"
					. $aField['after_input']
					. $this->_getExtraInputs( $aField )				
				. "</label>"
			. "</div>"
			. $aField['after_label']            
			. $this->_getUploadButtonScript( $aField['input_id'] );

	}


	protected function _getExtraInputs( $aField ) {

		return '<input ' . $this->generateAttributes(
				array(
					'id'    =>  "{$aField['input_id']}_title",
					'type'  =>  'hidden',
					'name'  =>  "{$aField['_input_name']}[title]",
					'value' =>  isset( $aField['attributes']['value']['title'] ) ? $aField['attributes']['value']['title'] : '',
				)
			) . '/>' . PHP_EOL
			. '<input ' . $this->generateAttributes( 
				array(
					'id'    =>  "{$aField['input_id']}_target",
					'type'  =>  'hidden',
					'name'  =>  "{$aField['_input_name']}[target]",
					'value' =>  isset( $aField['attributes']['value']['target'] ) ? $aField['attributes']['value']['target'] : '',
				)
			) . '/>' . PHP_EOL;
			
	}


	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	 protected function _getUploadButtonScript( $sInputID ) { 
	 
		return "<script type='text/javascript' class='admin-page-framework-link-modal-enabler-script'>" 
				. "jQuery( document ).ready( function(){
					jQuery( '#select_{$sInputID}' ).link_modal_dialog();
				});"
			. "</script>". PHP_EOL;

	}

	/**
	 * Prints out the jQuery plugin that adds the link modal dialog.
	 * 
	 */
	public function _replyToAddLinkModalQueryPlugin() {
		
		$_sScript = "
		(function ( $ ) {
		
			$.fn.link_modal_dialog = function() {
								
				this.on( 'click', function( event ) {
					
					// Find the input id and set the global variable.
					var oInput          = $( this ).siblings( '.link_modal_dialog' );
					sInputID_LinkModal  = oInput.attr( 'id' );

                    // for WP v3.8x or below             
					wpActiveEditor      = oInput.attr( 'id' );
                    tinyMCEPopup        = 'undefined' !== typeof tinyMCEPopup ? tinyMCEPopup : null;    
					
                    // Open the modal dialog. Since v3.9, we can directly pass the element id to the parameter.
					wpLink.open( oInput.attr( 'id' ) );

					return false;
					
				});     				
								
			};

		}( jQuery ));";
		
		echo "<script type='text/javascript' class='admin-page-framework-linkmodal-jQuery-plugin'>{$_sScript}</script>";
		
	}			
	
}
endif;