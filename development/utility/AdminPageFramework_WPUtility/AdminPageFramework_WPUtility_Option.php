<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WPUtility_Option' ) ) :
/**
 * Provides utility methods dealing with the options table which use WordPress functions.
 *
 * @since			3.0.1
 * @extends			AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Option extends AdminPageFramework_WPUtility_File {
	
	/**
	 * Retrieves the saved option value from the given option key, field ID, and section ID.
	 * 
	 * @since			3.0.1
	 * @param			string			$sOptionKey			the option key of the options table.
	 * @param			string			$asKey				the field id or the array that represents the key structure consisting of the section ID and the field ID.
	 * @param			mixed			$vDefault			the default value that will be returned if nothing is stored.
	 */
	static public function getOption( $sOptionKey, $asKey=null, $vDefault=null ) {
		
		// If only the option key is given, the default value is treated as the entire option data.
		if ( ! $asKey ) {
			return get_option( $sOptionKey, isset( $vDefault ) ? $vDefault : array() );
		}
		
		// Now either the section ID or field ID is given. 
		$_aOptions = get_option( $sOptionKey, array() );
		$_aKeys = self::shiftTillTrue( self::getAsArray( $asKey ) );

		return self::getArrayValueByArrayKeys( $_aOptions, $_aKeys, $vDefault );
		
	}
	
	/**
	 * Retrieves the saved option value from the given option key, field ID, and section ID.
	 * 
	 * @since			3.1.0
	 * @remark			Used in the network admin area.
	 * @param			string			$sOptionKey			the option key of the options table.
	 * @param			string			$asKey				the field id or the array that represents the key structure consisting of the section ID and the field ID.
	 * @param			mixed			$vDefault			the default value that will be returned if nothing is stored.
	 */
	static public function getSiteOption( $sOptionKey, $asKey=null, $vDefault=null ) {

		// If only the option key is given, the default value is treated as the entire option data.
		if ( ! $asKey ) {
			return get_site_option( $sOptionKey, isset( $vDefault ) ? $vDefault : array() );
		}
		
		// Now either the section ID or field ID is given. 
		$_aOptions = get_site_option( $sOptionKey, array() );
		$_aKeys = self::shiftTillTrue( self::getAsArray( $asKey ) );

		return self::getArrayValueByArrayKeys( $_aOptions, $_aKeys, $vDefault );
		
	}	
	
}
endif;