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
	 * @since			3.0.3			Changed the default log location and file name.
	 */
	static public function logArray( $asArray, $sFilePath=null ) {
		
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
}
endif;