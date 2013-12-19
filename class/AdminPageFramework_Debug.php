<?php
if ( ! class_exists( 'AdminPageFramework_Debug' ) ) :
/**
 * Provides debugging methods.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_Debug extends AdminPageFramework_Utility {
		
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
	 */
	static public function getArray( $arr, $sFilePath=null, $bEncloseInTag=true ) {
			
		if ( $sFilePath ) 
			self::logArray( $arr, $sFilePath );			
			
		// esc_html() has a bug that breaks with complex HTML code.
		$sResult = htmlspecialchars( print_r( $arr, true ) );
		return $bEncloseInTag
			? "<pre class='dump-array'>" . $sResult . "</pre>"
			: $sResult;
		
	}	
	
	/**
	 * Logs the given array output into the given file.
	 * 
	 * @since			2.1.1
	 */
	static public function logArray( $arr, $sFilePath=null ) {
		
		file_put_contents( 
			$sFilePath ? $sFilePath : dirname( __FILE__ ) . '/array_log.txt', 
			date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . PHP_EOL
			. print_r( $arr, true ) . PHP_EOL . PHP_EOL
			, FILE_APPEND 
		);					
							
	}	
}
endif;