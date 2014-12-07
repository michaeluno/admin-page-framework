<?php
if ( ! class_exists( 'FontCustomFieldType' ) ) :
class FontCustomFieldType extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'font', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes_to_store'   => array(), // ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
        'show_preview'          => true,    // ( boolean )
        'allow_external_source' => true,    // ( boolean ) Indicates whether the media library box has the From URL tab.
        'preview_text'          => 'The quick brown fox jumps over the lazy dog. Foxy parsons quiz and cajole the lovably dim wiki-girl. Watch “Jeopardy!”, Alex Trebek’s fun TV quiz game. How razorback-jumping frogs can level six piqued gymnasts! All questions asked by five watched experts — amaze the judge.',
        'attributes'            => array(
            'input'         => array(
                'size'      => 40,    
                'maxlength' => 400,
            ),
            'preview'       => array(),
            'button'        => array(),
            'remove_button' => array(), // 3.2.0+
        ),    
    );

    /**
     * The user constructor.
     * 
     * @remark  This must be instantiated also in in ...wp-admin/async-upload.php to modify the allowed mime types.
     */
    protected function construct() {
            
        add_filter( 'upload_mimes', array( $this, 'replyToFilterUploadMimes' ) );

    }
        /**
         * This allows several file types to be uploaded with the WordPress media uploader.
         * 
         */
        public function replyToFilterUploadMimes( $aMimes ) {                        
            $aMimes[ 'eot' ]    = 'application/vnd.ms-fontobject';
            $aMimes[ 'ttf' ]    = 'application/x-font-ttf';
            $aMimes[ 'otf' ]    = 'font/opentype';
            $aMimes[ 'woff' ]   = 'application/font-woff';
            $aMimes[ 'svg' ]    = 'image/svg+xml';
            return $aMimes;                        
        }

    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {
        
        $this->enqueueMediaUploader();    // defined in the parent class.

    }    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array(
            array( 'src' => dirname( __FILE__ ) . '/js/setFontPreview.js', 'dependencies' => array( 'jquery' ) ),
            array( 'src' => dirname( __FILE__ ) . '/js/jquery.nouislider.js', 'dependencies' => array( 'jquery-ui-core' ) ),            
        );
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array(
            dirname( __FILE__ ) . '/css/font-field-type.css',
            dirname( __FILE__ ) . '/css/jquery.nouislider.css',        
        ); 
    }            

    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 
        return // $this->_getScript_CustomMediaUploaderObject() . PHP_EOL
            $this->getScript_FontSelector(
                "admin_page_framework"
            ) . PHP_EOL
            . $this->getScript_CreateSlider()
            . $this->getScript_RepeatableFields();
    }
    
    protected function getScript_CreateSlider() {
        
        return "
            createFontSizeChangeSlider = function( sInputID ) {
                var _sSliderID = 'slider_' + sInputID;
                var _sSliderContainerID = 'slider_container_' + sInputID;
                return '<div class=\"fontSliderHolder\" id=\"' + _sSliderContainerID + '\" >'
                    + '<div class=\"sliderT\">A</div>'
                    + '<div class=\"holder\"><div id=\"' + _sSliderID + '\" class=\"noUiSlider\"></div></div>'
                    + '<div class=\"sliderB\">A</div>'
                + '</div>';
            }
        ";
        
    }
    
    protected function getScript_RepeatableFields() {
            
        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        /*    The below function will be triggered when a new repeatable field is added. */
        return "
            jQuery( document ).ready( function(){
                jQuery().registerAPFCallback( {                
                    added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
            
                        /* 1. Return if not for this field type */
                        if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) return;    // if it is not this field type
                        if ( node.find( '.font-field input' ).length <= 0 ) return;    // if the input tag is not found, do nothing
                
                        /* 2. Increment the ids of the next all (including this one) uploader buttons and the preview elements ( the input values are already dealt by the framework repeater script ) */
                        node.closest( '.admin-page-framework-field' ).nextAll().andSelf().each( function() {
                            
                            /* 2-1. Check if the parsing node holds necessary elements. */
                            var nodeFontInput = jQuery( this ).find( '.font-field input' );
                            if ( nodeFontInput.length <= 0 ) return true;
                                                        
                            /* 2-2. Deal with three elements: the Select Font button, the preview box, the preview font size change slider. */
                            nodeButton = jQuery( this ).find( '.select_font' );                            
                            nodeButton.incrementIDAttribute( 'id' );
                            jQuery( this ).find( '.font_preview' ).incrementIDAttribute( 'id' );
                            jQuery( this ).find( '.font-preview-text' ).incrementIDAttribute( 'id' );
                            jQuery( this ).find( '.fontSliderHolder' ).incrementIDAttribute( 'id' );
                            jQuery( this ).find( '.noUiSlider' ).incrementIDAttribute( 'id' );
                            
                            /* 2-3. Rebind functions to each element and update the associated properties. */
                            
                            /* 2-3-1. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
                             * otherwise, the script sets the preview image in the wrong place. */
                            var sInputID = nodeFontInput.attr( 'id' );                                 
                            setAPFFontUploader( sInputID, true, nodeButton.attr( 'data-enable_external_source' ) );    
                            
                            /* 2-3-2. Update the font-family style of the preview box. */
                            jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );
            
                            /* 2-3-3. Rebind the noUiSlider script to the font-size changer slider. */
                            jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
                            jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
                                range: [ 100, 300 ],
                                start: 150,
                                step: 1,
                                handles: 1,
                                slide: function() {
                                    jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
                                }                
                            });
                        });        
                        return false;
                    },
                    /**
                     * The repeatable field callback for the remove event.
                     * 
                     * @param    object    the field container element next to the removed field container.
                     * @param    string    the field type slug
                     * @param    string    the field container tag ID
                     * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */                            
                    removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID ) {
                        
                        /* 1. Return if not for this field type */
                        if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) return;    // if it is not this field type
                        if ( oNextFieldConainer.find( '.select_font' ).length <= 0 )  return;        // if the input tag is not found, do nothing                

                        /* 2. Decrement the ids of the next all (including this one) uploader buttons and the preview elements. ( the input values are already dealt by the framework repeater script ) */
                        oNextFieldConainer.nextAll().andSelf().each( function() {        
                            
                            /* 2-1. Check if the parsing node holds necessary elements. */
                            var nodeFontInput = jQuery( this ).find( '.font-field input' );
                            if ( nodeFontInput.length <= 0 ) return true;
                                                        
                            /* 2-2. Deal with three elements: the Select Font button, the preview box, the preview font size change slider. */
                            nodeButton = jQuery( this ).find( '.select_font' );                            
                            nodeButton.decrementIDAttribute( 'id' );
                            jQuery( this ).find( '.font_preview' ).decrementIDAttribute( 'id' );
                            jQuery( this ).find( '.font-preview-text' ).decrementIDAttribute( 'id' );
                            // jQuery( this ).find( '.fontSliderHolder' ).decrementIDAttribute( 'id' );
                            // jQuery( this ).find( '.noUiSlider' ).decrementIDAttribute( 'id' );                            
                            
                            /* 2-3. Rebind functions to each element and update the associated properties. */
                        
                            /* 2-3-1. Rebind the uploader script to each button. The previously assigned ones also need to be renewed; 
                             * otherwise, the script sets the preview image in the wrong place. */
                            var sInputID = nodeFontInput.attr( 'id' );
                            setAPFFontUploader( sInputID, true, nodeButton.attr( 'data-enable_external_source' ) );    
                            
                            /* 2-3-2. Update the font-family style of the preview box. */
                            jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );                            
                            
                            /* 2-3-3. Rebind the noUiSlider script to the font-size changer slider. */
                            jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
                            jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
                                range: [ 100, 300 ],
                                start: 150,
                                step: 1,
                                handles: 1,
                                slide: function() {
                                    jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
                                }                
                            });        
                        });
                    },                
                    
                    sorted_fields : function( node, sFieldType, sFieldsTagID ) {    // on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

                        /* 1. Return if it is not the type. */
                        if ( jQuery.inArray( sFieldType, {$_aJSArray} ) <= -1 ) return;    /* If it is not the color field type, do nothing. */                        
                        if ( node.find( '.select_font' ).length <= 0 )  return;    /* If the uploader buttons are not found, do nothing */
                        
                        /* 2. Update the Select File button */
                        var iCount = 0;
                        node.children( '.admin-page-framework-field' ).each( function() {
                            
                            nodeButton = jQuery( this ).find( '.select_font' );
                            
                            /* 2-1. Set the current iteration index to the button ID and the preview elements */
                            nodeButton.setIndexIDAttribute( 'id', iCount );    
                            jQuery( this ).find( '.font_preview' ).setIndexIDAttribute( 'id', iCount );
                            jQuery( this ).find( '.font-preview-text' ).setIndexIDAttribute( 'id', iCount );
                            
                            /* 2-2. Rebind the uploader script to the button */
                            var nodeFontInput = jQuery( this ).find( '.font-field input' );
                            if ( nodeFontInput.length <= 0 ) return true;
                            var sInputID = nodeFontInput.attr( 'id' );
                            setAPFFontUploader( sInputID, true, jQuery( nodeButton ).attr( 'data-enable_external_source' ) );
                            
                            /* 2-2-2. Update the font-family style of the preview box. */
                            jQuery( '#font_preview_' + sInputID ).css( 'font-family', sInputID );                            
                            
                            /* 2-2-3. Rebind the noUiSlider script to the font-size changer slider. */
                            jQuery( this ).find( '#slider_container_' + sInputID ).replaceWith( createFontSizeChangeSlider( sInputID ) );
                            jQuery( this ).find( '#slider_' + sInputID ).noUiSlider({
                                range: [ 100, 300 ],
                                start: 150,
                                step: 1,
                                handles: 1,
                                slide: function() {
                                    jQuery( '#font_preview_' + sInputID ).css( 'font-size', jQuery( this ).val() + '%' );
                                }                
                            });    
                            
                            iCount++;
                        });
                    },        
                    
                });
            });        
        " . PHP_EOL;
    }    
    
        /**
         * Returns the font selector JavaScript script to be loaded in the head tag of the created admin pages.
         */        
        private function getScript_FontSelector( $sReferrer ) {
            
            
            $_sThickBoxTitle         = esc_js( __( 'Upload Font', 'admin-page-framework-demo' ) );
            $_sThickBoxButtonUseThis = esc_js( __( 'Use This Font', 'admin-page-framework-demo' ) );
            $_sInsertFromURL         = esc_js( $this->oMsg->get( 'insert_from_url' ) );
            
            if( ! function_exists( 'wp_enqueue_media' ) )    // means the WordPress version is 3.4.x or below
                return "
                
                    
                    setAPFFontUploader = function( sInputID, fMultiple, fExternalSource ) {
                        jQuery( '#select_font_' + sInputID ).unbind( 'click' );    // for repeatable fields
                        jQuery( '#select_font_' + sInputID ).click( function() {
                            var sPressedID = jQuery( this ).attr( 'id' );
                            window.sInputID = sPressedID.substring( 12 );    // remove the select_font_ prefix and set a property to pass it to the editor callback method.
                            window.original_send_to_editor = window.send_to_editor;
                            window.send_to_editor = hfAPFSendToEditorFont;
                            var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
                            tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
                            return false;    // do not click the button after the script by returning false.                                    
                        });    
                    }                    
                    
                    var hfAPFSendToEditorFont = function( sRawHTML ) {
                        
                        var sHTML = '<div>' + sRawHTML + '</div>';    // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.                            
                        var src = jQuery( 'a', sHTML ).attr( 'href' );

                        // If the user wants to save relevant attributes, set them.
                        var sInputID = window.sInputID;    // window.sInputID should be assigned when the thickbox is opened.
                        jQuery( '#' + sInputID ).val( src );    // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
                                                                                
                        // restore the original send_to_editor
                        window.send_to_editor = window.original_send_to_editor;
                                                                                
                        // Set the font preview
                        setFontPreview( src, sInputID );        
                        
                        // close the thickbox
                        tb_remove();                            
                        
                    }
                    
                ";
                    
            return "// Global Function Literal 
                setAPFFontUploader = function( sInputID, fMultiple, fExternalSource ) {
                    
                    var _bEscaped = false;    // indicates whether the frame is escaped/canceled.
                    var _oFontUploader;
                    
                    jQuery( '#select_font_' + sInputID ).unbind( 'click' );    // for repeatable fields
                    jQuery( '#select_font_' + sInputID ).click( function( e ) {
                        
                        window.wpActiveEditor = null;                        
                        e.preventDefault();
                        
                        // If the uploader object has already been created, reopen the dialog
                        if ( _oFontUploader ) {
                            _oFontUploader.open();
                            return;
                        }                    
                        
                        // Store the original select object in a global variable
                        oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
                        
                        // Assign a custom select object.
                        wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
                        var _oFontUploader = wp.media({
                            title:      fExternalSource
                                ? '{$_sInsertFromURL}'
                                : '{$_sThickBoxTitle}',                            
                            button: {
                                text: '{$_sThickBoxButtonUseThis}'
                            },
                            
                            library: {
                                type: 'application/font-woff,application/x-font-ttf,application/vnd.ms-fontobject,application/x-font-otf',
                            },
                            multiple: fMultiple,  // Set this to true to allow multiple files to be selected
                            metadata: {},
                        });
            
                        // When the uploader window closes, 
                        _oFontUploader.on( 'escape', function() {
                            _bEscaped = true;
                            return false;
                        });                        
                        _oFontUploader.on( 'close', function() {

                            var state = _oFontUploader.state();
                            
                            // Check if it's an external URL
                            if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) {
                                
                                // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
                                var _oFont = {}, _sKey;
                                for ( _sKey in state.props.attributes ) {
                                    _oFont[ _sKey ] = state.props.attributes[ _sKey ];
                                }    
                                
                            } 
                                
                            // If the image variable is not defined at this point, it's an attachment, not an external URL.
                            if ( 'undefined' !== typeof( _oFont ) ) {
                                setFontPreviewElementWithDelay( sInputID, _oFont );
                            } else {
                                
                                var _oNewField;
                                _oFontUploader.state().get( 'selection' ).each( function( oAttachment, iIndex ) {
                                    
                                    var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                                        ? oAttachment.attributes
                                        : {};                                       
                                    if( 0 === iIndex ){    
                                        // place first attachment in field
                                        setFontPreviewElementWithDelay( sInputID, _oAttributes );
                                        return true;
                                    } 
                                        
                                    var _oFieldContainer    = 'undefined' === typeof _oNewField 
                                        ? jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' ) 
                                        : _oNewField;
                                    _oNewField              = jQuery( this ).addAPFRepeatableField( _oFieldContainer.attr( 'id' ) );
                                    var sInputIDOfNewField = _oNewField.find( 'input' ).attr( 'id' );
                                    setFontPreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
        
                                });                
                                
                            }
                            
                            // Restore the original select object.
                            wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
                                            
                        });
                        
                        // Open the uploader dialog
                        _oFontUploader.open();                                            
                        return false;       
                    });    
                                    
                    var setFontPreviewElementWithDelay = function( sInputID, oImage, iMilliSeconds ) {
                        
                        iMilliSeconds = iMilliSeconds === undefined ? 100 : iMilliSeconds;
                        setTimeout( function (){
                            if ( ! _bEscaped ) {
                                setFontPreviewElement( sInputID, oImage );
                            }
                            _bEscaped = false;                        
                        }, iMilliSeconds );
                        
                    }        
                  
                }    
                      
            ";
        }
        
    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() { 
        return "/* Font Custom Field Type */
            .admin-page-framework-field-font .admin-page-framework-repeatable-field-buttons {
                margin-left: 1em;                
            }
            
            /* Font Uploader Input Field */
            .admin-page-framework-field-font input {
                margin-right: 0.5em;
                vertical-align: middle;    
            }
            
            /* Font Uploader Button */
            .select_font.button.button-small,
            .remove_font.button.button-small
            {     
                vertical-align: middle;
            }
            .remove_media.button.button-small {
                margin-left: 0.2em;
            }                 
            " . PHP_EOL;
     }
        
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 
        
        /* Variables */
        $aOutput            = array();
        $iCountAttributes   = count( ( array ) $aField['attributes_to_store'] );    // If the saving extra attributes are not specified, the input field will be single only for the URL. 
        $sCaptureAttribute  = $iCountAttributes ? 'url' : '';
        $sFontURL = $sCaptureAttribute
            ? ( isset( $aField['attributes']['value'][ $sCaptureAttribute ] ) ? $aField['attributes']['value'][ $sCaptureAttribute ] : "" )
            : $aField['attributes']['value'];
        
        /* Set up the attribute arrays */
        $aBaseAttributes    = $aField['attributes'] + array( 'class' => null );
        unset( $aBaseAttributes['input'], $aBaseAttributes['button'], $aBaseAttributes['preview'], $aBaseAttributes['name'], $aBaseAttributes['value'], $aBaseAttributes['type'] );
        $aInputAttributes   = array(
            'name'    => $aField['attributes']['name'] . ( $iCountAttributes ? "[url]" : '' ),
            'value'   => $sFontURL,
            'type'    => 'text',
        ) + $aField['attributes']['input'] + $aBaseAttributes;
        $_aButtonAtributes          = $aField['attributes']['button'] + $aBaseAttributes;
        $_aRemoveButtonAtributes    = $aField['attributes']['remove_button'] + $aBaseAttributes;
        $_aPreviewAtrributes        = $aField['attributes']['preview'] + $aBaseAttributes;

        /* Compose the field output */
        $aOutput[] =
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField['type']}-field'>"    // image-field ( this will be media-field for the media field type )
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
                    . $this->getExtraInputFields( $aField )
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"            
            . $aField['after_label']
            . $this->_getPreviewContainer( $aField, $sFontURL, $_aPreviewAtrributes )
            . $this->_getRemoveButtonScript( $aField['input_id'], $_aRemoveButtonAtributes )            
            . $this->_getUploaderButtonScript( $aField['input_id'], $aField['repeatable'], $aField['allow_external_source'], $_aButtonAtributes );
            ;
                    
        return implode( PHP_EOL, $aOutput );
        
    }
        /**
         * Returns extra input fields to set capturing attributes.
         * @since            3.0.0
         */
        protected function getExtraInputFields( &$aField ) {
            
            // Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
            $aOutputs = array();
            foreach( ( array ) $aField['attributes_to_store'] as $sAttribute )
                $aOutputs[] = "<input " . $this->generateAttributes( 
                        array(
                            'id'        => "{$aField['input_id']}_{$sAttribute}",
                            'type'      => 'hidden',
                            'name'      => "{$aField['_input_name']}[{$sAttribute}]",
                            'disabled'  => isset( $aField['attributes']['disabled'] ) && $aField['attributes']['disabled'] ? 'disabled' : null,
                            'value'     => isset( $aField['attributes']['value'][ $sAttribute ] ) ? $aField['attributes']['value'][ $sAttribute ] : null,
                        )
                    ) . "/>";
            return implode( PHP_EOL, $aOutputs );
            
        }    
        
        /**
         * Returns the output of the preview box.
         * @since            3.0.0
         */
        protected function _getPreviewContainer( $aField, $sFontURL, $aPreviewAtrributes ) {

            if ( ! $aField['show_preview'] ) return '';
            
            $sFontURL = $this->resolveSRC( $sFontURL, true );
            return 
                "<div " . $this->generateAttributes( 
                        array(
                            'id'        => "font_preview_container_{$aField['input_id']}",                            
                            'class'     => 'font_preview ' . ( isset( $aPreviewAtrributes['class'] ) ? $aPreviewAtrributes['class'] : null ),
                            // 'style'     => ( $sFontURL ? '' : "display; none; "  ). ( isset( $aPreviewAtrributes['style'] ) ? $aPreviewAtrributes['style'] : '' ),
                        ) + $aPreviewAtrributes
                    )
                . ">"
                    . "<p class='font-preview-text' id='font_preview_{$aField['input_id']}' style='font-family: {$aField['input_id']}; opacity: 1;'>"
                        . $aField['preview_text']
                    . "</p>"                    
                . "</div>"
                . $this->getScopedStyle( $aField['input_id'], $sFontURL )
                . $this->getFontChangeScript( $aField['input_id'], $sFontURL )
                . $this->getFontSizeChangerElement( $aField['input_id'], "font_preview_container_{$aField['input_id']}", "font_preview_{$aField['input_id']}" )
            ;
        }
        
        /**
         * A helper function for the above getImageInputTags() method to add a image button script.
         * 
         * @since   2.1.3
         * @since   2.1.5   Moved from AdminPageFramework_InputField.
         */
        protected function _getUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes ) {
            
            $_bIsLabelSet           = isset( $aButtonAttributes['data-label'] ) && $aButtonAttributes['data-label'];
            $_bDashiconSupported    = ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' );            
            $_sDashIconSelector     = ! $_bDashiconSupported ? '' : 'dashicons dashicons-portfolio';
            $_aAttributes           = array(
                    'id'        => "select_font_{$sInputID}",
                    'href'      => '#',            
                    'data-uploader_type'            => function_exists( 'wp_enqueue_media' ) ? 1 : 0,
                    'data-enable_external_source'   => $bExternalSource ? 1 : 0,                    
                ) 
                + $aButtonAttributes
                + array(
                    'title'     => $_bIsLabelSet ? $aButtonAttributes['data-label'] : __( 'Select Font', 'admin-page-framework-demo' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'select_font button button-small ',
                trim( $aButtonAttributes['class'] ) ? $aButtonAttributes['class'] : $_sDashIconSelector
            );            
            $_sButton = 
                "<a " . $this->generateAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $aButtonAttributes['data-label'] 
                        : ( strrpos( $_aAttributes['class'], 'dashicons' ) 
                            ? '' 
                            : __( 'Select Font', 'admin-page-framework-demo' )
                        )
                    )                    
                ."</a>";
                
            $_sScript = "
                if ( jQuery( 'a#select_font_{$sInputID}' ).length == 0 ) {
                    jQuery( 'input#{$sInputID}' ).after( \"{$_sButton}\" );
                }
                jQuery( document ).ready( function(){            
                    setAPFFontUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
                });" . PHP_EOL;    
                    
            return "<script type='text/javascript' class='admin-page-framework-font-uploader-button'>" 
                    . $_sScript 
                . "</script>". PHP_EOL;

        }        
        
        private function getScopedStyle( $sInputID, $sFontURL ) {
            
            $sFormat = $this->getFontFormat( $sFontURL );
            return "
                <style id='font_preview_style_{$sInputID}'>
                    @font-face { 
                        font-family: '{$sInputID}'; 
                        src: url( {$sFontURL} ) format( '{$sFormat}' );
                    }
                </style>
            ";
            
        }
        
        private function getFontSizeChangerElement( $sInputID, $sPreviewContainerID, $sPreviewID ) {
            
            $_sSliderID             = "slider_{$sInputID}";
            $_sSliderContainerID    = "slider_container_{$sInputID}";            
            return "
                <script type='text/javascript' class='font-size-changer' >
                    jQuery( document ).ready( function() {
                        
                        // Write the element
                        if ( jQuery( '#{$_sSliderContainerID}' ).length == 0 ) {
                            jQuery( '#{$sPreviewContainerID}' ).before( createFontSizeChangeSlider( \"{$sInputID}\" ) );
                        }
                        
                        // Run noUiSlider
                        jQuery( '#{$_sSliderID}' ).noUiSlider({
                            range: [ 100, 300 ],
                            start: 150,
                            step: 1,
                            handles: 1,
                            slide: function() {
                                jQuery( '#{$sPreviewID}' ).css( 'font-size', jQuery( this ).val() + '%' );
                            }                
                        });
                        
                    }); 
                </script>";                
        }
        
        private function getFontChangeScript( $sInputID, $sFontURL ) {
            return "
                <script type='text/javascript' >
                    jQuery( document ).ready( function() {
                        setFontPreview( '{$sFontURL}', '{$sInputID}' );
                    }); 
                </script>";            
        }
    
            private function getFontFormat( $sURL ) {
                $_sExtension = strtolower( pathinfo( $sURL, PATHINFO_EXTENSION ) );
                switch( $_sExtension ) {
                    case 'eot':
                        return 'embedded-opentype';
                    case 'ttf':
                        return 'truetype';
                    case 'otf':
                        return 'opentype';
                    default:
                        return $_sExtension;    // woff, svg,
                }
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
                'id'        => "remove_font_{$sInputID}",
                'href'      => '#',            
                'onclick'   => esc_js( "removeInputValuesForFont( this ); return false;" ),
                ) 
                + $aButtonAttributes
                + array(
                    'title' => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get( 'remove_value' ),
                );
            $_aAttributes['class']  = $this->generateClassAttribute( 
                'remove_value remove_font button button-small', 
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
                
            $_sScript = "
                if ( 0 === jQuery( 'a#remove_font_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( \"{$_sButton}\" );
                }
                " . PHP_EOL;    
                    
            return "<script type='text/javascript' class='admin-page-framework-font-remove-button'>" 
                    . $_sScript 
                . "</script>". PHP_EOL;
           
        }

            
}
endif;