<?php
class FontCustomFieldType extends AdminPageFramework_InputFieldType_image {

	function __construct( $strClassName, $strFieldTypeSlug, $oMsg=null, $fAutoRegister=true ) {
		
		parent::__construct( $strClassName, $strFieldTypeSlug, $oMsg, $fAutoRegister );	
		
		add_filter( 'upload_mimes', array( $this, 'replyToFilterUploadMimes' ) );

	}
		/**
		 * This allows several file types to be uploaded with the WordPress media uploader.
		 * 
		 */
		public function replyToFilterUploadMimes( $arrMimes ) {
			$arrMimes[ 'eot' ] = 'application/vnd.ms-fontobject';
			$arrMimes[ 'ttf' ] = 'application/x-font-ttf';
			$arrMimes[ 'otf' ] = 'font/opentype';
			$arrMimes[ 'woff' ] = 'application/font-woff';
			$arrMimes['svg'] = 'image/svg+xml';
			return $arrMimes;						
		}
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'arrCaptureAttributes'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'vSize'									=> 60,
			'vMaxLength'							=> 400,
			'vFontPreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'strTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'strLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'fAllowExternalSource' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
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
	public function replyToGetInputScripts() {
		return $this->getScript_FontSelector(
				"admin_page_framework", 
				__( 'Upload Font', 'admin-page-framework-demo' ),
				__( 'Use This Font', 'admin-page-framework-demo' )
			);
	}

		/**
		 * Returns the font selector JavaScript script to be loaded in the head tag of the created admin pages.
		 */		
		private function getScript_FontSelector( $strReferrer, $strThickBoxTitle, $strThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							
							// This needs to be done every time the button gets clicked. Otherwise, it will not work from the second time.
							window.original_send_to_editor = window.send_to_editor;
							window.send_to_editor = function( strRawHTML ) {

								var strHTML = '<div>' + strRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.							
								var src = jQuery( 'a', strHTML ).attr( 'href' );

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
							tb_show( '{$strThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$strReferrer}&amp;button_label={$strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
							
						});
						
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( strInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + strInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + strInputID ).click( function( e ) {
						
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
							title: '{$strThickBoxTitle}',
							button: {
								text: '{$strThickBoxButtonUseThis}'
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
								setPreviewElement( strInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( strInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + strInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var strInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( strInputIDOfNewField, attachment );
			
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
				
					var setPreviewElement = function( strInputID, image ) {

						// Escape the strings of some of the attributes.
						// var strCaption = jQuery( '<div/>' ).text( image.caption ).html();
						// var strAlt = jQuery( '<div/>' ).text( image.alt ).html();
						// var strTitle = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + strInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						// jQuery( 'input#' + strInputID + '_id' ).val( image.id );
						// jQuery( 'input#' + strInputID + '_width' ).val( image.width );
						// jQuery( 'input#' + strInputID + '_height' ).val( image.height );
						// jQuery( 'input#' + strInputID + '_caption' ).val( strCaption );
						// jQuery( 'input#' + strInputID + '_alt' ).val( strAlt );
						// jQuery( 'input#' + strInputID + '_title' ).val( strTitle );
						// jQuery( 'input#' + strInputID + '_align' ).val( image.align );
						// jQuery( 'input#' + strInputID + '_link' ).val( image.link );
						
						// Update up the preview
						// jQuery( '#image_preview_' + strInputID ).attr( 'data-id', image.id );
						// jQuery( '#image_preview_' + strInputID ).attr( 'data-width', image.width );
						// jQuery( '#image_preview_' + strInputID ).attr( 'data-height', image.height );
						// jQuery( '#image_preview_' + strInputID ).attr( 'data-caption', strCaption );
						// jQuery( '#image_preview_' + strInputID ).attr( 'alt', strAlt );
						// jQuery( '#image_preview_' + strInputID ).attr( 'title', strTitle );
						// jQuery( '#image_preview_' + strInputID ).attr( 'src', image.url );
						// jQuery( '#image_preview_container_' + strInputID ).show();				
					
						// Change the font-face
						setFontPreview( image.url, strInputID );
					
					}
					
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "
		/* Font Size Changer */
		.fontSliderHolder {
			margin-left: 1em;
			float:right;
			width: 200px;
			padding:6px 10px 6px 18px;
			border:1px solid #CCC;
			background:#EEE;
			/* vertical-align:  text-bottom; */
		}
		.holder {
			padding: 10px !important;
			padding-right: 20px !important;
			width: 120px;
			float:left;
		}
		.sliderT,
		.sliderB
		{
			line-height: 20px;
			float:left;
			width:20px;
			top:8px;
			position:relative;	
		}
		.sliderT {
			font-size:100%;
		}
		.sliderB {
			font-size:200%;
		}
		.clearBlock {
			clear:both;
		}		
		";
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
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];
		$fMultipleFields = is_array( $arrFields );	
		$fRepeatable = $arrField['fRepeatable'];
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] =
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"					
					. $this->getFontInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$strTagID}'>" 
				. implode( PHP_EOL, $arrOutput ) 
			. "</div>";		
		
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() method to return input elements.
		 * 
		 */
		private function getFontInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$intCountAttributes = count( ( array ) $arrField['arrCaptureAttributes'] );
			
			// The URL input field is mandatory as the preview element uses it.
			$arrOutputs = array(
				( $strLabel && ! $arrField['fRepeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
					: ''
				)			
				. "<input id='{$strTagID}_{$strKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . ( $intCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $strFontURL = $this->getFontInputValue( $vValue, $strKey, $fMultipleFields, $intCountAttributes ? 'url' : '', $arrDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $arrField['arrCaptureAttributes'] as $strAttribute )
				$arrOutputs[] = 
					"<input id='{$strTagID}_{$strKey}_{$strAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . "[{$strAttribute}]' " 
						. "value='" . $this->getFontInputValue( $vValue, $strKey, $fMultipleFields, $strAttribute, $arrDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$strTagID}_{$strKey}' >"
						. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
						. implode( PHP_EOL, $arrOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $arrField['vFontPreview'], $strKey, $arrDefaultKeys['vFontPreview'] )
					? "<div id='image_preview_container_{$strTagID}_{$strKey}' "
							. "class='font_preview' "
						. ">"
							. "<p class='font-preview-text' id='font_preview_{$strTagID}_{$strKey}' style='font-family: {$strTagID}_{$strKey}; opacity: 1;'>"
								// . "<apex:sectionHeader title='' subtitle='BrowserFix' />"
								. $this->getCorrespondingArrayValue( $arrField['vPreviewText'], $strKey, $arrDefaultKeys['vPreviewText'] )
							. "</p>"
						. "</div>"
					: "" )
				. $this->getScopedStyle( "{$strTagID}_{$strKey}", $strFontURL )
				. $this->getFontChangeScript( "{$strTagID}_{$strKey}", $strFontURL )
				. $this->getFontUploaderButtonScript( "{$strTagID}_{$strKey}", $arrField['fRepeatable'] ? true : false, $arrField['fAllowExternalSource'] ? true : false )
				. $this->getFontSizeChangerElement( "{$strTagID}_{$strKey}", "image_preview_container_{$strTagID}_{$strKey}", "font_preview_{$strTagID}_{$strKey}" );
			
		}
		/**
		 * A helper function for the above method that retrieve the specified input field value.
		 */
		private function getFontInputValue( $vValue, $strKey, $fMultipleFields, $strCaptureAttribute, $arrDefaultKeys ) {	

			$vValue = $fMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $strKey, $arrDefaultKeys['vDefault'] )
				: ( isset( $vValue ) ? $vValue : $arrDefaultKeys['vDefault'] );

			return $strCaptureAttribute
				? ( isset( $vValue[ $strCaptureAttribute ] ) ? $vValue[ $strCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above method to add a image button script.
		 * 
		 */
		private function getFontUploaderButtonScript( $strInputID, $fRpeatable, $fExternalSource ) {
			
			$strButton ="<a id='select_image_{$strInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $fExternalSource ? 1 : 0 ) . "'"
					. ">"
						. __( 'Specify Font URL', 'admin-page-framework' )
				."</a>";
			
			$strScript = "
				if ( jQuery( 'a#select_image_{$strInputID}' ).length == 0 ) {
					jQuery( 'input#{$strInputID}' ).after( \"{$strButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$strScript .="
					jQuery( document ).ready( function(){			
						setAPFImageUploader( '{$strInputID}', '{$fRpeatable}', '{$fExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $strScript . "</script>" . PHP_EOL;

		}
		
		private function getScopedStyle( $strInputID, $strFontURL ) {
			
			$strFormat = $this->getFontFormat( $strFontURL );
			return "
			<style id='font_preview_style_{$strInputID}'>
				@font-face { 
					font-family: '{$strInputID}'; 
					src: url( {$strFontURL} ) format( '{$strFormat}' );
				}
			</style>
			";
			
		}
		
		private function getFontSizeChangerElement( $strTagID, $strPreviewContainerID, $strPreviewID ) {
			
			$strSliderID = "slider_{$strTagID}";
			$strSliderContainerID = "slider_container_{$strTagID}";
			$strFontSizeChangerHTML = 
				"<div class='fontSliderHolder' id='{$strSliderContainerID}' >"
					. "<div class='sliderT'>A</div>"
					. "<div class='holder'><div id='{$strSliderID}' class='noUiSlider'></div></div>"
					. "<div class='sliderB'>A</div>"
				. "</div>";
			
			return "
				<script type='text/javascript' class='font-size-changer' >
					jQuery( document ).ready( function() {
						
						// Write the element
						if ( jQuery( '#{$strSliderContainerID}' ).length == 0 ) {
							jQuery( '#{$strPreviewContainerID}' ).before( \"{$strFontSizeChangerHTML}\" );
						}
						
						// Run noUiSlider
						jQuery( '#{$strSliderID}' ).noUiSlider({
							range: [ 100, 300 ],
							start: 150,
							step: 1,
							handles: 1,
							slide: function() {
								jQuery( '#{$strPreviewID}' ).css( 'font-size', jQuery( this ).val() + '%' );
							}				
						});
						
					}); 
				</script>";				
		}
		
		private function getFontChangeScript( $strInputID, $strFontURL ) {
			
			$strFormat = $this->getFontFormat( $strFontURL );
			return "
				<script type='text/javascript'>
					
					// Remove the previous style element for the preview
					jQuery( '#font_preview_style_' + '{$strInputID}' ).remove();
					
					// Set the new url for the preview 
					var strCSS = '@font-face { font-family: \"{$strInputID}\"; src: url( ' + '{$strFontURL}' + ' ) format( \"{$strFormat}\" ) }';
					jQuery( 'head' ).append( '<style id=\"font_preview_style_' + '{$strInputID}' + '\" type=\"text/css\">' +  strCSS + '</style>' );
					
				</script>";		
			
		}
	
			private function getFontFormat( $strURL ) {
				$strExtension = strtolower( pathinfo( $strURL, PATHINFO_EXTENSION ) );
				switch( $strExtension ) {
					case 'eot':
						return 'embedded-opentype';
					case 'ttf':
						return 'truetype';
					case 'otf':
						return 'opentype';
					default:
						return $strExtension;	// woff, svg,
				}
			}
}