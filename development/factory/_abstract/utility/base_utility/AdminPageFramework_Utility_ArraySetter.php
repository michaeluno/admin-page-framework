<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with PHP arrays which do not use WordPress functions.
 *
 * Listed methods are to modify array contents.
 * 
 * @since       DEVVER
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_Utility_String
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_ArraySetter extends AdminPageFramework_Utility_ArrayGetter {
 
    /**
     * Calculates the subtraction of two values with the array key of `order`.
     * 
     * This is used to sort arrays.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the property class.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @since       3.6.0       Moved from `AdminPageFramework_Router`.
     * @since       DVVER       Moved from `AdminPageFramework_Utility`.
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
     * Unsets an element of a multi-dimensional array by given keys.
     * 
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
     *  $aKeys = array( 'a', 'b', 'c' );
     *  unsetDimensionalArrayElement( $a, $aKeys );
     *  var_dump( $a );
     * </code>
     * Will produce
     * <code>
     * array(
     *  'a' => array(
     *      'b' => array(
     *      )
     *  )
     * )
     * </code>
     * 
     * @remark      Introduced for resetting options with dimensional keys.
     * @since       3.5.3
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @return      void
     */
    static public function unsetDimensionalArrayElement( &$mSubject, array $aKeys ) {
        
        $_sKey = array_shift( $aKeys );
        if ( ! empty( $aKeys ) ) {
            if ( isset( $mSubject[ $_sKey ] ) && is_array( $mSubject[ $_sKey ] ) ) {
                self::unsetDimensionalArrayElement( $mSubject[ $_sKey ], $aKeys );
            }
            return;            
        }
        if ( is_array( $mSubject ) ) {
            unset( $mSubject[ $_sKey ] );
        }
        
    }
    
    /**
     * Sets a dimensional array value by dimensional array key path.
     * @since       3.5.3
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @return      void
     */
    static public function setMultiDimensionalArray( &$mSubject, array $aKeys, $mValue ) {

        $_sKey = array_shift( $aKeys );
        if ( ! empty( $aKeys ) ) {
            if( ! isset( $mSubject[ $_sKey ] ) || ! is_array( $mSubject[ $_sKey ] ) ) {
                $mSubject[ $_sKey ] = array();
            }
            self::setMultiDimensionalArray( $mSubject[ $_sKey ], $aKeys, $mValue );
            return;
            
        }
        $mSubject[ $_sKey ] = $mValue;

    }     
 
    /**
     * Reconstructs the given array by numerizing the keys. 
     * 
     * @since       3.0.0
     * @since       3.5.3       Added a type hint in the first parameter.
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array       The passed array structure looks like this.
     * <code>
     *   array( 
     *      0 => array(
     *          'field_id_1' => array( ... ),
     *          'field_id_2' => array( ... ),
     *      ), 
     *      1 => array(
     *          'field_id_1' => array( ... ),
     *          'field_id_2' => array( ... ),
     *      ),
     *      'field_id_1' => array( ... ),
     *      'field_id_2' => array( ... ),
     *   )
     * </code>
     * It will be converted to to
     * <code>
     *   array(
     *      0 => array(
     *          'field_id_1' => array( ... ),
     *          'field_id_2' => array( ... ),
     *      ), 
     *      1 => array(
     *          'field_id_1' => array( ... ),
     *          'field_id_2' => array( ... ),
     *      ),     
     *      2 => array(
     *          'field_id_1' => array( ... ),
     *          'field_id_2' => array( ... ),
     *      ),
     *   )
     * </code>
     */
    static public function numerizeElements( array $aSubject ) {

        $_aNumeric      = self::getIntegerKeyElements( $aSubject );
        $_aAssociative  = self::invertCastArrayContents( $aSubject, $_aNumeric );
        foreach( $_aNumeric as &$_aElem ) {
            $_aElem = self::uniteArrays( $_aElem, $_aAssociative );
        }
        if ( ! empty( $_aAssociative ) ) {
            array_unshift( $_aNumeric, $_aAssociative ); // insert the main section to the beginning of the array.
        }
        return $_aNumeric;
        
    }
 
    /**
     * Casts array contents into another while keeping the same key structure.
     * 
     * @since       3.0.0
     * @since       3.5.3       Added type hints to the parameter.
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @remark      It does not check key structure deeper than or equal to the second dimension.
     * @remark      If a key exists in the passed model array but does not exists in the subject array, 
     * a `null` value will be assigned to the resulting array.
     * @param       array       $aModel         the array that holds the necessary keys.
     * @param       array       $aSubject       the array from which the contents to be extracted.
     * @return      array       the extracted array contents with the keys of the model array.
     */
    public static function castArrayContents( array $aModel, array $aSubject ) {
        
        $_aCast = array();
        foreach( $aModel as $_isKey => $_v ) {
            $_aCast[ $_isKey ] = self::getElement(
                $aSubject,  // subject array
                $_isKey,    // key
                null        // default
            );                 
        }
        return $_aCast;
        
    }
    
    /**
     * Returns an array consisting of keys which don't exist in the other.
     * 
     * @since       3.0.0
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @remark      It won't check key structure deeper than or equal to the second dimension.
     * @param       array     $aModel       the array that holds the necessary keys.
     * @param       array     $aSubject     the array from which the contents to be extracted.
     * @return      array     the extracted array contents with the keys that do not exist in the model array.
     */
    public static function invertCastArrayContents( array $aModel, array $aSubject ) {
        
        $_aInvert = array();
        foreach( $aModel as $_isKey => $_v ) {
            if ( array_key_exists( $_isKey, $aSubject ) ) { 
                continue; 
            }
            $_aInvert[ $_isKey ] = $_v;
        }
        return $_aInvert;
        
    }
    
    /**
     * Merges multiple multi-dimensional arrays recursively.
     * 
     * The advantage of using this method over the array unite operator or `array_merge() is 
     * that it merges recursively and the null values of the preceding array will be overridden.
     * 
     * @since       2.1.2
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @static
     * @access      public
     * @remark      The parameters are variadic and can add arrays as many as necessary.
     * @return      array     the united array.
     */
    public static function uniteArrays( /* $aPrecedence, $aArray1, $aArray2, ... */ ) {
                
        $_aArray = array();
        foreach( array_reverse( func_get_args() ) as $_aArg ) {
            $_aArray = self::uniteArraysRecursive( 
                self::getAsArray( $_aArg ), 
                $_aArray 
            );
        }
        return $_aArray;
        
    }
    
    /**
     * Merges two multi-dimensional arrays recursively.
     * 
     * The first parameter array takes its precedence. This is useful to merge default option values. 
     * An alternative to `array_replace_recursive()` which is not available PHP 5.2.x or below.
     * 
     * @since       2.0.0
     * @since       2.1.5       Changed the visibility scope to `static`. 
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @access      public
     * @remark      null values will be overwritten.     
     * @param       array     the array that overrides the same keys.
     * @param       array     the array that is going to be overridden.
     * @return      array     the united array.
     */ 
    public static function uniteArraysRecursive( $aPrecedence, $aDefault ) {
                
        if ( is_null( $aPrecedence ) ) { 
            $aPrecedence = array(); 
        }
        
        if ( ! is_array( $aDefault ) || ! is_array( $aPrecedence ) ) { 
            return $aPrecedence; 
        }
            
        foreach( $aDefault as $sKey => $v ) {
            
            // If the precedence does not have the key, assign the default's value.
            if ( ! array_key_exists( $sKey, $aPrecedence ) || is_null( $aPrecedence[ $sKey ] ) ) {
                $aPrecedence[ $sKey ] = $v;
            } else {
                
                // if the both are arrays, do the recursive process.
                if ( is_array( $aPrecedence[ $sKey ] ) && is_array( $v ) ) {
                    $aPrecedence[ $sKey ] = self::uniteArraysRecursive( $aPrecedence[ $sKey ], $v );     
                }
            
            }
        }
        return $aPrecedence;     
    }
 
    /**
     * Removes array elements by the specified type.
     * 
     * @since       3.3.1
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @param       array       $aArray     The subject array to parse.
     * @param       array       $aTypes     The value types to drop. The supported types are the followings.
     *  - boolean
     *  - integer
     *  - double
     *  - string
     *  - array 
     *  - object
     *  - resource
     *  - NULL
     * @return      array       The modified array.
     */
    static public function dropElementsByType( array $aArray, $aTypes=array( 'array' ) ) {
        
        foreach( $aArray as $isKey => $vValue ) {
            if ( in_array( gettype( $vValue ), $aTypes ) ) {
                unset( $aArray[ $isKey ] );
            }
        }
        return $aArray;
    }
    
    /**
     * Removes an array element(s) by the given value.
     * @since       3.4.0
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array       The modified array.
     */
    static public function dropElementByValue( array $aArray, $vValue ) {
         
        foreach( self::getAsArray( $vValue, true ) as $_vValue ) {
            $_sKey = array_search( $_vValue, $aArray, true );
            if ( $_sKey === false ) {
                continue;
            }
            unset( $aArray[ $_sKey ] );
        }
        return $aArray;
        
    }
    
    /**
     * Removes given keys from the array.
     * 
     * This is used to drop unnecessary keys for a multidimensional array as multidimensinal arrays can cause PHP warnings used with `array_diff()`.
     * 
     * @since       3.4.6
     * @since       DVVER       Moved from `AdminPageFramework_Utility_Array`.
     * @return      array       The modified array.
     */
    static public function dropElementsByKey( array $aArray, $asKeys ) {
        
        foreach( self::getAsArray( $asKeys, true ) as $_isKey ) {
            unset( $aArray[ $_isKey ] );
        }
        return $aArray;
        
    }  
   
}