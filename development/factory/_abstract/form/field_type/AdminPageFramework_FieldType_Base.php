<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of field type classes that define input field types.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @internal
 */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_WPUtility {
    
    /**
     * Stores the field set type indicating what this field is for such as for meta boxes, taxonomy fields or page fields.
     * @remark This will be automatically set when head tag elements are enqueued.
     * @since 3.0.0
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
        'label_min_width'   => 140, // in pixel
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
     * @since       3.5.0       'admin_page_framework' can be passed to register the field type site-wide.
     * @param       string|array        $asClassName            The instantiated class name that uses the field type(s). To enable it site-wide, pass `admin_page_framework`. 
     * This value will be used to the `field_types_{instantiated class name}` filter to set field definition callbacks.
     * @param       string|array        $asFieldTypeSlug        The field type slugs. To have multiple slugs for one definition, pass an array holding the slugs.
     * @param       object              $oMsg                   The framework message object.
     * @param       boolean             $bAutoRegister          Whether or not to register the field type(s).
     */
    function __construct( $asClassName='admin_page_framework', $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) {
            
        $this->aFieldTypeSlugs  = empty( $asFieldTypeSlug ) ? $this->aFieldTypeSlugs : ( array ) $asFieldTypeSlug;
        $this->oMsg             = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        
        // This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
        if ( $bAutoRegister ) {
            foreach( ( array ) $asClassName as $_sClassName  ) {
                add_filter( "field_types_{$_sClassName}", array( $this, '_replyToRegisterInputFieldType' ) );
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
     * @since       DEVVER      Changed the third parameter to accept a label argument from a boolean value to be usable for other filed types.
     * @return      string
     */
    protected function getElementByLabel( $asElement, $asKey, $asLabel ) {
        if ( is_scalar( $asElement ) ) {
            return $asElement;
        }
        return is_array( $asLabel ) // if the user stes multiple items
            ? $this->getElement( 
                $asElement,         // subject
                $this->getAsArray( $asKey, true /* preserve empty */ ),     // dimensional path 
                ''                  // default - if the elementi is not found, return an empty
            )
            : $asElement;
    }    
      
    /**
     * Returns another field output by the given field definition array.
     * 
     * This is used to create nested fields or dynamically create a different type of field.
     * @since       3.4.0
     * @return      string      The fieldset output.
     */
    protected function geFieldOutput( array $aFieldset ) {
        
        if ( ! is_object( $aFieldset[ '_caller_object' ] ) ) {
            return '';
        }

        $aFieldset[ '_nested_depth' ]++;
        $aFieldset[ '_parent_field_object' ] = $aFieldset[ '_field_object' ]; // 3.6.0+
        
        // DEVVER+ The caller object is no longer a factory object but a form object.
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
        
        // @deprecated      DEVVER
        // $aFieldset[ '_nested_depth' ]++;
        // $aFieldset[ '_parent_field_object' ] = $aFieldset[ '_field_object' ]; // 3.6.0+
        // $_oCaller   = $aFieldset[ '_caller_object' ];
        // $_aOptions  = $_oCaller->getSavedOptions();
        // $_oFieldset = new AdminPageFramework_Form_View___Fieldset( 
            // $aFieldset,                                 // the field definition array
            // $_aOptions,                                 // the stored form data
            // $_oCaller->getFieldErrors(),                // the field error array.
            // $_oCaller->oProp->aFieldTypeDefinitions,    // the field type definition array.
            // $_oCaller->oMsg,                            // the system message object
            // $_oCaller->oProp->aFieldCallbacks           // field output element callables.
        // );           
        // return $_oFieldset->get();
        
    }
    
    /**
     * Registers the field type.
     * 
     * A callback function for the field_types_{$sClassName} filter.
     * @since       2.1.5
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
    /**#@+
     * @internal
     */    
    public function _replyToGetField( $aField ) { return ''; }          // should return the field output
    public function _replyToGetScripts() { return ''; }                 // should return the script
    public function _replyToGetInputIEStyles() { return ''; }           // should return the style for IE
    public function _replyToGetStyles() { return ''; }                  // should return the style
    public function _replyToFieldLoader() {}                            // do stuff that should be done when the field type is loaded for the first time.
    /**#@-*/
    /**
     * Sets the field set type.
     * 
     * Called when enqueuing the field type's head tag elements.
     * @since       3.0.0
     * @internal
     */
    public function _replyToFieldTypeSetter( $sFieldSetType='' ) {
        $this->_sFieldSetType = $sFieldSetType;
    }
    
    /**
     * Called when the given field of this field type is registered.
     * 
     * @since       3.5.0
     * @internal
     */
    public function _replyToDoOnFieldRegistration( array $aField ) {}
    
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
    
        if ( function_exists( 'wp_enqueue_media' ) ) {     
            // If the WordPress version is 3.5 or above,
            new AdminPageFramework_Form_View___Script_MediaUploader( $this->oMsg );
        } else {
            wp_enqueue_script( 'media-upload' );    
        }

        if ( in_array( $this->getPageNow(), array( 'media-upload.php', 'async-upload.php', ) ) ) {     
            add_filter( 'gettext', array( $this, '_replyToReplaceThickBoxText' ) , 1, 2 );     
        }
        
    }

        /**
         * Replaces the label text of a button used in the media uploader.
         * 
         * @internal
         * @since       2.0.0
         * @callback    filter      gettext
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
            
            if ( isset( $_GET['button_label'] ) ) { 
                return $_GET['button_label']; 
            }

            return $this->oProp->sThickBoxButtonUseThis 
                ? $this->oProp->sThickBoxButtonUseThis 
                : $this->oMsg->get( 'use_this_image' );
            
        }
        /**
         * Removes the From URL tab from the media uploader.
         * 
         * @internal
         * since        2.1.3
         * since        2.1.5       Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to _replyToRemovingMediaLibraryTab().
         * @callback    filter      media_upload_tabs
         * @return      array
         */
        public function _replyToRemovingMediaLibraryTab( $aTabs ) {
            
            if ( ! isset( $_REQUEST['enable_external_source'] ) ) { 
                return $aTabs; 
            }
            
            if ( ! $_REQUEST['enable_external_source'] ) {
                unset( $aTabs['type_url'] ); // removes the 'From URL' tab in the thick box.
            }
            return $aTabs;
            
        }     
        
}