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
	static public function dumpArray( $arr, $sFilePath=null ) {
		echo self::getArray( $arr, $sFilePath );
	}
	
	/**
	 * Retrieves the output of the given array contents.
	 * 
	 * If a file pass is given, it saves the output in the file.
	 * 
	 * @since			2.1.6			The $bEncloseInTag parameter is added.
	 * @since			3.0.0			Changed the $bEncloseInTag parameter to bEscape.
	 */
	static public function getArray( $arr, $sFilePath=null, $bEscape=true ) {
			
		if ( $sFilePath ) self::logArray( $arr, $sFilePath );			
		
		return $bEscape
			? "<pre class='dump-array'>" . htmlspecialchars( print_r( $arr, true ) ) . "</pre>"	// esc_html() has a bug that breaks with complex HTML code.
			: print_r( $arr, true );	// non-escape is used for exporting data into file.
		
	}	
	
	/**
	 * Logs the given array output into the given file.
	 * 
	 * @since			2.1.1
	 */
	static public function logArray( $arr, $sFilePath=null ) {
		
		static $_iPageLoadID;
		$_iPageLoadID = $_iPageLoadID ? $_iPageLoadID : uniqid();		
		
		$oCallerInfo = debug_backtrace();
		$sCallerFunction = $oCallerInfo[ 1 ]['function'];
		$sCallerClasss = $oCallerInfo[ 1 ]['class'];
		file_put_contents( 
			$sFilePath ? $sFilePath : dirname( __FILE__ ) . '/array_log.txt', 
			date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . ' ' . "{$_iPageLoadID} {$sCallerClasss}::{$sCallerFunction} " . AdminPageFramework_Utility::getCurrentURL() . PHP_EOL	
			. print_r( $asArray, true ) . PHP_EOL . PHP_EOL,
			FILE_APPEND 
		);			
							
	}	
}
endif;