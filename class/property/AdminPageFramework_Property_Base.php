<?php
if ( ! class_exists( 'AdminPageFramework_Property_Base' ) ) :

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */ 
abstract class AdminPageFramework_Property_Base {

	/**
	 * Represents the structure of the script info array.
	 * @internal
	 * @since			2.0.0
	 * @since			3.0.0			Moved from the link class.
	 */ 
	private static $_aStructure_CallerInfo = array(
		'sPath'			=> null,
		'sType'			=> null,
		'sName'			=> null,		
		'sURI'			=> null,
		'sVersion'		=> null,
		'sThemeURI'		=> null,
		'sScriptURI'		=> null,
		'sAuthorURI'		=> null,
		'sAuthor'			=> null,
		'sDescription'	=> null,
	);	
	
	/**
	 * Stores the library information.
	 * 
	 * @since			3.0.0
	 */
	static public $_aLibraryData;	// do not assign anything here because it is checked whether it is set.

	/**
	 * Stores the main (caller) object.
	 * 
	 * @since			2.1.5
	 */
	protected $oCaller;	
	
	/**
	 * Stores the caller script file path.
	 * 
	 * @since			3.0.0
	 */
	public $sCallerPath;
	
	/**
	 * Stores the caller script data
	 * 
	 * @since			Unknown
	 */
	public $aScriptInfo;		// do not assign a value here since it is checked whether it is set or not.
	
	/**
	 * Stores the extended class name that instantiated the property object.
	 * 
	 * @since			
	 */
	public $sClassName;
	
	/**
	 * The MD5 hash string of the extended class name.
	 * @since			
	 */
	public $sClassHash;
	
	/**
	 * Stores the script to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 			
	public $sScript = '';	

	/**
	 * Stores the CSS rules to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 		
	public $sStyle = '';	
	
	/**
	 * Stores the CSS rules for IE to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0 to 2.1.4
	 * @internal
	 */ 
	public $sStyleIE = '';	
	
	/**
	 * Stores the field type definitions.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public $aFieldTypeDefinitions = array();
	
	/**
	 * The default CSS rules loaded in the head tag of the created admin pages.
	 * 
	 * @since			2.0.0
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @access			public	
	 * @internal	
	 */
	public static $sDefaultStyle =
		".wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 		
				
		/* Contextual Help Page */
		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
		}
		
		/* Form Elements */
		/* Disabled */
		.admin-page-framework-fields .disabled {
			color: #888;
		}
		/* HR */
		.admin-page-framework-fields hr {
			border: 0; 
			height: 0;
			border-top: 1px solid #dfdfdf; 
			
		}
		
		/* Delimiter */
		.admin-page-framework-fields .delimiter {
			display: inline;
		}
		/* Description */
		.admin-page-framework-fields .admin-page-framework-fields-description {
			/* margin-top: 0px; */
			/* margin-bottom: 0.5em; */
			margin-bottom: 0;
		}
		/* Input form elements */
		.admin-page-framework-field {
			display: inline;
			margin-top: 1px;
			margin-bottom: 1px;
		}
		.admin-page-framework-field label{
			width: 100%;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-bottom: 0.25em;
		}
		@media only screen and ( max-width: 780px ) {	/* For WordPress v3.8 or greater */
			.admin-page-framework-field .admin-page-framework-input-label-container {
				margin-bottom: 0.5em;
			}
		}			
		
		.admin-page-framework-field .admin-page-framework-input-label-string {
			padding-right: 1em;	/* for checkbox label strings, a right padding is needed */
		}
		.admin-page-framework-field .admin-page-framework-input-button-container {
			padding-right: 1em; 
		}
		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle; 
		}
		.admin-page-framework-field-image .admin-page-framework-input-label-container {			
			vertical-align: middle;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field .admin-page-framework-input-label-string
		{
			display: inline-block;		
			vertical-align: middle;
		}
		.admin-page-framework-field-radio .admin-page-framework-input-container {
			display: inline;
		}
		
		/* Repeatable Fields */		
		.admin-page-framework-field.repeatable {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;
			margin-bottom: 0.5em;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 2px;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Import Field */
		.admin-page-framework-field-import input {
			margin-right: 0.5em;
		}
		/* Page Load Stats */
		#admin-page-framework-page-load-stats {
			clear: both;
			display: inline-block;
			width: 100%
		}
		#admin-page-framework-page-load-stats li{
			display: inline;
			margin-right: 1em;
		}		
		
		/* To give the footer area more space */
		#wpbody-content {
			padding-bottom: 140px;
		}
		";	
		
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 * @since			2.1.5			Moved the contents to the taxonomy field definition so it become an empty string.
	 */
	public static $sDefaultStyleIE = '';
		

	/**
	 * Stores enqueuing script URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $aEnqueuingScripts = array();
	/**	
	 * Stores enqueuing style URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $aEnqueuingStyles = array();
	/**
	 * Stores the index of enqueued scripts.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $iEnqueuedScriptIndex = 0;
	/**
	 * Stores the index of enqueued styles.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the script handle ID which is automatically generated.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $iEnqueuedStyleIndex = 0;		
		
	function __construct( $oCaller, $sCallerPath, $sClassName ) {
		
		$this->oCaller = $oCaller;
		$this->sCallerPath = $sCallerPath ? $sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ );
		$this->sClassName = $sClassName;		
		$this->sClassHash = md5( $sClassName );	
		$this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath );
		$GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) 
			? $GLOBALS['aAdminPageFramework']
			: array();

	}
		
	/**
	 * Returns the caller object.
	 * 
	 * This is used from other sub classes that need to retrieve the caller object.
	 * 
	 * @since			2.1.5
	 * @access			public	
	 * @return			object			The caller class object.
	 * @internal
	 */		
	public function _getParentObject() {
		return $this->oCaller;
	}
	
	/**
	 * Sets the library information property.
	 * @internal
	 * @since			3.0.0
	 */
	static public function _setLibraryData( $sLibraryFilePath ) {
		self::$_aLibraryData = AdminPageFramework_WPUtility::getScriptData( $sLibraryFilePath, 'library' );
	}
	/**
	 * Returns the set library data array.
	 * 
	 * @internal
	 * @since			3.0.0
	 */
	static public function _getLibraryData( $sLibraryFilePath=null ) {
		
		if ( isset( self::$_aLibraryData ) ) return self::$_aLibraryData;
		
		if ( $sLibraryFilePath ) 
			self::_setLibraryData( $sLibraryFilePath );
			
		return self::$_aLibraryData;
		
	}

	/*
	 * Methods for getting script info.
	 */ 	 
	/**
	 * Retrieves the caller script information whether it's a theme or plugin or something else.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Moved from the link class.
	 * @remark			The information can be used to embed into the footer etc.
	 * @return			array			The information of the script.
	 */	 
	protected function getCallerInfo( $sCallerPath=null ) {
		
		$aCallerInfo = self::$_aStructure_CallerInfo;
		$aCallerInfo['sPath'] = $sCallerPath;
		$aCallerInfo['sType'] = $this->_getCallerType( $aCallerInfo['sPath'] );

		if ( $aCallerInfo['sType'] == 'unknown' ) return $aCallerInfo;
		
		if ( $aCallerInfo['sType'] == 'plugin' ) 
			return AdminPageFramework_WPUtility::getScriptData( $aCallerInfo['sPath'], $aCallerInfo['sType'] ) + $aCallerInfo;
			
		if ( $aCallerInfo['sType'] == 'theme' ) {
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
		 * Determines the script type.
		 * 
		 * It tries to find what kind of script this is, theme, plugin or something else from the given path.
		 * @since			2.0.0
		 * @since			3.0.0			Moved from the link class.
		 * @return		string				Returns either 'theme', 'plugin', or 'unknown'
		 */ 
		private function _getCallerType( $sScriptPath ) {
			
			if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) return 'theme';
			if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) return 'plugin';
			return 'unknown';	
		
		}	
}
endif;