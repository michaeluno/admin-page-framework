<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the image field type.
 * 
 * @package AdminPageFramework
 * @subpackage FieldType
 * @since 2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType_Base {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'image', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes_to_store'       => array(), // ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
        'show_preview'              => true,    // ( boolean ) Indicates whether the image preview should be displayed or not.
        'allow_external_source'     => true,    // ( boolean ) Indicates whether the media library box has the From URL tab.
        'attributes'                => array(
            'input'     => array(
                'size'      => 40,
                'maxlength' => 400,     
            ),
            'button'            => array(
            ),
            'remove_button'     => array(       // 3.2.0+
            ),
            'preview'           => array(),
        ),    
    );

    /**
     * Loads the field type necessary components.
     */ 
    public function _replyToFieldLoader() {     
        $this->enqueueMediaUploader();
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    public function _replyToGetScripts() {     
        return // $this->_getScript_CustomMediaUploaderObject() . PHP_EOL    
            $this->_getScript_ImageSelector( 
                "admin_page_framework"
            )  . PHP_EOL
            . $this->_getScript_RegisterCallbacks();
    }
        /**
         * Returns the JavaScript script that handles repeatable events. 
         * 
         * @since 3.0.0
         */
        protected function _getScript_RegisterCallbacks() {

            $_aJSArray = json_encode( $this->aFieldTypeSlugs );
            /* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
                renew the upload button and the preview elements (while it does on the input tag value), the renewal task must be dealt here separately. */
            return <<<JAVASCRIPTS
jQuery( document ).ready( function(){

    jQuery().registerAPFCallback( {     
        /**
         * The repeatable field callback for the add event.
         * 
         * @param object    node
         * @param string    sFieldType      the field type slug
         * @param string    sFieldTagID     the field container tag ID
         * @param integer   iCallerType     the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */
        added_repeatable_field: function( node, sFieldType, sFieldTagID, iCallType ) {
            
            /* If it is not the image field type, do nothing. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { return; }
                                
            /* If the uploader buttons are not found, do nothing */
            if ( node.find( '.select_image' ).length <= 0 ) { return; }
            
            /* Remove the value of the cloned preview element - check the value for repeatable sections */
            var sValue = node.find( 'input' ).first().val();
            if ( 1 !== iCallType || ! sValue ) { // if it's not for repeatable sections
                node.find( '.image_preview' ).hide(); // for the image field type, hide the preview element
                node.find( '.image_preview img' ).attr( 'src', '' ); // for the image field type, empty the src property for the image uploader field
            }
            
            /* Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
            var nodeFieldContainer = node.closest( '.admin-page-framework-field' );
            var iOccurrence = 1 === iCallType ? 1 : 0;
            nodeFieldContainer.nextAll().andSelf().each( function( iIndex ) {

                var nodeButton = jQuery( this ).find( '.select_image' );     
                
                // If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
                if ( ! ( 1 === iCallType && 0 !== iIndex ) ) {
                        
                    nodeButton.incrementIDAttribute( 'id', iOccurrence );
                    jQuery( this ).find( '.image_preview' ).incrementIDAttribute( 'id', iOccurrence );
                    jQuery( this ).find( '.image_preview img' ).incrementIDAttribute( 'id', iOccurrence );
                    
                }
                
                /* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
                 * otherwise, the script sets the preview image in the wrong place. */     
                var nodeImageInput = jQuery( this ).find( '.image-field input' );
                if ( nodeImageInput.length <= 0 ) return true;
                
                var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
                setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );    

            });
        },
        /**
         * The repeatable field callback for the remove event.
         * 
         * @param object    oNextFieldContainer     the field container element next to the removed field container.
         * @param string    sFieldType              the field type slug
         * @param string    sFieldTagID             the field container tag ID
         * @param integer   iCallType               the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */     
        removed_repeatable_field: function( oNextFieldContainer, sFieldType, sFieldTagID, iCallType ) {
            
            /* If it is not the color field type, do nothing. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { return; }
                                
            /* If the uploader buttons are not found, do nothing */
            if ( oNextFieldContainer.find( '.select_image' ).length <= 0 ) { return; }
            
            /* Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
            var iOccurrence = 1 === iCallType ? 1 : 0; // the occurrence value indicates which part of digit to change 
            oNextFieldContainer.nextAll().andSelf().each( function( iIndex ) {
                
                var nodeButton = jQuery( this ).find( '.select_image' );     
                
                // If it's for repeatable sections, updating the attributes is only necessary for the first iteration.
                if ( ! ( 1 === iCallType && 0 !== iIndex ) ) {     
                    nodeButton.decrementIDAttribute( 'id', iOccurrence );
                    jQuery( this ).find( '.image_preview' ).decrementIDAttribute( 'id', iOccurrence );
                    jQuery( this ).find( '.image_preview img' ).decrementIDAttribute( 'id', iOccurrence );
                }
                
                /* Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
                 * otherwise, the script sets the preview image in the wrong place. */     
                var nodeImageInput = jQuery( this ).find( '.image-field input' );
                if ( nodeImageInput.length <= 0 ) { return true; }
                
                var fExternalSource = jQuery( nodeButton ).attr( 'data-enable_external_source' );
                setAPFImageUploader( nodeImageInput.attr( 'id' ), true, fExternalSource );    
            
            });
            
        },
        sorted_fields : function( node, sFieldType, sFieldsTagID, iCallType ) { // on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

            /* 1. Return if it is not the type. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { return; } /* If it is not the color field type, do nothing. */     
            if ( node.find( '.select_image' ).length <= 0 ) { return; } /* If the uploader buttons are not found, do nothing */
            
            /* 2. Update the Select File button */
            var iCount = 0;
            var iOccurrence = 1 === iCallType ? 1 : 0; // the occurrence value indicates which part of digit to change 
            node.children( '.admin-page-framework-field' ).each( function() {
                
                var nodeButton = jQuery( this ).find( '.select_image' );
                
                /* 2-1. Set the current iteration index to the button ID, and the image preview elements */
                nodeButton.setIndexIDAttribute( 'id', iCount, iOccurrence );    
                jQuery( this ).find( '.image_preview' ).setIndexIDAttribute( 'id', iCount, iOccurrence );
                jQuery( this ).find( '.image_preview img' ).setIndexIDAttribute( 'id', iCount, iOccurrence );
                
                /* 2-2. Rebind the uploader script to the button */
                var nodeImageInput = jQuery( this ).find( '.image-field input' );
                if ( nodeImageInput.length <= 0 ) { return true; }
                setAPFImageUploader( nodeImageInput.attr( 'id' ), true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );

                iCount++;
            });
        }
    });
});
JAVASCRIPTS;
            
        }
        
        /**
         * Returns the image selector JavaScript script to be loaded in the head tag of the created admin pages.
         * 
         * @var         string
         * @remark      It is accessed from the main class and meta box class.
         * @remark      Moved to the base class since 2.1.0.
         * @access      private    
         * @internal
         * @return      string      The image selector script.
         * @since       2.0.0
         * @since       2.1.5       Moved from the AdminPageFramework_Property_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
         * @since       2.4.2       Remved the second an the thir parameter as additional message items need to be defined.
         */     
        private function _getScript_ImageSelector( $sReferrer ) {
                            
            $_sThickBoxTitle         = esc_js( $this->oMsg->get( 'upload_image' ) );
            $_sThickBoxButtonUseThis = esc_js( $this->oMsg->get( 'use_this_image' ) );
            $_sInsertFromURL         = esc_js( $this->oMsg->get( 'insert_from_url' ) );

            // if the WordPress version is 3.4.x or below
            if ( ! function_exists( 'wp_enqueue_media' ) ) {
            
                return <<<JAVASCRIPTS
/**
 * Bind/rebinds the thickbox script the given selector element.
 * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
 */
setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {
    jQuery( '#select_image_' + sInputID ).unbind( 'click' ); // for repeatable fields
    jQuery( '#select_image_' + sInputID ).click( function() {
        var sPressedID                  = jQuery( this ).attr( 'id' );     
        window.sInputID                 = sPressedID.substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.
        window.original_send_to_editor  = window.send_to_editor;
        window.send_to_editor           = hfAPFSendToEditorImage;
        var fExternalSource             = jQuery( this ).attr( 'data-enable_external_source' );
        tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
        return false; // do not click the button after the script by returning false.     
    });    
}     

var hfAPFSendToEditorImage = function( sRawHTML ) {

    var sHTML       = '<div>' + sRawHTML + '</div>'; // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
    var src         = jQuery( 'img', sHTML ).attr( 'src' );
    var alt         = jQuery( 'img', sHTML ).attr( 'alt' );
    var title       = jQuery( 'img', sHTML ).attr( 'title' );
    var width       = jQuery( 'img', sHTML ).attr( 'width' );
    var height      = jQuery( 'img', sHTML ).attr( 'height' );
    var classes     = jQuery( 'img', sHTML ).attr( 'class' );
    var id          = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : ''; // attachment ID    
    var sCaption    = sRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
        .replace( /<a.*?>(.*?)<\/a>/m, '' );
    var align       = sRawHTML.replace( /^.*?\[\w+.*?\salign=([\'\"])(.*?)[\'\"]\s.+$/mg, '$2' ); //\'\" syntax fixer
    var link        = jQuery( sHTML ).find( 'a:first' ).attr( 'href' );

    // Escape the strings of some of the attributes.
    var sCaption    = jQuery( '<div/>' ).text( sCaption ).html();
    var sAlt        = jQuery( '<div/>' ).text( alt ).html();
    var title       = jQuery( '<div/>' ).text( title ).html();     

    // If the user wants to save relevant attributes, set them.
    var sInputID    = window.sInputID; // window.sInputID should be assigned when the thickbox is opened.

    jQuery( '#' + sInputID ).val( src ); // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
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
    jQuery( '#image_preview_' + sInputID ).attr( 'src', src ); // updates the preview image
    jQuery( '#image_preview_container_' + sInputID ).css( 'display', '' ); // updates the visibility
    jQuery( '#image_preview_' + sInputID ).show() // updates the visibility
    
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
setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

    var _bEscaped = false; // indicates whether the frame is escaped/canceled.
    var _oCustomImageUploader;

    // The input element.
    jQuery( '#' + sInputID + '[data-show_preview=\"1\"]' ).unbind( 'change' ); // for repeatable fields
    jQuery( '#' + sInputID + '[data-show_preview=\"1\"]' ).change( function( e ) {
        
        var _sImageURL = jQuery( this ).val();
        
        // Check if it is a valid image url.
        jQuery( '<img>', {
            src: _sImageURL,
            error: function() {},
            load: function() { 
                // if valid,  set the preview.
                setImagePreviewElement( 
                    sInputID, 
                    { 
                        url: _sImageURL 
                    } 
                );
            }
        });
        
        
    } );
    
    // The Select button element.
    jQuery( '#select_image_' + sInputID ).unbind( 'click' ); // for repeatable fields
    jQuery( '#select_image_' + sInputID ).click( function( e ) {
     
        // Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
        var sInputID = jQuery( this ).attr( 'id' ).substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.
        
        window.wpActiveEditor = null;     
        e.preventDefault();
        
        // If the uploader object has already been created, reopen the dialog
        if ( 'object' === typeof _oCustomImageUploader ) {
            _oCustomImageUploader.open();
            return;
        }     

        // Store the original select object in a global variable
        oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
        
        // Assign a custom select object
        wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
        _oCustomImageUploader = wp.media({
            id:         sInputID,
            title:      fExternalSource ? '{$_sInsertFromURL}' : '{$_sThickBoxTitle}',
            button:     {
                text: '{$_sThickBoxButtonUseThis}'
            },       
            type:       'image', 
            library:    { type : 'image' },                             
            multiple:   fMultiple,  // Set this to true to allow multiple files to be selected
            metadata:   {},
        });
        
        
        // When the uploader window closes, 
        _oCustomImageUploader.on( 'escape', function() {
            _bEscaped = true;
            return false;
        });
        _oCustomImageUploader.on( 'close', function() {
 
            var state = _oCustomImageUploader.state();     
            // Check if it's an external URL
            if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) {
                
                // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
                var _oImage = {}, _sKey;
                for ( _sKey in state.props.attributes ) {
                    _oImage[ _sKey ] = state.props.attributes[ _sKey ];
                }      
                
            }
            
            // If the _oImage variable is not defined at this point, it's an attachment, not an external URL.
            if ( typeof( _oImage ) !== 'undefined'  ) {
                setImagePreviewElementWithDelay( sInputID, _oImage );
          
            } else {
                
                var _oNewField;
                _oCustomImageUploader.state().get( 'selection' ).each( function( oAttachment, iIndex ) {

                    var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                        ? oAttachment.attributes
                        : {};
                    
                    if ( 0 === iIndex ){    
                        // place first attachment in the field
                        setImagePreviewElementWithDelay( sInputID, _oAttributes );
                        return true;
                    } 

                    var _oFieldContainer    = 'undefined' === typeof _oNewField 
                        ? jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' ) 
                        : _oNewField;
                    _oNewField              = jQuery( this ).addAPFRepeatableField( _oFieldContainer.attr( 'id' ) );
                    var sInputIDOfNewField  = _oNewField.find( 'input' ).attr( 'id' );
                    setImagePreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
                    
                });     
                
            }
            
            // Restore the original select object.
            wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
                            
        });
      
        // Open the uploader dialog
        _oCustomImageUploader.open();
        return false;
        
    });    

    var setImagePreviewElementWithDelay = function( sInputID, oImage, iMilliSeconds ) {
 
        iMilliSeconds = 'undefined' === typeof iMilliSeconds ? 100 : iMilliSeconds;
           
        setTimeout( function (){
            if ( ! _bEscaped ) {
                setImagePreviewElement( sInputID, oImage );
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
removeInputValuesForImage = function( oElem ) {

    var _oImageInput = jQuery( oElem ).closest( '.admin-page-framework-field' ).find( '.image-field input' );                  
    if ( _oImageInput.length <= 0 )  {
        return;
    }
    
    // Find the input tag.
    var _sInputID = _oImageInput.first().attr( 'id' );
    
    // Remove the associated values.
    setImagePreviewElement( _sInputID, {} );
    
}

/**
 * Sets the preview element.
 * 
 * @since   3.2.0   Changed the scope to global.
 */
setImagePreviewElement = function( sInputID, oImage ) {

    var oImage      = jQuery.extend( 
        true,   // recursive
        { 
            caption:    '',  
            alt:        '',
            title:      '',
            url:        '',
            id:         '',
            width:      '',
            height:     '',
            align:      '',
            link:       '',
        },
        oImage
    );    

    // Escape the strings of some of the attributes.
    var _sCaption   = jQuery( '<div/>' ).text( oImage.caption ).html();
    var _sAlt       = jQuery( '<div/>' ).text( oImage.alt ).html();
    var _sTitle     = jQuery( '<div/>' ).text( oImage.title ).html();

    // If the user wants the attributes to be saved, set them in the input tags.
    jQuery( 'input#' + sInputID ).val( oImage.url ); // the url field is mandatory so it does not have the suffix.
    jQuery( 'input#' + sInputID + '_id' ).val( oImage.id );
    jQuery( 'input#' + sInputID + '_width' ).val( oImage.width );
    jQuery( 'input#' + sInputID + '_height' ).val( oImage.height );
    jQuery( 'input#' + sInputID + '_caption' ).val( _sCaption );
    jQuery( 'input#' + sInputID + '_alt' ).val( _sAlt );
    jQuery( 'input#' + sInputID + '_title' ).val( _sTitle );
    jQuery( 'input#' + sInputID + '_align' ).val( oImage.align );
    jQuery( 'input#' + sInputID + '_link' ).val( oImage.link );
    
    // Update up the preview
    jQuery( '#image_preview_' + sInputID ).attr( 'data-id', oImage.id );
    jQuery( '#image_preview_' + sInputID ).attr( 'data-width', oImage.width );
    jQuery( '#image_preview_' + sInputID ).attr( 'data-height', oImage.height );
    jQuery( '#image_preview_' + sInputID ).attr( 'data-caption', _sCaption );
    jQuery( '#image_preview_' + sInputID ).attr( 'alt', _sAlt );
    jQuery( '#image_preview_' + sInputID ).attr( 'title', _sTitle );
    jQuery( '#image_preview_' + sInputID ).attr( 'src', oImage.url );
    if ( oImage.url ) {
        jQuery( '#image_preview_container_' + sInputID ).show();     
    } else {
        jQuery( '#image_preview_container_' + sInputID ).hide();     
    }
    
}                
JAVASCRIPTS;

        }
    
    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
        return <<<CSSRULES
/* Image Field Preview Container */
.admin-page-framework-field .image_preview {
    border: none; 
    clear:both; 
    margin-top: 0.4em;
    margin-bottom: 0.8em;
    display: block; 
    max-width: 100%;
    height: auto;   
    width: inherit;                
}     

.admin-page-framework-field .image_preview img {     
    height: auto; 
    max-width: 100%;
    display: block;         
}
.widget .admin-page-framework-field .image_preview {
    max-width: 100%;
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

/* Image Uploader Input Field */
.admin-page-framework-field-image input {
    margin-right: 0.5em;
    vertical-align: middle;    
}
/* Image Uploader Button */
.select_image.button.button-small,
.remove_image.button.button-small
{     
    vertical-align: middle;
}
.remove_image.button.button-small {
    margin-left: 0.2em;
}
@media screen and (max-width: 782px) {
    .admin-page-framework-field-image input {
        margin: 0.5em 0.5em 0.5em 0;
    }
}     
CSSRULES;

    }
    
    /**
     * Returns the output of the field type.
     * 
     * @since   2.1.5
     * @since   3.0.0   Reconstructed entirely.
     */
    public function _replyToGetField( $aField ) {
        
        /* Local variables */
        $_aOutput = array();
        $_iCountAttributes = count( ( array ) $aField['attributes_to_store'] ); // If the saving extra attributes are not specified, the input field will be single only for the URL. 
        $_sCaptureAttribute = $_iCountAttributes ? 'url' : '';
        $_sImageURL = $_sCaptureAttribute
                ? ( isset( $aField['attributes']['value'][ $_sCaptureAttribute ] ) ? $aField['attributes']['value'][ $_sCaptureAttribute ] : "" )
                : $aField['attributes']['value'];
        
        /* Set up the attribute arrays */
        $_aBaseAttributes = $aField['attributes'] + array( 'class' => null );
        unset( $_aBaseAttributes['input'], $_aBaseAttributes['button'], $_aBaseAttributes['preview'], $_aBaseAttributes['name'], $_aBaseAttributes['value'], $_aBaseAttributes['type'], $_aBaseAttributes['remove_button'] );
        $_aInputAttributes = array(
            'name'              => $aField['attributes']['name'] . ( $_iCountAttributes ? "[url]" : "" ),
            'value'             => $_sImageURL,
            'type'              => 'text',
            'data-show_preview' => $aField['show_preview'], // 3.4.2+ Referenced to bind an input update event to the preview updater script.
        ) + $aField['attributes']['input'] + $_aBaseAttributes;
        $_aButtonAtributes          = $aField['attributes']['button'] + $_aBaseAttributes;
        $_aRemoveButtonAtributes    = $aField['attributes']['remove_button'] + $_aBaseAttributes;
        $_aPreviewAtrributes        = $aField['attributes']['preview'] + $_aBaseAttributes;

        /* Construct the field output */
        $_aOutput[] =
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField['type']}-field'>" // image-field ( this will be media-field for the media field type )
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $_aInputAttributes ) . " />" // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                    . $this->getExtraInputFields( $aField )
                . "</label>"
            . "</div>"     
            . $aField['after_label']
            . $this->_getPreviewContainer( $aField, $_sImageURL, $_aPreviewAtrributes )
            . $this->_getRemoveButtonScript( $aField['input_id'], $_aRemoveButtonAtributes )
            . $this->_getUploaderButtonScript( $aField['input_id'], $aField['repeatable'], $aField['allow_external_source'], $_aButtonAtributes )
        ;
        
        return implode( PHP_EOL, $_aOutput );
        
    }
        /**
         * Returns extra input fields to set capturing attributes.
         * @since 3.0.0
         */
        protected function getExtraInputFields( &$aField ) {
            
            // Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
            $_aOutputs = array();
            foreach( ( array ) $aField['attributes_to_store'] as $sAttribute )
                $_aOutputs[] = "<input " . $this->generateAttributes( 
                        array(
                            'id'        => "{$aField['input_id']}_{$sAttribute}",
                            'type'      => 'hidden',
                            'name'      => "{$aField['_input_name']}[{$sAttribute}]",
                            'disabled'  => isset( $aField['attributes']['disabled'] ) && $aField['attributes']['disabled'] ? 'disabled' : null,
                            'value'     => isset( $aField['attributes']['value'][ $sAttribute ] ) ? $aField['attributes']['value'][ $sAttribute ] : '',
                        )
                    ) . "/>";
            return implode( PHP_EOL, $_aOutputs );
            
        }
        
        /**
         * Returns the output of the preview box.
         * @since 3.0.0
         */
        protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {

            if ( ! $aField['show_preview'] ) { return ''; }
            
            $sImageURL = $this->resolveSRC( $sImageURL, true );
            return 
                "<div " . $this->generateAttributes( 
                        array(
                            'id' => "image_preview_container_{$aField['input_id']}",     
                            'class' => 'image_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : '' ),
                            'style' => ( $sImageURL ? '' : "display: none; "  ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ),
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
         * @since   2.1.3
         * @since   2.1.5   Moved from AdminPageFramework_FormField.
         * @since   3.2.0   Made it use dashicon for the select image button.
         * @remark  This class is extended by the media field type and this method will be overridden. So the scope needs to be protected rather than private.
         */
        protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) {
            
            $_bIsLabelSet           = isset( $aButtonAttributes['data-label'] ) && $aButtonAttributes['data-label'];
            $_bDashiconSupported    = ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' );            
            $_sDashIconSelector     = ! $_bDashiconSupported ? '' : ( $bRpeatable ? 'dashicons dashicons-images-alt2' : 'dashicons dashicons-format-image' ); 
            $_aAttributes           = array(
                    'id'        => "select_image_{$sInputID}",
                    'href'      => '#',            
                    'data-uploader_type'            => function_exists( 'wp_enqueue_media' ) ? 1 : 0,
                    'data-enable_external_source'   => $bExternalSource ? 1 : 0,                    
                ) 
                + $aButtonAttributes
                + array(
                    'title'     => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get( 'select_image' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'select_image button button-small ',
                trim( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : $_sDashIconSelector
            );            
            $_sButton = 
                "<a " . $this->generateAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $aButtonAttributes['data-label'] 
                        : ( strrpos( $_aAttributes['class'], 'dashicons' ) 
                            ? '' 
                            : $this->oMsg->get( 'select_image' )
                        )
                    )                    
                ."</a>";
            // Do not include the escaping character (backslash) in the heredoc variable declaration 
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.                
            $_sButtonHTML = '"' . $_sButton . '"';
            $_sScript = <<<JAVASCRIPTS
if ( 0 === jQuery( 'a#select_image_{$sInputID}' ).length ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){     
    setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
});
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-image-uploader-button'>" 
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
                'id'        => "remove_image_{$sInputID}",
                'href'      => '#',            
                'onclick'   => esc_js( "removeInputValuesForImage( this ); return false;" ),
                ) 
                + $aButtonAttributes
                + array(
                    'title' => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get( 'remove_value' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'remove_value remove_image button button-small', 
                trim( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : $_sDashIconSelector
            );
            $_sButtonHTML               = 
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
            $_sButtonHTML  = '"' . $_sButtonHTML . '"';
            $_sScript = <<<JAVASCRIPTS
                if ( 0 === jQuery( 'a#remove_image_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
JAVASCRIPTS;
                    
            return "<script type='text/javascript' class='admin-page-framework-image-remove-button'>" 
                    . $_sScript 
                . "</script>". PHP_EOL;
           
        }
        
}