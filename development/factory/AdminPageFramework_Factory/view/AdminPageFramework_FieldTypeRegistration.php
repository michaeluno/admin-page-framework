<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides means to define field types.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       2.1.5
 * @since       2.1.6     Changed the name from AdminPageFramework_FieldTypeDefinitions
 * @internal
 */
class AdminPageFramework_FieldTypeRegistration  {
    
    /**
     * Holds the built-in filed type slugs.
     * 
     * @since   2.1.5
     */
    protected static $aDefaultFieldTypeSlugs = array(
        'default', // undefined ones will be applied 
        'text',
        'number',
        'textarea',
        'radio',
        'checkbox',
        'select',
        'hidden',
        'file',
        'submit',
        'import',
        'export',
        'image',
        'media',
        'color',
        'taxonomy',
        'posttype',
        'size',
        'section_title', // 3.0.0+
        'system',        // 3.3.0+
    );    
        
    /**
     * Registers field types.
     * 
     * @since       3.1.3       Moved from the constructor.
     */
    static public function register( $aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {

        foreach( self::$aDefaultFieldTypeSlugs as $_sFieldTypeSlug ) {
            
            $_sFieldTypeClassName = "AdminPageFramework_FieldType_{$_sFieldTypeSlug}";
            if ( ! class_exists( $_sFieldTypeClassName ) ) { 
                continue; 
            }

            $_oFieldType = new $_sFieldTypeClassName( 
                $sExtendedClassName, 
                null, 
                $oMsg, 
                false     // pass false to disable auto-registering.     
            );    
            foreach( $_oFieldType->aFieldTypeSlugs as $__sSlug ) {     
                $aFieldTypeDefinitions[ $__sSlug ] = $_oFieldType->getDefinitionArray();
            }
        }
        return $aFieldTypeDefinitions;
        
    }
    
    /**
     * The flags to indicate whether the field type is already processed or not.
     * 
     * Note that it must be checked per a type (here property type is used).
     */
    static private $_aLoadFlags = array();
    
    /**
     * Sets the given field type's enqueuing scripts and styles.
     * 
     * A helper function for the above addSettingField() method.
     * 
     * @since       2.1.5
     * @since       3.0.0   Moved to the field type registration class and made it static to be used by different classes.
     * @since       3.3.0   Changed the name from _setFieldHeadTagElements.
     */
    static public function _setFieldResources( array $aField, $oProp, &$oResource ) {

        $_sFieldType = $aField['type'];

        self::$_aLoadFlags[ $oProp->_sPropertyType ] = isset( self::$_aLoadFlags[ $oProp->_sPropertyType ] ) && is_array( self::$_aLoadFlags[ $oProp->_sPropertyType ] )
            ? self::$_aLoadFlags[ $oProp->_sPropertyType ]
            : array();
        if ( isset( self::$_aLoadFlags[ $oProp->_sPropertyType ][ $_sFieldType ] ) && self::$_aLoadFlags[ $oProp->_sPropertyType ][ $_sFieldType ] ) { return; }
        self::$_aLoadFlags[ $oProp->_sPropertyType ][ $_sFieldType ] = true;
                
        // If the field type is not defined, return.
        if ( ! isset( $oProp->aFieldTypeDefinitions[ $_sFieldType ] ) ) { return; }

        if ( is_callable( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfFieldSetTypeSetter'] ) ) {
            call_user_func_array( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfFieldSetTypeSetter'], array( $oProp->_sPropertyType ) );
        }
        
        if ( is_callable( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfFieldLoader'] ) ) {
            call_user_func_array( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfFieldLoader'], array() );     
        }
        
        if ( is_callable( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetScripts'] ) ) {
            $oProp->sScript .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetScripts'], array() );
        }
            
        if ( is_callable( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetStyles'] ) ) {
            $oProp->sStyle .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetStyles'], array() );
        }
            
        if ( is_callable( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetIEStyles'] ) ) {
            $oProp->sStyleIE .= call_user_func_array( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['hfGetIEStyles'], array() );     
        }
    
        foreach( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['aEnqueueStyles'] as $asSource ) { 
            if ( is_string( $asSource ) ) {
                $oResource->_forceToEnqueueStyle( $asSource );
            }
            else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) ) {
                $oResource->_forceToEnqueueStyle( $asSource[ 'src' ], $asSource );
            }
        }
        foreach( $oProp->aFieldTypeDefinitions[ $_sFieldType ]['aEnqueueScripts'] as $asSource ) {
            if ( is_string( $asSource ) ) {
                $oResource->_forceToEnqueueScript( $asSource );
            }
            else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) ) {
                $oResource->_forceToEnqueueScript( $asSource[ 'src' ], $asSource );     
            }
        }     
            
    }
}