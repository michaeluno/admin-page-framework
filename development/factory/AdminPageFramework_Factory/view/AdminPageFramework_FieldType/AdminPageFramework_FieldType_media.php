<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the media field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_media extends AdminPageFramework_FieldType_image {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'media', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes_to_store'   => array(), // ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
        'show_preview'          =>    true,
        'allow_external_source' =>    true, // ( boolean ) Indicates whether the media library box has the From URL tab.
        'attributes'            => array(
            'input'     => array(
                'size'      => 40,
                'maxlength' => 400,     
            ),
            'button'    => array(
            ),
            'remove_button' =>  array(  // 3.2.0+
            ),
            'preview'   => array(
            ),     
        ),    
    );
    
    /**
     * Loads the field type necessary components.
     */ 
    public function _replyToFieldLoader() {
        parent::_replyToFieldLoader();
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    public function _replyToGetScripts() {
        return // $this->_getScript_CustomMediaUploaderObject() . PHP_EOL // defined in the parent class
            $this->_getScript_MediaUploader(
                "admin_page_framework"
            ) . PHP_EOL
            . $this->_getScript_RegisterCallbacks();
    }    
    
        /**
         * Returns the JavaScript script that handles repeatable events. 
         * 
         * @since 3.0.0
         */
        protected function _getScript_RegisterCallbacks() {

            $_aJSArray = json_encode( $this->aFieldTypeSlugs );
            /* The below JavaScript functions are a callback triggered when a new repeatable field is added and removed. Since the APF repeater script does not
                renew the upload button (while it does on the input tag value), the renewal task must be dealt here separately. */
            return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
            
    jQuery().registerAPFCallback( {    
        /**
         * The repeatable field callback for the add event.
         * 
         * @param object node
         * @param string    the field type slug
         * @param string    the field container tag ID
         * @param integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */     
        added_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
            
            /* 1. Return if it is not the type. */     
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) return; /* If it is not the media field type, do nothing. */
            if ( node.find( '.select_media' ).length <= 0 )  return; /* If the uploader buttons are not found, do nothing */
            
            /* 2. Increment the ids of the next all (including this one) uploader buttons  */
            var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
            var iOccurence = iCallType === 1 ? 1 : 0;
            nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {

                /* 2-1. Increment the button ID */
                nodeButton = jQuery( this ).find( '.select_media' );
                
                // If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
                if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {
                    nodeButton.incrementIDAttribute( 'id', iOccurence );
                }
                
                /* 2-2. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
                 * otherwise, the script sets the preview image in the wrong place. */     
                var nodeMediaInput = jQuery( this ).find( '.media-field input' );
                if ( nodeMediaInput.length <= 0 ) return true;
                setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
                
            });     
        },
        /**
         * The repeatable field callback for the remove event.
         * 
         * @param object    the field container element next to the removed field container.
         * @param string    the field type slug
         * @param string    the field container tag ID
         * @param integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */     
        removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID, iCallType ) {
            
            /* 1. Return if it is not the type. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) return; /* If it is not the color field type, do nothing. */
            if ( oNextFieldConainer.find( '.select_media' ).length <= 0 )  return; /* If the uploader buttons are not found, do nothing */
            
            /* 2. Decrement the ids of the next all (including this one) uploader buttons. ( the input values are already dealt by the framework repeater script ) */
            var iOccurence = iCallType === 1 ? 1 : 0; // the occurrence value indicates which part of digit to change 
            oNextFieldConainer.nextAll().andSelf().each( function( iIndex ) {
                
                /* 2-1. Decrement the button ID */
                nodeButton = jQuery( this ).find( '.select_media' );     

                // If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
                if ( ! ( iCallType === 1 && iIndex !== 0 ) ) {     
                    nodeButton.decrementIDAttribute( 'id', iOccurence );
                }
                                            
                /* 2-2. Rebind the uploader script to each button. */
                var nodeMediaInput = jQuery( this ).find( '.media-field input' );
                if ( nodeMediaInput.length <= 0 ) return true;
                setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
            });
        },    
        sorted_fields : function( node, sFieldType, sFieldsTagID ) { // on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

            /* 1. Return if it is not the type. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) return; /* If it is not the color field type, do nothing. */     
            if ( node.find( '.select_media' ).length <= 0 )  return; /* If the uploader buttons are not found, do nothing */
            
            /* 2. Update the Select File button */
            var iCount = 0;
            node.children( '.admin-page-framework-field' ).each( function() {
                
                nodeButton = jQuery( this ).find( '.select_media' );
                
                /* 2-1. Set the current iteration index to the button ID */
                nodeButton.setIndexIDAttribute( 'id', iCount );    
                
                /* 2-2. Rebuind the uploader script to the button */
                var nodeMediaInput = jQuery( this ).find( '.media-field input' );
                if ( nodeMediaInput.length <= 0 ) return true;
                setAPFMediaUploader( nodeMediaInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );

                iCount++;
            });
        }
        
    });
});
JAVASCRIPTS;
            
        }
        
        /**
         * Returns the media uploader JavaScript script to be loaded in the head tag of the created admin pages.
         * 
         * @since       2.1.3
         * @since       2.1.5       Moved from ... Chaned the name from getMediaUploaderScript().
         * @since       2.4.2       Remved the second an the thir parameter as additional message items need to be defined.
         */
        private function _getScript_MediaUploader( $sReferrer ) {

            $_sThickBoxTitle         = esc_js( $this->oMsg->get( 'upload_file' ) );
            $_sThickBoxButtonUseThis = esc_js( $this->oMsg->get( 'use_this_file' ) );
            $_sInsertFromURL         = esc_js( $this->oMsg->get( 'insert_from_url' ) );
            
            // If the WordPress version is 3.4.x or below
            if ( ! function_exists( 'wp_enqueue_media' ) ) {
                return <<<JAVASCRIPTS
                    /**
                     * Bind/rebinds the thickbox script the given selector element.
                     * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
                     */
                    setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {
                        jQuery( '#select_media_' + sInputID ).unbind( 'click' ); // for repeatable fields
                        jQuery( '#select_media_' + sInputID ).click( function() {
                            var sPressedID = jQuery( this ).attr( 'id' );
                            window.sInputID = sPressedID.substring( 13 ); // remove the select_media_ prefix and set a property to pass it to the editor callback method.
                            window.original_send_to_editor = window.send_to_editor;
                            window.send_to_editor = hfAPFSendToEditorMedia;
                            var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
                            tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
                            return false; // do not click the button after the script by returning false.     
                        });    
                    }     
                                                    
                    var hfAPFSendToEditorMedia = function( sRawHTML, param ) {

                        var sHTML = '<div>' + sRawHTML + '</div>'; // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
                        var src = jQuery( 'a', sHTML ).attr( 'href' );
                        var classes = jQuery( 'a', sHTML ).attr( 'class' );
                        var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : ''; // attachment ID    
                    
                        // If the user wants to save relavant attributes, set them.
                        var sInputID = window.sInputID;
                        jQuery( '#' + sInputID ).val( src ); // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
                        jQuery( '#' + sInputID + '_id' ).val( id );     
                            
                        // restore the original send_to_editor
                        window.send_to_editor = window.original_send_to_editor;
                        
                        // close the thickbox
                        tb_remove();    

                    }
JAVASCRIPTS;
            }
            
            return <<<JAVASCRIPTS
                // Global Function Literal 
                /**
                 * Binds/rebinds the uploader button script to the specified element with the given ID.
                 */     
                setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

                    var _bEscaped = false;
                    var _oMediaUploader;
                    
                    jQuery( '#select_media_' + sInputID ).unbind( 'click' ); // for repeatable fields
                    jQuery( '#select_media_' + sInputID ).click( function( e ) {
                
                        // Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
                        var sInputID = jQuery( this ).attr( 'id' ).substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.

                        window.wpActiveEditor = null;     
                        e.preventDefault();
                        
                        // If the uploader object has already been created, reopen the dialog
                        if ( 'object' === typeof _oMediaUploader ) {
                            _oMediaUploader.open();
                            return;
                        }     
                        
                        // Store the original select object in a global variable
                        oAPFOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
                        
                        // Assign a custom select object.
                        wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalMediaUploaderSelectObject;
                        _oMediaUploader = wp.media({
                            title:      fExternalSource
                                ? '{$_sInsertFromURL}'
                                : '{$_sThickBoxTitle}',
                            button:     {
                                text: '{$_sThickBoxButtonUseThis}'
                            },
                            multiple:   fMultiple, // Set this to true to allow multiple files to be selected
                            metadata:   {},
                        });
            
                        // When the uploader window closes, 
                        _oMediaUploader.on( 'escape', function() {
                            _bEscaped = true;
                            return false;
                        });    
                        _oMediaUploader.on( 'close', function() {

                            var state = _oMediaUploader.state();
                            
                            // Check if it's an external URL
                            if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) {

                                // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
                                var _oMedia = {}, _sKey;
                                for ( _sKey in state.props.attributes ) {
                                    _oMedia[ _sKey ] = state.props.attributes[ _sKey ];
                                }      
                                
                            }
                            
                            // If the image variable is not defined at this point, it's an attachment, not an external URL.
                            if ( typeof( _oMedia ) !== 'undefined'  ) {
                                setMediaPreviewElementWithDelay( sInputID, _oMedia );
                            } else {
                                
                                var _oNewField;
                                _oMediaUploader.state().get( 'selection' ).each( function( oAttachment, iIndex ) {

                                    var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                                        ? oAttachment.attributes
                                        : {};                                    
                                    
                                    if( 0 === iIndex ){    
                                        // place first attachment in field
                                        setMediaPreviewElementWithDelay( sInputID, _oAttributes );
                                        return true;
                                    } 
                                        
                                    var _oFieldContainer    = 'undefined' === typeof _oNewField 
                                        ? jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' ) 
                                        : _oNewField;
                                    _oNewField              = jQuery( this ).addAPFRepeatableField( _oFieldContainer.attr( 'id' ) );
                                    var sInputIDOfNewField  = _oNewField.find( 'input' ).attr( 'id' );
                                    setMediaPreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
                                
                                });     
                                
                            }
                            
                            // Restore the original select object.
                            wp.media.view.MediaFrame.Select = oAPFOriginalMediaUploaderSelectObject;    
                            
                        });
                        
                        // Open the uploader dialog
                        _oMediaUploader.open();     
                        return false;       
                    });    
                
                
                    var setMediaPreviewElementWithDelay = function( sInputID, oImage, iMilliSeconds ) {
                        
                        iMilliSeconds = 'undefiend' === typeof iMilliSeconds ? 100 : iMilliSeconds;
                        setTimeout( function (){
                            if ( ! _bEscaped ) {
                                setMediaPreviewElement( sInputID, oImage );
                            }
                            _bEscaped = false;
                        }, iMilliSeconds );
                        
                    }
                    
                }   

                /**
                 * Removes the set values to the input tags.
                 * 
                 * @since   3.2.0
                 */
                removeInputValuesForMedia = function( oElem ) {

                    var _oImageInput = jQuery( oElem ).closest( '.admin-page-framework-field' ).find( '.media-field input' );                  
                    if ( _oImageInput.length <= 0 )  {
                        return;
                    }
                    
                    // Find the input tag.
                    var _sInputID = _oImageInput.first().attr( 'id' );
                    
                    // Remove the associated values.
                    setMediaPreviewElement( _sInputID, {} );
                    
                }
                
                /**
                 * Sets the preview element.
                 * 
                 * @since   3.2.0   Changed the scope to global.
                 */                
                setMediaPreviewElement = function( sInputID, oSelectedFile ) {
                                
                    // If the user want the attributes to be saved, set them in the input tags.
                    jQuery( '#' + sInputID ).val( oSelectedFile.url ); // the url field is mandatory so  it does not have the suffix.
                    jQuery( '#' + sInputID + '_id' ).val( oSelectedFile.id );     
                    jQuery( '#' + sInputID + '_caption' ).val( jQuery( '<div/>' ).text( oSelectedFile.caption ).html() );     
                    jQuery( '#' + sInputID + '_description' ).val( jQuery( '<div/>' ).text( oSelectedFile.description ).html() );     
                    
                }                 
JAVASCRIPTS;

        }
    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
        
        return <<<CSSRULES
/* Media Uploader Button */
.admin-page-framework-field-media input {
    margin-right: 0.5em;
    vertical-align: middle;    
}
@media screen and (max-width: 782px) {
    .admin-page-framework-field-media input {
        margin: 0.5em 0.5em 0.5em 0;
    }
}     
.select_media.button.button-small,
.remove_media.button.button-small
{     
    vertical-align: middle;
}
.remove_media.button.button-small {
    margin-left: 0.2em;
}            
CSSRULES;
    }
    
    /**
     * Returns the output of the field type.
     * 
     * @since 2.1.5
     */
    public function _replyToGetField( $aField ) {
        return parent::_replyToGetField( $aField );
    }
        
        /**
         * Returns the output of the preview box.
         * @since 3.0.0
         */
        protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) { return ""; }
        
        /**
         * A helper function for the above getImageInputTags() method to add a image button script.
         * 
         * @since   2.1.3
         * @since   2.1.5   Moved from AdminPageFramework_FormField.
         * @since   3.2.0   Made it use dashicon for the select button.
         */     
        protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) {

            $_bIsLabelSet           = isset( $aButtonAttributes['data-label'] ) && $aButtonAttributes['data-label'];
            $_bDashiconSupported    = ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' );            
            $_sDashIconSelector     = ! $_bDashiconSupported ? '' : 'dashicons dashicons-portfolio';
            $_aAttributes           = array(
                    'id'        => "select_media_{$sInputID}",
                    'href'      => '#',            
                    'data-uploader_type'            => function_exists( 'wp_enqueue_media' ) ? 1 : 0,
                    'data-enable_external_source'   => $bExternalSource ? 1 : 0,                    
                ) 
                + $aButtonAttributes
                + array(
                    'title'     => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get( 'select_file' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'select_media button button-small ',
                trim( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : $_sDashIconSelector
            );            
            $_sButton = 
                "<a " . $this->generateAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $aButtonAttributes['data-label'] 
                        : ( strrpos( $_aAttributes['class'], 'dashicons' ) 
                            ? '' 
                            : $this->oMsg->get( 'select_file' )
                        )
                    )                    
                ."</a>";
            // Do not include the escaping character (backslash) in the heredoc variable declaration 
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML = '"' . $_sButton . '"';
            $_sScript                = <<<JAVASCRIPTS
                if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
                jQuery( document ).ready( function(){     
                    setAPFMediaUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
                });
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-media-uploader-button'>" 
                    . $_sScript 
                . "</script>". PHP_EOL;

        }

        /**
         * Removes the set image values and attributes.
         * 
         * @since   3.2.0
         */
        protected function _getRemoveButtonScript( $sInputID, array $aButtonAttributes ) {
           
            if ( ! function_exists( 'wp_enqueue_media' ) ) {
                return '';
            }
           
            $_bIsLabelSet           = isset( $aButtonAttributes['data-label'] ) && $aButtonAttributes['data-label'];
            $_bDashiconSupported    = ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' );
            $_sDashIconSelector     = $_bDashiconSupported ? 'dashicons dashicons-dismiss' : '';           
            $_aAttributes           = array(
                'id'        => "remove_media_{$sInputID}",
                'href'      => '#',            
                'onclick'   => esc_js( "removeInputValuesForMedia( this ); return false;" ),
                ) 
                + $aButtonAttributes
                + array(
                    'title' => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get( 'remove_value' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'remove_value remove_media button button-small', 
                trim( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : $_sDashIconSelector
            );
            $_sButton               = 
                "<a " . $this->generateAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $_aAttributes['data-label'] 
                        : ( strrpos( $_aAttributes['class'], 'dashicons' ) 
                            ? '' 
                            : 'x'
                        )
                    )
                . "</a>";
            // Do not include the escaping character (backslash) in the heredoc variable declaration 
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML = '"' . $_sButton . '"';
            $_sScript = <<<JAVASCRIPTS
                if ( 0 === jQuery( 'a#remove_media_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-media-remove-button'>" 
                    . $_sScript 
                . "</script>". PHP_EOL;
           
        }     
        
}