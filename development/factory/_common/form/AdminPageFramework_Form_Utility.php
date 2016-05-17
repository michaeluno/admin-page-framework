<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides shared methods for the form classes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Form_Utility extends AdminPageFramework_FrameworkUtility {
    
    /**
     * @since       3.7.0
     * @return      array
     */
    static public function getElementPathAsArray( $asPath ) {
        if ( is_array( $asPath ) ) {
            return;
        }
        return explode( '|', $asPath );
    }
    
    /**
     * @since       3.7.0
     * @return      string      The section path. e.g. my_section|nested_section
     */
    static public function getFormElementPath( $asID ) {
        return implode( 
            '|', 
            self::getAsArray( $asID ) 
        );        
    }
  
    /**
     * Sanitizes a given section or field id.
     * @return      array|string
     * @since       3.7.0
     */
    static public function getIDSanitized( $asID ) {
        return is_scalar( $asID )
            ? self::sanitizeSlug( $asID )
            : self::getAsArray( $asID );
            
    }
    
    /**
     * Checks whether the given fieldset definition has nested field items.
     * @since       3.8.0
     * @return      boolean
     */
    static public function hasNestedFields( $aFieldset ) {
        
        if ( ! isset( $aFieldset[ 'content' ] ) ) {
            return false;
        }
        if ( empty( $aFieldset[ 'content' ] ) ) {
            return false;
        }
        if ( ! is_array( $aFieldset[ 'content' ] ) ) {
            return false;
        }
        // At this point, the `content` argument contains either the definition of nested fields or inline-mixed fields.
        
        // If the first element is a string, it is a inline mixed field definition.
       return is_array( self::getElement( $aFieldset[ 'content' ], 0 ) );
       
    }    
    
    /**
     * Checks whether the given field has a sub-field.
     * @since       3.8.0
     * @param       array       $aFields        An array holding sub-fields.
     * @param       array       $aField         A field definition array. 
     * @return      boolean
     */
    static public function hasSubFields( array $aFields, array $aField ) {
        
        if ( count( $aFields ) > 1 ) {
            return true;
        }
        if ( $aField[ 'repeatable' ] || $aField[ 'sortable' ] ) {
            return true;
        }
        return false;
        
    }
    
    /**
     * Checks whether the parent field is repeatable or sortable.
     * 
     * @since       3.8.0
     * @return      boolean
     * @deprecated  3.8.0
     */
    // static public function isParentFieldDynamic( $aFieldset ) {
// return false;        
    // }
    
    /**
     * Adds a trailing pipe (|) character to the given string value.
     * 
     * Used to construct a field path.
     * 
     * @return      string
     * @since       3.8.0
     */
    static public function getTrailingPipeCharacterAppended( $sString ) {
        if ( empty( $sString ) ) {
            return $sString;
        }
        return $sString . '|';
    }    
    
}
