<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       3.7.0
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_HTMLAttribute extends AdminPageFramework_Utility_SystemInformation {

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
            if ( is_null( $_sValue ) ) {
                continue;
            }
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
            if ( in_array( gettype( $v ), array( 'array', 'object', 'NULL' ) ) ) {
                continue;
            }
            // 3.8.4+
            if ( '' === $v ) {
                $_aNewArray[ "data-{$sKey}" ] = '';
                continue;
            }
            $_aNewArray[ "data-{$sKey}" ] = $v 
                ? $v 
                : '0';
        }
        return $_aNewArray;
        
    }
    
}
