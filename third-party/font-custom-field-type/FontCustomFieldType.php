<?php
class FontCustomFieldType extends AdminPageFramework_FieldType_image {

	function __construct( $sClassName, $sFieldTypeSlug, $oMsg=null, $bAutoRegister=true ) {
		
		parent::__construct( $sClassName, $sFieldTypeSlug, $oMsg, $bAutoRegister );	
		
		add_filter( 'upload_mimes', array( $this, 'replyToFilterUploadMimes' ) );

	}
		/**
		 * This allows several file types to be uploaded with the WordPress media uploader.
		 * 
		 */
		public function replyToFilterUploadMimes( $aMimes ) {
			$aMimes[ 'eot' ] = 'application/vnd.ms-fontobject';
			$aMimes[ 'ttf' ] = 'application/x-font-ttf';
			$aMimes[ 'otf' ] = 'font/opentype';
			$aMimes[ 'woff' ] = 'application/font-woff';
			$aMimes['svg'] = 'image/svg+xml';
			return $aMimes;						
		}
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'extra_attributes'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'size'									=> 60,
			'max_length'							=> 400,
			'vFontPreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
			'vPreviewText'							=> 'The quick brown fox jumps over the lazy dog. Foxy parsons quiz and cajole the lovably dim wiki-girl. Watch “Jeopardy!”, Alex Trebek’s fun TV quiz game. How razorback-jumping frogs can level six piqued gymnasts! All questions asked by five watched experts — amaze the judge.',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		
		$this->enqueueMediaUploader();	// defined in the parent class.
	
		// The global functions
		wp_enqueue_script(
			'getAPFFontUploaderSelectObject',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/getAPFFontUploaderSelectObject.js' ),
			array( 'jquery' )	// dependency
		);
		wp_localize_script(
			'getAPFFontUploaderSelectObject', 
			'oAPFFontUploader', 
			array(  
				'upload_font' => __( 'Upload Font', 'admin-page-framework-demo' ),
				'use_this_font' => __( 'Use This Font', 'admin-page-framework-demo' ),
			) 
		);
		wp_enqueue_script(
			'setFontPreview',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/setFontPreview.js' ),
			array( 'jquery' )	// dependency
		);		
		
		// noUISlider
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script(
			'nouislider',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/jquery.nouislider.js' ),
			array( 'jquery-ui-core' )	// dependency
		);			
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetScripts() {
		return $this->getScript_FontSelector(
				"admin_page_framework", 
				__( 'Upload Font', 'admin-page-framework-demo' ),
				__( 'Use This Font', 'admin-page-framework-demo' )
			);
	}

		/**
		 * Returns the font selector JavaScript script to be loaded in the head tag of the created admin pages.
		 */		
		private function getScript_FontSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							
							// This needs to be done every time the button gets clicked. Otherwise, it will not work from the second time.
							window.original_send_to_editor = window.send_to_editor;
							window.send_to_editor = function( sRawHTML ) {

								var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.							
								var src = jQuery( 'a', sHTML ).attr( 'href' );

								// If the user wants to save relevant attributes, set them.
								jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
															
								// restore the original send_to_editor
								window.send_to_editor = window.original_send_to_editor;
															
								// close the thickbox
								tb_remove();	
								
								// Set the font preview
								setFontPreview( src, field_id );					
							}
							
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix							
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
							
						});
						
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + sInputID ).click( function( e ) {
						
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
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFFontUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						var custom_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							
							library: {
								type: 'application/font-woff,application/x-font-ttf,application/vnd.ms-fontobject,application/x-font-otf',
							},
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( sInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( sInputIDOfNewField, attachment );
			
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
				
					var setPreviewElement = function( sInputID, image ) {

						// Escape the strings of some of the attributes.
						// var sCaption = jQuery( '<div/>' ).text( image.caption ).html();
						// var sAlt = jQuery( '<div/>' ).text( image.alt ).html();
						// var title = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + sInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						// jQuery( 'input#' + sInputID + '_id' ).val( image.id );
						// jQuery( 'input#' + sInputID + '_width' ).val( image.width );
						// jQuery( 'input#' + sInputID + '_height' ).val( image.height );
						// jQuery( 'input#' + sInputID + '_caption' ).val( sCaption );
						// jQuery( 'input#' + sInputID + '_alt' ).val( sAlt );
						// jQuery( 'input#' + sInputID + '_title' ).val( title );
						// jQuery( 'input#' + sInputID + '_align' ).val( image.align );
						// jQuery( 'input#' + sInputID + '_link' ).val( image.link );
						
						// Update up the preview
						// jQuery( '#image_preview_' + sInputID ).attr( 'data-id', image.id );
						// jQuery( '#image_preview_' + sInputID ).attr( 'data-width', image.width );
						// jQuery( '#image_preview_' + sInputID ).attr( 'data-height', image.height );
						// jQuery( '#image_preview_' + sInputID ).attr( 'data-caption', sCaption );
						// jQuery( '#image_preview_' + sInputID ).attr( 'alt', sAlt );
						// jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
						// jQuery( '#image_preview_' + sInputID ).attr( 'src', image.url );
						// jQuery( '#image_preview_container_' + sInputID ).show();				
					
						// Change the font-face
						setFontPreview( image.url, sInputID );
					
					}
					
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
		return "";
	}
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array();
	}	

	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
			dirname( __FILE__ ) . '/css/font-field-type.css',
			dirname( __FILE__ ) . '/css/jquery.nouislider.css',
		); 
	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"					
					. $this->getFontInputTags( $vValue, $aField, $field_name, $tag_id, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$tag_id}'>" 
				. implode( PHP_EOL, $aOutput ) 
			. "</div>";		
		
	}	
	
		/**
		 * A helper function for the above replyToGetField() method to return input elements.
		 * 
		 */
		private function getFontInputTags( $vValue, $aField, $field_name, $tag_id, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['extra_attributes'] );
			
			// The URL input field is mandatory as the preview element uses it.
			$aOutputs = array(
				( $sLabel && ! $aField['repeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel . "</span>"
					: ''
				)			
				. "<input id='{$tag_id}_{$sKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $bMultipleFields ? "{$field_name}[{$sKey}]" : "{$field_name}" ) . ( $iCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $sFontURL = $this->getFontInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['extra_attributes'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$tag_id}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$field_name}[{$sKey}]" : "{$field_name}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getFontInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$tag_id}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['before_input'], $sKey, $_aDefaultKeys['before_input'] ) 
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['after_input'], $sKey, $_aDefaultKeys['after_input'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $aField['vFontPreview'], $sKey, $_aDefaultKeys['vFontPreview'] )
					? "<div id='image_preview_container_{$tag_id}_{$sKey}' "
							. "class='font_preview' "
						. ">"
							. "<p class='font-preview-text' id='font_preview_{$tag_id}_{$sKey}' style='font-family: {$tag_id}_{$sKey}; opacity: 1;'>"
								// . "<apex:sectionHeader title='' subtitle='BrowserFix' />"
								. $this->getCorrespondingArrayValue( $aField['vPreviewText'], $sKey, $_aDefaultKeys['vPreviewText'] )
							. "</p>"
						. "</div>"
					: "" )
				. $this->getScopedStyle( "{$tag_id}_{$sKey}", $sFontURL )
				. $this->getFontChangeScript( "{$tag_id}_{$sKey}", $sFontURL )
				. $this->getFontUploaderButtonScript( "{$tag_id}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false )
				. $this->getFontSizeChangerElement( "{$tag_id}_{$sKey}", "image_preview_container_{$tag_id}_{$sKey}", "font_preview_{$tag_id}_{$sKey}" );
			
		}
		/**
		 * A helper function for the above method that retrieve the specified input field value.
		 */
		private function getFontInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above method to add a image button script.
		 * 
		 */
		private function getFontUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_image_{$sInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. __( 'Font URL', 'admin-page-framework' )
				."</a>";
			
			$sScript = "
				if ( jQuery( 'a#select_image_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$sScript .="
					jQuery( document ).ready( function(){			
						setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $sScript . "</script>" . PHP_EOL;

		}
		
		private function getScopedStyle( $sInputID, $sFontURL ) {
			
			$sFormat = $this->getFontFormat( $sFontURL );
			return "
			<style id='font_preview_style_{$sInputID}'>
				@font-face { 
					font-family: '{$sInputID}'; 
					src: url( {$sFontURL} ) format( '{$sFormat}' );
				}
			</style>
			";
			
		}
		
		private function getFontSizeChangerElement( $tag_id, $sPreviewContainerID, $sPreviewID ) {
			
			$sSliderID = "slider_{$tag_id}";
			$sSliderContainerID = "slider_container_{$tag_id}";
			$sFontSizeChangerHTML = 
				"<div class='fontSliderHolder' id='{$sSliderContainerID}' >"
					. "<div class='sliderT'>A</div>"
					. "<div class='holder'><div id='{$sSliderID}' class='noUiSlider'></div></div>"
					. "<div class='sliderB'>A</div>"
				. "</div>";
			
			return "
				<script type='text/javascript' class='font-size-changer' >
					jQuery( document ).ready( function() {
						
						// Write the element
						if ( jQuery( '#{$sSliderContainerID}' ).length == 0 ) {
							jQuery( '#{$sPreviewContainerID}' ).before( \"{$sFontSizeChangerHTML}\" );
						}
						
						// Run noUiSlider
						jQuery( '#{$sSliderID}' ).noUiSlider({
							range: [ 100, 300 ],
							start: 150,
							step: 1,
							handles: 1,
							slide: function() {
								jQuery( '#{$sPreviewID}' ).css( 'font-size', jQuery( this ).val() + '%' );
							}				
						});
						
					}); 
				</script>";				
		}
		
		private function getFontChangeScript( $sInputID, $sFontURL ) {
			
			$sFormat = $this->getFontFormat( $sFontURL );
			return "
				<script type='text/javascript'>
					
					// Remove the previous style element for the preview
					jQuery( '#font_preview_style_' + '{$sInputID}' ).remove();
					
					// Set the new url for the preview 
					var sCSS = '@font-face { font-family: \"{$sInputID}\"; src: url( ' + '{$sFontURL}' + ' ) format( \"{$sFormat}\" ) }';
					jQuery( 'head' ).append( '<style id=\"font_preview_style_' + '{$sInputID}' + '\" type=\"text/css\">' +  sCSS + '</style>' );
					
				</script>";		
			
		}
	
			private function getFontFormat( $sURL ) {
				$sExtension = strtolower( pathinfo( $sURL, PATHINFO_EXTENSION ) );
				switch( $sExtension ) {
					case 'eot':
						return 'embedded-opentype';
					case 'ttf':
						return 'truetype';
					case 'otf':
						return 'opentype';
					default:
						return $sExtension;	// woff, svg,
				}
			}
}