<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with variable types which do not use WordPress functions.
 *
 * @since       3.6.3
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_VariableType extends AdminPageFramework_Utility_Deprecated {
    
    /**
     * Checks if the passed string is a url or path.
     * 
     * This is used to check if an asset file can be used or not.
     * 
     * @since       3.6.3
     * @return      boolean
     */
    static public function isResourcePath( $sPathOrURL ) {
        
        // PHP_MAXPATHLEN is available since PHP 5.3.
        if ( defined( 'PHP_MAXPATHLEN' ) && strlen( $sPathOrURL ) > PHP_MAXPATHLEN ) {
            // At this point, the variable is not a file path. 
            return ( boolean ) filter_var( $sPathOrURL, FILTER_VALIDATE_URL );
        }
        
        if ( file_exists( $sPathOrURL ) ) {
            return true;
        } 
        return ( boolean ) filter_var( $sPathOrURL, FILTER_VALIDATE_URL );
        
    }
    
    /**
     * Checks if the given value is not null.
     * 
     * This is mainly used for the callback function of the `array_filter()` function.
     * 
     * @since       3.6.3
     * @return      boolean     If the passed value is not null, true; otherwise, false.
     */ 
    static public function isNotNull( $mValue=null ) {
        return ! is_null( $mValue );
    }    
 
    /**
     * Checks whether the given value is numeric and can be resolved as an integer.
     * 
     * Saves one conditional statement.
     * Used to determine sub-sections and sub-fields elements.
     * 
     * <code>
     * var_dump( is_int( '0' ) ); // false 
     * var_dump( isNumericInteger( '0' ) ); // true
     * var_dump( is_int( '' + 0 ) ); // true
     * var_dump( isNumericInteger( '' ) ); // false
     * </code>
     * 
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Utility`.
     * @return      boolean
     */
    static public function isNumericInteger( $mValue ) {
        return is_numeric( $mValue ) && is_int( $mValue + 0 );
    }
     
 
}
