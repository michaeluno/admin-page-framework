<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field with a media uploader lets the user set a file URL.
 *
 * This class defines the media field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
 *     <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
 *     <li>**attributes** - [3.2.0+] (optional, boolean) there are additional nested attribute arguments.
 *         <ul>
 *             <li>`button` - (array) applies to the Select File button.</li>
 *             <li>`remove_button` - (array) applies to the Remove button.</li>
 *         </ul>
 *     </li>
 *
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 ** <code>
 *   array(
 *       'field_id'              => 'media_with_attributes',
 *       'title'                 => __( 'Media File with Attributes', 'admin-page-framework-loader' ),
 *       'type'                  => 'media',
 *       'attributes_to_store'   => array( 'id', 'caption', 'description' ),
 *       'attributes'            => array(
 *           'button'        => array(
 *               'data-label' => __( 'Select File', 'admin-page-framework-loader' ),
 *           ),
 *           'remove_button' => array(      // 3.2.0+
 *               'data-label' => __( 'Remove', 'admin-page-framework-loader' ), // will set the Remove button label instead of the dashicon
 *           ),
 *       ),
 *   )
 ** </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/media.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @extends     AdminPageFramework_FieldType_image
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
     * Returns the field type specific JavaScript script.
     * @internal
     * @return      string
     */
    protected function getScripts() {
        return
            $this->_getScript_MediaUploader(
                "admin_page_framework"
            ) . PHP_EOL
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
            /* The below JavaScript functions are a callback triggered when a new repeatable field is added and removed. Since the APF repeater script does not
                renew the upload button (while it does on the input tag value), the renewal task must be dealt here separately. */
            return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
            
    jQuery().registerAdminPageFrameworkCallbacks( {    
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                           
            // Update attributes.
            oCloned.find( '.select_media' ).incrementAttribute(
                'id', // attribute name
                aModel[ 'incremented_from' ], // index incremented from
                aModel[ 'id' ] // digit model
            );   
            
            // Bind the event.
            var _oMediaInput = oCloned.find( '.media-field input' );
            if ( _oMediaInput.length <= 0 ) {
                return true;
            }
            setAdminPageFrameworkMediaUploader( 
                _oMediaInput.attr( 'id' ), 
                true, 
                oCloned.find( '.select_media' ).attr( 'data-enable_external_source' )
            );                      
            
        },    
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;

        }

        /**
         * Returns the media uploader JavaScript script to be loaded in the head tag of the created admin pages.
         *
         * @since       2.1.3
         * @since       2.1.5       Moved from ... Changed the name from `getMediaUploaderScript()`.
         * @since       2.4.2       Removed the second an the their parameter as additional message items need to be defined.
         * @internal
         * @return      string
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
                    setAdminPageFrameworkMediaUploader = function( sInputID, fMultiple, fExternalSource ) {
                        jQuery( '#select_media_' + sInputID ).off( 'click' ); // for repeatable fields
                        jQuery( '#select_media_' + sInputID ).on( 'click', function() {
                            var sPressedID = jQuery( this ).attr( 'id' );
                            window.sInputID = sPressedID.substring( 13 ); // remove the select_media_ prefix and set a property to pass it to the editor callback method.
                            window.original_send_to_editor = window.send_to_editor;
                            window.send_to_editor = hfAdminPageFrameworkSendToEditorMedia;
                            var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
                            tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
                            return false; // do not click the button after the script by returning false.     
                        });    
                    }     
                                                    
                    var hfAdminPageFrameworkSendToEditorMedia = function( sRawHTML, param ) {

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
                setAdminPageFrameworkMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

                    var _bEscaped = false;
                    var _oMediaUploader;
                    
                    jQuery( '#select_media_' + sInputID ).off( 'click' ); // for repeatable fields
                    jQuery( '#select_media_' + sInputID ).on( 'click', function( e ) {
                
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
                        oAdminPageFrameworkOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
                        
                        // Assign a custom select object.
                        wp.media.view.MediaFrame.Select = fExternalSource ? getAdminPageFrameworkCustomMediaUploaderSelectObject() : oAdminPageFrameworkOriginalMediaUploaderSelectObject;
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
                                    _oNewField              = jQuery( this ).addAdminPageFrameworkRepeatableField( _oFieldContainer.attr( 'id' ) );
                                    var sInputIDOfNewField  = _oNewField.find( 'input' ).attr( 'id' );
                                    setMediaPreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
                                
                                });     
                                
                            }
                            
                            // Restore the original select object.
                            wp.media.view.MediaFrame.Select = oAdminPageFrameworkOriginalMediaUploaderSelectObject;    
                            
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
    protected function getStyles() {

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
         * Returns the output of the preview box.
         * @since       3.0.0
         * @return      string
         */
        protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) { return ""; }

        /**
         * Returns a `<script>` tag element with a JavaScript script that enables media select buttons.
         *
         * @since       2.1.3
         * @since       2.1.5   Moved from AdminPageFramework_FormField.
         * @since       3.2.0   Made it use dashicon for the select button.
         * @return      string
         * @internal
         */
        protected function _getUploaderButtonScript( $sInputID, $abRepeatable, $bExternalSource, array $aButtonAttributes ) {

            // Do not include the escaping character (backslash) in the heredoc variable declaration
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML       = '"' . $this->_getUploaderButtonHTML_Media( $sInputID, $aButtonAttributes, $bExternalSource ) . '"';
            $_sRpeatable        = $this->getAOrB( ! empty( $abRepeatable ), 'true', 'false' );
            $_sExternalSource   = $this->getAOrB( $bExternalSource, 'true', 'false' );
            $_sScript                = <<<JAVASCRIPTS
if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){   
    setAdminPageFrameworkMediaUploader( '{$sInputID}', 'true' === '{$_sRpeatable}', 'true' === '{$_sExternalSource}' );
});
JAVASCRIPTS;

            return "<script type='text/javascript' class='admin-page-framework-media-uploader-button'>"
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
            private function _getUploaderButtonHTML_Media( $sInputID, array $aButtonAttributes, $bExternalSource ) {

                $_bIsLabelSet = isset( $aButtonAttributes['data-label'] ) && $aButtonAttributes['data-label'];
                $_aAttributes = $this->_getFormattedUploadButtonAttributes_Media(
                    $sInputID,
                    $aButtonAttributes,
                    $_bIsLabelSet,
                    $bExternalSource
                );
                return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                        . $this->getAOrB(
                            $_bIsLabelSet,
                            $_aAttributes['data-label'],
                            $this->getAOrB(
                                strrpos( $_aAttributes['class'], 'dashicons' ),
                                '',
                                $this->oMsg->get( 'select_file' )
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
                private function _getFormattedUploadButtonAttributes_Media( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $bExternalSource ) {

                    $_aAttributes           = array(
                            'id'        => "select_media_{$sInputID}",
                            'href'      => '#',
                            'data-uploader_type'            => ( string ) function_exists( 'wp_enqueue_media' ),    //  ? 1 : 0,
                            'data-enable_external_source'   => ( string ) ( bool ) $bExternalSource,    //  ? 1 : 0,
                        )
                        + $aButtonAttributes
                        + array(
                            'title'     => $_bIsLabelSet
                                ? $aButtonAttributes['data-label']
                                : $this->oMsg->get( 'select_file' ),
                            'data-label' => null,
                        );
                    $_aAttributes['class']  = $this->getClassAttribute(
                        'select_media button button-small ',
                        $this->getAOrB(
                            trim( $aButtonAttributes['class'] ),
                            $aButtonAttributes['class'],
                            $this->getAOrB(
                                ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' ),
                                'dashicons dashicons-portfolio',
                                ''
                            )
                        )
                    );
                    return $_aAttributes;

                }

}
