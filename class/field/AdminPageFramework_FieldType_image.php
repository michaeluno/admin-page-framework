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
				
		add_filter( 'media_upload_tabs', array( $this, '_replyToRemovingMediaLibraryTab' ) );
		
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
		 * since			2.1.5			Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to _replyToRemovingMediaLibraryTab().
		 * @remark			A callback for the <em>media_upload_tabs</em> hook.	
		 * @internal
		 */
		public function _replyToRemovingMediaLibraryTab( $aTabs ) {
			
			if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
			
			if ( ! $_REQUEST['enable_external_source'] )
				unset( $aTabs['type_url'] );	// removes the 'From URL' tab in the thick box.
			
			return $aTabs;
			
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
			. $this->_getScript_RepeatEvent();
	}
		/**
		 * Returns the JavaScript script that handles repeatable events. 
		 * 
		 * @since			3.0.0
		 */
		protected function _getScript_RepeatEvent() {

			$aJSArray = json_encode( $this->aFieldTypeSlugs );
			/*	The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
				renew the upload button and the preview elements (while it does on the input tag value), the renewal task must be dealt here separately. */
			return"
			jQuery( document ).ready( function(){
		
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
						
						/* If it is not the image field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( node.find( '.select_image' ).length <= 0 )  return;
						
						/* Remove the value of the cloned preview element */
						node.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
						node.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
						
						/* Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						nodeFieldContainer.nextAll().andSelf().each( function() {

							nodeButton = jQuery( this ).find( '.select_image' );							
							nodeButton.incrementIDAttribute( 'id' );
							jQuery( this ).find( '.image_preview' ).incrementIDAttribute( 'id' );
							jQuery( this ).find( '.image_preview img' ).incrementIDAttribute( 'id' );
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	
							
						});
					},
					removed_repeatable_field: function( node, sFieldType, sFieldTagID ) {
						
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
											
						/* If the uploader buttons are not found, do nothing */
						if ( node.find( '.select_image' ).length <= 0 )  return;						
						
						/* Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
						var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
						nodeFieldContainer.nextAll().andSelf().each( function() {
							
							nodeButton = jQuery( this ).find( '.select_image' );							
							nodeButton.decrementIDAttribute( 'id' );
							jQuery( this ).find( '.image_preview' ).decrementIDAttribute( 'id' );
							jQuery( this ).find( '.image_preview img' ).decrementIDAttribute( 'id' );
							
							/* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
							 * otherwise, the script sets the preview image in the wrong place. */						
							var nodeImageInput = jQuery( this ).find( '.image-field input' );
							if ( nodeImageInput.length <= 0 ) return true;
							
							var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
							setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );	
							
						});
						
					},
				});
			});" . PHP_EOL;	
			
		}
		
		/**
		 * Returns the JavaScript script that creates a custom media uploader object.
		 * 
		 * @remark			Used by the image and media field types.
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_Property_Base.
		 */
		protected function _getScript_CustomMediaUploaderObject() {
			
			 $bLoaded = isset( $GLOBALS['aAdminPageFramework']['bIsLoadedCustomMediaUploaderObject'] )
				? $GLOBALS['aAdminPageFramework']['bIsLoadedCustomMediaUploaderObject'] : false;
			
			if ( ! function_exists( 'wp_enqueue_media' ) || $bLoaded )	// means the WordPress version is 3.4.x or below
				return "";
			
			$GLOBALS['aAdminPageFramework']['bIsLoadedCustomMediaUploaderObject'] = true;
			
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
	public function _replyToGetStyles() {
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
			/* Image Uploader Input Field */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
				vertical-align: middle;	
			}
			/* Image Uploader Button */
			.select_image.button.button-small {
				vertical-align: baseline;
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
		
		/* Variables */
		$aOutput = array();
		$iCountAttributes = count( ( array ) $aField['attributes_to_store'] );	// If the saving extra attributes are not specified, the input field will be single only for the URL. 
		$sCaptureAttribute = $iCountAttributes ? 'url' : '';
		$sImageURL = $sCaptureAttribute
				? ( isset( $aField['attributes']['value'][ $sCaptureAttribute ] ) ? $aField['attributes']['value'][ $sCaptureAttribute ] : "" )
				: $aField['attributes']['value'];
		
		/* Set up the attribute arrays */
		$aBaseAttributes = $aField['attributes'];
		unset( $aBaseAttributes['input'], $aBaseAttributes['button'], $aBaseAttributes['preview'], $aBaseAttributes['name'], $aBaseAttributes['value'], $aBaseAttributes['type'] );
		$aInputAttributes = array(
			'name'	=>	$aField['attributes']['name'] . ( $iCountAttributes ? "[url]" : "" ),
			'value'	=>	$sImageURL,
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
			. ( $aField['show_preview'] ? $this->_getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) : '' )
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
		protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {

			$sImageURL = $this->resolveSRC( $sImageURL, true );
			return 
				"<div " . $this->generateAttributes( 
						array(
							'id'	=>	"image_preview_container_{$aField['input_id']}",							
							'class'	=>	'image_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : '' ),
							'style'	=> ( $sImageURL ? '' : "display; none; "  ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ),
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
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
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