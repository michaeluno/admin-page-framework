<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Put deprecated utility methods together.
 *
 * @since       3.5.3
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 * @deprecated
 */
abstract class AdminPageFramework_Utility_Deprecated {
    
    /**
     * Retrieves a corresponding array value from the given array.
     * 
     * When there are multiple arrays and they have similar index structures but it's not certain if one has the key and the others,
     * use this method to retrieve the corresponding key value. 
     * 
     * @remark      This is mainly used by the field array to insert user-defined key values.
     * @return      string|array    If the key does not exist in the passed array, it will return the default. If the subject value is not an array, it will return the subject value itself.
     * @since       2.0.0
     * @since       2.1.3           Added the $bBlankToDefault parameter that sets the default value if the subject value is empty.
     * @since       2.1.5           Changed the scope to public static from protected as converting all the utility methods to all public static.
     * @since       3.5.3           Moved from `AdminPageFramework_Utility_Array`.
     * @deprecated  3.5.3           Use `getElement()`. 
     */
    public static function getCorrespondingArrayValue( $vSubject, $sKey, $sDefault='', $bBlankToDefault=false ) {    
                
        // If $vSubject is null,
        if ( ! isset( $vSubject ) ) { return $sDefault; }
            
        // If the $bBlankToDefault flag is set and the subject value is a blank string, return the default value.
        if ( $bBlankToDefault && $vSubject == '' ) { return  $sDefault; }
            
        // If $vSubject is not an array, 
        if ( ! is_array( $vSubject ) ) { return ( string ) $vSubject; } // consider it as string.
        
        // Consider $vSubject as array.
        if ( isset( $vSubject[ $sKey ] ) ) { return $vSubject[ $sKey ]; }
        
        return $sDefault;
        
    }    
    
    /**
     * Check if the given array is an associative array.
     * 
     * @since       3.0.0
     * @since       3.5.3           Moved from `AdminPageFramework_Utility_Array`.
     */
    static public function isAssociativeArray( array $aArray ) {
        return ( bool ) count( array_filter( array_keys( $aArray ), 'is_string' ) );
    }        
    
    /**
     * Returns the element value by the given key as an array.
     * 
     * When the retrieving element value is unknown whether it is set and it is an array, use this method 
     * to save the line of isset() and is_array().
     * 
     * @since       3.4.0
     * @since       3.5.3           Moved from `AdminPageFramework_Utility_Array`.
     * @deprecaed   3.5.3
     */
    static public function getElementAsArray( $aSubject, $isKey, $vDefault=null ) {
        return self::getAsArray( 
            self::getElement( $aSubject, $isKey, $vDefault ),
            true       // preserve an empty value
        );
    }    
    
    /**
     * Finds the dimension depth of the given array.
     * 
     * @since       2.0.0
     * @since       3.5.3           Moved from `AdminPageFramework_Utility_Array`.
     * @remark      There is a limitation that this only checks the first element so if the second or other elements have deeper dimensions, it will not be caught.
     * @param       array           $array     the subject array to check.
     * @return      integer         returns the number of dimensions of the array.
     * @deprecated  3.5.3
     */
    public static function getArrayDimension( $array ) {
        return ( is_array( reset( $array ) ) ) 
            ? self::getArrayDimension( reset( $array ) ) + 1 
            : 1;
    }    
    
}