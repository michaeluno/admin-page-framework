<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field with an image uploader.
 *
 * This class defines the image field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *      <li>**show_preview** - (optional, boolean) if this is set to false, the image preview will be disabled.</li>
 *      <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
 *      <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
 *      <li>**attributes** - [3.0.0+] (optional, array) there are additional nested arguments.
 *          <ul>
 *              <li>`input` - (array) applies to the input tag element.</li>
 *              <li>`preview` - (array) applies to the preview container element.</li>
 *              <li>`button` - (array) applies to the image select (uploader) button. To set a custom text label instead on of an image icon, set it to the `data-label` attribute. e.g. `'button' => array( 'data-label' => 'Select Image' )`</li>
 *              <li>`remove_button` - (array) [3.2.0+] applies to the remove-image button. To set a custom text label instead on of an image icon, set it to the `data-label` attribute. e.g. `'remove_button' => array( 'data-label' => 'Remove Image' )`</li>
 *          </ul>
 *      </li>
 * </ul>
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'image_select_field',
 *      'title'         => __( 'Select an Image', 'admin-page-framework-loader' ),
 *      'type'          => 'image',
 *      'label'         => __( 'First', 'admin-page-framework-loader' ),
 *      'default'       =>  plugins_url( 'asset/image/demo/wordpress-logo-2x.png', AdminPageFrameworkLoader_Registry::$sFilePath ),
 *      'allow_external_source' => false,
 *      'attributes'    => array(
 *          'preview' => array(
 *              'style' => 'max-width:300px;' // the size of the preview image.
 *          ),
 *      )
 *  )
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/image.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @since       3.5.3       Changed it to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @extends     AdminPageFramework_FieldType
 */
class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType {

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
     * @internal
     */
    protected function setUp() {
        $this->enqueueMediaUploader();
    }

    /**
     * Returns the field type specific JavaScript script.
     * @internal
     */
    protected function getScripts() {
        return // $this->_getScript_CustomMediaUploaderObject() . PHP_EOL
            $this->_getScript_ImageSelector(
                "admin_page_framework"
            )  . PHP_EOL
            . $this->_getScript_RegisterCallbacks();
    }
        /**
         * Returns the JavaScript script that handles repeatable events.
         *
         * @since       3.0.0
         * @return      string
         * @internal
         */
        protected function _getScript_RegisterCallbacks() {

            $_aJSArray = json_encode( $this->aFieldTypeSlugs );
            /* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
                renew the upload button and the preview elements (while it does on the input tag value), the renewal task must be dealt here separately. */
            return <<<JAVASCRIPTS
jQuery( document ).ready( function(){

    jQuery().registerAdminPageFrameworkCallbacks( { 
        
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                                                
            // Remove the value of the cloned preview element - check the value for repeatable sections.
            var sValue = oCloned.find( 'input' ).first().val();
            if ( 1 !== aModel[ 'call_type' ] || ! sValue ) { // if it's not for repeatable sections
                oCloned.find( '.image_preview' ).hide(); // for the image field type, hide the preview element
                oCloned.find( '.image_preview img' ).attr( 'src', '' ); // for the image field type, empty the src property for the image uploader field
            }                        
                        
            // Increment element IDs.
            oCloned.find( '.image_preview, .image_preview img, .select_image' ).incrementAttribute(
                'id', // attribute name
                aModel[ 'incremented_from' ], // index incremented from
                aModel[ 'id' ] // digit model
            );            
            
            // Bind the event.
            var _oFieldContainer = oCloned.closest( '.admin-page-framework-field' );
            var _oSelectButton   = _oFieldContainer.find( '.select_image' );            
            var _oImageInput     = _oFieldContainer.find( '.image-field input' );
            if ( _oImageInput.length <= 0 ) {
                return true;
            }           

            setAdminPageFrameworkImageUploader( 
                _oImageInput.attr( 'id' ), 
                true, 
                _oSelectButton.attr( 'data-enable_external_source' )
            );                              
            
        },
    },
    $_aJSArray
    );
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
         * @return      string      The image selector script.
         * @since       2.0.0
         * @since       2.1.5       Moved from the AdminPageFramework_Property_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
         * @since       2.4.2       Removed the second an the their parameter as additional message items need to be defined.
         * @internal
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
setAdminPageFrameworkImageUploader = function( sInputID, fMultiple, fExternalSource ) {
    jQuery( '#select_image_' + sInputID ).off( 'click' ); // for repeatable fields
    jQuery( '#select_image_' + sInputID ).on( 'click', function() {
        var sPressedID                  = jQuery( this ).attr( 'id' );     
        window.sInputID                 = sPressedID.substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.
        window.original_send_to_editor  = window.send_to_editor;
        window.send_to_editor           = hfAdminPageFrameworkSendToEditorImage;
        var fExternalSource             = jQuery( this ).attr( 'data-enable_external_source' );
        tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
        return false; // do not click the button after the script by returning false.     
    });    
}     

var hfAdminPageFrameworkSendToEditorImage = function( sRawHTML ) {

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
setAdminPageFrameworkImageUploader = function( sInputID, fMultiple, fExternalSource ) {

    var _bEscaped = false; // indicates whether the frame is escaped/cancelled.
    var _oCustomImageUploader;

    // The input element.
    jQuery( '#' + sInputID + '[data-show_preview=\"1\"]' ).off( 'change' ); // for repeatable fields
    jQuery( '#' + sInputID + '[data-show_preview=\"1\"]' ).on( 'change', function( e ) {
        
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
    jQuery( '#select_image_' + sInputID ).off( 'click' ); // for repeatable fields
    jQuery( '#select_image_' + sInputID ).on( 'click', function( e ) {
     
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
        oAdminPageFrameworkOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
        
        // Assign a custom select object
        wp.media.view.MediaFrame.Select = fExternalSource ? getAdminPageFrameworkCustomMediaUploaderSelectObject() : oAdminPageFrameworkOriginalImageUploaderSelectObject;
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
                    _oNewField              = jQuery( this ).addAdminPageFrameworkRepeatableField( _oFieldContainer.attr( 'id' ) );
                    var sInputIDOfNewField  = _oNewField.find( 'input' ).attr( 'id' );
                    setImagePreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
                    
                });     
                
            }
            
            // Restore the original select object.
            wp.media.view.MediaFrame.Select = oAdminPageFrameworkOriginalImageUploaderSelectObject;
                            
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
     * @internal
     */
    protected function getStyles() {
        return <<<CSSRULES
/* Image Field Preview Container */
.admin-page-framework-field .image_preview {
    border: none; 
    clear: both; 
    margin-top: 0.4em;
    margin-bottom: 0.8em;
    display: block;     
    max-width: 100%;
    height: auto;   
    width: inherit;                
}     
.admin-page-framework-field .image_preview img {     
    display: block;  
    height: auto; 
    max-width: 100%;
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
     * @since   3.5.3   Changed the name from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {

        // If the saving extra attributes are not specified, the input field will be single only for the URL.
        $_iCountAttributes  = count( $this->getElementAsArray( $aField, 'attributes_to_store' ) );
        $_sImageURL         = $this->_getTheSetImageURL( $aField, $_iCountAttributes );
        $_aBaseAttributes   = $this->_getBaseAttributes( $aField );

        // Output
        return
            $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField[ 'type' ]}-field'>" // image-field ( this will be media-field for the media field type )
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . $this->getAOrB(
                        $aField[ 'label' ] && ! $aField[ 'repeatable' ],
                        "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                            . $aField[ 'label' ]
                        . "</span>",
                        ''
                    )
                    . "<input " . $this->getAttributes( $this->_getImageInputAttributes( $aField, $_iCountAttributes, $_sImageURL, $_aBaseAttributes ) ) . " />"
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                    . $this->getExtraInputFields( $aField )
                . "</label>"
            . "</div>"
            . $aField[ 'after_label' ]
            . $this->_getPreviewContainer(
                $aField,
                $_sImageURL,
                // Preview container attributes
                $this->getElementAsArray( $aField, array( 'attributes', 'preview' ) )
                + $_aBaseAttributes
            )
            . $this->_getRemoveButtonScript(
                $aField[ 'input_id' ],
                // Remove button atributes
                $this->getElementAsArray( $aField, array( 'attributes', 'remove_button' ) )
                + $_aBaseAttributes,
                $aField[ 'type' ] // image
            )
            . $this->_getUploaderButtonScript(
                $aField[ 'input_id' ],
                $aField[ 'repeatable' ],
                $aField[ 'allow_external_source' ],
                // Uploader button attributes
                $this->getElementAsArray( $aField, array( 'attributes', 'button' ) )
                + $_aBaseAttributes
            )
        ;

    }
        /**
         * Returns a base attribute array.
         * @since       3.5.3
         * @return      array       The generated base attribute array.
         * @internal
         */
        private function _getBaseAttributes( array $aField ) {

            $_aBaseAttributes   = $aField[ 'attributes' ] + array( 'class' => null );
            unset(
                $_aBaseAttributes[ 'input' ],
                $_aBaseAttributes[ 'button' ],
                $_aBaseAttributes[ 'preview' ],
                $_aBaseAttributes[ 'name' ],
                $_aBaseAttributes[ 'value' ],
                $_aBaseAttributes[ 'type' ],
                $_aBaseAttributes[ 'remove_button' ]
            );
            return $_aBaseAttributes;

        }
       /**
         * Returns the set image url.
         *
         * When the 'attributes_to_store' argument is present, there will be sub elements to the field value.
         * This method checks that and returns the set (stored) image url.
         *
         * This value will be used for the preview container as well.
         *
         * @since       3.5.3
         * @return      string      The found image url.
         * @internal
         */
        private function _getTheSetImageURL( array $aField, $iCountAttributes ) {

            $_sCaptureAttribute = $this->getAOrB( $iCountAttributes, 'url', '' );
            return $_sCaptureAttribute
                ? $this->getElement( $aField, array( 'attributes', 'value', $_sCaptureAttribute ), '' )
                : $aField[ 'attributes' ][ 'value' ];


        }
        /**
         * Returns an image field input attribute for the url input tag.
         * @since       3.5.3
         * @return      array
         * @internal
         */
        private function _getImageInputAttributes( array $aField, $iCountAttributes, $sImageURL, array $aBaseAttributes ) {

            return array(
                'name'              => $aField[ 'attributes' ][ 'name' ]
                    . $this->getAOrB( $iCountAttributes, '[url]', '' ),
                'value'             => $sImageURL,
                'type'              => 'text',

                // 3.4.2+ Referenced to bind an input update event to the preview updater script.
                'data-show_preview' => $aField[ 'show_preview' ],
            )
            + $aField[ 'attributes' ][ 'input' ]
            + $aBaseAttributes;

        }

        /**
         * Returns extra input fields to set capturing attributes.
         *
         * This adds input fields for saving extra attributes.
         * It overrides the name attribute of the default text field for URL and saves them as an array.
         *
         * @since       3.0.0
         * @return      string
         * @internal
         */
        protected function getExtraInputFields( array $aField ) {

            $_aOutputs = array();
            foreach( $this->getElementAsArray( $aField, 'attributes_to_store' ) as $sAttribute ) {
                $_aOutputs[] = "<input " . $this->getAttributes(
                    array(
                        'id'        => "{$aField[ 'input_id' ]}_{$sAttribute}",
                        'type'      => 'hidden',
                        'name'      => "{$aField[ '_input_name' ]}[{$sAttribute}]",
                        'disabled'  => $this->getAOrB(
                            isset( $aField[ 'attributes' ][ 'disabled' ] ) && $aField[ 'attributes' ][ 'disabled' ],
                            'disabled',
                            null
                        ),
                        'value'     => $this->getElement(
                            $aField,
                            array( 'attributes', 'value', $sAttribute ),
                            ''
                        ),
                    )
                ) . "/>";
            }
            return implode( PHP_EOL, $_aOutputs );

        }

        /**
         * Returns the output of the preview box.
         * @since   3.0.0
         * @internal
         */
        protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {

            if ( ! $aField[ 'show_preview' ] ) {
                return '';
            }

            $sImageURL = esc_url( $this->getResolvedSRC( $sImageURL, true ) );
            return
                "<div " . $this->getAttributes(
                        array(
                            'id'    => "image_preview_container_{$aField[ 'input_id' ]}",
                            'class' => 'image_preview ' . $this->getElement( $aPreviewAtrributes, 'class', '' ),
                            'style' => $this->getAOrB( $sImageURL, '', "display: none; "  )
                                . $this->getElement( $aPreviewAtrributes, 'style', '' ),
                        ) + $aPreviewAtrributes
                    )
                . ">"
                    . "<img src='{$sImageURL}' "
                        . "id='image_preview_{$aField[ 'input_id' ]}' "
                    . "/>"
                . "</div>";

        }

        /**
         * A helper function for the above getImageInputTags() method to add a image button script.
         *
         * @since       2.1.3
         * @since       2.1.5   Moved from AdminPageFramework_FormField.
         * @since       3.2.0   Made it use dashicon for the select image button.
         * @remark      This class is extended by the media field type and this method will be overridden. So the scope needs to be protected rather than private.
         * @internal
         */
        protected function _getUploaderButtonScript( $sInputID, $abRepeatable, $bExternalSource, array $aButtonAttributes ) {

            $_bRepeatable     = ! empty( $abRepeatable );

            // Do not include the escaping character (backslash) in the heredoc variable declaration
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML     = '"' . $this->_getUploaderButtonHTML( $sInputID, $aButtonAttributes, $_bRepeatable, $bExternalSource ) . '"';
            $_sRepeatable     = $this->getAOrB( $_bRepeatable, 'true', 'false' );
            $_bExternalSource = $this->getAOrB( $bExternalSource, 'true', 'false' );
            $_sScript = <<<JAVASCRIPTS
if ( 0 === jQuery( 'a#select_image_{$sInputID}' ).length ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){     
    setAdminPageFrameworkImageUploader( '{$sInputID}', 'true' === '{$_sRepeatable}', 'true' === '{$_bExternalSource}' );
});
JAVASCRIPTS;

            return "<script type='text/javascript' class='admin-page-framework-image-uploader-button'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>". PHP_EOL;

        }
            /**
             * Returns an HTML output of an uploader button.
             * @since       3.5.3
             * @return      string      The generated HTML uploader button output.
             * @internal
             */
            private function _getUploaderButtonHTML( $sInputID, array $aButtonAttributes, $bRepeatable, $bExternalSource ) {

                $_bIsLabelSet = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
                $_aAttributes = $this->_getFormattedUploadButtonAttributes(
                    $sInputID,
                    $aButtonAttributes,
                    $_bIsLabelSet,
                    $bRepeatable,
                    $bExternalSource
                );
                return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                        . ( $_bIsLabelSet
                            ? $_aAttributes[ 'data-label' ]
                            : ( strrpos( $_aAttributes[ 'class' ], 'dashicons' )
                                ? ''
                                : $this->oMsg->get( 'select_image' )
                            )
                        )
                    ."</a>";

            }
                /**
                 * Returns a formatted upload button attributes array.
                 * @since       3.5.3
                 * @return      array       The formatted upload button attributes array.
                 * @internal
                 */
                private function _getFormattedUploadButtonAttributes( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $bRepeatable, $bExternalSource ) {

                    $_aAttributes           = array(
                            'id'        => "select_image_{$sInputID}",
                            'href'      => '#',
                            'data-uploader_type'            => ( string ) function_exists( 'wp_enqueue_media' ),
                            'data-enable_external_source'   => ( string ) ( bool ) $bExternalSource, // ? 1 : 0,
                        )
                        + $aButtonAttributes
                        + array(
                            'title'     => $_bIsLabelSet
                                ? $aButtonAttributes[ 'data-label' ]
                                : $this->oMsg->get( 'select_image' ),
                            'data-label' => null,
                        );
                    $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                        'select_image button button-small ',
                        $this->getAOrB(
                            trim( $aButtonAttributes[ 'class' ] ),
                            $aButtonAttributes[ 'class' ],
                            $this->getAOrB(
                                $_bIsLabelSet,
                                '',
                                $this->getAOrB(
                                    $bRepeatable,
                                    $this->_getDashIconSelectorsBySlug( 'images-alt2' ),
                                    $this->_getDashIconSelectorsBySlug( 'format-image' )
                                )
                            )
                        )
                    );
                    return $_aAttributes;

                }

        /**
         * Removes the set image values and attributes.
         *
         * @since       3.2.0
         * @since       3.5.3       Added the `$sType` parameter.
         * @return      string
         * @internal
         */
        protected function _getRemoveButtonScript( $sInputID, array $aButtonAttributes, $sType='image' ) {

            if ( ! function_exists( 'wp_enqueue_media' ) ) {
                return '';
            }

            // Do not include the escaping character (backslash) in the heredoc variable declaration
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML  = '"' . $this->_getRemoveButtonHTMLByType( $sInputID, $aButtonAttributes, $sType ) . '"';
            $_sScript = <<<JAVASCRIPTS
                if ( 0 === jQuery( 'a#remove_{$sType}_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
JAVASCRIPTS;

            return "<script type='text/javascript' class='admin-page-framework-{$sType}-remove-button'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>". PHP_EOL;

        }

        /**
         * Returns an HTML output of a remove button.
         * @since       3.5.3
         * @return      string      The generated HTML remove button output.
         * @internal
         */
        protected function _getRemoveButtonHTMLByType( $sInputID, array $aButtonAttributes, $sType='image' ) {

            $_bIsLabelSet   = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
            $_aAttributes   = $this->_getFormattedRemoveButtonAttributesByType( $sInputID, $aButtonAttributes, $_bIsLabelSet, $sType );
            return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $_aAttributes[ 'data-label' ]
                        : $this->getAOrB(
                            strrpos( $_aAttributes[ 'class' ], 'dashicons' ),
                            '',
                            'x'
                        )
                    )
                . "</a>";

        }

            /**
             * Returns a formatted remove button attributes array.
             * @since       3.5.3
             * @return      array       The formatted remove button attributes array.
             * @internal
             */
            protected function _getFormattedRemoveButtonAttributesByType( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $sType='image' ) {

                $_sOnClickFunctionName  = 'removeInputValuesFor' . ucfirst( $sType );
                $_aAttributes           = array(
                        'id'        => "remove_{$sType}_{$sInputID}",
                        'href'      => '#',
                        'onclick'   => esc_js( "{$_sOnClickFunctionName}( this ); return false;" ),
                    )
                    + $aButtonAttributes
                    + array(
                        'title' => $_bIsLabelSet
                            ? $aButtonAttributes[ 'data-label' ]
                            : $this->oMsg->get( 'remove_value' ),
                    );
                $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                    "remove_value remove_{$sType} button button-small",
                    $this->getAOrB(
                        trim( $aButtonAttributes[ 'class' ] ),
                        $aButtonAttributes[ 'class' ],
                        $this->getAOrB(
                            $_bIsLabelSet,
                            '',
                            $this->_getDashIconSelectorsBySlug( 'dismiss' )
                        )
                    )
                );
                return $_aAttributes;

            }

        /**
         * Returns a set of dash-icon selectors by the given dash-icon slug.
         *
         * It checks whether the WordPress version is enough to support dash-icons.
         *
         * @since       3.5.3
         * @return      string      The generated class selectors.
         * @internal
         */
        private function _getDashIconSelectorsBySlug( $sDashIconSlug ) {

            static $_bDashIconSupported;

            $_bDashIconSupported = isset( $_bDashIconSupported )
                ? $_bDashIconSupported
                : version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' );

            return $this->getAOrB(
                $_bDashIconSupported,
                "dashicons dashicons-{$sDashIconSlug}",
                ''
            );

        }

}
