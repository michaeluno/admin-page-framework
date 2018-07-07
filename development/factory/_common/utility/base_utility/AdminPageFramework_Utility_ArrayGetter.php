<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with PHP arrays which do not use WordPress functions.
 *
 * Listed methods are to modify array contents.
 * 
 * @since       3.7.0
 * @extends     AdminPageFramework_Utility_String
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_ArrayGetter extends AdminPageFramework_Utility_Array {

    /**
     * Returns a first iterated array element.
     * @since       3.6.0
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     */
    static public function getFirstElement( array $aArray ) {
        foreach( $aArray as $_mElement ) {
            return $_mElement;
        }
    }
    
    /**
     * Returns an array element value by the given key. 
     * 
     * It just saves isset() conditional checks and allows a default value to be set.
     * 
     * @since       3.4.0
     * @since       3.5.3       The second parameter accepts an array representing dimensional keys. Added the fourth parameter to set values that the default value will be applied to.
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @param       array                       $aSubject       The subject array to parse.
     * @param       string|array|integer        $aisKey         The key to check. If an array is passed, it checks dimensional keys. 
     * @param       mixed                       $mDefault       The default value to return when the key is not set.
     * @param       string|array                $asToDefault    When the returning value matches oen of the set values here, the value(s) will be discarded and the default value will be applied.
     * @return      mixed       The set value or the default value.
     */
    static public function getElement( $aSubject, $aisKey, $mDefault=null, $asToDefault=array( null ) ) {
        
        $_aToDefault = is_null( $asToDefault )
            ? array( null )
            : self::getAsArray( $asToDefault, true );
        $_mValue     = self::getArrayValueByArrayKeys( 
            $aSubject, 
            self::getAsArray( $aisKey, true ),
            $mDefault
        );
        return in_array( $_mValue, $_aToDefault, true /* important! type-sensitive */ ) 
            ? $mDefault
            : $_mValue;
                    
    }
           
    /**
     * Returns an array element value by the given key as an array.
     * 
     * When the retrieving array element value is unknown whether it is set or not and it should be an array, 
     * this method can save the lines of isset() and is_array().
     * 
     * @since       3.4.0
     * @since       3.5.3       The second parameter accepts dimensional array keys and added the fourth parameter.
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array       The cast retrieved element value.
     */
    static public function getElementAsArray( $aSubject, $aisKey, $mDefault=null, $asToDefault=array( null ) ) {
        return self::getAsArray( 
            self::getElement( $aSubject, $aisKey, $mDefault, $asToDefault ),
            true       // preserve an empty value
        );
    }        
    
    /**
     * Removes elements of non-numeric keys from the given array.
     * 
     * @since       3.0.0
     * @since       3.5.3       Changed the name from `getIntegerElements`. Added a type hint in the first parameter.
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array
     */
    static public function getIntegerKeyElements( array $aParse ) {
        
        foreach ( $aParse as $_isKey => $_v ) {
            
            if ( ! is_numeric( $_isKey ) ) {
                unset( $aParse[ $_isKey ] );
                continue;
            }
            
            // Convert string numeric value to integer or float.
            $_isKey = $_isKey + 0; 
            
            if ( ! is_int( $_isKey ) ) {
                unset( $aParse[ $_isKey ] );
            }
                
        }
        return $aParse;
    }
    
    /**
     * Removes integer keys from the array.
     * 
     * @since       3.0.0
     * @since       3.5.3       Changed the name from `getNonIntegerElements()`.
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array
     */
    static public function getNonIntegerKeyElements( array $aParse ) {
        
        foreach ( $aParse as $_isKey => $_v ) {
            if ( is_numeric( $_isKey ) && is_int( $_isKey+ 0 ) ) {
                unset( $aParse[ $_isKey ] );     
            }
        }
        return $aParse;
        
    }

    /**
     * Retrieves an array element by the given array representing the dimensional key structure.
     * 
     * e.g. The following code will yield eee.
     * <code>
     * $a = array(
     *  'a' => array(
     *      'b' => array(
     *          'c' => array(
     *              'd' => array(
     *                  'e' => 'eee',
     *              ),
     *          ),
     *      ),
     *  ),
     *  );
     *  $aKeys = array( 'a', 'b', 'c', 'd', 'e' );
     *  $v = getArrayValueByArrayKeys( $a, $aKeys, 'default value' );
     *  var_dump( $v );
     * </code>
     * 
     * @since       3.0.1
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @return      mixed
     */
    static public function getArrayValueByArrayKeys( $aArray, $aKeys, $vDefault=null ) {
        
        $_sKey = array_shift( $aKeys );
 
        // array_key_exists( $_sKey, $aArray ) caused warnings in some occasions
        if ( isset( $aArray[ $_sKey ] ) ) {
            
            if ( empty( $aKeys ) ) { // no more keys 
                return $aArray[ $_sKey ];
            }
            
            if ( is_array( $aArray[ $_sKey ] ) ) {
                return self::getArrayValueByArrayKeys( $aArray[ $_sKey ], $aKeys, $vDefault );
            }
            
            // 3.5.3+ Fixes an issue that setting a non existent key resulted in null.
            // @deprecated
            // return $aArray[ $_sKey ];   
            
            // 3.7.0+ When a too deep element that the subject array does not hold is searched,
            // it returns the default value. It used to return the value of the most upper dimension.
            return $vDefault;
            
        }
        return $vDefault;
        
    }    
        
    /**
     * Casts array but does not create an empty element with the zero key when non-true value is given.
     * 
     * @remark      If `null` is passed an empty array `array()` will be returned.
     * @since       3.0.1
     * @since       3.5.3       Added the `$bPreserveEmpty` parameter.
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @param       mixed       $mValue             The subject value.
     * @param       boolean     bPreserveEmpty      If `false` is given, a value that yields `false` such as `false`, an empty sttring `''`, or `0` will not create an element such as `array( false )`. It will be just `array()`.
     * @return      array       The cast array.
     */
    static public function getAsArray( $mValue, $bPreserveEmpty=false ) {
        
        if ( is_array( $mValue ) ) {
            return $mValue; 
        }
        
        if ( $bPreserveEmpty ) {
            return ( array ) $mValue;
        }
        
        if ( empty( $mValue ) ) {
            return array();
        }
                        
        return ( array ) $mValue;
        
    }

    /**
     * Extracts array elements by giving keys.
     * 
     * <h4>Example</h4>
     * <code>
     * $array = array( 'a' => 1, 'b' => 3, 'c' => 5 );
     * $array = getArrayElementsByKeys( $array, array( 'a', 'c', ) ),
     * </code>
     * will produce
     * <code>
     * array(
     *  'a' => 1,
     *  'c' => 5,
     * )
     * </code>
     * @since       3.5.4
     * @since       3.7.0       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array
     */
    static public function getArrayElementsByKeys( array $aSubject, array $aKeys ) {
        return array_intersect_key(
            $aSubject,
            array_flip( $aKeys )
        );
    }    
   
}
