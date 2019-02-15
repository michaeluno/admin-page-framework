<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * A base class of the debug class.
 *
 * Mainly provides methods for debug outputs.
 *
 * @since           3.8.9
 * @extends         AdminPageFramework_FrameworkUtility
 * @package         AdminPageFramework/Common/Utility
 */
class AdminPageFramework_Debug_Base extends AdminPageFramework_FrameworkUtility {

    /**
     * Returns a legible value representation with value details.
     * @since       3.8.9
     * @return      string
     */
    static protected function _getLegibleDetails( $mValue ) {
        if ( is_array( $mValue ) ) {
            return '(array, length: ' . count( $mValue ).') '
                . print_r( self::_getLegibleArray( $mValue ) , true );
        }
        return print_r( self::_getLegibleValue( $mValue ), true );
    }

    /**
     * Returns a string representation of the given value.
     * @since       3.8.9
     * @return      string
     */
    static protected function _getLegible( $mValue ) {

        $mValue = is_object( $mValue )
            ? ( method_exists( $mValue, '__toString' )
                ? ( string ) $mValue          // cast string
                : ( array ) $mValue           // cast array
            )
            : $mValue;
        $mValue = is_array( $mValue )
            ? self::_getArrayMappedRecursive(
                self::_getSlicedByDepth( $mValue, 10 ),
                array( __CLASS__, '_getObjectName' )
            )
            : $mValue;
        return self::_getArrayRepresentationSanitized( print_r( $mValue, true ) );

    }

        /**
         * Returns a object name if it is an object. Otherwise, the value itself.
         * This is used to convert objects into a string in array-walk functions
         * as objects tent to get large when they are converted to a string representation.
         * @since       3.8.9
         */
        static private function _getObjectName( $mItem ) {
            if ( is_object( $mItem ) ) {
                return '(object) ' . get_class( $mItem );
            }
            return $mItem;
        }

        /**
         * @since       3.8.9
         * @param       callable     $asoCallable
         * @return      string
         */
        static private function _getLegibleCallable( $asoCallable ) {
            return '(callable) ' . self::_getCallableName( $asoCallable );
        }
            /**
             * @since       3.8.9
             * @param       callable     $asoCallable
             * @return      string
             */
            static public function _getCallableName( $asoCallable ) {

                if ( is_string( $asoCallable ) ) {
                    return $asoCallable;
                }
                if ( is_object( $asoCallable ) ) {
                    return get_class( $asoCallable );
                }
                $_sSubject = is_object( $asoCallable[ 0 ] )
                    ? get_class( $asoCallable[ 0 ] )
                    : ( string ) $asoCallable[ 0 ];

                return $_sSubject . '::' . ( string ) $asoCallable[ 1 ];

            }

        /**
         * @since       3.8.9
         * @param       object      $oObject
         * @return      string
         */
        static public function _getLegibleObject( $oObject ) {

            if ( method_exists( $oObject, '__toString' ) ) {
                return ( string ) $oObject;
            }
            return '(object) ' . get_class( $oObject ) . ' '
                . count( get_object_vars( $oObject ) ) . ' properties.';

        }
        /**
         * Returns an array representation with value types in each element.
         * The element deeper than 10 dimensions will be dropped.
         * @since       3.8.9
         * @return      array
         */
        static public function _getLegibleArray( array $aArray ) {
            return self::_getArrayMappedRecursive(
                self::_getSlicedByDepth( $aArray, 10 ),
                array( __CLASS__, '_getLegibleValue' )
            );
        }
            /**
             * @since       3.8.9
             * @return      string
             */
            static private function _getLegibleValue( $mItem ) {
                if ( is_callable( $mItem ) ) {
                    return self::_getLegibleCallable( $mItem );
                }
                return is_scalar( $mItem )
                    ? self::_getLegibleScalar( $mItem )
                    : self::_getLegibleNonScalar( $mItem );
            }
                /**
                 * @since       3.8.9
                 * @return      string
                 */
                static private function _getLegibleNonScalar( $mNonScalar ) {

                    $_sType = gettype( $mNonScalar );
                    if ( is_null( $mNonScalar ) ) {
                        return '(null)';
                    }
                    if ( is_object( $mNonScalar ) ) {
                        return '(' . $_sType . ') ' . get_class( $mNonScalar );
                    }
                    if ( is_array( $mNonScalar ) ) {
                        return '(' . $_sType . ') ' . count( $mNonScalar ) . ' elements';
                    }
                    return '(' . $_sType . ') ' . ( string ) $mNonScalar;

                }
                /**
                 * @return      string
                 * @param       scalar      $sScalar
                 * @since       3.8.9
                 */
                static private function _getLegibleScalar( $sScalar ) {
                    if ( is_bool( $sScalar ) ) {
                        return '(boolean) ' . ( $sScalar ? 'true' : 'false' );
                    }
                    return is_string( $sScalar )
                        ? self::_getLegibleString( $sScalar )
                        : '(' . gettype( $sScalar ) . ', length: ' . self::_getValueLength( $sScalar ) .  ') ' . $sScalar;
                }
                    /**
                     * Returns a length of a value.
                     * @since       3.5.3
                     * @internal
                     * @return      integer|null        For string or integer, the string length. For array, the element lengths. For other types, null.
                     */
                    static private function _getValueLength( $mValue ) {
                        $_sVariableType = gettype( $mValue );
                        if ( in_array( $_sVariableType, array( 'string', 'integer' ) ) ) {
                            return strlen( $mValue );
                        }
                        if ( 'array' === $_sVariableType ) {
                            return count( $mValue );
                        }
                        return null;

                    }
                    /**
                     * @param       string      $sString
                     * @return      string
                     */
                    static private function _getLegibleString( $sString ) {

                        static $_iMBSupport;
                        $_iMBSupport    = isset( $_iMBSupport ) ? $_iMBSupport : ( integer ) function_exists( 'mb_strlen' );
                        $_aStrLenMethod = array( 'strlen', 'mb_strlen' );
                        $_aSubstrMethod = array( 'substr', 'mb_substr' );

                        $iCharLimit     = self::$iLegibleStringCharacterLimit;
                        $_iCharLength   = call_user_func_array( $_aStrLenMethod[ $_iMBSupport ], array( $sString ) );
                        return $_iCharLength <= $iCharLimit
                            ? '(string, length: ' . $_iCharLength . ') ' . $sString
                            : '(string, length: ' . $_iCharLength . ') ' . call_user_func_array( $_aSubstrMethod[ $_iMBSupport ], array( $sString, 0, $iCharLimit ) )
                                . '...';

                    }

    /**
     * Character length limit to truncate.
     */
    static public $iLegibleStringCharacterLimit = 200;

    /**
     * @return      string
     * @since       3.8.9
     */
    static protected function _getArrayRepresentationSanitized( $sString ) {

        // Fix extra line breaks after `Array()`
        $sString = preg_replace(
            '/\)(\r\n?|\n)(?=(\r\n?|\n)\s+[\[\)])/', // needle
            ')', // replacement
            $sString // subject
        );

        // Fix empty array output
        $sString = preg_replace(
            '/Array(\r\n?|\n)\s+\((\r\n?|\n)\s+\)/', // needle
            'Array()', // replacement
            $sString // subject
        );
        return $sString;

    }

    /**
     * Slices an array by the given depth.
     *
     * @since       3.4.4
     * @since       3.8.9       Changed it not to convert an object into an array.
     * @since       3.8.9       Changed the scope to private.
     * @since       3.8.9       Renamed from `getSliceByDepth()`.
     * @return      array
     * @internal
     */
    static private function _getSlicedByDepth( array $aSubject, $iDepth=0 ) {

        foreach ( $aSubject as $_sKey => $_vValue ) {
            if ( is_array( $_vValue ) ) {
                $_iDepth = $iDepth;
                if ( $iDepth > 0 ) {
                    $aSubject[ $_sKey ] = self::_getSlicedByDepth( $_vValue, --$iDepth );
                    $iDepth = $_iDepth;
                    continue;
                }
                unset( $aSubject[ $_sKey ] );
            }
        }
        return $aSubject;

    }

    /**
     * Performs `array_map()` recursively.
     * @return      array
     * @since       3.8.9
     */
    static private function _getArrayMappedRecursive( array $aArray, $oCallable ) {

        self::$_oCurrentCallableForArrayMapRecursive = $oCallable;
        $_aArray = array_map( array( __CLASS__, '_getArrayMappedNested' ), $aArray );
        self::$_oCurrentCallableForArrayMapRecursive = null;
        return $_aArray;

    }
        static private $_oCurrentCallableForArrayMapRecursive;
        /**
         * @internal
         * @return      mixed       A modified value.
         * @since       3.8.9
         */
        static private function _getArrayMappedNested( $mItem ) {
            return is_array( $mItem )
                ? array_map( array( __CLASS__, '_getArrayMappedNested' ), $mItem )
                : call_user_func( self::$_oCurrentCallableForArrayMapRecursive, $mItem );
        }

}
