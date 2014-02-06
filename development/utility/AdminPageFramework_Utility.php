<?php
if ( ! class_exists( 'AdminPageFramework_Utility' ) ) :
/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility_Array
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
abstract class AdminPageFramework_Utility extends AdminPageFramework_Utility_Array {
	
	/**
	 * Converts non-alphabetic characters to underscore.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string|null			The sanitized string.
	 */ 
	public static function sanitizeSlug( $sSlug ) {
		return is_null( $sSlug )
			? null
			: preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $sSlug ) );
	}	
	
	/**
	 * Converts non-alphabetic characters to underscore except hyphen(dash).
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public static function sanitizeString( $sString ) {
		return is_null( $sString )
			? null
			: preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString );
	}	
		
	/**
	 * Retrieves the query value from the given URL with a key.
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	static public function getQueryValueInURLByKey( $sURL, $sQueryKey ) {
		
		$aURL = parse_url( $sURL );
		parse_str( $aURL['query'], $aQuery );		
		return isset( $aQuery[ $sQueryKey ] ) ? $aQuery[ $sQueryKey ] : null;
		
	}
	
	/**
	 * Checks if the passed value is a number and set it to the default if not.
	 * 
	 * This is useful for form data validation. If it is a number and exceeds the set maximum number, 
	 * it sets it to the maximum value. If it is a number and is below the minimum number, it sets to the minimum value.
	 * Set a blank value for no limit.
	 * 
	 * @since			2.0.0
	 * @return			string|integer			A numeric value will be returned. 
	 */ 
	static public function fixNumber( $nToFix, $nDefault, $nMin="", $nMax="" ) {

		if ( ! is_numeric( trim( $nToFix ) ) ) return $nDefault;
		if ( $nMin !== "" && $nToFix < $nMin ) return $nMin;
		if ( $nMax !== "" && $nToFix > $nMax ) return $nMax;
		return $nToFix;
		
	}		
	
	/**
	 * Calculates the relative path from the given path.
	 * 
	 * This function is used to generate a template path.
	 * 
	 * @since			2.1.5
	 * @author			Gordon
	 * @author			Michael Uno,			Modified variable names and spacing.
	 * @see				http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
	 */
	static public function getRelativePath( $from, $to ) {
		
		// some compatibility fixes for Windows paths
		$from = is_dir( $from ) ? rtrim( $from, '\/') . '/' : $from;
		$to   = is_dir( $to )   ? rtrim( $to, '\/') . '/'   : $to;
		$from = str_replace( '\\', '/', $from );
		$to   = str_replace( '\\', '/', $to );

		$from     = explode( '/', $from );
		$to       = explode( '/', $to );
		$relPath  = $to;

		foreach( $from as $depth => $dir ) {
			// find first non-matching dir
			if( $dir === $to[ $depth ] ) {
				// ignore this directory
				array_shift( $relPath );
			} else {
				// get number of remaining dirs to $from
				$remaining = count( $from ) - $depth;
				if( $remaining > 1 ) {
					// add traversals up to first matching dir
					$padLength = ( count( $relPath ) + $remaining - 1 ) * -1;
					$relPath = array_pad( $relPath, $padLength, '..' );
					break;
				} else {
					$relPath[ 0 ] = './' . $relPath[ 0 ];
				}
			}
		}
		return implode( '/', $relPath );
		
	}
	
	/**
	 * Attempts to find the caller scrip path.
	 * 
	 * @since			3.0.0
	 * @return			string
	 */
	static public function getCallerScriptPath( $asRedirectedFiles=array( __FILE__ ) ) {
		
		$aRedirectedFiles = ( array ) $asRedirectedFiles;
		$aRedirectedFiles[] = __FILE__;
		$sCallerFilePath = '';
		foreach( debug_backtrace() as $aDebugInfo )  {			
			$sCallerFilePath = $aDebugInfo['file'];
			if ( in_array( $sCallerFilePath, $aRedirectedFiles ) ) continue;
			break;	// the first found item.
		}
		return $sCallerFilePath;
	}	
	
	/**
	 * Generates the string of attributes to be embedded in an HTML tag from an associative array.
	 * 
	 * For example, 
	 * 	array( 'id' => 'my_id', 'name' => 'my_name', 'style' => 'background-color:#fff' )
	 * becomes
	 * 	id="my_id" name="my_name" style="background-color:#fff"
	 * 
	 * This is mostly used by the method to output input fields.
	 * @since			3.0.0
	 */
	static public function generateAttributes( array $aAttributes ) {
		
		$aOutput = array();
		foreach( $aAttributes as $sAttribute => $sProperty ) {
			if ( empty( $sProperty ) && $sProperty !== 0  )	continue;	// drop non-value elements.
			if ( is_array( $sProperty ) || is_object( $sProperty ) ) continue;	// must be resolved as a string.
			$aOutput[] = "{$sAttribute}='{$sProperty}'";
		}
		return implode( ' ', $aOutput );
		
	}	
	
	/**
	 * Compresses CSS rules.
	 * 
	 * @since			3.0.0
	 */
	static public function minifyCSS( $sCSSRules ) {
		
		return str_replace( 
			array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '), 	// remove line breaks, tab, and white sspaces.
			'', 
			preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCSSRules )	// remove comments
		);
		
	}	
		
}
endif;