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

        if ( $sFilePath ) self::log( $asArray, $sFilePath );     
        
        return $bEscape
            ? "<pre class='dump-array'>" . htmlspecialchars( self::getAsString( $asArray ) ) . "</pre>" // esc_html() has a bug that breaks with complex HTML code.
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
         * @since        3.5.3 
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
            self::log( $asArray, $sFilePath );     
        }      
        
    /**
     * Returns a string representation of the given value.
     * @since       3.5.0
     * @param       mized       $mValue     The value to get as a string
     * @internal
     */
    static public function getAsString( $mValue ) {
        
        $mValue = is_object( $mValue )
            ? ( method_exists( $mValue, '__toString' ) 
                ? ( string ) $mValue          // cast string
                : ( array ) $mValue           // cast array
            )
            : $mValue;
        $mValue = is_array( $mValue )
            ? self::getSliceByDepth( $mValue, 10 )
            : $mValue;
            
        return print_r( $mValue, true );
        
    }
    
    /**
     * Slices the given array by depth.
     * 
     * @since       3.4.4
     * @return      array
     * @internal
     */
    static public function getSliceByDepth( array $aSubject, $iDepth=0 ) {

        foreach ( $aSubject as $_sKey => $_vValue ) {
            if ( is_object( $_vValue ) ) {
                $aSubject[ $_sKey ] = method_exists( $_vValue, '__toString' ) 
                    ? ( string ) $_vValue           // cast string
                    : get_object_vars( $_vValue );  // convert it to array.
                continue;
            }
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
    
}
