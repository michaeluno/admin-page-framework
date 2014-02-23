<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WPUtility_File' ) ) :
/**
 * Provides utility methods regarding reading file which use WordPress built-in functions and classes.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
class AdminPageFramework_WPUtility_File extends AdminPageFramework_WPUtility_Hook {
	
	/**
	 * Returns an array of plugin data from the given path.		
	 * 
	 * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public and become static.
	 * @access			public
	 */ 
	static public function getScriptData( $sPath, $sType='plugin' )	{
	
		$aData = get_file_data( 
			$sPath, 
			array(
				'sName' => 'Name',
				'sURI' => 'URI',
				'sScriptName' => 'Script Name',
				'sLibraryName' => 'Library Name',
				'sLibraryURI' => 'Library URI',
				'sPluginName' => 'Plugin Name',
				'sPluginURI' => 'Plugin URI',
				'sThemeName' => 'Theme Name',
				'sThemeURI' => 'Theme URI',
				'sVersion' => 'Version',
				'sDescription' => 'Description',
				'sAuthor' => 'Author',
				'sAuthorURI' => 'Author URI',
				'sTextDomain' => 'Text Domain',
				'sDomainPath' => 'Domain Path',
				'sNetwork' => 'Network',
				// Site Wide Only is deprecated in favour of Network.
				'_sitewide' => 'Site Wide Only',
			),
			in_array( $sType, array( 'plugin', 'theme' ) ) ? $sType : 'plugin' 
		);			

		switch ( trim( $sType ) ) {
			case 'theme':	
				$aData['sName'] = $aData['sThemeName'];
				$aData['sURI'] = $aData['sThemeURI'];
				break;
			case 'library':	
				$aData['sName'] = $aData['sLibraryName'];
				$aData['sURI'] = $aData['sLibraryURI'];
				break;
			case 'script':	
				$aData['sName'] = $aData['sScriptName'];
				break;		
			case 'plugin':	
				$aData['sName'] = $aData['sPluginName'];
				$aData['sURI'] = $aData['sPluginURI'];
				break;
			default:	
				break;				
		}		

		return $aData;
		
	}
	
}
endif;