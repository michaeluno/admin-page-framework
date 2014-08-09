<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_image' ) ) :
/**
 * Defines the image field type.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'image', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'attributes_to_store'	=>	array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
		'show_preview'	=>	true,
		'allow_external_source'	=>	true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		'attributes'	=>	array(
			'input'		=> array(
				'size'	=>	40,
				'maxlength'	=>	400,			
			),
			'button'	=>	array(
			),
			'preview'	=>	array(
			),			
		),	
	);

	/**
	 * Loads the field type necessary components.
	 */ 
	public function _replyToFieldLoader() {		
		$this->enqueueMediaUploader();
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function _replyToGetScripts() {		
		return $this->_getScript_CustomMediaUploaderObject() . PHP_EOL	
			. $this->_getScript_ImageSelector( 
				"admin_page_framework", 
				$this->oMsg->__( 'upload_image' ),
				$this->oMsg->__( 'use_this_image' )
			)  . PHP_EOL
			. $this->_getScript_RegisterCallbacks();
	}
		/**
		 * Returns the JavaScript script that handles repeatable events. 
		 * 
		 * @since			3.0.0
		 */
		protected function _getScript_RegisterCallbacks() {

			$aJSArray = json_encode( $this->aFieldTypeSlugs );
			/*	The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
				renew the upload button and the preview elements (while it does on the input tag value), the renewal task must be dealt here separately. */
			return"
			jQuery( document ).ready( function(){
		
				jQuery().registerAPFCallback( {				
					/**
					 * The repeatable field callback for the add event.
					 * 
					 * @param	object	node
					 * @param	string	the field type slug
					 * @param	string	the field container tag ID
					 * @param	integer	the caller type. 1 : repeatable sections. 0 : repeatable fields.
					 */
					added_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
						
						/* If it is not the image field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( node.find( '.select_image' ).length <= 0 )  return;
						
						/* Remove the value of the cloned preview element - check the value for repeatable sections */
						var sValue = node.find( 'input' ).first().val();
						if ( iCallType !== 1 || ! sValue ) {	// if it's not for repeatable sections
							node.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
							node.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
						}
						
						/* Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						var iOccurence = iCallType === 1 ? 1 : 0;
						nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {

							var nodeButton = jQuery( this ).find( '.select_image' );							
							
							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {
									
								nodeButton.incrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview' ).incrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview img' ).incrementIDAttribute( 'id', iOccurence );
								
							}
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	

						});
					},
					/**
					 * The repeatable field callback for the remove event.
					 * 
					 * @param	object	the field container element next to the removed field container.
					 * @param	string	the field type slug
					 * @param	string	the field container tag ID
					 * @param	integer	the caller type. 1 : repeatable sections. 0 : repeatable fields.
					 */					
					removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID, iCallType ) {
						
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( oNextFieldConainer.find( '.select_image' ).length <= 0 )  return;						
						
						/* Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
						var iOccurence = iCallType === 1 ? 1 : 0;	// the occurrence value indicates which part of digit to change 
						oNextFieldConainer.nextAll().andSelf().each( function( iIndex ) {
							
							var nodeButton = jQuery( this ).find( '.select_image' );			
							
							// If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
							if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {							
								nodeButton.decrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview' ).decrementIDAttribute( 'id', iOccurence );
								jQuery( this ).find( '.image_preview img' ).decrementIDAttribute( 'id', iOccurence );
							}
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	
						
						});
						
					},
					sorted_fields : function( node, sFieldType, sFieldsTagID, iCallType ) {	// on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

						/* 1. Return if it is not the type. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the color field type, do nothing. */						
						if ( node.find( '.select_image' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Update the Select File button */
						var iCount = 0;
						var iOccurence = iCallType === 1 ? 1 : 0;	// the occurrence value indicates which part of digit to change 
						node.children( '.admin-page-framework-field' ).each( function() {
							
							var nodeButton = jQuery( this ).find( '.select_image' );
							
							/* 2-1. Set the current iteration index to the button ID, and the image preview elements */
							nodeButton.setIndexIDAttribute( 'id', iCount, iOccurence );	
							jQuery( this ).find( '.image_preview' ).setIndexIDAttribute( 'id', iCount, iOccurence );
							jQuery( this ).find( '.image_preview img' ).setIndexIDAttribute( 'id', iCount, iOccurence );
							
							/* 2-2. Rebuind the uploader script to the button */
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
	
							iCount++;
						});
					},					
				});
			});" . PHP_EOL;	
			
		}
		
		/**
		 * Returns the image selector JavaScript script to be loaded in the head tag of the created admin pages.
		 * @var				string
		 * @remark			It is accessed from the main class and meta box class.
		 * @remark			Moved to the base class since 2.1.0.
		 * @access			private	
		 * @internal
		 * @return			string			The image selector script.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_Property_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
		 */		
		private function _getScript_ImageSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if ( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						/**
						 * Bind/rebinds the thickbox script the given selector element.
						 * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
						 */
						setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {
							jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
							jQuery( '#select_image_' + sInputID ).click( function() {
								var sPressedID = jQuery( this ).attr( 'id' );			
								window.sInputID = sPressedID.substring( 13 );	// remove the select_image_ prefix and set a property to pass it to the editor callback method.
								window.original_send_to_editor = window.send_to_editor;
								window.send_to_editor = hfAPFSendToEditorImage;
								var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
								tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
								return false;	// do not click the button after the script by returning false.									
							});	
						}			
						
						var hfAPFSendToEditorImage = function( sRawHTML ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'img', sHTML ).attr( 'src' );
							var alt = jQuery( 'img', sHTML ).attr( 'alt' );
							var title = jQuery( 'img', sHTML ).attr( 'title' );
							var width = jQuery( 'img', sHTML ).attr( 'width' );
							var height = jQuery( 'img', sHTML ).attr( 'height' );
							var classes = jQuery( 'img', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
							var sCaption = sRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
								.replace( /<a.*?>(.*?)<\/a>/m, '' );
							var align = sRawHTML.replace( /^.*?\[\w+.*?\salign=([\'\"])(.*?)[\'\"]\s.+$/mg, '$2' );	//\'\" syntax fixer
							var link = jQuery( sHTML ).find( 'a:first' ).attr( 'href' );

							// Escape the strings of some of the attributes.
							var sCaption = jQuery( '<div/>' ).text( sCaption ).html();
							var sAlt = jQuery( '<div/>' ).text( alt ).html();
							var title = jQuery( '<div/>' ).text( title ).html();						
				
							// If the user wants to save relevant attributes, set them.
							var sInputID = window.sInputID;	// window.sInputID should be assigned when the thickbox is opened.
				
							jQuery( '#' + sInputID ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + sInputID + '_id' ).val( id );
							jQuery( '#' + sInputID + '_width' ).val( width );
							jQuery( '#' + sInputID + '_height' ).val( height );
							jQuery( '#' + sInputID + '_caption' ).val( sCaption );
							jQuery( '#' + sInputID + '_alt' ).val( sAlt );
							jQuery( '#' + sInputID + '_title' ).val( title );						
							jQuery( '#' + sInputID + '_align' ).val( align );						
							jQuery( '#' + sInputID + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + sInputID ).attr( 'alt', alt );
							jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
							jQuery( '#image_preview_' + sInputID ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + sInputID ).attr( 'data-id', id );
							jQuery( '#image_preview_' + sInputID ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + sInputID ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + sInputID ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;

							// close the thickbox
							tb_remove();	

						}
					});
				";

			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				/**
				 * Binds/rebinds the uploader button script to the specified element with the given ID.
				 */
				setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

					var fEscaped = false;	// indicates whether the frame is escaped/canceled.
					
					jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + sInputID ).click( function( e ) {
						
						// Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
						var sInputID = jQuery( this ).attr( 'id' ).substring( 13 );	// remove the select_image_ prefix and set a property to pass it to the editor callback method.
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( custom_uploader ) {
							custom_uploader.open();
							return;
						}					
						
						// Store the original select object in a global variable
						oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						var custom_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							library     : { type : 'image' },
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'escape', function() {
							fEscaped = true;
							return false;
						});
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();				
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) {
								var image = state.props.attributes;	
							}
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElementWithDelay( sInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElementWithDelay( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = jQuery( this ).addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElementWithDelay( sInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
											
						});
						
						// Open the uploader dialog
						custom_uploader.open();											
						return false;       
					});	
				
					var setPreviewElementWithDelay = function( sInputID, oImage, iMilliSeconds ) {
						
						iMilliSeconds = iMilliSeconds === undefined ? 100 : iMilliSeconds;
						setTimeout( function (){
							if ( ! fEscaped ) {
								setPreviewElement( sInputID, oImage );
							}
							fEscaped = false;
						}, iMilliSeconds );
						
					}
				
					var setPreviewElement = function( sInputID, image ) {

						// Escape the strings of some of the attributes.
						var sCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var sAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var title = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user wants the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + sInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						jQuery( 'input#' + sInputID + '_id' ).val( image.id );
						jQuery( 'input#' + sInputID + '_width' ).val( image.width );
						jQuery( 'input#' + sInputID + '_height' ).val( image.height );
						jQuery( 'input#' + sInputID + '_caption' ).val( sCaption );
						jQuery( 'input#' + sInputID + '_alt' ).val( sAlt );
						jQuery( 'input#' + sInputID + '_title' ).val( title );
						jQuery( 'input#' + sInputID + '_align' ).val( image.align );
						jQuery( 'input#' + sInputID + '_link' ).val( image.link );
						
						// Update up the preview
						jQuery( '#image_preview_' + sInputID ).attr( 'data-id', image.id );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-width', image.width );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-height', image.height );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-caption', sCaption );
						jQuery( '#image_preview_' + sInputID ).attr( 'alt', sAlt );
						jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
						jQuery( '#image_preview_' + sInputID ).attr( 'src', image.url );
						jQuery( '#image_preview_container_' + sInputID ).show();				
						
					}
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return 
			"/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 0.4em;
				margin-bottom: 0.8em;
				display: block; 
				
			}		
			@media only screen and ( max-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			} 
			@media only screen and ( max-width: 900px ) {
				.admin-page-framework-field .image_preview {
					max-width: 440px;
				}
			}	
			@media only screen and ( max-width: 600px ) {
				.admin-page-framework-field .image_preview {
					max-width: 300px;
				}
			}		
			@media only screen and ( max-width: 480px ) {
				.admin-page-framework-field .image_preview {
					max-width: 240px;
				}
			}
			@media only screen and ( min-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			}		 
			.admin-page-framework-field .image_preview img {		
				width: auto;
				height: auto; 
				max-width: 100%;
				display: block;
			}
			/* Image Uploader Input Field */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
				vertical-align: middle;	
			}
			/* Image Uploader Button */
			.select_image.button.button-small {		
				vertical-align: middle;
			}
			@media screen and (max-width: 782px) {
				.admin-page-framework-field-image input {
					margin: 0.5em 0.5em 0.5em 0;
				}
			}			
		" . PHP_EOL;	
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Reconstructed entirely.
	 */
	public function _replyToGetField( $aField ) {
		
		/* Local variables */
		$_aOutput = array();
		$_iCountAttributes = count( ( array ) $aField['attributes_to_store'] );	// If the saving extra attributes are not specified, the input field will be single only for the URL. 
		$_sCaptureAttribute = $_iCountAttributes ? 'url' : '';
		$_sImageURL = $_sCaptureAttribute
				? ( isset( $aField['attributes']['value'][ $_sCaptureAttribute ] ) ? $aField['attributes']['value'][ $_sCaptureAttribute ] : "" )
				: $aField['attributes']['value'];
		
		/* Set up the attribute arrays */
		$_aBaseAttributes = $aField['attributes'];
		unset( $_aBaseAttributes['input'], $_aBaseAttributes['button'], $_aBaseAttributes['preview'], $_aBaseAttributes['name'], $_aBaseAttributes['value'], $_aBaseAttributes['type'] );
		$_aInputAttributes = array(
			'name'	=>	$aField['attributes']['name'] . ( $_iCountAttributes ? "[url]" : "" ),
			'value'	=>	$_sImageURL,
			'type'	=>	'text',
		) + $aField['attributes']['input'] + $_aBaseAttributes;
		$_aButtonAtributes = $aField['attributes']['button'] + $_aBaseAttributes;
		$_aPreviewAtrributes = $aField['attributes']['preview'] + $_aBaseAttributes;

		/* Compose the field output */
		$_aOutput[] =
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField['type']}-field'>"	// image-field ( this will be media-field for the media field type )
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $_aInputAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input']
					. "<div class='repeatable-field-buttons'></div>"	// the repeatable field buttons will be replaced with this element.
					. $this->getExtraInputFields( $aField )
				. "</label>"
			. "</div>"			
			. $aField['after_label']
			. $this->_getPreviewContainer( $aField, $_sImageURL, $_aPreviewAtrributes )
			. $this->_getUploaderButtonScript( $aField['input_id'], $aField['repeatable'], $aField['allow_external_source'], $_aButtonAtributes );
		;
		
		return implode( PHP_EOL, $_aOutput );
		
	}
		/**
		 * Returns extra input fields to set capturing attributes.
		 * @since			3.0.0
		 */
		protected function getExtraInputFields( &$aField ) {
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			$_aOutputs = array();
			foreach( ( array ) $aField['attributes_to_store'] as $sAttribute )
				$_aOutputs[] = "<input " . $this->generateAttributes( 
						array(
							'id'	=>	"{$aField['input_id']}_{$sAttribute}",
							'type'	=>	'hidden',
							'name'	=>	"{$aField['_input_name']}[{$sAttribute}]",
							'disabled'	=>	isset( $aField['attributes']['diabled'] ) && $aField['attributes']['diabled'] ? 'Disabled' : '',
							'value'	=>	isset( $aField['attributes']['value'][ $sAttribute ] ) ? $aField['attributes']['value'][ $sAttribute ] : '',
						)
					) . "/>";
			return implode( PHP_EOL, $_aOutputs );
			
		}
		
		/**
		 * Returns the output of the preview box.
		 * @since			3.0.0
		 */
		protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {

			if ( ! $aField['show_preview'] ) return '';
			
			$sImageURL = $this->resolveSRC( $sImageURL, true );
			return 
				"<div " . $this->generateAttributes( 
						array(
							'id'	=>	"image_preview_container_{$aField['input_id']}",							
							'class'	=>	'image_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : '' ),
							'style'	=> ( $sImageURL ? '' : "display: none; "  ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ),
						) + $aPreviewAtrributes
					)
				. ">"
					. "<img src='{$sImageURL}' "
						. "id='image_preview_{$aField['input_id']}' "
					. "/>"
				. "</div>";

		}
			
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_FormField.
		 */
		protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) {
			
			$sButton = 
				"<a " . $this->generateAttributes( 
					array(
						'id'	=>	"select_image_{$sInputID}",
						'href'	=>	'#',
						'class'	=>	'select_image button button-small ' . ( isset( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : '' ),
						'data-uploader_type'	=>	function_exists( 'wp_enqueue_media' ) ? 1 : 0,
						'data-enable_external_source' => $bExternalSource ? 1 : 0,
					) + $aButtonAttributes
				) . ">"
					. $this->oMsg->__( 'select_image' )
				."</a>";
				
			$sScript = "
				if ( jQuery( 'a#select_image_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}
				jQuery( document ).ready( function(){			
					setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
				});" . PHP_EOL;	
					
			return "<script type='text/javascript' class='admin-page-framework-image-uploader-button'>" . $sScript . "</script>". PHP_EOL;

		}
}
endif;