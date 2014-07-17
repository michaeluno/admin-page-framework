<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
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
abstract class AdminPageFramework_Utility extends AdminPageFramework_Utility_URL {
			
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
			if ( empty( $sProperty ) && 0 !== $sProperty && '0' !== $sProperty ) { continue; }	// drop non value elements except numeric 0.
			if ( is_array( $sProperty ) || is_object( $sProperty ) ) continue;	// must be resolved as a string.
			$aOutput[] = "{$sAttribute}='{$sProperty}'";
		}
		return implode( ' ', $aOutput );
		
	}	
	
}
endif;