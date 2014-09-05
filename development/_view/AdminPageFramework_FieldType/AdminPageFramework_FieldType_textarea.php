<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_textarea' ) ) :
/**
 * Defines the 'textarea' field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_textarea extends AdminPageFramework_FieldType_Base {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'textarea' );

    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'rich'          => false,
        'attributes'    => array(     
            'autofocus'     => '',
            'cols'          => 60,
            'disabled'      => '',
            'formNew'       => '',
            'maxlength'     => '',
            'placeholder'   => '',
            'readonly'      => '',
            'required'      => '',
            'rows'          => 4,
            'wrap'          => '',     
        ),
    );

    /**
     * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
     * @since   3.1.4
     * @internal
     */ 
    public function _replyToGetScripts() {
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "
            jQuery( document ).ready( function(){
                
                // Move the link tag into the bottom of the page
                jQuery( 'link#editor-buttons-css' ).appendTo( '#wpwrap' );
                
                /**
                 * Determines whether the callback is handleable or not.
                 */
                var isHandleable = function( oField, sFieldType ) {
                 
                    if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) {
                        return false
                    }
                                
                    // If tinyMCE is not ready, return.
                    if ( 'object' !== typeof tinyMCEPreInit ){
                        return;
                    }
                                        
                    return true;
                    
                };
                
                /**
                 * Removes the editor by the given textarea ID.
                 */
                var removeEditor = function( sTextAreaID ) {

                    if ( 'object' !== typeof tinyMCEPreInit ){
                        return;
                    }
// tinyMCE.execCommand( 'mceFocus', false, sTextAreaID );
// tinyMCE.execCommand( 'mceRemoveControl', false, sTextAreaID );                    
                    tinyMCE.execCommand( 'mceRemoveEditor', false, sTextAreaID );
                    delete tinyMCEPreInit[ 'mceInit' ][ sTextAreaID ];
                    delete tinyMCEPreInit[ 'qtInit' ][ sTextAreaID ];
                
                }
                
                jQuery().registerAPFCallback( {				
					/**
					 * The repeatable field callback.
					 * 
					 * When a repeat event occurs and a field is copied, this method will be triggered.
					 * 
					 * @param   object  oCopied     the copied node object.
					 * @param   string  sFieldType  the field type slug
					 * @param   string  sFieldTagID the field container tag ID
					 * @param   integer iCallType   the caller type. 1 : repeatable sections. 0 : repeatable fields.
					 */
					added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {
                                               
                        if ( ! isHandleable( oCopied, sFieldType ) ) {
                            return;
                        }
                      
                        /* If the textarea tag is not found, do nothing  */
                        var oTextAreas = oCopied.find( 'textarea.wp-editor-area' );
                        if ( oTextAreas.length <= 0 ) {
                            return;
                        }                    
                        
                        // Find the tinyMCE wrapper element
                        var oWrap       = oCopied.find( '.wp-editor-wrap' );
                        if ( oWrap.length <= 0 ) {
                            return;
                        }                      
                                              
                        // Retrieve the TinyMCE and Quick Tags settings
                        var oSettings = jQuery().getAPFInputOptions( oWrap.attr( 'data-id' ) );   // the enabler script stores the original element id.
  
                        // Increment the ids of the next all (including this copied element) sub-fields.
                        var iOccurrence          = 1 === iCallType ? 1 : 0;                        
                        var oFields = oCopied.closest( '.admin-page-framework-field' ).nextAll();
                        oFields.andSelf().each( function( iIndex ) {

                            var oWrap               = jQuery( this ).find( '.wp-editor-wrap' );
                            if ( oWrap.length <= 0 ) {
                                return true;
                            }        
                            
                            var oTextArea           = jQuery( this ).find( 'textarea.wp-editor-area' ).first().clone()
                                .show()
                                .removeAttr( 'aria-hidden' );

                            if ( 0 === oFields.length || 0 === iIndex ) {
                                oTextArea.val( '' );    // only delete the value of the directly copied one
                                oTextArea.empty();      // the above use of val( '' ) does not erase the value completely.
                            } 
                            var oEditorContainer    = jQuery( this ).find( '.wp-editor-container' ).first().clone().empty();
                            var oToolBar            = jQuery( this ).find( '.wp-editor-tools' ).first().clone();
                                   
                            // Remove the old tinyMCE editor.
                            tinyMCE.execCommand( 'mceRemoveEditor', true, oTextArea.attr( 'id' ) );

                            // Replace the tinyMCE wrapper with the plain textarea tag element.
                            oWrap.empty()
                                .prepend( oEditorContainer.prepend( oTextArea.show() ) )
                                .prepend( oToolBar );   
                                 
                            // Update the settings
                            var aTMCSettings    = jQuery.extend( 
                                {}, 
                                oSettings['TinyMCE'], 
                                { 
                                    selector : '#' + oTextArea.attr( 'id' ),
                                    body_class : oTextArea.attr( 'id' ),
                                    height: '100px',  
                                    setup : function( ed ) {    // see: http://www.tinymce.com/wiki.php/API3:event.tinymce.Editor.onChange
                                        
                                        // It seems for tinyMCE 4 or above the on() method must be used.
                                        ed.on( 'change', function(){                                           
                                            jQuery( '#' + this.id ).html( this.getContent() );
                                        });
                                        
                                        // For tinyMCE 3.x or below the onChange.add() method needs to be used.
                                        // ed.onChange.add( function( ed, l ) {
                                            // console.debug( ed.id + ' : Editor contents was modified. Contents: ' + l.content);
                                            // jQuery( '#' + ed.id ).html( ed.getContent() );
                                        // });
                                    },      
                                }
                            );   
                            var aQTSettings     = jQuery.extend( {}, oSettings['QuickTags'], { id : oTextArea.attr( 'id' ) } );    
                            
                            // Store the settings.
                            tinyMCEPreInit.mceInit[ oTextArea.attr( 'id' ) ]   = aTMCSettings;
                            tinyMCEPreInit.qtInit[ oTextArea.attr( 'id' ) ]    = aQTSettings;
                            QTags.instances[ aQTSettings.id ] = aQTSettings;
                            
                             // Enable quick tags
                            quicktags( aQTSettings );   // does not work... See https://core.trac.wordpress.org/ticket/26183
                            
console.log( 'initializing' );                            
                            window.tinymce.dom.Event.domLoaded = true;   
                            tinyMCE.init( aTMCSettings );
                            jQuery( this ).find( '.wp-editor-wrap' ).first().on( 'click.wp-editor', function() {
                                if ( this.id ) {
                                    window.wpActiveEditor = this.id.slice( 3, -5 );
                                }
                            }); 
console.log( 'initialized' );                                                          
                            // The ID attributes of sub-elements are not updated yet
                            oToolBar.find( 'a,div' ).incrementIDAttribute( 'id', iOccurrence );
                            jQuery( this ).find( '.wp-editor-wrap a' ).incrementIDAttribute( 'data-editor', iOccurrence );
                            jQuery( this ).find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).incrementIDAttribute( 'id', iOccurrence );
console.log( 'going to click the tab' );
                            // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
                            if ( 0 === iCallType ) {
                                jQuery( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );
                            }
console.log( 'end of iteration' );
                        });    
console.log( 'done' );                              
					},
                    
                    /**
                     * The repeatable field callback for the remove event.
                     * 
                     * @param object    oNextFieldContainer     the field container element next to the removed field container.
                     * @param string    sFieldType              the field type slug
                     * @param string    sFieldTagID             the removed field container tag ID
                     * @param integer   iCallType               the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */     
                    removed_repeatable_field: function( oNextFieldContainer, sFieldType, sFieldTagID, iCallType ) {

                        if ( ! isHandleable( oNextFieldContainer, sFieldType ) ) {
                            return;
                        }  

                        // Find the tinyMCE wrapper element
                        var oWrap       = oNextFieldContainer.find( '.wp-editor-wrap' );
                        if ( oWrap.length <= 0 ) {
     
                            // Remove the old one from the internal tinyMCE setting object.
                            removeEditor( sFieldTagID.substring( 6 ) );

console.log( tinyMCEPreInit );                            
                            return;
                            
                        }
                        
                        // Retrieve the TinyMCE and Quick Tags settings
                        var oSettings = jQuery().getAPFInputOptions( oWrap.attr( 'data-id' ) );   // the enabler script stores the original element id.

                        // Increment the ids of the next all (including this copied element) sub-fields.
                        var iOccurrence = 1 === iCallType ? 1 : 0;                        
                        oNextFieldContainer.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function( iIndex ) {

                            var oWrap               = jQuery( this ).find( '.wp-editor-wrap' );
                            if ( oWrap.length <= 0 ) {
console.log( 'No editor wrapper found' );                                
                                return true;
                            }        
                            var oTextArea           = jQuery( this ).find( 'textarea.wp-editor-area' ).first().clone()
                                .show()
                                .removeAttr( 'aria-hidden' );
                            var oEditorContainer    = jQuery( this ).find( '.wp-editor-container' ).first().clone().empty();
                            var oToolBar            = jQuery( this ).find( '.wp-editor-tools' ).first().clone();
                            var oTextAreaPrevious   = oTextArea.clone().incrementIDAttribute( 'id', iOccurrence );
                            
console.log( 'renewing textarea id: ' + oTextArea.attr( 'id' ) );
console.log( 'previous textarea id: ' +  oTextAreaPrevious.attr( 'id' ) );
                            // Remove the editor which is assigned to the newly decremented ID if exists and the old assigned editor.
                            removeEditor( oTextAreaPrevious.attr( 'id' ) );
                            removeEditor( oTextArea.attr( 'id' ) );

                            // Replace the tinyMCE wrapper with the plain textarea tag element.
                            oWrap.empty()
                                .prepend( oEditorContainer.prepend( oTextArea.show() ) )
                                .prepend( oToolBar );   
                                
                            // Update the settings
                            var aTMCSettings    = jQuery.extend( {}, oSettings['TinyMCE'], { selector : '#' + oTextArea.attr( 'id' ), body_class : oTextArea.attr( 'id' ), height: '100px', } );   
                            var aQTSettings     = jQuery.extend( {}, oSettings['QuickTags'], { id : oTextArea.attr( 'id' ) } );    

                            // Store the settings.
                            window.tinymce.dom.Event.domLoaded = true; 
                            tinyMCEPreInit.mceInit[ oTextArea.attr( 'id' ) ]   = aTMCSettings;  
                            tinyMCEPreInit.qtInit[ oTextArea.attr( 'id' ) ]    = aQTSettings;
                            QTags.instances[ aQTSettings.id ] = aQTSettings;
                            
                            // Enable quick tags
                            quicktags( aQTSettings );   // does not work... See https://core.trac.wordpress.org/ticket/26183
                                  
                            // Initialize TinyMCE
console.log( 'number of editors: ' + tinymce.editors.length );
                            tinyMCE.init( aTMCSettings );
console.log( 'number of editors (after initializing): ' + tinymce.editors.length );                            
                            jQuery( this ).find( '.wp-editor-wrap' ).first().on( 'click.wp-editor', function() {
                                if ( this.id ) {
                                    window.wpActiveEditor = this.id.slice( 3, -5 );
                                }
                            }); 
                                           
                            // The ID attributes of sub-elements are not updated yet
                            oToolBar.find( 'a,div' ).decrementIDAttribute( 'id', iOccurrence );
                            jQuery( this ).find( '.wp-editor-wrap a' ).decrementIDAttribute( 'data-editor', iOccurrence );
                            jQuery( this ).find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).decrementIDAttribute( 'id', iOccurrence );

                            // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
                            if ( 0 === iCallType ) {
                                jQuery( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );
                            }
                            
                            // If this is called for repeatable section, handle only the first iteration as the rest will be also called one by one.
                            if ( 1 === iCallType ) {
                                return false;   
                            }

                        });                            
console.log( tinyMCEPreInit );
                        
                    },
                    sorted_fields : function( oSorted, sFieldType, sFieldsTagID, iCallType ) { // on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

                        /* 1. Return if it is not the type. */
                        if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) { return; }
                        if ( oSorted.find( '.select_image' ).length <= 0 )  { return;  }
                   
                    },   

					
				});	        
            });
        ";     
    }    
    
    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
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
     * Returns the output of the 'textarea' input field.
     * 
     * @since 2.1.5
     * @since 3.0.0 Removed redundant elements including parameters.
     */
    public function _replyToGetField( $aField ) {

        return 
            "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . $this->_getEditor( $aField )
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.                                                       
                    . $aField['after_input']
                . "</label>"
            . "</div>"
        ;
        
    }
    
        /**
         * Returns the output of the editor.
         * 
         * @since 3.0.7
         */
        private function _getEditor( $aField ) {
                        
            unset( $aField['attributes']['value'] );
            
            // For no TinyMCE
            if ( empty( $aField['rich'] ) || ! version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) || ! function_exists( 'wp_editor' ) ) {
                return "<textarea " . $this->generateAttributes( $aField['attributes'] ) . " >" // this method is defined in the base class
                            . $aField['value']
                        . "</textarea>";
            }
            
            // Rich editor
            // Capture the output buffer.
            ob_start(); // Start buffer.
            wp_editor( 
                $aField['value'],
                $aField['attributes']['id'],  
                $this->uniteArrays( 
                    ( array ) $aField['rich'],
                    array(
                        'wpautop'           => true, // use wpautop?
                        'media_buttons'     => true, // show insert/upload button(s)
                        'textarea_name'     => $aField['attributes']['name'],
                        'textarea_rows'     => $aField['attributes']['rows'],
                        'tabindex'          => '',
                        'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
                        'editor_css'        => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
                        'editor_class'      => $aField['attributes']['class'], // add extra class(es) to the editor textarea
                        'teeny'             => false, // output the minimal editor config used in Press This
                        'dfw'               => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
                        'tinymce'           => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                        'quicktags'         => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()     
                    )
                )
            );
            $_sContent = ob_get_contents(); // Assign the content buffer to a variable.
            ob_end_clean(); // End buffer and remove the buffer.
            
            return $_sContent
                . $this->_getScriptForRichEditor( $aField['attributes']['id'] );
            
        }
    
        /**
         * Provides the JavaScript script that hides the rich editor until the document gets loaded and places into the right position.
         * 
         * This adds a script that forces the rich editor element to be inside the field table cell.
         * 
         * @since 2.1.2
         * @since 2.1.5 Moved from AdminPageFramework_FormField.
         */    
        private function _getScriptForRichEditor( $sIDSelector ) {

            // id: wp-sample_rich_textarea_0-wrap
            return "<script type='text/javascript' class='admin-page-framework-textarea-enabler'>
                jQuery( document ).ready( function() {
                                        
                    // Store the textarea tag ID to be referred by the repeatable routines.
                    jQuery( '#wp-{$sIDSelector}-wrap' ).attr( 'data-id', '{$sIDSelector}' );    // store the id
                    if ( 'object' !== typeof tinyMCEPreInit ){ 
                        return; 
                    }
                    
                    // Store the settings.
                    jQuery().storeAPFInputOptions( 
                        '{$sIDSelector}', 
                        { 
                            TinyMCE: tinyMCEPreInit.mceInit[ '{$sIDSelector}' ], 
                            QuickTags: tinyMCEPreInit.qtInit[ '{$sIDSelector}' ],
                        } 
                    );
                                        
                })
            </script>";     
            
        }    
        
}
endif;