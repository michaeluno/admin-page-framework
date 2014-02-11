<?php
if ( ! class_exists( 'AdminPageFramework_Utility_Array' ) ) :
/**
 * Provides utility methods dealing with PHP arrays which do not use WordPress functions.
 *
 * @since			2.0.0
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_Array {
	
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
	 * Casts array contents into another while keeping the same key structure.
	 * 
	 * @since			3.0.0
	 * @remark			It won't check key structure deeper than or equal to the second dimension.
	 * @param			array				the array that holds the necessary keys.
	 * @param			array				the array to be modified.
	 * @return			array				the modified array.
	 */
	public static function castArrayContents( $aModel, $aSubject ) {
		
		$aMod = array();
		foreach( $aModel as $sKey => $_v ) 
			$aMod[ $sKey ] = isset( $aSubject[ $sKey ] ) ? $aSubject[ $sKey ] : null;

		return $aMod;
		
	}
	
	/**
	 * Returns the array consisting of keys which don't exist in another.
	 * 
	 * @since			3.0.0
	 * @remark			It won't check key structure deeper than or equal to the second dimension.
	 * @param			array				the array that holds the necessary keys.
	 * @param			array				the array to be modified.
	 * @return			array				the modified array.
	 */
	public static function invertCastArrayContents( $sModel, $aSubject ) {
		
		$aMod = array();
		foreach( $sModel as $sKey => $_v ) {
			
			if ( array_key_exists( $sKey, $aSubject ) ) continue;
			
			$aMod[ $sKey ] = $_v;
			
		}
		return $aMod;
		
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
	 * @param			array			the array that overrides the same keys.
	 * @param			array			the array that is going to be overridden.
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
	 * Determines whether the key is the last element of an array.
	 * 
	 * @since			3.0.0
	 */
	static public function isLastElement( array $aArray, $sKey ) {
		end( $aArray );
		return $sKey === key( $aArray );
	}	
		
	/**
	 * Removes non-numeric keys from the array 
	 * 
	 * @since			3.0.0
	 */
	static public function getIntegerElements( $aParse ) {
		
		foreach ( $aParse as $isKey => $v ) {
			
			if ( ! is_numeric( $isKey ) ) {
				unset( $aParse[ $isKey ] );
				continue;
			}
			
			$isKey = $isKey + 0;	// this will convert string numeric value to integer or flaot.
			
			if ( ! is_int( $isKey ) ) 
				unset( $aParse[ $isKey ] );
				
		}
		return $aParse;
	} 
	
	/**
	 * Re-composes the given array by numerizing the keys. 
	 * 
	 * @since			3.0.0
	 */
	static public function numerizeElements( $aSubject ) {
		
		/* The passed array structure looks like this
		 array( 
			0 => array(
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
			), 
			1 => array(
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
			),
			'field_id_1' => array( ... ),
			'field_id_2' => array( ... ),
		 )
		 
		 It will be converted to to
		 array(
			0 => array(
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
			), 
			1 => array(
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
			),				
			2 => array(
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
			),
		 )
		 */
		$_aNumeric = self::getIntegerElements( $aSubject );
		$_aAssociative = self::invertCastArrayContents( $aSubject, $_aNumeric );
		foreach( $_aNumeric as &$_aElem ) 
			$_aElem = self::uniteArrays( $_aElem, $_aAssociative );
		if ( ! empty( $_aAssociative ) )
			array_unshift( $_aNumeric, $_aAssociative );	// insert the main section to the beginning of the array.
		return $_aNumeric;
		
	}
	
	
}
endif;