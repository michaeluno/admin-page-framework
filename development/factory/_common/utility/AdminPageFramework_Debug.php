<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides debugging methods.
 * 
 * Use the methods of this class to check variable contents. 
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/utility/debug.png
 * @since           2.0.0
 * @since           3.1.3       Extends AdminPageFramework_WPUtility
 * @since           3.7.1       Extends AdminPageFramework_FrameworkUtility
 * @extends         AdminPageFramework_FrameworkUtility
 * @package         AdminPageFramework
 * @subpackage      Common/Utility
 */
class AdminPageFramework_Debug extends AdminPageFramework_FrameworkUtility {
            
    /**
     * Prints out the given variable contents
     * 
     * If a file pass is given to the second parameter, it saves the output in the file.
     * 
     * @since       3.2.0
     * @remark      An alias of the dumpArray() method.
     * @param       array|string    $asArray        The variable to check its contents.
     * @param       string          $sFilePath      The file path for a log file.
     * @return      void
     */
    static public function dump( $asArray, $sFilePath=null ) {
        echo self::get( $asArray, $sFilePath );
    }    
        /**
         * Prints out the given variable contents.
         * 
         * If a file pass is given, it saves the output in the file.
         * 
         * @since unknown
         * @deprecated      3.2.0
         */
        static public function dumpArray( $asArray, $sFilePath=null ) {
            self::dump( $asArray, $sFilePath );
        }    
        
    /**
     * Retrieves the output of the given variable contents.
     * 
     * If a file pass is given to the second parameter, it saves the output in the file.
     * 
     * @remark      An alias of getArray() method.
     * @since       3.2.0
     * @param       array|string    $asArray        The variable to check its contents.
     * @param       string          $sFilePath      The file path for a log file.
     * @param       boolean         $bEscape        Whether to escape characters.
     */
    static public function get( $asArray, $sFilePath=null, $bEscape=true ) {

        if ( $sFilePath ) {
            self::log( $asArray, $sFilePath );     
        }
        
        return $bEscape
            ? "<pre class='dump-array'>" 
                    . htmlspecialchars( self::getAsString( $asArray ) ) // `esc_html()` breaks with complex HTML code.
                . "</pre>" 
            : self::getAsString( $asArray ); // non-escape is used for exporting data into file.    
        
    }
        /**
         * Retrieves the output of the given array contents.
         * 
         * If a file pass is given, it saves the output in the file.
         * 
         * @since       2.1.6 The $bEncloseInTag parameter is added.
         * @since       3.0.0 Changed the $bEncloseInTag parameter to bEscape.
         * @deprecated  3.2.0
         */
        static public function getArray( $asArray, $sFilePath=null, $bEscape=true ) {
            self::showDeprecationNotice( __CLASS__ .'::' . __FUNCTION__,  __CLASS__ .'::get()' );
            return self::get( $asArray, $sFilePath, $bEscape );
        }      
            
    /**
     * Logs the given variable output to a file.
     * 
     * <h4>Example</h4>
     * <code>
     * $_aValues = array( 'foo', 'bar' );
     * AdminPageFramework_Debug::log( $aValues );
     * </code>
     * 
     * @remark      The alias of the `logArray()` method.
     * @since       3.1.0
     * @since       3.1.3       Made it leave milliseconds and elapsed time from the last call of the method.
     * @since       3.3.0       Made it indicate the data type.
     * @since       3.3.1       Made it indicate the data length.
     * @param       mixed       $mValue         The value to log.  
     * @param       string      $sFilePath      The log file path.
     * @return      void
     **/
    static public function log( $mValue, $sFilePath=null ) {
                
        static $_fPreviousTimeStamp = 0;
        
        $_oCallerInfo       = debug_backtrace();
        $_sCallerFunction   = self::getElement(
            $_oCallerInfo,  // subject array
            array( 1, 'function' ), // key
            ''      // default
        );                        
        $_sCallerClass      = self::getElement(
            $_oCallerInfo,  // subject array
            array( 1, 'class' ), // key
            ''      // default
        );           
        $_fCurrentTimeStamp = microtime( true );
        
        file_put_contents( 
            self::_getLogFilePath( $sFilePath, $_sCallerClass ), 
            self::_getLogHeadingLine( 
                $_fCurrentTimeStamp,
                round( $_fCurrentTimeStamp - $_fPreviousTimeStamp, 3 ),     // elapsed time
                $_sCallerClass,
                $_sCallerFunction
            ) . PHP_EOL
            . self::_getLogContents( $mValue ),
            FILE_APPEND 
        );     
        
        $_fPreviousTimeStamp = $_fCurrentTimeStamp;
        
    }   
        /**
         * Determines the log file path.
         * @since       3.5.3 
         * @internal    
         * @return      string      The path of the file to log the contents.
         */
        static private function _getLogFilePath( $bsFilePath, $sCallerClass ) {
        
            $_bFileExists = self::_createFile( $bsFilePath );
            if ( $_bFileExists ) {
                return $bsFilePath;
            }
            // Return a generated default log path.
            if ( true === $bsFilePath ) {
                return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . basename( get_class() ) . '_' . date( "Ymd" ) . '.log';
            }
            return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . basename( get_class() ) . '_' . basename( $sCallerClass ) . '_' . date( "Ymd" ) . '.log';
            
        }
            /**
             * Creates a file.
             * @return      boolean
             * @internal
             */
            static private function _createFile( $sFilePath ) {
                if ( ! $sFilePath || true === $sFilePath ) {
                    return false;
                }
                if ( file_exists( $sFilePath ) ) {
                    return true;
                }
                // Otherwise, create a file.
                $_bhResrouce = fopen( $sFilePath, 'w' );
                return ( boolean ) $_bhResrouce;                
            }
            
        /**
         * Returns the log contents.
         * @internal
         * @since       3.5.3
         * @param       mixed       $mValue     The value to log.  
         */
        static private function _getLogContents( $mValue ) {

            $_sType     = gettype( $mValue );
            $_iLengths  = self::_getValueLength( $mValue, $_sType );
            return '(' 
                . $_sType
                . ( null !== $_iLengths ? ', length: ' . $_iLengths : '' )
            . ') '
            . self::getAsString( $mValue ) 
            . PHP_EOL . PHP_EOL;
        
        }      
            /**
             * Returns a length of a value.
             * @since       3.5.3
             * @internal
             * @return      integer|null        For string or integer, the string length. For array, the element lengths. For other types, null.
             */
            static private function _getValueLength( $mValue, $sVariableType ) {
                
                if ( in_array( $sVariableType, array( 'string', 'integer' ) ) ) {
                    return strlen( $mValue );
                }
                if ( 'array' === $sVariableType ) {
                    return count( $mValue );
                }
                return null;
                
            }
        /**
         * Returns the heading part of a log item.
         * @since       3.5.3
         * @internal
         * @return      string      the heading part of a log item.
         */
        static private function _getLogHeadingLine( $fCurrentTimeStamp, $nElapsed, $sCallerClass, $sCallerFunction ) {
            
            static $_iPageLoadID; // identifies the page load.
            static $_nGMTOffset;
            
            $_nGMTOffset        = isset( $_nGMTOffset ) 
                ? $_nGMTOffset 
                : get_option( 'gmt_offset' );
            $_iPageLoadID       = $_iPageLoadID 
                ? $_iPageLoadID 
                : uniqid();
            $_nNow              = $fCurrentTimeStamp + ( $_nGMTOffset * 60 * 60 );
            $_nMicroseconds     = str_pad( round( ( $_nNow - floor( $_nNow ) ) * 10000 ), 4, '0' );
            
            $_aOutput           = array(
                date( "Y/m/d H:i:s", $_nNow ) . '.' . $_nMicroseconds,
                self::_getFormattedElapsedTime( $nElapsed ),
                $_iPageLoadID,
                AdminPageFramework_Registry::getVersion(),
                $sCallerClass . '::' . $sCallerFunction,
                current_filter(),
                self::getCurrentURL(),
            );
            return implode( ' ', $_aOutput );         
            
        }
            /**
             * Returns formatted elapsed time.
             * @since       3.5.3
             * @internal
             * @return      string      Formatted elapsed time.
             */
            static private function _getFormattedElapsedTime( $nElapsed ) {
                
                $_aElapsedParts     = explode( ".", ( string ) $nElapsed );
                $_sElapsedFloat     = str_pad(
                    self::getElement(
                        $_aElapsedParts,  // subject array
                        1, // key
                        0      // default
                    ),      
                    3, 
                    '0'
                );
                $_sElapsed          = self::getElement(
                    $_aElapsedParts,  // subject array
                    0,  // key
                    0   // default
                );                                   
                $_sElapsed          = strlen( $_sElapsed ) > 1 
                    ? '+' . substr( $_sElapsed, -1, 2 ) 
                    : ' ' . $_sElapsed;
                return $_sElapsed . '.' . $_sElapsedFloat;
            
            }
        /**
         * Logs the given array output into the given file.
         * 
         * @since       2.1.1
         * @since       3.0.3   Changed the default log location and file name.
         * @deprecated  3.1.0   Use the `log()` method instead.
         */
        static public function logArray( $asArray, $sFilePath=null ) {
            self::showDeprecationNotice( __CLASS__ .'::' . __FUNCTION__,  __CLASS__ .'::log()' );
            self::log( $asArray, $sFilePath );     
        }      
        
    /**
     * Returns a string representation of the given value.
     * @since       3.5.0
     * @param       mixed       $mValue     The value to get as a string
     * @internal
     */
    static public function getAsString( $mValue ) {
        
        $mValue = self::_getLegibleObject( $mValue );
        $mValue = self::_getLegibleCallable( $mValue );
        $mValue = self::_getLegibleArray( $mValue );            
        return print_r( $mValue, true );
        
    }
        /**
         * @since       3.8.9
         * @return      mixed
         */
        static private function _getLegibleCallable( $asoCallable ) {
            
            if ( ! is_callable( $asoCallable ) ) {
                return $asoCallable;
            }
            if ( is_string( $asoCallable ) ) {
                return '(callable) ' . $asoCallable;
            }
            if ( is_object( $asoCallable ) ) {
                return '(callable) ' . get_class( $asoCallable );
            }
            $_sSubject = is_object( $asoCallable[ 0 ] )
                ? get_class( $asoCallable[ 0 ] )
                : ( string ) $asoCallable[ 0 ];

            return '(callable) ' . $_sSubject . '::' . ( string ) $asoCallable[ 1 ];
            
        }
        
        /**
         * @since       3.8.9
         * @return      mixed
         */
        static public function _getLegibleObject( $oObject ) {
            
            if ( ! is_object( $oObject ) ) {
                return $oObject;
            }
            
            if ( method_exists( $oObject, '__toString' ) ) {
                return ( string ) $oObject;
            }
            
            return '(object) ' . get_class( $oObject ) . ' ' 
                . count( get_object_vars( $oObject ) ) . ' properties.';
            
        }
        
        /**
         * @since       3.8.9
         * @return      mixed
         */
        static public function _getLegibleArray( $aArray ) {
            
            if ( ! is_array( $aArray ) ) {
                return $aArray;
            }
            return self::_getArrayMappedRecursive( 
                self::getSliceByDepth( $aArray, 10 ), 
                array( __CLASS__, '_getLegibleValue' ) 
            );
            
        }
 
    /**
     * Slices the given array by depth.
     * 
     * @since       3.4.4
     * @since       3.8.9       Changed it not to convert an object into an array.
     * @return      array
     * @internal
     */
    static public function getSliceByDepth( array $aSubject, $iDepth=0 ) {

        foreach ( $aSubject as $_sKey => $_vValue ) {
            if ( is_array( $_vValue ) ) {
                $_iDepth = $iDepth;
                if ( $iDepth > 0 ) {
                    $aSubject[ $_sKey ] = self::getSliceByDepth( $_vValue, --$iDepth );
                    $iDepth = $_iDepth;
                    continue;
                } 
                unset( $aSubject[ $_sKey ] );
            }
        }
        return $aSubject;
        
    }        

        /**
         * @return      string
         * @since       3.8.9
         */
        static private function _getLegibleValue( $mItem ) {
            
            if ( is_object( $mItem ) ) {
                return '(object) ' . get_class( $mItem );
            }
            if ( is_null( $mItem ) ) {
                return '(null)';
            }
            if ( is_bool( $mItem ) ) {
                return '(boolean) ' . ( $mItem ? 'true' : 'false' );
            }
            // At this point, the value is a scalar.
            return self::_getLegibleString( $mItem );

        }
            /**
             * @return      string
             * @since       3.8.9
             */
            static private function _getLegibleString( $sScalar, $iCharLimit=300 ) {
                
                static $_iMBSupport;
                $_iMBSupport = isset( $_iMBSupport ) ? $_iMBSupport : function_exists( 'mb_strlen' );      
                $_aStrLenMethod = array( 'strlen', 'mb_strlen' );
                $_aSubstrMethod = array( 'substr', 'mb_substr' );
                
                $_iCharLength = call_user_func_array( $_aStrLenMethod[ $_iMBSupport ], array( $sScalar ) );
                if ( $_iCharLength <= $iCharLimit ) {
                    return '(' . gettype( $sScalar ) . ') ' . $sScalar;
                }
                return '(' . gettype( $sScalar ) . ') '
                    . call_user_func_array( $_aSubstrMethod[ $_iMBSupport ], array( $sScalar, 0, $iCharLimit ) ) 
                    . '...';
                    
            }
        /**
         * Performs `array_map()` recursively.
         * @return      array.
         * @since       3.8.9
         */
        static private function _getArrayMappedRecursive( array $aArray, $oCallable ) {
            
            self::$oCurrentCallable = $oCallable;
            $_aArray = array_map( array( __CLASS__, '_getArrayMappedNested' ), $aArray );
            self::$oCurrentCallable = null;
            return $_aArray;
            
        }
            static public $oCurrentCallable;
            /**
             * @internal
             * @return      mixed       A modified value.
             * @since       3.8.9
             */
            static private function _getArrayMappedNested( $mItem ) {            
                return is_array( $mItem ) 
                    ? array_map( array( __CLASS__, '_getArrayMappedNested' ), $mItem ) 
                    : call_user_func( self::$oCurrentCallable, $mItem );            
            }    
    
}
