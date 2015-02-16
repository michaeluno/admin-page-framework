<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     * Retrieves the query value from the given URL with a key.
     * 
     * @since       2.0.0
     * @return      string|null
     */ 
    static public function getQueryValueInURLByKey( $sURL, $sQueryKey ) {
        
        $aURL = parse_url( $sURL ) + array( 'query' => '' );
        parse_str( $aURL['query'], $aQuery );     
        return self::getElement(
            $aQuery,  // subject array
            $sQueryKey, // key
            null      // default
        );                    
    }
    
    /**
     * Generates inline CSS rules from the given array.
     * 
     * For example,
     * <code>
     * array(
     *      'width' => '32px',
     *      'height' => '32px',
     * )
     * </code>
     * will be
     * <code>
     * 'width: 32px; height: 32px;'
     * </code>
     * 
     * @since       3.2.0
     * @return      string
     */
    static public function generateInlineCSS( array $aCSSRules ) {
        $_aOutput = array();
        foreach( $aCSSRules as $_sProperty => $_sValue ) {
            $_aOutput[] = $_sProperty . ': ' . $_sValue;
        }
        return implode( '; ', $_aOutput );
    }
    
    /**
     * Generates a string of inline styles for the style attribute value from multiple arguments.
     * 
     * Duplicated items will be merged.
     * 
     * For example,
     * <code>generateStyleAttribute( array( 'margin-top' => '10px', 'display: inline-block' ), 'float:right; display: none;' )</code>
     * will generate
     * <code>margin-top: 10px; display: inline-block; float:right;</code>
     * @since       3.3.1
     * @return      string
     */
    static public function generateStyleAttribute( $asInlineCSSes ) {
        
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
        return self::generateInlineCSS( array_unique( $_aCSSRules ) );
        
    }
    
    /**
     * Generates a string of class selectors for the class attribute value from multiple arguments.
     * 
     * Duplicated items will be merged.
     * 
     * For example, 
     * <code>$sClasses = generateClassAttribute( array( 'button, button-primary' ), 'remove_button button' );</code>
     * Will generates
     * <code>button button-primary remove_button</code>
     * 
     * @since   3.2.0
     * @todo    Fix an issue that occurs when a multidimentinal array is passed, which causes a warning:  Notice: Array to string conversion.
     * @return  string
     */
    static public function generateClassAttribute( /* $asClassSelectors1, $asClassSelectors12 */ ) {
        
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
        $_aClasses  = array_unique( $_aClasses );
        return trim( implode( ' ', $_aClasses ) );
        
    }
    
    /**
     * Generates a string of attributes to be embedded in an HTML tag from an associative array.
     * 
     * For example, 
     * <code>
     *     array( 'id' => 'my_id', 'name' => 'my_name', 'style' => 'background-color:#fff' )
     * </code>
     * becomes
     * <code>
     *     id="my_id" name="my_name" style="background-color:#fff"
     * </code>
     * 
     * This is mostly used by methods to output input fields.
     * 
     * @since       3.0.0
     * @since       3.3.0       Made it allow empty value.
     * @return      string
     */
    static public function generateAttributes( array $aAttributes ) {
        
        $_sQuoteCharactor   ="'";
        $_aOutput           = array();
        foreach( $aAttributes as $sAttribute => $sProperty ) {
       
            // Must be resolved as a string.
            if ( in_array( gettype( $sProperty ), array( 'array', 'object' ) ) ) {
                continue;
            }
            $_aOutput[] = "{$sAttribute}={$_sQuoteCharactor}{$sProperty}{$_sQuoteCharactor}";
            
        }
        return implode( ' ', $_aOutput );
        
    }    
    
    
    /**
     * Returns an array for generating a data attribute from the given associative array.
     * 
     * @since   3.4.0
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