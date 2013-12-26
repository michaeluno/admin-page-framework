<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_image' ) ) :
/**
 * Defines the image field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'attributes_to_capture'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'size'									=> 60,
			'max_length'							=> 400,
			'vImagePreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();	
	}	
	/**
	 * Enqueues scripts and styles for the media uploader.
	 * 
	 * @remark			Used by the image and media field types.
	 * @since			2.1.5
	 */
	protected function enqueueMediaUploader() {
		
		// add_filter( 'gettext', array( $this, 'replyToReplacingThickBoxText' ) , 1, 2 );
		add_filter( 'media_upload_tabs', array( $this, 'replyToRemovingMediaLibraryTab' ) );
		
		wp_enqueue_script( 'jquery' );			
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	
		if ( function_exists( 'wp_enqueue_media' ) ) 	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else		
			wp_enqueue_script( 'media-upload' );
			
	}
		/**
		 * Removes the From URL tab from the media uploader.
		 * 
		 * since			2.1.3
		 * since			2.1.5			Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to replyToRemovingMediaLibraryTab().
		 * @remark			A callback for the <em>media_upload_tabs</em> hook.	
		 */
		public function replyToRemovingMediaLibraryTab( $aTabs ) {
			
			if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
			
			if ( ! $_REQUEST['enable_external_source'] )
				unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
			
			return $aTabs;
			
		}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {		
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	
			. $this->getScript_ImageSelector( 
				"admin_page_framework", 
				$this->oMsg->__( 'upload_image' ),
				$this->oMsg->__( 'use_this_image' )
		);
	}
		/**
		 * Returns the JavaScript script that creates a custom media uploader object.
		 * 
		 * @remark			Used by the image and media field types.
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_Property_Base.
		 */
		protected function getScript_CustomMediaUploaderObject() {
			
			 $bLoaded = isset( $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] )
				? $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] : false;
			
			if( ! function_exists( 'wp_enqueue_media' ) || $bLoaded )	// means the WordPress version is 3.4.x or below
				return "";
			
			$GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] = true;
			
			// Global function literal
			return "
				getAPFCustomMediaUploaderSelectObject = function() {
					return wp.media.view.MediaFrame.Select.extend({

						initialize: function() {
							wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

							_.defaults( this.options, {
								multiple:  true,
								editing:   false,
								state:    'insert'
							});

							this.createSelection();
							this.createStates();
							this.bindHandlers();
							this.createIframeStates();
						},

						createStates: function() {
							var options = this.options;

							// Add the default states.
							this.states.add([
								// Main states.
								new wp.media.controller.Library({
									id:         'insert',
									title:      'Insert Media',
									priority:   20,
									toolbar:    'main-insert',
									filterable: 'image',
									library:    wp.media.query( options.library ),
									multiple:   options.multiple ? 'reset' : false,
									editable:   true,

									// If the user isn't allowed to edit fields,
									// can they still edit it locally?
									allowLocalEdits: true,

									// Show the attachment display settings.
									displaySettings: true,
									// Update user settings when users adjust the
									// attachment display settings.
									displayUserSettings: true
								}),

								// Embed states.
								new wp.media.controller.Embed(),
							]);


							if ( wp.media.view.settings.post.featuredImageId ) {
								this.states.add( new wp.media.controller.FeaturedImage() );
							}
						},

						bindHandlers: function() {
							// from Select
							this.on( 'router:create:browse', this.createRouter, this );
							this.on( 'router:render:browse', this.browseRouter, this );
							this.on( 'content:create:browse', this.browseContent, this );
							this.on( 'content:render:upload', this.uploadContent, this );
							this.on( 'toolbar:create:select', this.createSelectToolbar, this );
							//

							this.on( 'menu:create:gallery', this.createMenu, this );
							this.on( 'toolbar:create:main-insert', this.createToolbar, this );
							this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
							this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
							this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

							var handlers = {
									menu: {
										'default': 'mainMenu'
									},

									content: {
										'embed':          'embedContent',
										'edit-selection': 'editSelectionContent'
									},

									toolbar: {
										'main-insert':      'mainInsertToolbar'
									}
								};

							_.each( handlers, function( regionHandlers, region ) {
								_.each( regionHandlers, function( callback, handler ) {
									this.on( region + ':render:' + handler, this[ callback ], this );
								}, this );
							}, this );
						},

						// Menus
						mainMenu: function( view ) {
							view.set({
								'library-separator': new wp.media.View({
									className: 'separator',
									priority: 100
								})
							});
						},

						// Content
						embedContent: function() {
							var view = new wp.media.view.Embed({
								controller: this,
								model:      this.state()
							}).render();

							this.content.set( view );
							view.url.focus();
						},

						editSelectionContent: function() {
							var state = this.state(),
								selection = state.get('selection'),
								view;

							view = new wp.media.view.AttachmentsBrowser({
								controller: this,
								collection: selection,
								selection:  selection,
								model:      state,
								sortable:   true,
								search:     false,
								dragInfo:   true,

								AttachmentView: wp.media.view.Attachment.EditSelection
							}).render();

							view.toolbar.set( 'backToLibrary', {
								text:     'Return to Library',
								priority: -100,

								click: function() {
									this.controller.content.mode('browse');
								}
							});

							// Browse our library of attachments.
							this.content.set( view );
						},

						// Toolbars
						selectionStatusToolbar: function( view ) {
							var editable = this.state().get('editable');

							view.set( 'selection', new wp.media.view.Selection({
								controller: this,
								collection: this.state().get('selection'),
								priority:   -40,

								// If the selection is editable, pass the callback to
								// switch the content mode.
								editable: editable && function() {
									this.controller.content.mode('edit-selection');
								}
							}).render() );
						},

						mainInsertToolbar: function( view ) {
							var controller = this;

							this.selectionStatusToolbar( view );

							view.set( 'insert', {
								style:    'primary',
								priority: 80,
								text:     'Select Image',
								requires: { selection: true },

								click: function() {
									var state = controller.state(),
										selection = state.get('selection');

									controller.close();
									state.trigger( 'insert', selection ).reset();
								}
							});
						},

						featuredImageToolbar: function( toolbar ) {
							this.createSelectToolbar( toolbar, {
								text:  'Set Featured Image',
								state: this.options.state || 'upload'
							});
						},

						mainEmbedToolbar: function( toolbar ) {
							toolbar.view = new wp.media.view.Toolbar.Embed({
								controller: this,
								text: 'Insert Image'
							});
						}		
					});
				}
			";
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
		private function getScript_ImageSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( sRawHTML ) {

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
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );
							jQuery( '#' + field_id + '_width' ).val( width );
							jQuery( '#' + field_id + '_height' ).val( height );
							jQuery( '#' + field_id + '_caption' ).val( sCaption );
							jQuery( '#' + field_id + '_alt' ).val( sAlt );
							jQuery( '#' + field_id + '_title' ).val( title );						
							jQuery( '#' + field_id + '_align' ).val( align );						
							jQuery( '#' + field_id + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + field_id ).attr( 'alt', alt );
							jQuery( '#image_preview_' + field_id ).attr( 'title', title );
							jQuery( '#image_preview_' + field_id ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + field_id ).attr( 'data-id', id );
							jQuery( '#image_preview_' + field_id ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + field_id ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + field_id ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
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
						var sCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var sAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var title = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
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
	public function replyToGetInputStyles() {
		return 
			"/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 1em;
				margin-bottom: 1em;
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
		/* Image Uploader Button */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
			}
			.select_image.button.button-small {
				vertical-align: baseline;
			}			
		" . PHP_EOL;	
	}
	
	/**
	 * Returns the output of the field type.
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
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"					
					. $this->getImageInputTags( $vValue, $aField, $field_name, $tag_id, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
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
		 * A helper function for the above replyToGetInputField() method to return input elements.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added some parameters.
		 */
		private function getImageInputTags( $vValue, $aField, $field_name, $tag_id, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['attributes_to_capture'] );
			
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
					. "value='" . ( $sImageURL = $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['attributes_to_capture'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$tag_id}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$field_name}[{$sKey}]" : "{$field_name}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$tag_id}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $aField['vImagePreview'], $sKey, true )
					? "<div id='image_preview_container_{$tag_id}_{$sKey}' "
							. "class='image_preview' "
							. "style='" . ( $sImageURL ? "" : "display : none;" ) . "'"
						. ">"
							. "<img src='{$sImageURL}' "
								. "id='image_preview_{$tag_id}_{$sKey}' "
							. "/>"
						. "</div>"
					: "" )
				. $this->getImageUploaderButtonScript( "{$tag_id}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false );
			
		}
		/**
		 * A helper function for the above getImageInputTags() method that retrieve the specified input field value.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField
		 */
		private function getImageInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */
		private function getImageUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_image_{$sInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->__( 'select_image' )
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
}
endif;