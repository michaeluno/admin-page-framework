<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of field type classes that define input field types.
 * 
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @since       3.8.0       Changed it to extend the `AdminPageFramework_Form_Utility`.
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_Form_Utility {
    
    /**
     * Stores the field set type indicating what this field is for such as for meta boxes, taxonomy fields or page fields.
     * @remark      This will be automatically set when head tag elements are enqueued.
     * @since       3.0.0
     * @internal
     */
    public $_sFieldSetType = '';
    
    /**
     * Defines the slugs used for this field type.
     * 
     * This should be overridden in the extended class.
     * 
     * @access       public      This must be public as accessed from outside.
     */
    public $aFieldTypeSlugs = array( 'default' );
    
    /**
     * Defines the default key-values of the extended field type. 
     * This should be overridden in the extended class.
     */
    protected $aDefaultKeys = array();
    
    /**
     * Defines the default key-values of all field types.
     * 
     * @internal
     */
    protected static $_aDefaultKeys = array(
        'value'             => null, // ( array or string ) this suppresses the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
        'default'           => null, // ( array or string )
        'repeatable'        => false,
        'sortable'          => false,
        'label'             => '', // ( string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
        'delimiter'         => '',
        'before_input'      => '',
        'after_input'       => '',     
        'before_label'      => null,
        'after_label'       => null,    
        'before_field'      => null,
        'after_field'       => null,
        'label_min_width'   => '',   // (string|integer) min-width applied to the input label in pixels. 3.8.0+ Changed the default value from 140 to 0 as it is now applied via embedded CSS. When this value is set by the user, it is set inline and the value will be overridden. [3.8.4+] Changed the value from `0`  to `''`.
        'before_fieldset'   => null, // 3.1.1+
        'after_fieldset'    => null, // 3.1.1+
        
        /* Mandatory keys */
        'field_id'          => null,     
        
        /* For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings. */
        'page_slug'         => null,
        'section_id'        => null,
        'before_fields'     => null,
        'after_fields'      => null,    
        
        'attributes'        => array(
            /* Root Attributes - the root attributes are assumed to be for the input tag. */
            'disabled'  => null, // set 'Disabled' or an empty value '' to make it disabled. Null will not set the attribute.
            'class'     => '',
            
            /* Component Attributes */
            'fieldrow'  => array(), // attributes applied to the field group container row tag that holds all the field components including descriptions and scripts and the title.
            'fieldset'  => array(), // attributes applied to the field group container tag that holds all the field components including descriptions and scripts.
            'fields'    => array(), // attributes applied to the fields container tag that holds all sub-fields.
            'field'     => array(), // attributes applied to each field container tag.
        ),
    );    
    
    protected $oMsg;
    
    /**
     * Sets up hooks and properties.
     * 
     * @internal
     * @since       2.1.5
     * @since       3.5.0               'admin_page_framework' can be passed to register the field type site-wide.
     * @param       string|array        $asClassName            The instantiated class name that uses the field type(s). To enable it site-wide, pass `admin_page_framework`. 
     * This value will be used to the `field_types_{instantiated class name}` filter to set field definition callbacks.
     * @param       string|array        $asFieldTypeSlug        The field type slugs. To have multiple slugs for one definition, pass an array holding the slugs.
     * @param       object              $oMsg                   The framework message object.
     * @param       boolean             $bAutoRegister          Whether or not to register the field type(s).
     */
    public function __construct( $asClassName='admin_page_framework', $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) {
            
        $this->aFieldTypeSlugs  = empty( $asFieldTypeSlug ) 
            ? $this->aFieldTypeSlugs 
            : ( array ) $asFieldTypeSlug;
        $this->oMsg             = $oMsg 
            ? $oMsg 
            : AdminPageFramework_Message::getInstance();
        
        // This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
        if ( $bAutoRegister ) {
            foreach( ( array ) $asClassName as $_sClassName ) {
                add_filter( 
                    'field_types_' . $_sClassName, 
                    array( $this, '_replyToRegisterInputFieldType' )
                );
            }
        }
        
        // User constructor
        $this->construct();
        
    }    
    
    /**
     * The user constructor.
     * 
     * When the user defines a field type, they may use this instead of the real constructor 
     * so that they don't have to care about the internal parameters.
     * 
     * @since       3.1.3
     */
    protected function construct() {}
    
    /**
     * Checks whether TinyMCE is supported.
     * @since       3.5.8
     * @return      boolean
     */
    protected function isTinyMCESupported() {
        return version_compare( $GLOBALS[ 'wp_version' ], '3.3', '>=' )
            && function_exists( 'wp_editor' )
        ;
    }
    
    /**
     * Returns the sub-element of a given element by the element key.
     * 
     * @remark      Used by the `text`, `textarea`, `size`, 'radio', and `checkbox` field types.
     * @since       3.5.8
     * @since       3.7.0        Changed the third parameter to accept a label argument from a boolean value to be usable for other filed types.
     * @param       array|string $asElement
     * @param       array|string $asKey
     * @param       array|string $asLabel
     * @return      string
     */
    protected function getElementByLabel( $asElement, $asKey, $asLabel ) {
        if ( is_scalar( $asElement ) ) {
            return $asElement;
        }
        return is_array( $asLabel ) // if the user sets multiple items
            ? $this->getElement( 
                $asElement,         // subject
                $this->getAsArray( $asKey, true /* preserve empty */ ),     // dimensional path 
                ''                  // default - if the element is not found, return an empty
            )
            : $asElement;
    }    
      
    /**
     * Returns another field output by the given field definition array.
     * 
     * This is used to create nested fields or dynamically create a different type of field.
     * @since       3.4.0
     * @param       array       $aFieldset
     * @return      string      The fieldset output.
     */
    protected function getFieldOutput( array $aFieldset ) {
        
        if ( ! is_object( $aFieldset[ '_caller_object' ] ) ) {
            return '';
        }

        $aFieldset[ '_nested_depth' ]++;
        $aFieldset[ '_parent_field_object' ] = $aFieldset[ '_field_object' ]; // 3.6.0+
        
        // 3.7.0+ The caller object is no longer a factory object but a form object.
        $_oCallerForm   = $aFieldset[ '_caller_object' ];

        $_oFieldset = new AdminPageFramework_Form_View___Fieldset( 
            $aFieldset,                          // the field definition array
            $_oCallerForm->aSavedData,               // the stored form data
            $_oCallerForm->getFieldErrors(),         // the field error array.
            $_oCallerForm->aFieldTypeDefinitions,    // the field type definition array.
            $_oCallerForm->oMsg,                     // the system message object
            $_oCallerForm->aCallbacks                // field output element callables.
        );           
        return $_oFieldset->get();
        
    }
        /**
         * @deprecated  Kept for backward compatibility.
         * @param       array       $aFieldset
         * @return      string
         */
        protected function geFieldOutput( array $aFieldset ) {
            return $this->getFieldOutput( $aFieldset );
        }
    
    /**
     * Registers the field type.
     * 
     * @since       2.1.5
     * @callback    filter      field_types_{class name}
     * @callback    filter      field_types_admin_page_framework
     * @return      array
     * @internal
     */
    public function _replyToRegisterInputFieldType( $aFieldDefinitions ) {
        
        foreach ( $this->aFieldTypeSlugs as $sFieldTypeSlug ) {
            $aFieldDefinitions[ $sFieldTypeSlug ] = $this->getDefinitionArray( $sFieldTypeSlug );
        }
        return $aFieldDefinitions;     

    }
    
    /**
     * Returns the field type definition array.
     * 
     * @remark      The scope is public since AdminPageFramework_FieldType class allows the user to use this method.
     * @since       2.1.5
     * @since       3.0.0       Added the $sFieldTypeSlug parameter.
     * @since       3.0.3       Tweaked it to have better execution speed.
     * @param       string      $sFieldTypeSlug
     * @return      array
     * @internal
     */
    public function getDefinitionArray( $sFieldTypeSlug='' ) {
        
        // The uniteArrays() method resulted in somewhat being slow due to overhead on checking array keys for recursive array merges.
        $_aDefaultKeys = $this->aDefaultKeys + self::$_aDefaultKeys;
        $_aDefaultKeys['attributes'] = isset( $this->aDefaultKeys['attributes'] ) && is_array( $this->aDefaultKeys['attributes'] )
            ? $this->aDefaultKeys['attributes'] + self::$_aDefaultKeys['attributes'] 
            : self::$_aDefaultKeys['attributes'];
        
        return array(
            'sFieldTypeSlug'        => $sFieldTypeSlug,
            'aFieldTypeSlugs'       => $this->aFieldTypeSlugs,
            'hfRenderField'         => array( $this, "_replyToGetField" ),
            'hfGetScripts'          => array( $this, "_replyToGetScripts" ),
            'hfGetStyles'           => array( $this, "_replyToGetStyles" ),
            'hfGetIEStyles'         => array( $this, "_replyToGetInputIEStyles" ),
            'hfFieldLoader'         => array( $this, "_replyToFieldLoader" ),
            'hfFieldSetTypeSetter'  => array( $this, "_replyToFieldTypeSetter" ),
            'hfDoOnRegistration'    => array( $this, "_replyToDoOnFieldRegistration" ), // 3.5.0+
            'aEnqueueScripts'       => $this->_replyToGetEnqueuingScripts(), // urls of the scripts
            'aEnqueueStyles'        => $this->_replyToGetEnqueuingStyles(), // urls of the styles
            'aDefaultKeys'          => $_aDefaultKeys,       
        );
        
    }
    
    /*
     * These methods should be overridden in the extended class.
     */
    /**
     * @internal
     * @param       array      $aField
     * @return      string
     */    
    public function _replyToGetField( $aField ) { return ''; }          // should return the field output
    /**#@+
     * @internal
     * @return   string
     */
    public function _replyToGetScripts() { return ''; }                 // should return the script
    public function _replyToGetInputIEStyles() { return ''; }           // should return the style for IE
    public function _replyToGetStyles() { return ''; }                  // should return the style
    /**#@-*/
    public function _replyToFieldLoader() {}                            // do stuff that should be done when the field type is loaded for the first time.

    /**
     * Sets the field set type.
     * 
     * Called when enqueuing the field type's head tag elements.
     * @since       3.0.0
     * @param       string  $sFieldSetType
     * @internal
     */
    public function _replyToFieldTypeSetter( $sFieldSetType='' ) {
        $this->_sFieldSetType = $sFieldSetType;
    }
    
    /**
     * Called when the given field of this field type is registered.
     * 
     * @since       3.5.0
     * @param       array       $aField
     * @internal
     */
    public function _replyToDoOnFieldRegistration( $aField ) {}
    
    /**
     * 
     * @return array e.g. each element can hold a sting of the source url: array( 'http://..../my_script.js', 'http://..../my_script2.js' )
     * Optionally, an option array can be passed to specify dependencies etc.
     * array( array( 'src' => 'http://...my_script1.js', 'dependencies' => array( 'jquery' ) ), 'http://.../my_script2.js' )
     * @internal
     */
    protected function _replyToGetEnqueuingScripts() { return array(); } // should return an array holding the urls of enqueuing items
    
    /**
     * @return array e.g. each element can hold a sting of the source url: array( 'http://..../my_style.css', 'http://..../my_style2.css' )
     * Optionally, an option array can be passed to specify dependencies etc.
     * array( array( 'src' => 'http://...my_style1.css', 'dependencies' => array( 'jquery' ) ), 'http://.../my_style2.css' )
     * @internal
     */
    protected function _replyToGetEnqueuingStyles() { return array(); } // should return an array holding the urls of enqueuing items
        
    /*
     * Shared methods
     */
    
    /**
     * Enqueues scripts for the media uploader.
     * 
     * @remark      Used by the image and the media field types.
     * @internal
     */
    protected function enqueueMediaUploader() {
        
        add_filter( 'media_upload_tabs', array( $this, '_replyToRemovingMediaLibraryTab' ) );
        
        wp_enqueue_script( 'jquery' );     
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );

        // If the WordPress version is 3.5 or above,
        if ( function_exists( 'wp_enqueue_media' ) ) {
            add_action( is_admin() ? 'admin_footer' : 'wp_footer', array( $this, 'replyToEnqueueScriptsForMediaUpload' ), 1 );
        } else {
            wp_enqueue_script( 'media-upload' );    
        }

        if ( in_array( $this->getPageNow(), array( 'media-upload.php', 'async-upload.php', ) ) ) {     
            add_filter( 'gettext', array( $this, '_replyToReplaceThickBoxText' ) , 1, 2 );     
        }
        
    }
        /**
         * Enqueues scripts for media upload.
         * These must be done after the `admin-page-framework-script-form-media-uploader` script is registered.
         * The `enqueueMediaUploader()` method maybe called earlier than that, so do it in the footer action hook.
         * @since    3.9.0
         * @callback action wp_footer|admin_footer
         */
        public function replyToEnqueueScriptsForMediaUpload() {
            wp_enqueue_media();
            wp_enqueue_script( 'admin-page-framework-script-form-media-uploader' );
        }

        /**
         * Replaces the label text of a button used in the media uploader.
         * 
         * @internal
         * @since       2.0.0
         * @param       string       $sTranslated
         * @param       string       $sText
         * @return      string
         * @callback    add_filter() gettext
         */ 
        public function _replyToReplaceThickBoxText( $sTranslated, $sText ) {

            // Replace the button label in the media thick box.
            if ( ! in_array( $this->getPageNow(), array( 'media-upload.php', 'async-upload.php' ) ) ) { 
                return $sTranslated; 
            }
            if ( $sText !== 'Insert into Post' ) { 
                return $sTranslated; 
            }
            if ( $this->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) !== 'admin_page_framework' ) { 
                return $sTranslated; 
            }
            
            if ( isset( $_GET[ 'button_label' ] ) ) {   // sanitization unnecessary
                return $this->getHTTPQueryGET( 'button_label', '' );
            }

            return $this->oMsg->get( 'use_this_image' );
            // @deprecated 3.8.32 Accessing an unreferenced property
            // return $this->oProp->sThickBoxButtonUseThis
            //     ? $this->oProp->sThickBoxButtonUseThis
            //     : $this->oMsg->get( 'use_this_image' );
            
        }
        /**
         * Removes the From URL tab from the media uploader.
         * 
         * @internal
         * @since       2.1.3
         * @since       2.1.5        Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to _replyToRemovingMediaLibraryTab().
         * @callback    add_filter() media_upload_tabs
         * @param       array        $aTabs
         * @return      array
         */
        public function _replyToRemovingMediaLibraryTab( $aTabs ) {
            
            if ( ! isset( $_REQUEST[ 'enable_external_source' ] ) ) {    // sanitization unnecessary
                return $aTabs; 
            }
            if ( ! ( boolean ) $_REQUEST[ 'enable_external_source' ] ) { // sanitization unnecessary
                unset( $aTabs[ 'type_url' ] ); // removes the 'From URL' tab in the thick box.
            }
            return $aTabs;
            
        }

    /**
     * Generates HTML attributes of label containers.
     *
     * This is used for element that `label_min_width` is applied.
     *
     * @param  array         $aField
     * @param  array|string $asClassAttributes
     * @param  array        $aAttributes
     * @return string
     * @since  3.8.0
     */
    protected function getLabelContainerAttributes( $aField, $asClassAttributes, array $aAttributes=array() ) {

        $aAttributes[ 'class' ] = $this->getClassAttribute( $asClassAttributes, $this->getElement( $aAttributes, 'class' ) );
        $aAttributes[ 'style' ] = $this->getStyleAttribute(
            array(
                'min-width' => $aField[ 'label_min_width' ] || '0' === ( string ) $aField[ 'label_min_width' ]
                    ? $this->getLengthSanitized( $aField[ 'label_min_width' ] ) 
                    : null,
            ),
            $this->getElement( $aAttributes, 'style' )
        );
        return $this->getAttributes( $aAttributes );

    }
        
}