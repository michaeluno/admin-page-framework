<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility_SystemInformation
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility extends AdminPageFramework_Utility_SystemInformation {
       
    /**
     * Calculates the subtraction of two values with the array key of `order`.
     * 
     * This is used to sort arrays.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the property class.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @since       3.6.0       Moved from `AdminPageFramework_Router`.
     * @remark      a callback method for `uasort()`.
     * @return      integer
     * @internal
     */        
    static public function sortArrayByKey( $a, $b, $sKey='order' ) {
        return isset( $a[ $sKey ], $b[ $sKey ] )
            ? $a[ $sKey ] - $b[ $sKey ]
            : 1;
    }       
       
       
    /**
     * Generates brief object information.
     * 
     * @remark      Meant to be used for the `__toString()` method.
     * @since       3.6.0
     * @return      string
     */   
    static public function getObjectInfo( $oInstance ) {
        
        $_iCount     = count( get_object_vars( $oInstance ) );
        $_sClassName = get_class( $oInstance );
        return '(object) ' . $_sClassName . ': ' . $_iCount . ' properties.';
        
    }
       
    /**
     * Returns the width for HTML attributes.
     * 
     * When a value may be a number with a unit like, '100%', it returns the value itself.
     * When a value misses a unit like '60', it returns with the unit such as '60%'.
     * 
     * @since       3.1.1
     * @return      string
     */
    static public function sanitizeLength( $sLength, $sUnit='px' ) {
        return is_numeric( $sLength ) 
            ? $sLength . $sUnit
            : $sLength;
    }
            
    /**
     * Generates inline CSS rules from the given array.
     * 
     * For example,
     * <code>
     * array(
     *      'width'  => '32px',
     *      'height' => '32px',
     * )
     * </code>
     * will be
     * <code>
     * 'width: 32px; height: 32px;'
     * </code>
     * 
     * @since       3.6.0
     * @return      string
     */
    static public function getInlineCSS( array $aCSSRules ) {
        $_aOutput = array();
        foreach( $aCSSRules as $_sProperty => $_sValue ) {
            $_aOutput[] = $_sProperty . ': ' . $_sValue;
        }
        return implode( '; ', $_aOutput );
    }    
        /**
         * @since           3.2.0
         * @deprecated      3.6.0       Use `getInlineCSS()` instead.
         */
        static public function generateInlineCSS( array $aCSSRules ) {
            return self::getInlineCSS( $aCSSRules );
        }
    
    /**
     * Generates a string of inline styles for the style attribute value from multiple arguments.
     * 
     * Duplicated items will be merged.
     * 
     * For example,
     * `
     * getStyleAttribute( array( 'margin-top' => '10px', 'display: inline-block' ), 'float:right; display: none;' )
     * `
     * will generate
     * `
     * margin-top: 10px; display: inline-block; float:right;
     * `
     * @since       3.6.0
     * @return      string
     */
    static public function getStyleAttribute( $asInlineCSSes ) {
        
        $_aCSSRules = array();
        foreach( array_reverse( func_get_args() ) as $_asCSSRules ) {
            
            // For array, store in the container.
            if ( is_array( $_asCSSRules ) ) {
                $_aCSSRules = array_merge( $_asCSSRules, $_aCSSRules );
                continue;
            }
            
            // At this point, it is a string. Break them down to array elements.
            $__aCSSRules = explode( ';', $_asCSSRules );
            foreach( $__aCSSRules as $_sPair ) {
                $_aCSSPair = explode( ':', $_sPair );
                if ( ! isset( $_aCSSPair[ 0 ], $_aCSSPair[ 1 ] ) ) {
                    continue;
                }
                $_aCSSRules[ $_aCSSPair[ 0 ] ] = $_aCSSPair[ 1 ];
            }
            
        }
        return self::getInlineCSS( array_unique( $_aCSSRules ) );
        
    }
        /**
         * @since           3.3.1
         * @deprecated      3.6.0       Use `getStyleAttribute()` instead.
         */
        static public function generateStyleAttribute( $asInlineCSSes ) {
            self::getStyleAttribute( $asInlineCSSes );
        }
    
    /**
     * Generates a string of class selectors from multiple arguments.
     * 
     * For example, 
     * <code>
     * $sClasses = getClassAttribute( array( 'button, button-primary' ), 'remove_button button' );
     * </code>
     * Will generates
     * <code>
     *  button button-primary remove_button
     * </code>
     * 
     * @remark      Duplicated items will be merged.
     * @since       3.6.0
     * @todo        Fix an issue that when a multidimensional array is passed, it causes a warning:  Notice: Array to string conversion.
     * @return      string
     */
    static public function getClassAttribute( /* $asClassSelectors1, $asClassSelectors12, ... */ ) {
        
        $_aClasses  = array();
        foreach( func_get_args() as $_asClasses ) {
            if ( ! in_array( gettype( $_asClasses ), array( 'array', 'string' ) ) ) {
                continue;
            }            
            $_aClasses = array_merge( 
                $_aClasses,
                is_array( $_asClasses )
                    ? $_asClasses
                    : explode( ' ', $_asClasses )
            );
        }
        $_aClasses  = array_unique( array_filter( $_aClasses ) );
        
        // @todo examine if it is okay to remove the trim() function below.
        return trim( implode( ' ', $_aClasses ) );
        
    }    
        /**
         * Generates a string of class selectors from multiple arguments.
         * 
         * @since       3.2.0
         * @return      string
         * @deprecated  3.6.0
         */
        static public function generateClassAttribute( /* $asClassSelectors1, $asClassSelectors12 ... */ ) {
            $_aParams = func_get_args();
            return call_user_func_array(
                array( __CLASS__ , 'getClassAttribute' ), 
                $_aParams
            );        
        }
    
    /**
     * Returns an array for generating a data attribute from the given associative array.
     * 
     * @since       3.4.0
     * @return      array
     */
    static public function getDataAttributeArray( array $aArray ) {
        
        $_aNewArray = array();
        foreach( $aArray as $sKey => $v ) {
            if ( in_array( gettype( $v ), array( 'array', 'object' ) ) ) {
                continue;
            }            
            $_aNewArray[ "data-{$sKey}" ] = $v ? $v : '0';
        }
        return $_aNewArray;
        
    }
    
    /**
     * Returns one or the other.
     * 
     * Saves one conditional statement.
     * 
     * @remark      Use this only when the performance is not critical.
     * @since       3.5.3
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mValue     The value to evaluate.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mTrue      The value to return when the first parameter value yields true.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mTrue      The value to return when the first parameter value yields false.
     * @return      mixed
     */
    static public function getAOrB( $mValue, $mTrue=null, $mFalse=null ) {
        return $mValue ? $mTrue : $mFalse;
    }    
    
}