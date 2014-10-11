<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Utility' ) ) :
/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since 2.0.0
 * @extends AdminPageFramework_Utility_Array
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
abstract class AdminPageFramework_Utility extends AdminPageFramework_Utility_URL {
        
    /**
     * Returns the width for HTML attributes.
     * 
     * When a value may be a number with a unit like, '100%', it returns the value itself.
     * When a value misses a unit like '60', it returns with the unit.
     * 
     * @since 3.1.1
     */
    static public function sanitizeLength( $sLength, $sUnit='px' ) {
        return is_numeric( $sLength ) 
            ? $sLength . $sUnit
            : $sLength;
    }
        
    /**
     * Retrieves the query value from the given URL with a key.
     * 
     * @since 2.0.0
     * @return string|null
     */ 
    static public function getQueryValueInURLByKey( $sURL, $sQueryKey ) {
        
        $aURL = parse_url( $sURL );
        parse_str( $aURL['query'], $aQuery );     
        return isset( $aQuery[ $sQueryKey ] ) ? $aQuery[ $sQueryKey ] : null;
        
    }
    
    /**
     * Generates inline CSS rules from the given array.
     * 
     * For example,
     * array(
     *      'width' => '32px',
     *      'height' => '32px',
     * )
     * will be
     * 'width: 32px; height: 32px;'
     * 
     * @since       3.2.0
     */
    static public function generateInlineCSS( array $aCSSRules ) {
        
        $_sOutput = '';
        foreach( $aCSSRules as $_sProperty => $_sValue ) {
            $_sOutput .= $_sProperty . ': ' . $_sValue . '; ';
        }
        return trim( $_sOutput );
        
    }
    
    /**
     * Generates the string of class selectors for the class attribute value from multiple arguments.
     * 
     * Duplicated items will be single.
     * 
     * For example, 
     * <code>$sClasses = generateClassAttribute( array( 'button, button-primary' ), 'remove_button button' );</code>
     * Will generates
     * <code>button button-primary remove_button</code>
     * 
     * @since   3.2.0
     */
    static public function generateClassAttribute( $asClassSelectors ) {
        
        $_aClasses  = array();
        foreach( func_get_args() as $_asClasses ) {
            if ( ! is_string( $_asClasses ) && ! is_array( $_asClasses ) ) {
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
     * Generates the string of attributes to be embedded in an HTML tag from an associative array.
     * 
     * For example, 
     *     array( 'id' => 'my_id', 'name' => 'my_name', 'style' => 'background-color:#fff' )
     * becomes
     *     id="my_id" name="my_name" style="background-color:#fff"
     * 
     * This is mostly used by the method to output input fields.
     * 
     * @since   3.0.0
     * @since   3.3.0   Made it allow empty value.
     */
    static public function generateAttributes( array $aAttributes ) {
        
        $sQuoteCharactor    ="'";
        $_aOutput           = array();
        foreach( $aAttributes as $sAttribute => $sProperty ) {
            
            // @deprecated 3.3.0 to allow custom arguments in enqueuing resource tags.
            // Drop non value elements except numeric 0.
            // if ( empty( $sProperty ) && 0 !== $sProperty && '0' !== $sProperty ) { continue; } 
            
            // Must be resolved as a string.
            if ( is_array( $sProperty ) || is_object( $sProperty ) ) { continue; }  
                        
            $_aOutput[] = "{$sAttribute}={$sQuoteCharactor}{$sProperty}{$sQuoteCharactor}";
            
        }
        return implode( ' ', $_aOutput );
        
    }    
    
}
endif;