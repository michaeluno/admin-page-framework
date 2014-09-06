jQuery( document ).ready( function(){
	getAPFFontUploaderSelectObject = function() {
		return wp.media.view.MediaFrame.Select.extend({

			initialize: function() {
				wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

				_.defaults( this.options, {
					multiple:   true,
					editing:    false,
					state:      'insert',
                    metadata:   {},
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
						title:      oAPFFontUploader.upload_font,
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
					new wp.media.controller.Embed( options ),
                    
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
					text:     oAPFFontUploader.use_this_font,
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
});