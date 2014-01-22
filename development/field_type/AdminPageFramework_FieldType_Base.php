<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @internal
 */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_WPUtility {
	
	/**
	 * Stores the field set type indicating what this field is for such as for meta boxes, taxonomy fields or page fields.
	 * @remark			This will be automatically set when head tag elements are enqueued.
	 * @since			3.0.0
	 * @internal
	 */
	public $_sFieldSetType = '';
	
	/**
	 * Defines the slugs used for this field type.
	 * This should be overridden in the extended class.
	 * @access			public
	 */
	public $aFieldTypeSlugs = array( 'default' );
	
	/**
	 * Defines the default key-values of the extended field type. 
	 * This should be overridden in the extended class.
	 */
	protected $aDefaultKeys = array();
	
	/**
	 * Defines the default key-values of all field types.
	 */
	protected static $_aDefaultKeys = array(
		'value'	=>	null,	// ( array or string ) this suppresses the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'default'	=>	null,	// ( array or string )
		'repeatable'	=>	false,
		'sortable'	=>	false,
		'label'	=>	'',	// ( string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'delimiter'	=>	'',
		'before_input'	=>	'',
		'after_input'	=>	'',				
		'before_label'	=>	null,
		'after_label'	=>	null,	
		'before_field'	=>	null,
		'after_field'	=>	null,
		'label_min_width'	=> 140,	// in pixel
		
		/* Mandatory keys */
		'field_id' => null,		
		
		/* For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings. */
		'page_slug' => null,
		'section_id' => null,
		'before_fields' => null,
		'after_fields' => null,	
		
		'attributes'			=> array(
			/* Root Attributes - the root attributes are assumed to be for the input tag. */
			'disabled'			=> '',	// set 'Disabled' to make it disabled
			'class'				=> '',
			
			/* Component Attributes */
			'fieldset'	=> array(),	// attributes applied to the field group container tag that holds all the field components including descriptions and scripts.
			'fields'	=>	array(),	// attributes applied to the fields container tag that holds all sub-fields.
			'field'	=>	array(),	// attributes applied to each field container tag.
		),
	);	
	
	protected $oMsg;
	
	function __construct( $asClassName, $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) {
			
		$this->aFieldTypeSlugs = empty( $asFieldTypeSlug ) ? $this->aFieldTypeSlugs : ( array ) $asFieldTypeSlug;
		$this->oMsg	= $oMsg ? $oMsg : AdminPageFramework_Message::instantiate();
		
		// This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
		if ( $bAutoRegister ) {
			foreach( ( array ) $asClassName as $sClassName  )
				add_filter( "field_types_{$sClassName}", array( $this, 'replyToRegisterInputFieldType' ) );
		}
	
	}	
	
	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * @since			2.1.5
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( $this->aFieldTypeSlugs as $sFieldTypeSlug )
			$aFieldDefinitions[ $sFieldTypeSlug ] = $this->getDefinitionArray( $sFieldTypeSlug );

		return $aFieldDefinitions;		

	}
	
	/**
	 * Returns the field type definition array.
	 * 
	 * @remark			The scope is public since AdminPageFramework_FieldType class allows the user to use this method.
	 * @since			2.1.5
	 * @since			3.0.0			Added the $sFieldTypeSlug parameter.
	 */
	public function getDefinitionArray( $sFieldTypeSlug='' ) {
		
		return array(
			'sFieldTypeSlug'	=> $sFieldTypeSlug,
			'aFieldTypeSlugs'	=> $this->aFieldTypeSlugs,
			'hfRenderField' => array( $this, "_replyToGetField" ),
			'hfGetScripts' => array( $this, "_replyToGetScripts" ),
			'hfGetStyles' => array( $this, "_replyToGetStyles" ),
			'hfGetIEStyles' => array( $this, "_replyToGetInputIEStyles" ),
			'hfFieldLoader' => array( $this, "_replyToFieldLoader" ),
			'hfFieldSetTypeSetter' => array( $this, "_replyToFieldTypeSetter" ),
			'aEnqueueScripts' => $this->_replyToGetEnqueuingScripts(),	// urls of the scripts
			'aEnqueueStyles' => $this->_replyToGetEnqueuingStyles(),	// urls of the styles
			'aDefaultKeys' => $this->uniteArrays( $this->aDefaultKeys, self::$_aDefaultKeys ), 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function _replyToGetField( $aField ) { return ''; }	// should return the field output
	public function _replyToGetScripts() { return ''; }	// should return the script
	public function _replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function _replyToGetStyles() { return ''; }	// should return the style
	public function _replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	
	/**
	 * Sets the field set type.
	 * 
	 * Called when enqueuing the field type's head tag elements.
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToFieldTypeSetter( $sFieldSetType='' ) {
		$this->_sFieldSetType = $sFieldSetType;
	}
	
	/**
	 * 
	 * return			array			e.g. each element can hold a sting of the source url: array( 'http://..../my_script.js', 'http://..../my_script2.js' )
	 * Optionally, an option array can be passed to specify dependencies etc.
	 * array( array( 'src' => 'http://...my_script1.js', 'dependencies' => array( 'jquery' ) ), 'http://.../my_script2.js' )
	 */
	protected function _replyToGetEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	
	/**
	 * return			array			e.g. each element can hold a sting of the source url: array( 'http://..../my_style.css', 'http://..../my_style2.css' )
	 * Optionally, an option array can be passed to specify dependencies etc.
	 * array( array( 'src' => 'http://...my_style1.css', 'dependencies' => array( 'jquery' ) ), 'http://.../my_style2.css' )
	 */
	protected function _replyToGetEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	
	/*
	 * Shared methods
	 */
	/**
	 * Returns the element value of the given field element.
	 * 
	 * When there are multiple input/select tags in one field such as for the radio and checkbox input type, 
	 * the framework user can specify the key to apply the element value. In this case, this method will be used.
	 * 
	 * @since			3.0.0
	 */
	protected function getFieldElementByKey( $asElement, $sKey, $asDefault='' ) {
					
		if ( ! is_array( $asElement ) || ! isset( $sKey ) ) return $asElement;
				
		$aElements = &$asElement;	// it is an array
		return isset( $aElements[ $sKey ] )
			? $aElements[ $sKey ]
			: $asDefault;
		
	}	
	
	/**
	 * Enqueues scripts for the media uploader.
	 * 
	 * @remark			Used by the image and the media field types.
	 */
	protected function enqueueMediaUploader() {
		
		add_filter( 'media_upload_tabs', array( $this, '_replyToRemovingMediaLibraryTab' ) );
		
		wp_enqueue_script( 'jquery' );			
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	
		if ( function_exists( 'wp_enqueue_media' ) ) 	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else		
			wp_enqueue_script( 'media-upload' );	

		if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
			add_filter( 'gettext', array( $this, '_replyToReplaceThickBoxText' ) , 1, 2 );				
		
	}
		/**
		 * Replaces the label text of a button used in the media uploader.
		 * @since			2.0.0
		 * @remark			A callback for the <em>gettext</em> hook.
		 * @internal
		 */ 
		public function _replyToReplaceThickBoxText( $sTranslated, $sText ) {

			// Replace the button label in the media thick box.
			if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $sTranslated;
			if ( $sText != 'Insert into Post' ) return $sTranslated;
			if ( $this->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $sTranslated;
			
			if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

			return $this->oProp->sThickBoxButtonUseThis ?  $this->oProp->sThickBoxButtonUseThis : $this->oMsg->__( 'use_this_image' );
			
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
	 * Returns the JavaScript script that creates a custom media uploader object.
	 * 
	 * @remark			Used by the image and media field types.
	 * @since			2.1.3
	 * @since			2.1.5			Moved from AdminPageFramework_Property_Base.
	 */
	protected function _getScript_CustomMediaUploaderObject() {
		
		if ( ! function_exists( 'wp_enqueue_media' ) ) return "";	// means the WordPress version is 3.4.x or below
		
		// Check if it's loaded in this field set type to prevent multiple insertions.
		$GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] = isset( $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] )
			? $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'] : array();
		if ( isset( $GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'][ $this->_sFieldSetType ] ) ) return '';
		$GLOBALS['aAdminPageFramework']['aLoadedCustomMediaUploaderObject'][ $this->_sFieldSetType ] = true;
				
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
}
endif;