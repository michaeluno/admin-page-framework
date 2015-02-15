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
 * @since       2.0.0
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_Utility_String
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_Array extends AdminPageFramework_Utility_String {
        
    /**
     * Returns an array element value by the given key. 
     * 
     * It just saves isset() conditional checks and allows a default value to be set.
     * 
     * @since       3.4.0
     * @since       3.5.3       The second parameter accepts an array representing dimensional keys. Added the fourth parameter to set values that the default value will be applied to.
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
     * @since       3.5.3       The second parameter accepts dimensinal array keys and added the fourth parameter.
     * @return      array       The cast retrieved element value.
     */
    static public function getElementAsArray( $aSubject, $aisKey, $mDefault=null, $asToDefault=array( null ) ) {
        return self::getAsArray( 
            self::getElement( $aSubject, $aisKey, $mDefault, $asToDefault ),
            true       // preserve an empty value
        );
    }               
           
    /**
     * Casts array contents into another while keeping the same key structure.
     * 
     * @since       3.0.0
     * @since       3.5.3       Added type hints to the parameter.
     * @remark      It won't check key structure deeper than or equal to the second dimension.
     * @param       array       the array that holds the necessary keys.
     * @param       array       the array to be modified.
     * @return      array       the modified array.
     */
    public static function castArrayContents( array $aModel, array $aSubject ) {
        
        $_aNew = array();
        foreach( $aModel as $_sKey => $_v ) {
            $_aNew[ $_sKey ] = self::getElement(
                $aSubject,  // subject array
                $_sKey,     // key
                null        // default
            );                 
        }
        return $_aNew;
        
    }
    
    /**
     * Returns an array consisting of keys which don't exist in another.
     * 
     * @since       3.0.0
     * @remark      It won't check key structure deeper than or equal to the second dimension.
     * @param       array     the array that holds the necessary keys.
     * @param       array     the array to be modified.
     * @return      array     the modified array.
     */
    public static function invertCastArrayContents( array $aModel, array $aSubject ) {
        
        $_aNew = array();
        foreach( $aModel as $_sKey => $_v ) {
            if ( array_key_exists( $_sKey, $aSubject ) ) { 
                continue; 
            }
            $_aNew[ $_sKey ] = $_v;
        }
        return $_aNew;
        
    }
    
    /**
     * Merges multiple multi-dimensional arrays recursively.
     * 
     * The advantage of using this method over the array unite operator or `array_merge() is that it merges recursively 
     * and the null values of the preceding array will be overridden.
     * 
     * @since       2.1.2
     * @static
     * @access      public
     * @remark      The parameters are variadic and can add arrays as many as necessary.
     * @return      array     the united array.
     */
    public static function uniteArrays( /* $aPrecedence, $aArray1, $aArray2, ... */ ) {
                
        $_aArray = array();
        foreach( array_reverse( func_get_args() ) as $_aArg ) {
            $_aArray = self::uniteArraysRecursive( $_aArg, $_aArray );
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
     * @since       2.1.5       Changed the scope to static. 
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
     * Determines whether the element is the last element of an array by the given key.
     * 
     * @since       3.0.0
     * @return      boolean
     */
    static public function isLastElement( array $aArray, $sKey ) {
        end( $aArray );
        return $sKey === key( $aArray );
    }    
    /**
     * Determines whether element is the first element of an array by the given key.
     * 
     * @since       3.4.0
     * @return      boolean
     */
    static public function isFirstElement( array $aArray, $sKey ) {
        reset( $aArray );
        return $sKey === key( $aArray );
    }    
        
    /**
     * Removes elements of non-numeric keys from the given array.
     * 
     * @since       3.0.0
     * @since       3.5.3       Changed the name from `getIntegerElements`. Added a type hint in the first parameter.
     * @return      array
     */
    static public function getIntegerKeyElements( array $aParse ) {
        
        foreach ( $aParse as $_isKey => $_v ) {
            
            if ( ! is_numeric( $_isKey ) ) {
                unset( $aParse[ $_isKey ] );
                continue;
            }
            
            // Convert string numeric value to integer or flaot.
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
     * @since       3.5.3       Changed the name from `getNonIntegerElements`.
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
     * Re-constructs the given array by numerizing the keys. 
     * 
     * @since       3.0.0
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
    static public function numerizeElements( $aSubject ) {

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
     * 
     * @since       3.0.1
     * @return      mixed
     */
    static public function getArrayValueByArrayKeys( $aArray, $aKeys, $vDefault=null ) {
        
        $_sKey = array_shift( $aKeys );
 
        if ( array_key_exists( $_sKey, $aArray ) ) {
            
            if ( empty( $aKeys ) ) { // no more keys 
                return $aArray[ $_sKey ];
            }
            
            if ( is_array( $aArray[ $_sKey ] ) ) {
                return self::getArrayValueByArrayKeys( $aArray[ $_sKey ], $aKeys, $vDefault );
            }
            
            return $aArray[ $_sKey ];   // 3.5.3+ Fixes an issue that setting a non existent key resulted in null.
            
        }
        return $vDefault;
        
    }    
    
    /**
     * Sets a dimansional array value by dimansional array keys.
     * @since       3.5.3
     * @return      void
     */
    public static function setMultiDimensionalArray( &$mSubject, array $aKeys, $mValue ) {

        $_sKey = array_shift( $aKeys );
        if ( $aKeys ) {
            if( ! isset( $mSubject[ $_sKey ] ) || ! is_array( $mSubject[ $_sKey ] ) ) {
                $mSubject[ $_sKey ] = array();
            }
            self::setMultiDimensionalArray( $mSubject[ $_sKey ], $aKeys, $mValue );
            return;
            
        }
        $mSubject[ $_sKey ] = $mValue;

    }    
    
    /**
     * Casts array but does not create an empty element with the zero key when non-true value is given.
     * 
     * @remark      If `null` is passed an empty array `array()` will be returned.
     * @since       3.0.1
     * @since       3.5.3       Added the `$bPreserveEmpty` parameter.
     * @param       mixed       $mValue             The subject value.
     * @param       boolean     bPreserveEmpty      If fasle is given, `false`, empty sttring ( `''` ), `0` will not create an element.
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
     * Returns a readable list of the given array contents.
     * 
     * @remark      If the second dimension element is an array, it will be enclosed in parenthesis.
     * @since       3.3.0
     * @return      string      A readable list generated from the given array.
     */
    static public function getReadableListOfArray( array $aArray ) {
        
        $_aOutput   = array();
        foreach( $aArray as $_sKey => $_vValue ) {        
            $_aOutput[] = self::getReadableArrayContents( $_sKey, $_vValue, 32 ) . PHP_EOL;
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
    /**
     * Generates readable array contents.
     * 
     * @since       3.3.0
     * @return      string      The generated human readable array contents.
     */
    static public function getReadableArrayContents( $sKey, $vValue, $sLabelCharLengths=16, $iOffset=0 ) {
        
        $_aOutput   = array();
        $_aOutput[] = ( $iOffset ? str_pad( ' ', $iOffset  ) : '' ) 
            . ( $sKey ? '[' . $sKey . ']' : '' );
        
        if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) {
            $_aOutput[] = $vValue;
            return implode( PHP_EOL, $_aOutput );    
        }
        
        foreach ( $vValue as $_sTitle => $_asDescription ) {
            if ( ! is_array( $_asDescription ) && ! is_object( $_asDescription ) ) {
                $_aOutput[] = str_pad( ' ', $iOffset )
                    . $_sTitle 
                    . str_pad( ':', $sLabelCharLengths - self::getStringLength( $_sTitle ) )
                    . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad( ' ', $iOffset )
                . $_sTitle 
                . ": {" 
                . self::getReadableArrayContents( '', $_asDescription, 16, $iOffset + 4 )
                . PHP_EOL
                . str_pad( ' ', $iOffset ) . "}";
        }
        return implode( PHP_EOL, $_aOutput );    
        
    }        
    /**
     * Returns the readable list of the given array contents as HTML.
     * 
     * @since       3.3.0
     * @return      string      The HTML list generated from the given array.
     */
    static public function getReadableListOfArrayAsHTML( array $aArray ) {

        $_aOutput   = array();
        foreach( $aArray as $_sKey => $_vValue ) {        
            $_aOutput[] = "<ul class='array-contents'>" 
                    .  self::getReadableArrayContentsHTML( $_sKey, $_vValue )
                . "</ul>" . PHP_EOL;
        }
        return implode( PHP_EOL, $_aOutput );    
        
    } 
        /**
         * Returns the readable array contents.
         * 
         * @since       3.3.0
         * @return      string      The HTML output generated from the given array.
         */    
        static public function getReadableArrayContentsHTML( $sKey, $vValue ) {
            
            // Output container.
            $_aOutput   = array();
            
            // Title - array key
            $_aOutput[] = $sKey 
                ? "<h3 class='array-key'>" . $sKey . "</h3>"
                : "";
                
            // If it does not have a nested array or object, 
            if ( ! in_array( gettype( $vValue ), array( 'array', 'object' ) ) ) {
                $_aOutput[] = "<div class='array-value'>" 
                        . html_entity_decode( nl2br( str_replace( ' ', '&nbsp;', $vValue ) ), ENT_QUOTES )
                    . "</div>";
                return "<li>" . implode( PHP_EOL, $_aOutput ) . "</li>";    
            }
            
            // Now it is a nested item.
            foreach ( $vValue as $_sKey => $_vValue ) {   
                $_aOutput[] =  "<ul class='array-contents'>" 
                        . self::getReadableArrayContentsHTML( $_sKey, $_vValue ) 
                    . "</ul>";
            }
            return implode( PHP_EOL, $_aOutput ) ;
            
        }
    
    /**
     * Removes array elements by the specified type.
     * 
     * @since       3.3.1
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
     * Removes given keys fro the array.
     * 
     * This is used to drop unnecessary keys for a multidimensional array as multidimensinal arrays can cause PHP warnings used with `array_diff()`.
     * 
     * @since       3.4.6
     * @return      array       The modified array.
     */
    static public function dropElementsByKey( array $aArray, $asKeys ) {
        
        foreach( self::getAsArray( $asKeys, true ) as $_isKey ) {
            unset( $aArray[ $_isKey ] );
        }
        return $aArray;
        
    }
    
}