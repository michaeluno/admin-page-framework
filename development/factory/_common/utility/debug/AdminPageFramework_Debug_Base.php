<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
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
     * @var int
     * @since   3.8.19
     */
    static public $iLegibleArrayDepthLimit = 50;

    /**
     * Character length limit to truncate.
     */
    static public $iLegibleStringCharacterLimit = 99999;

    /**
     * Returns a legible value representation with value details.
     * @param   mixed   $mValue
     * @param   integer $iStringLengthLimit
     * @param   integer $iArrayDepthLimit
     * @return  string
     * @since   3.8.9
     */
    static protected function _getLegibleDetails( $mValue, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {
        if ( is_array( $mValue ) ) {
            return '(array, length: ' . count( $mValue ).') '
                . print_r( self::___getLegibleDetailedArray( $mValue, $iStringLengthLimit, $iArrayDepthLimit ) , true );
        }
        return print_r( self::___getLegibleDetailedValue( $mValue, $iStringLengthLimit ), true );
    }

    /**
     * Returns a string representation of the given value with no variable details.
     *
     * @param  $mValue
     * @param  integer $iStringLengthLimit
     * @param  integer $iArrayDepthLimit
     * @return string
     * @since  3.8.9
     * @since  3.8.22  Added the `$sStringLengthLimit` and `$iArrayDepthLimit` parameters.
     */
    static protected function _getLegible( $mValue, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {

        $iArrayDepthLimit = $iArrayDepthLimit ? $iArrayDepthLimit : self::$iLegibleArrayDepthLimit;
        $mValue           = is_object( $mValue )
            ? ( method_exists( $mValue, '__toString' )
                ? ( string ) $mValue          // cast string
                : ( array ) $mValue           // cast array
            )
            : $mValue;
        $mValue = is_array( $mValue )
            ? self::___getArrayMappedRecursive(
                self::_getSlicedByDepth( $mValue, $iArrayDepthLimit ),
                array( __CLASS__, '___getObjectName' ),
                array()
            )
            : $mValue;
        $mValue = is_string( $mValue )
            ? self::___getLegibleString( $mValue,  $iStringLengthLimit, false )
            : $mValue;
        return self::_getArrayRepresentationSanitized( print_r( $mValue, true ) );

    }

        /**
         * Returns a object name if it is an object. Otherwise, the value itself.
         * This is used to convert objects into a string in array-walk functions
         * as objects tent to get large when they are converted to a string representation.
         * @since       3.8.9
         * @param mixed $mItem
         * @return mixed
         */
        static private function ___getObjectName( $mItem ) {
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
        static private function ___getLegibleDetailedCallable( $asoCallable ) {
            return '(callable) ' . self::___getCallableName( $asoCallable );
        }
            /**
             * @since       3.8.9
             * @param       callable     $asoCallable
             * @return      string
             */
            static public function ___getCallableName( $asoCallable ) {

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
        static private function ___getLegibleDetailedObject( $oObject ) {

            if ( method_exists( $oObject, '__toString' ) ) {
                return ( string ) $oObject;
            }
            return '(object) ' . get_class( $oObject ) . ' ' . count( get_object_vars( $oObject ) ) . ' properties.';

        }

        /**
         * Returns an array representation with value types in each element.
         * The element deeper than 10 dimensions will be dropped.
         * @param  array   $aArray
         * @param  integer $iStringLengthLimit
         * @param  integer $iDepthLimit
         * @return array
         * @since  3.8.9
         * @since  3.8.22  Added the `$iDepthLimit` parameter
         * @since  3.8.22  Changed the scope to private from public.
         * @since  3.8.22  Renamed from `_getLegibleArray()`.
         */
        static private function ___getLegibleDetailedArray( array $aArray, $iStringLengthLimit=0, $iDepthLimit=0 ) {
            $_iDepthLimit = $iDepthLimit ? $iDepthLimit : self::$iLegibleArrayDepthLimit;
            return self::___getArrayMappedRecursive(
                self::_getSlicedByDepth( $aArray, $_iDepthLimit ),
                array( __CLASS__, '___getLegibleDetailedValue' ),
                array( $iStringLengthLimit )
            );
        }

            /**
             * @param mixed $mItem
             * @param integer $iStringLengthLimit
             * @return      string
             * @since       3.8.22  Renamed from `_getLegibleValue()`.
             * @since       3.8.9
             */
            static private function ___getLegibleDetailedValue( $mItem, $iStringLengthLimit ) {
                if ( is_callable( $mItem ) ) {
                    return self::___getLegibleDetailedCallable( $mItem );
                }
                return is_scalar( $mItem )
                    ? self::___getLegibleDetailedScalar( $mItem, $iStringLengthLimit )
                    : self::___getLegibleDetailedNonScalar( $mItem );
            }
                /**
                 * @since       3.8.9
                 * @since       3.8.22  Renamed from `_getLegibleNonScalar()`.
                 * @return      string
                 * @param       mixed   $mNonScalar
                 */
                static private function ___getLegibleDetailedNonScalar( $mNonScalar ) {

                    $_sType = gettype( $mNonScalar );
                    if ( is_null( $mNonScalar ) ) {
                        return '(null)';
                    }
                    if ( is_object( $mNonScalar ) ) {
                        return self::___getLegibleDetailedObject( $mNonScalar );
                    }
                    if ( is_array( $mNonScalar ) ) {
                        return '(' . $_sType . ') ' . count( $mNonScalar ) . ' elements';
                    }
                    return '(' . $_sType . ') ' . ( string ) $mNonScalar;

                }
                /**
                 * @return      string
                 * @param       integer|float|boolean $sScalar
                 * @param       integer $iStringLengthLimit
                 * @since       3.8.9
                 * @since       3.8.22      Renamed from `_getLegibleScalar()`.
                 */
                static private function ___getLegibleDetailedScalar( $sScalar, $iStringLengthLimit ) {
                    if ( is_bool( $sScalar ) ) {
                        return '(boolean) ' . ( $sScalar ? 'true' : 'false' );
                    }
                    return is_string( $sScalar )
                        ? self::___getLegibleString( $sScalar, $iStringLengthLimit, true )
                        : '(' . gettype( $sScalar ) . ', length: ' . self::___getValueLength( $sScalar ) .  ') ' . $sScalar;
                }
                    /**
                     * Returns a length of a value.
                     * @since       3.5.3
                     * @internal
                     * @return      integer|null For string or integer, the string length. For array, the element lengths. For other types, null.
                     * @param       mixed        $mValue
                     */
                    static private function ___getValueLength( $mValue ) {
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
                     * @param       integer     $iLengthLimit
                     * @param       boolean     $bShowDetails
                     * @return      string
                     */
                    static private function ___getLegibleString( $sString, $iLengthLimit, $bShowDetails=true ) {

                        static $_iMBSupport;
                        $_iMBSupport    = isset( $_iMBSupport ) ? $_iMBSupport : ( integer ) function_exists( 'mb_strlen' );
                        $_aStrLenMethod = array( 'strlen', 'mb_strlen' );
                        $_aSubstrMethod = array( 'substr', 'mb_substr' );
                        $iCharLimit     = $iLengthLimit ? $iLengthLimit : self::$iLegibleStringCharacterLimit;
                        $_iCharLength   = call_user_func_array( $_aStrLenMethod[ $_iMBSupport ], array( $sString ) );

                        if ( $bShowDetails ) {
                            return $_iCharLength <= $iCharLimit
                                ? '(string, length: ' . $_iCharLength . ') ' . $sString
                                : '(string, length: ' . $_iCharLength . ') ' . call_user_func_array( $_aSubstrMethod[ $_iMBSupport ], array( $sString, 0, $iCharLimit ) )
                                    . '...';
                        }
                        return $_iCharLength <= $iCharLimit
                            ? $sString
                            : call_user_func_array( $_aSubstrMethod[ $_iMBSupport ], array( $sString, 0, $iCharLimit ) );

                    }

    /**
     * @return      string
     * @since       3.8.9
     * @param       string  $sString
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
     * @param array $aSubject
     * @param int $iDepth
     * @param string $sMore
     * @return array
     * @since  3.8.22
     */
    static public function getSlicedByDepth( array $aSubject, $iDepth=0, $sMore='(array truncated) ...' ) {
       return self::_getSlicedByDepth( $aSubject, $iDepth, $sMore );
    }

    /**
     * Slices an array by the given depth.
     *
     * @param array $aSubject
     * @param int $iDepth
     * @param string $sMore
     *
     * @return      array
     * @since       3.4.4
     * @since       3.8.9       Changed it not to convert an object into an array.
     * @since       3.8.9       Changed the scope to private.
     * @since       3.8.9       Renamed from `getSliceByDepth()`.
     * @since       3.8.22      Show a message when truncated by depth.
     * @since       3.8.22      Added the `$sMore` parameter.
     * @internal
     */
    static private function _getSlicedByDepth( array $aSubject, $iDepth=0, $sMore='(array truncated) ...' ) {

        foreach ( $aSubject as $_sKey => $_vValue ) {

            if ( is_array( $_vValue ) ) {

                $_iDepth = $iDepth;
                if ( $iDepth > 0 ) {
                    $aSubject[ $_sKey ] = self::_getSlicedByDepth( $_vValue, --$iDepth );
                    $iDepth = $_iDepth;
                    continue;
                }

                if ( strlen( $sMore ) ) {
                    $aSubject[ $_sKey ] = $sMore;
                    continue;
                }
                unset( $aSubject[ $_sKey ] );

            }

        }
        return $aSubject;

    }

    /**
     * Performs `array_map()` recursively.
     * @remark Accepts arguments.
     * @param  array    $aArray
     * @param  callable $oCallable
     * @param  array    $aArguments
     * @return array
     * @since  3.8.9
     */
    static private function ___getArrayMappedRecursive( array $aArray, $oCallable, array $aArguments=array() ) {

        self::$___oCurrentCallableForArrayMapRecursive = $oCallable;
        self::$___aArgumentsForArrayMapRecursive       = $aArguments;
        $_aArray = array_map( array( __CLASS__, '___getArrayMappedNested' ), $aArray );
        self::$___oCurrentCallableForArrayMapRecursive = null;
        self::$___aArgumentsForArrayMapRecursive       = array();
        return $_aArray;

    }
        static private $___oCurrentCallableForArrayMapRecursive;
        static private $___aArgumentsForArrayMapRecursive;
        /**
         * @internal
         * @return      mixed       A modified value.
         * @since       3.8.9
         * @param       mixed       $mItem
         */
        static private function ___getArrayMappedNested( $mItem ) {
            return is_array( $mItem )
                ? array_map( array( __CLASS__, '___getArrayMappedNested' ), $mItem )
                : call_user_func_array( 
                    self::$___oCurrentCallableForArrayMapRecursive, 
                    array_merge( array( $mItem ), self::$___aArgumentsForArrayMapRecursive )  
                );
        }

    /**
     * @param   integer $iSkip    The number of skipping records. This is used when the caller does not want to include the self function/method.
     * @param   null    $_deprecated
     * @return  string
     * @since   3.8.22
     * @since   3.8.23 Deprecated the `$oException` parameter.
     */
    static public function getStackTrace( $iSkip=0, $_deprecated=null ) {

        $_iSkip      = 1;   // need to skip this method trace itself
        $_oException = new Exception();

        // Backward compatibility.
        if ( is_object( $iSkip ) && $iSkip instanceof Exception ) {
            $_oException = $iSkip;
            $iSkip = ( integer ) $_deprecated;
        }

        $_iSkip      = $_iSkip + $iSkip;

        $_aTraces    = array();
        $_aFrames    = $_oException->getTrace();
        $_aFrames    = array_slice( $_aFrames, $_iSkip );
        foreach ( array_reverse( $_aFrames ) as $_iIndex => $_aFrame ) {

            $_aFrame     = $_aFrame + array(
                'file'  => null, 'line' => null, 'function' => null,
                'class' => null, 'args' => array(),
            );
            $_sArguments = self::___getArgumentsOfEachStackTrace( $_aFrame[ 'args' ] );
            $_aTraces[]  = sprintf(
                "#%s %s(%s): %s(%s)",
                $_iIndex + 1,
                $_aFrame[ 'file' ],
                $_aFrame[ 'line' ],
                isset( $_aFrame[ 'class' ] ) ? $_aFrame[ 'class' ] . '->' . $_aFrame[ 'function' ] : $_aFrame[ 'function' ],
                $_sArguments
            );

        }
        return implode( PHP_EOL, $_aTraces ) . PHP_EOL;

    }
        /**
         * @param array $aTraceArguments
         * @return string
         * @since 3.8.22
         * @internal
         */
        static private function ___getArgumentsOfEachStackTrace( array $aTraceArguments ) {

            $_aArguments = array();
            foreach ( $aTraceArguments as $_mArgument ) {
                $_sType        = gettype( $_mArgument );
                $_sType        = str_replace(
                    array( 'resource (closed)', 'unknown type', 'integer', 'double', ),
                    array( 'resource', 'unknown', 'scalar', 'scalar', ),
                    $_sType
                );
                $_sMethodName  = "___getStackTraceArgument_{$_sType}";
                $_aArguments[] = method_exists( __CLASS__, $_sMethodName )
                    ? self::{$_sMethodName}( $_mArgument )
                    : $_sType;
            }
            return join(", ",  $_aArguments );
        }
            /**
             * @since 3.8.22
             * @param mixed $mArgument
             * @internal
             * @return string
             */
            static private function ___getStackTraceArgument_string( $mArgument ) {
                $_sString = self::___getLegibleString( $mArgument, 200, true );
                return "'" . $_sString . "'";
            }
            static private function ___getStackTraceArgument_scalar( $mArgument ) {
                return $mArgument;
            }
            static private function ___getStackTraceArgument_boolean( $mArgument ) {
                return ( $mArgument ) ? "true" : "false";
            }
            static private function ___getStackTraceArgument_NULL( $mArgument ) {
                return 'NULL';
            }
            static private function ___getStackTraceArgument_object( $mArgument ) {
                return 'Object(' . get_class( $mArgument ) . ')';
            }
            static private function ___getStackTraceArgument_resource( $mArgument ) {
                return get_resource_type( $mArgument );
            }
            static private function ___getStackTraceArgument_unknown( $mArgument ) {
                return gettype( $mArgument );
            }
            static private function ___getStackTraceArgument_array( $mArgument ) {
                $_sOutput = '';
                $_iMax    = 10;
                $_iTotal  = count( $mArgument );
                $_iIndex  = 0;
                foreach( $mArgument as $_sKey => $_mValue ) {
                    $_iIndex++;
                    $_mValue   = is_scalar( $_mValue )
                        ? self::___getLegibleDetailedScalar( $_mValue, 100 )
                        : ucfirst( gettype( $_mValue ) ) . (
                            is_object( $_mValue )
                                ? ' (' . get_class( $_mValue ) . ')'
                                : ''
                        );
                    $_sOutput .= $_sKey . ': ' . $_mValue . ', ';
                    if ( $_iIndex > $_iMax && $_iTotal > $_iMax ) {
                        $_sOutput  = rtrim( $_sOutput, ','  ) . '...';
                        break;
                    }
                }
                $_sOutput = rtrim( $_sOutput, ',' );
                return "Array({$_sOutput})";
            }

}