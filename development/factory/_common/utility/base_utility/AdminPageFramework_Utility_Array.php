<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with PHP arrays which do not use WordPress functions.
 *
 * @since       2.0.0
 * @package     AdminPageFramework/Utility
 * @extends     AdminPageFramework_Utility_String
 * @internal
 */
abstract class AdminPageFramework_Utility_Array extends AdminPageFramework_Utility_String {

    /**
     * Returns the first element of an array.
     * @param  array $aArray
     * @return mixed|null
     * @since  3.9.0
     */
    static public function getFirstElement( array $aArray ) {
        foreach( $aArray as $mValue ) {
            return $mValue;
        }
        return null;
    }

    /**
     * Performs `array_map()` recursively.
     *
     * @remark Accepts arguments.
     * @param  callable $cCallback      A callback function.
     * @param  array    $aArray         The subject array to process.
     * @param  array    $aArguments     Additional arguments to pass to the callable. Useful for custom functions.
     * @return array
     * @since  3.8.9
     * @since  3.8.32   Moved from `AdminPageFramework_Debug_Base` and renamed from `___getArrayMappedRecursive()` and made public as a part of the utility class.
     */
    static public function getArrayMappedRecursive( $cCallback, $aArray, array $aArguments=array() ) {
        $_aOutput = array();
        foreach( $aArray as $_isKey => $_vValue ) {
            if ( is_array( $_vValue ) ) {
                $_aOutput[ $_isKey ] = self::getArrayMappedRecursive( $cCallback, $_vValue, $aArguments );
                continue;
            }
            $_aOutput[ $_isKey ] = call_user_func_array( $cCallback, array_merge( array( $_vValue ), $aArguments ) );
        }
        return $_aOutput;
    }

    /**
     * Finds an unused numeric index of an array.
     *
     * @remark      the user may set a decimal number for the `order` argument.
     * @return      numeric
     * @since       3.7.4
     */
    static public function getUnusedNumericIndex( $aArray, $nIndex, $iOffset=1 ) {

        // Check if the order value is not used.
        if ( ! isset( $aArray[ $nIndex ] ) ) {
            return $nIndex;
        }

        // At this point, the index is already taken. So find one.
        return self::getUnusedNumericIndex( $aArray, $nIndex + $iOffset, $iOffset );

    }

    /**
     * Checks if the given array is an associative array or not.
     * @since       3.7.0
     * @return      boolean
     */
    static public function isAssociative( array $aArray ) {
        return array_keys ( $aArray ) !== range( 0, count( $aArray ) - 1 );
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
     * @param  array   $aArray
     * @return boolean
     * @since  3.9.0
     */
    static public function isMultiDimensional( array $aArray ) {
        return count( $aArray ) !== count( $aArray, COUNT_RECURSIVE );
    }

    /**
     * Check if the given array is an associative array.
     *
     * @since  3.0.0
     * @since  3.5.3   Moved from `AdminPageFramework_Utility_Array`.
     * @since  3.9.0   Revived and moved from `AdminPageFramework_Utility_Deprecated`.
     * @remark Same as `isAssociative()`.
     * @see    AdminPageFramework_Utility_Array::isAssociative()
     * @todo   Deprecate either of AdminPageFramework_Utility_Array::isAssociative() or this.
     * @param  array   $aArray
     * @return boolean
     */
    static public function isAssociativeArray( array $aArray ) {
        return ( bool ) count( array_filter( array_keys( $aArray ), 'is_string' ) );
    }

}