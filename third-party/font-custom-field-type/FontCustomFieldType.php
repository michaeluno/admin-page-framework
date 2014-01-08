<?php
class FontCustomFieldType extends AdminPageFramework_FieldType {

	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'font', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'attributes_to_store'	=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
		'show_preview'	=> true,	// ( boolean )
		'allow_external_source'	=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		'preview_text'	=> 'The quick brown fox jumps over the lazy dog. Foxy parsons quiz and cajole the lovably dim wiki-girl. Watch “Jeopardy!”, Alex Trebek’s fun TV quiz game. How razorback-jumping frogs can level six piqued gymnasts! All questions asked by five watched experts — amaze the judge.',
		'attributes'	=>	array(
			'input'	=>	array(
				'size'	=>	60,	
				'maxlength'	=>	400,
			),
			'preview'	=>	array(),
			'button'	=>	array(),
		),	
	);

	function __construct() {
				
		$aArgs = func_get_args();
		call_user_func_array( array( $this, "parent::__construct" ), $aArgs );	// Call the parent constructor.
				
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
	 * Loads the field type necessary components.
	 */ 
	public function setUp() {
		
		$this->enqueueMediaUploader();	// defined in the parent class.
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );

 		wp_enqueue_script(
			'getAPFFontUploaderSelectObject',
			$this->resolveSRC( dirname( __FILE__ ) . '/js/getAPFFontUploaderSelectObject.js' ),
			array( 'jquery' )	// dependency
		);
		wp_localize_script(
			'getAPFFontUploaderSelectObject', 
			'oAPFFontUploader', 	// the translation object name - used in the above script
			array(  
				'upload_font' => __( 'Upload Font', 'admin-page-framework-demo' ),
				'use_this_font' => __( 'Use This Font', 'admin-page-framework-demo' ),
			) 
		);
	
	}	

	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			array( 'src'	=>	dirname( __FILE__ ) . '/js/setFontPreview.js', 'dependencies'	=> array( 'jquery' ) ),
			array( 'src'	=>	dirname( __FILE__ ) . '/js/jquery.nouislider.js', 'dependencies'	=> array( 'jquery-ui-core' ) ),			
		);
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
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 
		return $this->_getScript_CustomMediaUploaderObject() . PHP_EOL
			. $this->getScript_FontSelector(
				"admin_page_framework", 
				__( 'Upload Font', 'admin-page-framework-demo' ),
				__( 'Use This Font', 'admin-page-framework-demo' )
			) . PHP_EOL
			. $this->getScript_CreateSlider()
			. $this->getScript_RepeatableFields();
	}
	
	protected function getScript_CreateSlider() {
		
		return "
			createFontSizeChangeSlider = function( sInputID ) {
				var sSliderID = 'slider_' + sInputID;
				var sSliderContainerID = 'slider_container_' + sInputID;
				return '<div class=\"fontSliderHolder\" id=\"' + sSliderContainerID + '\" >'
					+ '<div class=\"sliderT\">A</div>'
					+ '<div class=\"holder\"><div id=\"' + sSliderID + '\" class=\"noUiSlider\"></div></div>'
					+ '<div class=\"sliderB\">A</div>'
				+ '</div>';
				
			}
		";
		
	}
	
	protected function getScript_RepeatableFields() {
			
		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		/*	The below function will be triggered when a new repeatable field is added. */
		return "
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* 1. Return if not for this field type */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	// if it is not this field type
						if ( node.find( '.font-field input' ).length <= 0 ) return;	// if the input tag is not found, do nothing
				
						/* 2. Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
						node.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function() {
							
							/* 2-1. Check if the parsing node holds necessary elements. */
							var nodeFontInput = jQuery( this ).find( '.font-field input' );
							if ( nodeFontInput.length <= 0 ) return true;
														
							/* 2-2. Deal with three elements: the Select Font button, the preview box, the preview font size change slider. */
							nodeButton = jQuery( this ).find( '.select_font' );							
							nodeButton.incrementIDAttribute( 'id' );
							jQuery( this ).find( '.font_preview' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( '.font-preview-text' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( '.fontSliderHolder' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( '.noUiSlider' ).incrementIDAttribute( 'id' );
							
							/* 2-3. Rebind functions to each element and update the associated properties. */
							
							/* 2-3-1. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */
							var sInputID = nodeFontInput.attr( 'id' );								 
							setAPFFontUploader( sInputID, true, nodeButton.attr( 'data-enable_external_source' ) );	
							
							/* 2-3-2. Update the font-family style of the preview box. */
							jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );
			
							/* 2-3-3. Rebind the noUiSlider script to the font-size changer slider. */
							jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
							jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
								range: [ 100, 300 ],
								start: 150,
								step: 1,
								handles: 1,
								slide: function() {
									jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
								}				
							});
						});		
						return false;
					},
					removed_repeatable_field: function( node, sFieldType, sFieldTagID ) {
						
						/* 1. Return if not for this field type */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	// if it is not this field type
						if ( node.find( '.select_font' ).length <= 0 )  return;		// if the input tag is not found, do nothing				

						/* 2. Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
						node.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function() {		
							
							/* 2-1. Check if the parsing node holds necessary elements. */
							var nodeFontInput = jQuery( this ).find( '.font-field input' );
							if ( nodeFontInput.length <= 0 ) return true;
														
							/* 2-2. Deal with three elements: the Select Font button, the preview box, the preview font size change slider. */
							nodeButton = jQuery( this ).find( '.select_font' );							
							nodeButton.decrementIDAttribute( 'id' );
							jQuery( this ).find( '.font_preview' ).decrementIDAttribute( 'id' );
							jQuery( this ).find( '.font-preview-text' ).decrementIDAttribute( 'id' );
							// jQuery( this ).find( '.fontSliderHolder' ).decrementIDAttribute( 'id' );
							// jQuery( this ).find( '.noUiSlider' ).decrementIDAttribute( 'id' );							
							
							/* 2-3. Rebind functions to each element and update the associated properties. */
						
							/* 2-3-1. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */
							var sInputID = nodeFontInput.attr( 'id' );
							setAPFFontUploader( sInputID, true, nodeButton.attr( 'data-enable_external_source' ) );	
							
							/* 2-3-2. Update the font-family style of the preview box. */
							jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );							
							
							/* 2-3-3. Rebind the noUiSlider script to the font-size changer slider. */
							jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
							jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
								range: [ 100, 300 ],
								start: 150,
								step: 1,
								handles: 1,
								slide: function() {
									jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
								}				
							});		
						});
					},				
					
					sorted_fields : function( node, sFieldType, sFieldsTagID ) {	// on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

						/* 1. Return if it is not the type. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;	/* If it is not the color field type, do nothing. */						
						if ( node.find( '.select_font' ).length <= 0 )  return;	/* If the uploader buttons are not found, do nothing */
						
						/* 2. Update the Select File button */
						var iCount = 0;
						node.children( '.admin-page-framework-field' ).each( function() {
							
							nodeButton = jQuery( this ).find( '.select_font' );
							
							/* 2-1. Set the current iteration index to the button ID and the preview elements */
							nodeButton.setIndexIDAttribute( 'id', iCount );	
							jQuery( this ).find( '.font_preview' ).setIndexIDAttribute( 'id', iCount );
							jQuery( this ).find( '.font-preview-text' ).setIndexIDAttribute( 'id', iCount );
							
							/* 2-2. Rebind the uploader script to the button */
							var nodeFontInput = jQuery( this ).find( '.font-field input' );
							if ( nodeFontInput.length <= 0 ) return true;
							var sInputID = nodeFontInput.attr( 'id' );
							setAPFFontUploader( sInputID, true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
							
							/* 2-2-2. Update the font-family style of the preview box. */
							jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );							
							
							/* 2-2-3. Rebind the noUiSlider script to the font-size changer slider. */
							jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
							jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
								range: [ 100, 300 ],
								start: 150,
								step: 1,
								handles: 1,
								slide: function() {
									jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
								}				
							});	
							
							iCount++;
						});
					},		
					
				});
			});		
		" . PHP_EOL;
	}	
	
		/**
		 * Returns the font selector JavaScript script to be loaded in the head tag of the created admin pages.
		 */		
		private function getScript_FontSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						
						setAPFFontUploader = function( sInputID, fMultiple, fExternalSource ) {
							jQuery( '#select_font_' + sInputID ).unbind( 'click' );	// for repeatable fields
							jQuery( '#select_font_' + sInputID ).click( function() {
								var sPressedID = jQuery( this ).attr( 'id' );
								window.sInputID = sPressedID.substring( 12 );	// remove the select_font_ prefix and set a property to pass it to the editor callback method.
								window.original_send_to_editor = window.send_to_editor;
								window.send_to_editor = hfAPFSendToEditorFont;
								var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
								tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
								return false;	// do not click the button after the script by returning false.									
							});	
						}					
						
						var hfAPFSendToEditorFont = function( sRawHTML ) {
							
							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.							
							var src = jQuery( 'a', sHTML ).attr( 'href' );

							// If the user wants to save relevant attributes, set them.
							var sInputID = window.sInputID;	// window.sInputID should be assigned when the thickbox is opened.
							jQuery( '#' + sInputID ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
																					
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
																					
							// Set the font preview
							setFontPreview( src, sInputID );		
							
							// close the thickbox
							tb_remove();							
							
						}
												
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFFontUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_font_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_font_' + sInputID ).click( function( e ) {
						
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
						// jQuery( '#font_preview_' + sInputID ).attr( 'data-id', image.id );
						// jQuery( '#font_preview_' + sInputID ).attr( 'data-width', image.width );
						// jQuery( '#font_preview_' + sInputID ).attr( 'data-height', image.height );
						// jQuery( '#font_preview_' + sInputID ).attr( 'data-caption', sCaption );
						// jQuery( '#font_preview_' + sInputID ).attr( 'alt', sAlt );
						// jQuery( '#font_preview_' + sInputID ).attr( 'title', title );
						// jQuery( '#font_preview_' + sInputID ).attr( 'src', image.url );
						// jQuery( '#font_preview_container_' + sInputID ).show();				
					
						// Change the font-face
						setFontPreview( image.url, sInputID );
					
					}
					
				}		
			});
			";
		}
		
	/**
	 * Returns IE specific CSS rules.
	 */
	protected function getIEStyles() { return ''; }

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	protected function getStyles() { 
		return "/* Font Custom Field Type */
			.admin-page-framework-field-font .admin-page-framework-repeatable-field-buttons {
				margin-left: 1em;				
			}" . PHP_EOL;
	 }
		
	/**
	 * Returns the output of the field type.
	 */
	protected function getField( $aField ) { 
		
		/* Variables */
		$aOutput = array();
		$iCountAttributes = count( ( array ) $aField['attributes_to_store'] );	// If the saving extra attributes are not specified, the input field will be single only for the URL. 
		$sCaptureAttribute = $iCountAttributes ? 'url' : '';
		$sFontURL = $sCaptureAttribute
			? ( isset( $aField['attributes']['value'][ $sCaptureAttribute ] ) ? $aField['attributes']['value'][ $sCaptureAttribute ] : "" )
			: $aField['attributes']['value'];
		
		/* Set up the attribute arrays */
		$aBaseAttributes = $aField['attributes'];
		unset( $aBaseAttributes['input'], $aBaseAttributes['button'], $aBaseAttributes['preview'], $aBaseAttributes['name'], $aBaseAttributes['value'], $aBaseAttributes['type'] );
		$aInputAttributes = array(
			'name'	=>	$aField['attributes']['name'] . ( $iCountAttributes ? "[url]" : "" ),
			'value'	=>	$sFontURL,
			'type'	=>	'text',
		) + $aField['attributes']['input'] + $aBaseAttributes;
		$aButtonAtributes = $aField['attributes']['button'] + $aBaseAttributes;
		$aPreviewAtrributes = $aField['attributes']['preview'] + $aBaseAttributes;

		/* Compose the field output */
		$aOutput[] =
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField['type']}-field'>"	// image-field ( this will be media-field for the media field type )
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['is_repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class
					. $this->getExtraInputFields( $aField )
					. $aField['after_input']
				. "</label>"
			. "</div>"			
			. $aField['after_label']
			. $this->_getPreviewContainer( $aField, $sFontURL, $aPreviewAtrributes )
			. $this->_getUploaderButtonScript( $aField['input_id'], $aField['is_repeatable'], $aField['allow_external_source'], $aButtonAtributes );
			;
					
		return implode( PHP_EOL, $aOutput );
		
	}
		/**
		 * Returns extra input fields to set capturing attributes.
		 * @since			3.0.0
		 */
		protected function getExtraInputFields( &$aField ) {
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			$aOutputs = array();
			foreach( ( array ) $aField['attributes_to_store'] as $sAttribute )
				$aOutputs[] = "<input " . $this->generateAttributes( 
						array(
							'id'	=>	"{$aField['input_id']}_{$sAttribute}",
							'type'	=>	'hidden',
							'name'	=>	"{$aField['field_name']}[{$sAttribute}]",
							'disabled'	=>	isset( $aField['attributes']['diabled'] ) && $aField['attributes']['diabled'] ? 'Disabled' : '',
							'value'	=>	isset( $aField['attributes']['value'][ $sAttribute ] ) ? $aField['attributes']['value'][ $sAttribute ] : '',
						)
					) . "/>";
			return implode( PHP_EOL, $aOutputs );
			
		}	
		
		/**
		 * Returns the output of the preview box.
		 * @since			3.0.0
		 */
		protected function _getPreviewContainer( $aField, $sFontURL, $aPreviewAtrributes ) {

			if ( ! $aField['show_preview'] ) return '';
			
			$sFontURL = $this->resolveSRC( $sFontURL, true );
			return 
				"<div " . $this->generateAttributes( 
						array(
							'id'	=>	"font_preview_container_{$aField['input_id']}",							
							'class'	=>	'font_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : '' ),
							// 'style'	=> ( $sFontURL ? '' : "display; none; "  ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ),
						) + $aPreviewAtrributes
					)
				. ">"
					. "<p class='font-preview-text' id='font_preview_{$aField['input_id']}' style='font-family: {$aField['input_id']}; opacity: 1;'>"
						. $aField['preview_text']
					. "</p>"					
				. "</div>"
				. $this->getScopedStyle( $aField['input_id'], $sFontURL )
				. $this->getFontChangeScript( $aField['input_id'], $sFontURL )
				. $this->getFontSizeChangerElement( $aField['input_id'], "font_preview_container_{$aField['input_id']}", "font_preview_{$aField['input_id']}" )
			;
		}
		
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */
		protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) {
			
			$sButton = 
				"<a " . $this->generateAttributes( 
					array(
						'id'	=>	"select_font_{$sInputID}",
						'href'	=>	'#',
						'class'	=>	'select_font button button-small ' . ( isset( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : '' ),
						'data-uploader_type'	=>	function_exists( 'wp_enqueue_media' ) ? 1 : 0,
						'data-enable_external_source' => $bExternalSource ? 1 : 0,
					) + $aButtonAttributes
				) . ">"
					. __( 'Select Font', 'admin-page-framework-demo' )
				."</a>";
				
			$sScript = "
				if ( jQuery( 'a#select_font_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}
				jQuery( document ).ready( function(){			
					setAPFFontUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
				});" . PHP_EOL;	
					
			return "<script type='text/javascript' class='admin-page-framework-font-uploader-button'>" . $sScript . "</script>". PHP_EOL;

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
		
		private function getFontSizeChangerElement( $sInputID, $sPreviewContainerID, $sPreviewID ) {
			
			$sSliderID = "slider_{$sInputID}";
			$sSliderContainerID = "slider_container_{$sInputID}";
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
							// jQuery( '#{$sPreviewContainerID}' ).before( \"{$sFontSizeChangerHTML}\" );
							jQuery( '#{$sPreviewContainerID}' ).before( createFontSizeChangeSlider( \"{$sInputID}\" ) );
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
			return "
				<script type='text/javascript' >
					jQuery( document ).ready( function() {
						setFontPreview( '{$sFontURL}', {'$sInputID'} );
					}); 
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