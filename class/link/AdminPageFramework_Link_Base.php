<?php
if ( ! class_exists( 'AdminPageFramework_Link_Base' ) ) :
/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
abstract class AdminPageFramework_Link_Base extends AdminPageFramework_Utility {
	
	/**
	 * @internal
	 * @since			2.0.0
	 */ 
	private static $_aStructure_CallerInfo = array(
		'sPath'			=> null,
		'type'			=> null,
		'sName'			=> null,		
		'sURI'			=> null,
		'sVersion'		=> null,
		'sThemeURI'		=> null,
		'sScriptURI'		=> null,
		'sAuthorURI'		=> null,
		'sAuthor'			=> null,
		'description'	=> null,
	);	
	
	/*
	 * Methods for getting script info.
	 */ 
	
	/**
	 * Retrieves the caller script information whether it's a theme or plugin or something else.
	 * 
	 * @since			2.0.0
	 * @remark			The information can be used to embed into the footer etc.
	 * @return			array			The information of the script.
	 */	 
	protected function getCallerInfo( $sCallerPath=null ) {
		
		$aCallerInfo = self::$_aStructure_CallerInfo;
		$aCallerInfo['sPath'] = $sCallerPath;
		$aCallerInfo['type'] = $this->getCallerType( $aCallerInfo['sPath'] );

		if ( $aCallerInfo['type'] == 'unknown' ) return $aCallerInfo;
		
		if ( $aCallerInfo['type'] == 'plugin' ) 
			return $this->getScriptData( $aCallerInfo['sPath'], $aCallerInfo['type'] ) + $aCallerInfo;
			
		if ( $aCallerInfo['type'] == 'theme' ) {
			$oTheme = wp_get_theme();	// stores the theme info object
			return array(
				'sName'			=> $oTheme->Name,
				'sVersion' 		=> $oTheme->Version,
				'sThemeURI'		=> $oTheme->get( 'ThemeURI' ),
				'sURI'			=> $oTheme->get( 'ThemeURI' ),
				'sAuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'sAuthor'			=> $oTheme->get( 'Author' ),				
			) + $aCallerInfo;	
		}
	}

	/**
	 * Retrieves the library script info.
	 * 
	 * @since			2.1.1
	 */
	protected function getLibraryInfo() {
		return $this->getScriptData( __FILE__, 'library' ) + self::$_aStructure_CallerInfo;
	}
	
	/**
	 * Determines the script type.
	 * 
	 * It tries to find what kind of script this is, theme, plugin or something else from the given path.
	 * @since			2.0.0
	 * @return		string				Returns either 'theme', 'plugin', or 'unknown'
	 */ 
	protected function getCallerType( $sScriptPath ) {
		
		if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) return 'theme';
		if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) return 'plugin';
		return 'unknown';	
	
	}
	protected function getCallerPath() {

		foreach( debug_backtrace() as $aDebugInfo )  {			
			if ( $aDebugInfo['file'] == __FILE__ ) continue;
			return $aDebugInfo['file'];	// return the first found item.
		}
	}	
	
	/**
	 * Sets the default footer text on the left hand side.
	 * 
	 * @since			2.1.1
	 */
	protected function setFooterInfoLeft( $aScriptInfo, &$sFooterInfoLeft ) {
		
		$sDescription = empty( $aScriptInfo['description'] ) 
			? ""
			: "&#13;{$aScriptInfo['description']}";
		$sVersion = empty( $aScriptInfo['sVersion'] )
			? ""
			: "&nbsp;{$aScriptInfo['sVersion']}";
		$sPluginInfo = empty( $aScriptInfo['sURI'] ) 
			? $aScriptInfo['sName'] 
			: "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";
		$sAuthorInfo = empty( $aScriptInfo['sAuthorURI'] )	
			? $aScriptInfo['sAuthor'] 
			: "<a href='{$aScriptInfo['sAuthorURI']}' target='_blank'>{$aScriptInfo['sAuthor']}</a>";
		$sAuthorInfo = empty( $aScriptInfo['sAuthor'] ) 
			? $sAuthorInfo 
			: ' by ' . $sAuthorInfo;
		$sFooterInfoLeft =  $sPluginInfo . $sAuthorInfo;
		
	}
	/**
	 * Sets the default footer text on the right hand side.
	 * 
	 * @since			2.1.1
	 */	
	protected function setFooterInfoRight( $aScriptInfo, &$sFooterInfoRight ) {
	
		$sDescription = empty( $aScriptInfo['description'] ) 
			? ""
			: "&#13;{$aScriptInfo['description']}";
		$sVersion = empty( $aScriptInfo['sVersion'] )
			? ""
			: "&nbsp;{$aScriptInfo['sVersion']}";		
		$sLibraryInfo = empty( $aScriptInfo['sURI'] ) 
			? $aScriptInfo['sName'] 
			: "<a href='{$aScriptInfo['sURI']}' target='_blank' title='{$aScriptInfo['sName']}{$sVersion}{$sDescription}'>{$aScriptInfo['sName']}</a>";	
	
		$sFooterInfoRight = $this->oMsg->__( 'powered_by' ) . '&nbsp;' 
			. $sLibraryInfo
			. ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>";
		
	}
}
endif;