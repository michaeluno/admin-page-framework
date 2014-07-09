<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Debug' ) ) :
/**
 * Provides debugging methods.
 *
 * @since			2.0.0
 * @package			AdminPageFramework
 * @subpackage		Debug
 * @internal
 */
class AdminPageFramework_Debug {
		
	/**
	 * Prints out the given array contents
	 * 
	 * If a file pass is given, it saves the output in the file.
	 * 
	 * @since			unknown
	 */
	static public function dumpArray( $asArray, $sFilePath=null ) {
		echo self::getArray( $asArray, $sFilePath );
	}
	
	/**
	 * Retrieves the output of the given array contents.
	 * 
	 * If a file pass is given, it saves the output in the file.
	 * 
	 * @since			2.1.6			The $bEncloseInTag parameter is added.
	 * @since			3.0.0			Changed the $bEncloseInTag parameter to bEscape.
	 */
	static public function getArray( $asArray, $sFilePath=null, $bEscape=true ) {
			
		if ( $sFilePath ) self::logArray( $asArray, $sFilePath );			
		
		return $bEscape
			? "<pre class='dump-array'>" . htmlspecialchars( print_r( $asArray, true ) ) . "</pre>"	// esc_html() has a bug that breaks with complex HTML code.
			: print_r( $asArray, true );	// non-escape is used for exporting data into file.
		
	}	
	
	/**
	 * Logs the given array output into the given file.
	 * 
	 * @since			2.1.1
	 * @since			3.0.3		Changed the default log location and file name.
	 * @deprecated		3.1.0		Use the log() method instead
	 */
	static public function logArray( $asArray, $sFilePath=null ) {
		
		self::log( $asArray, $sFilePath );
		static $_iPageLoadID;
		$_iPageLoadID = $_iPageLoadID ? $_iPageLoadID : uniqid();		
		
		$_oCallerInfo = debug_backtrace();
		$_sCallerFunction = isset( $_oCallerInfo[ 1 ]['function'] ) ? $_oCallerInfo[ 1 ]['function'] : '';
		$_sCallerClasss = isset( $_oCallerInfo[ 1 ]['class'] ) ? $_oCallerInfo[ 1 ]['class'] : '';
		$sFilePath = $sFilePath
			? $sFilePath
			: WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . date( "Ymd" ) . '.log';		
		file_put_contents( 
			$sFilePath,
			date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . ' ' . "{$_iPageLoadID} {$_sCallerClasss}::{$_sCallerFunction} " . AdminPageFramework_Utility::getCurrentURL() . PHP_EOL	
			. print_r( $asArray, true ) . PHP_EOL . PHP_EOL,
			FILE_APPEND 
		);			
			
	}	
	
	/**
	 * Logs the given variable output to a file.
	 * 
	 * @remark		The alias of the logArray() method.
	 * @since		3.1.0
	 **/
	static public function log( $v, $sFilePath=null ) {
				
		static $_iPageLoadID;	// identifies the page load.
		$_iPageLoadID		= $_iPageLoadID ? $_iPageLoadID : uniqid();		
		$_oCallerInfo		= debug_backtrace();
		$_sCallerFunction	= isset( $_oCallerInfo[ 1 ]['function'] ) ? $_oCallerInfo[ 1 ]['function'] : '';
		$_sCallerClasss		= isset( $_oCallerInfo[ 1 ]['class'] ) ? $_oCallerInfo[ 1 ]['class'] : '';
		$sFilePath 			= ! $sFilePath
			? WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . $_sCallerClasss . '_' . date( "Ymd" ) . '.log'
			: ( true === $sFilePath
				? WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . date( "Ymd" ) . '.log'
				: $sFilePath
			);
		$_sHeading = date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . ' ' 
			. "{$_iPageLoadID} {$_sCallerClasss}::{$_sCallerFunction} " 
			. AdminPageFramework_Utility::getCurrentURL();
		file_put_contents( 
			$sFilePath, 
			$_sHeading . PHP_EOL . print_r( $v, true ) . PHP_EOL . PHP_EOL,
			FILE_APPEND 
		);			
	}		
}
endif;