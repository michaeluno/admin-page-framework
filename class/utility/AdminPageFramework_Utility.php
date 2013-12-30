<?php
if ( ! class_exists( 'AdminPageFramework_Utility' ) ) :
/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_WPUtility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_Utility extends AdminPageFramework_WPUtility {
	
	/**
	 * Converts non-alphabetic characters to underscore.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public static function sanitizeSlug( $sSlug ) {
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $sSlug ) );
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
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString );
	}	
	
	/**
	 * Retrieves a corresponding array value from the given array.
	 * 
	 * When there are multiple arrays and they have similar index structures but it's not certain if one has the key and the others,
	 * use this method to retrieve the corresponding key value. 
	 * 
	 * @remark			This is mainly used by the field array to insert user-defined key values.
	 * @return			string|array			If the key does not exist in the passed array, it will return the default. If the subject value is not an array, it will return the subject value itself.
	 * @since			2.0.0
	 * @since			2.1.3					Added the $bBlankToDefault parameter that sets the default value if the subject value is empty.
	 * @since			2.1.5					Changed the scope to public static from protected as converting all the utility methods to all public static.
	 */
	public static function getCorrespondingArrayValue( $vSubject, $sKey, $sDefault='', $bBlankToDefault=false ) {	
				
		// If $vSubject is null,
		if ( ! isset( $vSubject ) ) return $sDefault;	
			
		// If the $bBlankToDefault flag is set and the subject value is a blank string, return the default value.
		if ( $bBlankToDefault && $vSubject == '' ) return $sDefault;
			
		// If $vSubject is not an array, 
		if ( ! is_array( $vSubject ) ) return ( string ) $vSubject;	// consider it as string.
		
		// Consider $vSubject as array.
		if ( isset( $vSubject[ $sKey ] ) ) return $vSubject[ $sKey ];
		
		return $sDefault;
		
	}
	
	/**
	 * Finds the dimension depth of the given array.
	 * 
	 * @access			protected
	 * @since			2.0.0
	 * @remark			There is a limitation that this only checks the first element so if the second or other elements have deeper dimensions, it will not be caught.
	 * @param			array			$array			the subject array to check.
	 * @return			integer			returns the number of dimensions of the array.
	 */
	public static function getArrayDimension( $array ) {
		return ( is_array( reset( $array ) ) ) ? self::getArrayDimension( reset( $array ) ) + 1 : 1;
	}
	
	
	/**
	 * Merges multiple multi-dimensional array recursively.
	 * 
	 * The advantage of using this method over the array unite operator or array_merge() is that it merges recursively and the null values of the preceding array will be overridden.
	 * 
	 * @since			2.1.2
	 * @static
	 * @access			public
	 * @remark			The parameters are variadic and can add arrays as many as necessary.
	 * @return			array			the united array.
	 */
	public static function uniteArrays( $aPrecedence, $aDefault1 ) {
				
		$aArgs = array_reverse( func_get_args() );
		$aArray = array();
		foreach( $aArgs as $aArg ) 
			$aArray = self::uniteArraysRecursive( $aArg, $aArray );
			
		return $aArray;
		
	}
	
	/**
	 * Merges two multi-dimensional arrays recursively.
	 * 
	 * The first parameter array takes its precedence. This is useful to merge default option values. 
	 * An alternative to <em>array_replace_recursive()</em>; it is not supported PHP 5.2.x or below.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Changed the scope to static. 
	 * @access			public
	 * @remark			null values will be overwritten. 	
	 * @param			array			$aPrecedence			the array that overrides the same keys.
	 * @param			array			$aDefault				the array that is going to be overridden.
	 * @return			array			the united array.
	 */ 
	public static function uniteArraysRecursive( $aPrecedence, $aDefault ) {
				
		if ( is_null( $aPrecedence ) ) $aPrecedence = array();
		
		if ( ! is_array( $aDefault ) || ! is_array( $aPrecedence ) ) return $aPrecedence;
			
		foreach( $aDefault as $sKey => $v ) {
			
			// If the precedence does not have the key, assign the default's value.
			if ( ! array_key_exists( $sKey, $aPrecedence ) || is_null( $aPrecedence[ $sKey ] ) )
				$aPrecedence[ $sKey ] = $v;
			else {
				
				// if the both are arrays, do the recursive process.
				if ( is_array( $aPrecedence[ $sKey ] ) && is_array( $v ) ) 
					$aPrecedence[ $sKey ] = self::uniteArraysRecursive( $aPrecedence[ $sKey ], $v );			
			
			}
		}
		return $aPrecedence;		
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
	 * Determines whether the key is the last element of an array.
	 * 
	 * @since			3.0.0
	 */
	static public function isLastElement( array $aArray, $sKey ) {
		end( $aArray );
		return $sKey === key( $aArray );
	}	
	
	/**
	 * Generates the string of attributes to be embedded in an HTML tag.
	 * @since			3.0.0
	 */
	protected function getHTMLTagAttributesFromArray( array $aAttributes ) {
		
		$aOutput = array();
		foreach( $aAttributes as $sAttribute => $sProperty ) {
			if ( empty( $sProperty ) && $sProperty !== 0  )	continue;	// drop non-value elements.
			if ( is_array( $sProperty ) || is_object( $sProperty ) ) continue;	// must be resolved as a string.
			$aOutput[] = "{$sAttribute}='{$sProperty}'";
		}
		return implode( ' ', $aOutput );
		
	}	
	
}
endif;