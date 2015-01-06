<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the 'textarea' field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_textarea extends AdminPageFramework_FieldType {
    
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
            'autofocus'     => null,
            'cols'          => 60,
            'disabled'      => null,
            'formNew'       => null,
            'maxlength'     => null,
            'placeholder'   => null,
            'readonly'      => null,
            'required'      => null,
            'rows'          => 4,
            'wrap'          => null,     
        ),
    );

    /**
     * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
     * @since       3.1.4
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     * @internal
     */ 
    public function getScripts() {
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    
    // Move the link tag into the bottom of the page
    jQuery( 'link#editor-buttons-css' ).appendTo( '#wpwrap' );
    
    /**
     * Determines whether the callback is handleable or not.
     */
    var isHandleable = function( oField, sFieldType ) {
     
        if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) {
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
     
        // Store the previous texatrea value. jQuery has a bug that val() for <textarea> does not work for cloned element. @see: http://bugs.jquery.com/ticket/3016
        var oTextArea       = jQuery( '#' + sTextAreaID );
        var sTextAreaValue  = oTextArea.val();
        
        // Delete the rich editor. Somehow this deletes the value of the textarea tag in some occasions.
        tinyMCE.execCommand( 'mceRemoveEditor', false, sTextAreaID );
        delete tinyMCEPreInit[ 'mceInit' ][ sTextAreaID ];
        delete tinyMCEPreInit[ 'qtInit' ][ sTextAreaID ];
        
        // Restore the previous textarea value
        oTextArea.val( sTextAreaValue );
    
    };
    
    /**
     * Updates the editor
     * 
     * @param   string  sTextAreaID     The textarea element ID without the sharp mark(#).
     */
    var updateEditor = function( sTextAreaID, oTinyMCESettings, oQickTagSettings ) {
        
        removeEditor( sTextAreaID );
        var aTMCSettings    = jQuery.extend( 
            {}, 
            oTinyMCESettings, 
            { 
                selector:       '#' + sTextAreaID,
                body_class:     sTextAreaID,
                height:         '100px',  
                setup :         function( ed ) {    // see: http://www.tinymce.com/wiki.php/API3:event.tinymce.Editor.onChange
                    // It seems for tinyMCE 4 or above the on() method must be used.
                    if ( tinymce.majorVersion >= 4 ) {
                        ed.on( 'change', function(){                                           
                            jQuery( '#' + this.id ).val( this.getContent() );
                            jQuery( '#' + this.id ).html( this.getContent() );
                        });
                    } else {
                        // For tinyMCE 3.x or below the onChange.add() method needs to be used.
                        ed.onChange.add( function( ed, l ) {
                            // console.debug( ed.id + ' : Editor contents was modified. Contents: ' + l.content);
                            jQuery( '#' + ed.id ).val( ed.getContent() );
                            jQuery( '#' + ed.id ).html( ed.getContent() );
                        });
                    }
                },      
            }
        );   
        var aQTSettings     = jQuery.extend( {}, oQickTagSettings, { id : sTextAreaID } );    
        
        // Store the settings.
        tinyMCEPreInit.mceInit[ sTextAreaID ]   = aTMCSettings;
        tinyMCEPreInit.qtInit[ sTextAreaID ]    = aQTSettings;
        QTags.instances[ aQTSettings.id ]       = aQTSettings;
        
         // Enable quick tags
        quicktags( aQTSettings );   // does not work... See https://core.trac.wordpress.org/ticket/26183
        QTags._buttonsInit();                     
        
        window.tinymce.dom.Event.domLoaded = true;   
        tinyMCE.init( aTMCSettings );
        jQuery( this ).find( '.wp-editor-wrap' ).first().on( 'click.wp-editor', function() {
            if ( this.id ) {
                window.wpActiveEditor = this.id.slice( 3, -5 );
            }
        }); 

    };
    
    /**
     * Decides whether the textarea element should be empty.
     */
    var shouldEmpty = function( iCallType, iIndex, iCountNextAll, iSectionIndex ) {

        // For repeatable fields,
        if ( 0 === iCallType ) {
           return ( 0 === iCountNextAll || 0 === iIndex )
        }

        // At this point, this is for repeatable sections. In this case, only the first iterated section should empty the fields.
        return ( 0 === iSectionIndex );
        
    };
    
    jQuery().registerAPFCallback( {				
        /**
         * The repeatable field callback.
         * 
         * When a repeat event occurs and a field is copied, this method will be triggered.
         * 
         * @param   object  oCopied         the copied node object.
         * @param   string  sFieldType      the field type slug
         * @param   string  sFieldTagID     the field container tag ID
         * @param   integer iCallType       the caller type. 1 : repeatable sections. 0 : repeatable fields.\
         * @param   integer iSectionIndex   the section index. For repeatable fields, it will be always 0
         * @param   integer iFieldIndex     the field index. For repeatable fields, it will be always 0.
         */
        added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType, iSectionIndex, iFieldIndex ) {
                                   
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

            // Update the TinyMCE editor and Quick Tags bar and increment the ids of the next all (including this copied element) sub-fields.
            var iOccurrence          = 1 === iCallType ? 1 : 0;                        
            var oFieldsNextAll = oCopied.closest( '.admin-page-framework-field' ).nextAll();
            oFieldsNextAll.andSelf().each( function( iIndex ) {

                var oWrap               = jQuery( this ).find( '.wp-editor-wrap' );
                if ( oWrap.length <= 0 ) {
                    return true;
                }        
                
                // Cloning is needed here as repeatable sections does not work with the original element for unknown reasons.
                var oTextArea           = jQuery( this ).find( 'textarea.wp-editor-area' ).first().clone().show().removeAttr( 'aria-hidden' );

                if ( shouldEmpty( iCallType, iIndex, oFieldsNextAll.length, iSectionIndex ) ) {
                    oTextArea.val( '' );    // only delete the value of the directly copied one
                    oTextArea.empty();      // the above use of val( '' ) does not erase the value completely.
                } 
                var oEditorContainer    = jQuery( this ).find( '.wp-editor-container' ).first().clone().empty();
                var oToolBar            = jQuery( this ).find( '.wp-editor-tools' ).first().clone();
                       
                // Replace the tinyMCE wrapper with the plain textarea tag element.
                oWrap.empty()
                    .prepend( oEditorContainer.prepend( oTextArea.show() ) )
                    .prepend( oToolBar );   
                    
                // Update the editor. For repeatable sections, remove the previously assigned editor.                        
                updateEditor( oTextArea.attr( 'id' ), oSettings['TinyMCE'], oSettings['QuickTags'] );
                                      
                // The ID attributes of sub-elements are not updated yet
                oToolBar.find( 'a,div' ).incrementIDAttribute( 'id', iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap a' ).incrementIDAttribute( 'data-editor', iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).incrementIDAttribute( 'id', iOccurrence );

                // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
                if ( 0 === iCallType ) {
                    jQuery( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );
                }
                
                // If this is called for a repeatable section, handle only the first iteration as the rest will be also called one by one.
                if ( 1 === iCallType ) {
                    return false;   // break
                }                            

            });    

        },
        
        /**
         * The repeatable field callback for the remove event.
         * 
         * @param   object      oNextFieldContainer     the field container element next to the removed field container.
         * @param   string      sFieldType              the field type slug.
         * @param   string      sFieldTagID             the removed field container tag ID.
         * @param   integer     iCallType               the caller type. 1 : repeatable sections. 0 : repeatable fields.
         * @param   integer     iSectionIndex           the section index. For repeatable fields, it will be always 0.
         * @param   integer     iFieldIndex             the field index. For repeatable fields, it will be always 0.
         */     
        removed_repeatable_field: function( oNextFieldContainer, sFieldType, sFieldTagID, iCallType, iSectionIndex, iFieldIndex ) {

            if ( ! isHandleable( oNextFieldContainer, sFieldType ) ) {
                return;
            }  

            // Find the tinyMCE wrapper element
            var oWrap       = oNextFieldContainer.find( '.wp-editor-wrap' );
            if ( oWrap.length <= 0 ) {

                // Remove the old one from the internal tinyMCE setting object.
                removeEditor( sFieldTagID.substring( 6 ) );              
                return;
                
            }
            
            // Retrieve the TinyMCE and Quick Tags settings. The enabler script stores the original element id.
            var oSettings = jQuery().getAPFInputOptions( oWrap.attr( 'data-id' ) );  

            // Increment the ids of the next all (including this copied element) sub-fields.
            var iOccurrence = 1 === iCallType ? 1 : 0;                        
            oNextFieldContainer.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function( iIndex ) {

                var oWrap               = jQuery( this ).find( '.wp-editor-wrap' );
                if ( oWrap.length <= 0 ) {                       
                    return true;    // continue
                }        
                
                var oTextArea           = jQuery( this ).find( 'textarea.wp-editor-area' ).first().show().removeAttr( 'aria-hidden' );
                var oEditorContainer    = jQuery( this ).find( '.wp-editor-container' ).first().clone().empty();
                var oToolBar            = jQuery( this ).find( '.wp-editor-tools' ).first().clone();
                var oTextAreaPrevious   = oTextArea.clone().incrementIDAttribute( 'id', iOccurrence );
                
                // Replace the tinyMCE wrapper with the plain textarea tag element.
                oWrap.empty()
                    .prepend( oEditorContainer.prepend( oTextArea.show() ) )
                    .prepend( oToolBar );   

                // Remove the editor which is assigned to the newly decremented ID if exists and the old assigned editor.
                if ( 0 === iIndex ) {
                    removeEditor( oTextAreaPrevious.attr( 'id' ) );
                }

                updateEditor( oTextArea.attr( 'id' ), oSettings['TinyMCE'], oSettings['QuickTags'] );
                                          
                // The ID attributes of sub-elements are not updated yet
                oToolBar.find( 'a,div' ).decrementIDAttribute( 'id', iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap a' ).decrementIDAttribute( 'data-editor', iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).decrementIDAttribute( 'id', iOccurrence );

                // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
                if ( 0 === iCallType ) {
                    jQuery( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );
                }
                
                // If this is called for a repeatable section, handle only the first iteration as the rest will be also called one by one.
                if ( 1 === iCallType ) {
                    return false;   // break
                }

            });                            
            
        },
        /**
         * The sortable field callback for the sort update event.
         * 
         * On contrary to repeatable fields callbacks, the _fields_ container element object and its ID will be passed.
         * 
         * @param object    oSortedFields   the sorted fields container element.
         * @param string    sFieldType      the field type slug
         * @param string    sFieldTagID     the field container tag ID
         * @param integer   iCallType       the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */
        stopped_sorting_fields : function( oSortedFields, sFieldType, sFieldsTagID, iCallType ) { 

            if ( ! isHandleable( oSortedFields, sFieldType ) ) {
                return;
            }                     
            
            // Update the editor.
            var iOccurrence = 1 === iCallType ? 1 : 0; // the occurrence value indicates which part of digit to change 
            oSortedFields.children( '.admin-page-framework-field' ).each( function( iIndex ) {
                                            
                /* If the textarea tag is not found, do nothing  */
                var oTextAreas = jQuery( this ).find( 'textarea.wp-editor-area' );
                if ( oTextAreas.length <= 0 ) {
                    return true;
                }                    
                
                // Find the tinyMCE wrapper element
                var oWrap       = jQuery( this ).find( '.wp-editor-wrap' );
                if ( oWrap.length <= 0 ) {
                    return true;
                }                                   

                // Retrieve the TinyMCE and Quick Tags settings. The enabler script stores the original element id.
                var oSettings = jQuery().getAPFInputOptions( oWrap.attr( 'data-id' ) );   

                var oTextArea           = jQuery( this ).find( 'textarea.wp-editor-area' ).first().show().removeAttr( 'aria-hidden' );
                var oEditorContainer    = jQuery( this ).find( '.wp-editor-container' ).first().clone().empty();
                var oToolBar            = jQuery( this ).find( '.wp-editor-tools' ).first().clone();
                
                // Replace the tinyMCE wrapper with the plain textarea tag element.
                oWrap.empty()
                    .prepend( oEditorContainer.prepend( oTextArea.show() ) )
                    .prepend( oToolBar );   

                
                updateEditor( oTextArea.attr( 'id' ), oSettings['TinyMCE'], oSettings['QuickTags'] );

                // The ID attributes of sub-elements are not updated yet
                oToolBar.find( 'a,div' ).setIndexIDAttribute( 'id', iIndex, iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap a' ).setIndexIDAttribute( 'data-editor', iIndex, iOccurrence );
                jQuery( this ).find( '.wp-editor-wrap,.wp-editor-tools,.wp-editor-container' ).setIndexIDAttribute( 'id', iIndex, iOccurrence );

                // Switch the tab to the visual editor. This will trigger the switch action on the both of the tabs as clicking on only the Visual tab did not work.
                jQuery( this ).find( 'a.wp-switch-editor' ).trigger( 'click' );
                                                                    
            });
            
        },
        /**
         * The saved widget callback.
         * 
         * It is called when a widget is saved.
         */
        saved_widget : function( oWidget ) { 
        
             // If tinyMCE is not ready, return.
            if ( 'object' !== typeof tinyMCEPreInit ){
                return;
            }       

            var _sWidgetInitialTextareaID;
            jQuery( oWidget ).find( '.admin-page-framework-field' ).each( function( iIndex ) {
                                            
                /* If the textarea tag is not found, do nothing  */
                var oTextAreas = jQuery( this ).find( 'textarea.wp-editor-area' );
                if ( oTextAreas.length <= 0 ) {
                    return true;
                }                    
                
                // Find the tinyMCE wrapper element
                var oWrap       = jQuery( this ).find( '.wp-editor-wrap' );
                if ( oWrap.length <= 0 ) {
                    return true;
                }                                   

                // Retrieve the TinyMCE and Quick Tags settings from the initial widget form element. The initial widget is the one from which the user drags.
                var oTextArea  = jQuery( this ).find( 'textarea.wp-editor-area' ).first(); // .show().removeAttr( 'aria-hidden' );
                var _sID                  = oTextArea.attr( 'id' );
                var _sInitialTextareaID   = _sID.replace( /(widget-.+-)([0-9]+)(-)/i, '$1__i__$3' );
                _sWidgetInitialTextareaID = 'undefined' === typeof  tinyMCEPreInit.mceInit[ _sInitialTextareaID ]
                    ? _sWidgetInitialTextareaID 
                    : _sInitialTextareaID;
                if ( 'undefined' === typeof  tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ] ) {
                    return true;
                }
                
                updateEditor( 
                    oTextArea.attr( 'id' ), 
                    tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ],
                    tinyMCEPreInit.qtInit[ _sWidgetInitialTextareaID ]
                );          

                // Store the settings.
                jQuery().storeAPFInputOptions( 
                    oWrap.attr( 'data-id' ), 
                    { 
                        TinyMCE:    tinyMCEPreInit.mceInit[ _sWidgetInitialTextareaID ],
                        QuickTags:  tinyMCEPreInit.qtInit[ _sWidgetInitialTextareaID ]
                    } 
                );                            
            });                                          
        
        }   // end of 'saved_widget'
    });	        
});
JAVASCRIPTS;

    }    
    
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Textarea Field Type */
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
/* For meta-boxes */
.postbox .admin-page-framework-field-textarea .admin-page-framework-input-label-container {
    width: 100%;
}
CSSRULES;

    }    
        
    /**
     * Returns the output of the 'textarea' input field.
     * 
     * @since       2.1.5
     * @since       3.0.0       Removed redundant elements including parameters.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

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
                            . esc_textarea( $aField['value'] )
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
            $_sScript = <<<JAVASCRIPTS
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
JAVASCRIPTS;
            return "<script type='text/javascript' class='admin-page-framework-textarea-enabler'>"
                    . $_sScript
                . "</script>";            
        }    
        
}