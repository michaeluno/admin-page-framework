<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with strings which do not use WordPress functions.
 *
 * @since       2.0.0
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_String extends AdminPageFramework_Utility_VariableType {
    
    /**
     * Converts non-alphabetic characters to underscore.
     * 
     * @since       2.0.0
     * @return      string|null     The sanitized string.
     * @todo        Change the method name as it does not tell for what it will sanitized.
     * @todo        Examine why null needs to be returned.
     */ 
    public static function sanitizeSlug( $sSlug ) {
        return is_null( $sSlug )
            ? null
            : preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $sSlug ) );
    }    
    
    /**
     * Converts non-alphabetic characters to underscore except hyphen(dash).
     * 
     * @since       2.0.0
     * @return      string|null      The sanitized string.
     * @todo        Change the method name as it does not tell for what it will sanitized.
     * @todo        Examine why null needs to be returned.
     */ 
    public static function sanitizeString( $sString ) {
        return is_null( $sString )
            ? null
            : preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString );
    }    
        
    
    /**
     * Checks if the passed value is a number and sets it to the default if not.
     * 
     * This is useful for form data validation. If it is a number and exceeds a set maximum number, 
     * it sets it to the maximum value. If it is a number and is below the minimum number, it sets to the minimum value.
     * Set a blank value for no limit.
     * 
     * @since       2.0.0
     * @return      string|integer      A numeric value will be returned. 
     */ 
    static public function fixNumber( $nToFix, $nDefault, $nMin='', $nMax='' ) {

        if ( ! is_numeric( trim( $nToFix ) ) ) { 
            return $nDefault; 
        }
        if ( $nMin !== '' && $nToFix < $nMin ) { 
            return $nMin; 
        }
        if ( $nMax !== '' && $nToFix > $nMax ) { 
            return $nMax; 
        }
        return $nToFix;
        
    }     
    
    /**
     * Compresses CSS rules.
     * 
     * @since       3.0.0
     * @return      string
     */
    static public function minifyCSS( $sCSSRules ) {
        
        return str_replace( 
            array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '),     // remove line breaks, tab, and white sspaces.
            '', 
            preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCSSRules ) // remove comments
        );
        
    }    
    
    /**
     * Returns the given string length.
     * @since       3.3.0
     * @return      integer|null        Null if an array is given.
     */
    static public function getStringLength( $sString ) {
        return function_exists( 'mb_strlen' )
            ? mb_strlen( $sString )
            : strlen( $sString );
    }    
        
    /**
     * Returns a number from the given human readable size representation.
     * @since       3.4.6
     * @return      string|integer
     */     
    static public function getNumberOfReadableSize( $nSize ) {
        
        $_nReturn     = substr( $nSize, 0, -1 );
        switch( strtoupper( substr( $nSize, -1 ) ) ) {
            case 'P':
                $_nReturn *= 1024;
            case 'T':
                $_nReturn *= 1024;
            case 'G':
                $_nReturn *= 1024;
            case 'M':
                $_nReturn *= 1024;
            case 'K':
                $_nReturn *= 1024;
        }
        return $_nReturn;
        
    }   
    
    /**
     * Returns a human readable size from the given byte number.
     * @since       3.4.6
     * @return      string
     */     
    static public function getReadableBytes( $nBytes ) {
        $_aUnits    = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
        $_nLog      = log( $nBytes, 1024 );
        $_iPower    = ( int ) $_nLog;
        $_iSize     = pow( 1024, $_nLog - $_iPower );
        return $_iSize . $_aUnits[ $_iPower ];
    }
    
    /**
     * Checks if the given string has a certain prefix.
     * 
     * Used mainly in the __call() method to determine the called undefined method name has a certain prefix.
     * 
     * @since       3.5.3
     * @return      boolean     True if it has the given prefix; otherwise, false.
     */
    static public function hasPrefix( $sNeedle, $sHaystack ) {
        return $sNeedle === substr( $sHaystack, 0, strlen( $sNeedle ) );
    }       
 
    /**
     * Checks if the given string has a certain suffix.
     * 
     * Used to check file base name etc.
     * 
     * @since   3.5.4
     * @reurn   boolean
     */
    static public function hasSuffix( $sNeedle, $sHaystack ) {
        
        $_iLength = strlen( $sNeedle );
        if ( 0 === $_iLength ) {
            return true;
        }
        return substr( $sHaystack, - $_iLength ) === $sNeedle;
        
    }
 
}