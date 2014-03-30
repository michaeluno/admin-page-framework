<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Property_Base' ) ) :

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since			2.1.0
 * @package			AdminPageFramework
 * @subpackage		Property
 * @internal
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
		'sScriptURI'	=> null,
		'sAuthorURI'	=> null,
		'sAuthor'		=> null,
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
	 * Will store true when the style is added in the head tag.
	 * @since			3.0.0
	 */
	public $_bAddedStyle = false;	
	/**
	 * Will store true when the script is added in the head tag.
	 * @since			3.0.0
	 */
	public $_bAddedScript = false;

	/**
	 * Stores the field type definitions.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public $aFieldTypeDefinitions = array();
	
	/**
	 * The default JavaScript script loaded in the head tag of the created admin pages.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public static $_sDefaultScript = "";
	
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
	public static $_sDefaultStyle =
		"
		/* Settings Notice */
		.wrap div.updated, 
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
		
		/* Page Meta Boxes */
		.admin-page-framework-content {
			margin-bottom: 1.48em;		
			display: inline-table;	/* Fixes the bottom margin gets placed at the top. */
/* display: block; */
			width: 100%;	/* This allows float:right elements to go to the very right end of the page. */
		}
		
		/* Heading - the meta box container element affects the styles of regular main content output. So it needs to be fixed. */
		#poststuff .admin-page-framework-content h3 {
			font-weight: bold;
			font-size: 1.3em;
			margin: 1em 0;
			padding: 0;
			font-family: 'Open Sans', sans-serif;
		}
		
		/* Form Elements */
		/* Section Table */
		.admin-page-framework-section .form-table {
			margin-top: 0;
		}
		.admin-page-framework-section .form-table td label {
		   	display: inline;  /* adjusts the horizontal alignment with the th element */
		}
		/* Section Tabs */
		.admin-page-framework-section-tabs-contents {
			margin-top: 1em;
		}
		.admin-page-framework-section-tabs {	/* The section tabs' container */
			margin: 0;
		}
		.admin-page-framework-tab-content {		/* each section including sub-sections of repeatable fields */
			padding: 0.5em 2em 1.5em 2em;
			margin: 0;
			border-style: solid;
			border-width: 1px;
			border-color: #dfdfdf;
			background-color: #fdfdfd;				
			
		}
		.admin-page-framework-section-tab {
			background-color: transparent;
			vertical-align: bottom;	/* for Firefox */
		}
		.admin-page-framework-section-tab.active {
			background-color: #fdfdfd;			
		}
		.admin-page-framework-section-tab h4 {
			margin: 0;
			padding: 8px 14px 10px;
			font-size: 1.2em;
		}
		.admin-page-framework-section-tab.nav-tab {
			padding: 0;
		}
		.admin-page-framework-section-tab.nav-tab a {
			text-decoration: none;
			color: #464646;
			vertical-align: inherit; /* for Firefox - without this tiny dots appear */
		}
		.admin-page-framework-section-tab.nav-tab.active a {
			color: #000;
		}
		/* Repeatable Sections */
		.admin-page-framework-repeatable-section-buttons {
			float: right;
		}
		/* Section Caption */
		.admin-page-framework-section-caption {
			text-align: left;
			margin: 1em 0;
		}
		/* Section Title */
		.admin-page-framework-section .admin-page-framework-section-title {
			background: none;
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		/* Metabox Section Heading Info */
		#poststuff .metabox-holder .admin-page-framework-section-title h3{
			border: none;
			font-weight: bold;
			font-size: 1.3em;
			margin: 1em 0;
			padding: 0;
			font-family: 'Open Sans', sans-serif;		
			cursor: inherit;			
			-webkit-user-select: inherit;
			-moz-user-select: inherit;
			user-select: inherit;	

			/* v3.5 or below */
			text-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			background: none;
		}
		
		/* Fields Container */
		.admin-page-framework-fields {
			display: table;	/* the block property does not give the element the solid height */
			width: 100%;
		}
		
		/* Disabled */
		.admin-page-framework-fields .disabled,
		.admin-page-framework-fields .disabled input,
		.admin-page-framework-fields .disabled textarea,
		.admin-page-framework-fields .disabled select,
		.admin-page-framework-fields .disabled option {
			color: #BBB;
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
		.admin-page-framework-fields-description {
			margin-bottom: 0;
		}
		/* Field Container */
		.admin-page-framework-field {
			float: left;
			clear: both;
			display: inline-block;
			margin: 1px 0;
		}
		.admin-page-framework-field label{
			display: inline-block;	/* for WordPress v3.7.x or below */
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
		
		/* Repeatable Fields */		
		.repeatable .admin-page-framework-field {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;		
			margin: 0.1em 0 0.5em 0.3em;
			vertical-align: middle;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 0.1em;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Sortable Fields */
		.sortable .admin-page-framework-field {
			clear: both;
			float: left;
			display: inline-block;
			padding: 1em 1.2em 0.72em;
			margin: 1px 0 0 0;
			border-top-width: 1px;
			border-bottom-width: 1px;
			border-bottom-style: solid;
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;			
			text-shadow: #fff 0 1px 0;
			-webkit-box-shadow: 0 1px 0 #fff;
			box-shadow: 0 1px 0 #fff;
			-webkit-box-shadow: inset 0 1px 0 #fff;
			box-shadow: inset 0 1px 0 #fff;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			background: #f1f1f1;
			background-image: -webkit-gradient(linear, left bottom, left top, from(#ececec), to(#f9f9f9));
			background-image: -webkit-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image:    -moz-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image:      -o-linear-gradient(bottom, #ececec, #f9f9f9);
			background-image: linear-gradient(to top, #ececec, #f9f9f9);
			border: 1px solid #CCC;
			background: #F6F6F6;	
		}		
		.admin-page-framework-fields.sortable {
			margin-bottom: 1.2em;	/* each sortable field does not have a margin bottom so this rule gives a margin between the fields and the description */
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
		}";	
		
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 * @since			2.1.5			Moved the contents to the taxonomy field definition so it become an empty string.
	 */
	public static $_sDefaultStyleIE = '';
		

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
	
	/**
	 * Stores is_admin() value to be reused. 
	 * 
	 * @since			3.0.0
	 */
	public $bIsAdmin;
	
	/**
	 * Stores the flag that indicates whether the library is minified or not.
	 * @since			3.0.0
	 */
	public $bIsMinifiedVersion;
		
	/**
	 * Stores the capability for displayable elements.
	 * 
	 * @since			2.0.0
	 */ 	
	public $sCapability;
	
	/**
	 * Defines the fields type.
	 * @since			3.0.4
	 * @internal
	 */
	public $sFieldsType;		
		
	/**
	 * Stores the text domain.
	 * 
	 * @since			3.0.4
	 * @internal
	 */ 
	public $sTextDomain;
	
	function __construct( $oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sFieldsType ) {
		
		$this->oCaller = $oCaller;
		$this->sCallerPath = $sCallerPath ? $sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ );
		$this->sClassName = $sClassName;		
		$this->sClassHash = md5( $sClassName );	
		$this->sCapability = empty( $sCapability ) ? 'manage_options' : $sCapability ;
		$this->sTextDomain = empty( $sTextDomain ) ? 'admin-page-framework' : $sTextDomain;
		$this->sFieldsType = $sFieldsType;
		$this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath );
		$GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) 
			? $GLOBALS['aAdminPageFramework']
			: array( 'aFieldFlags' => array() );
		$this->bIsAdmin = is_admin();
		$this->bIsMinifiedVersion = ! class_exists( 'AdminPageFramework_Bootstrap' );
		if ( ! isset( self::$_aLibraryData ) ) {			
			$_sLibraryMainClassName = ( $this->bIsMinifiedVersion )
				? 'AdminPageFramework'	
				: 'AdminPageFramework_Bootstrap';	// the minified version does not have the bootstrap class.
			$oRC = new ReflectionClass( $_sLibraryMainClassName );
			self::_setLibraryData( $oRC->getFileName() );
		}
		
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
	public function _getCallerObject() {
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
		
	
	/**
	 * Checks if the current page is post editing page that belongs to the given post type(s).
	 * 
	 * @since			3.0.0
	 * @param			array|string			The post type slug(s) to check. If this is empty, the method just checks the current page is a post definition page.
	 * Otherwise, it will check if the page belongs to the given post type(s).
	 * @return			boolean
	 * @deprecated
	 */
	public function isPostDefinitionPage( $asPostTypes=array() ) {
		
		$_aPostTypes = ( array ) $asPostTypes;
		
		// If it's not the post definition page, 
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return false;
		
		// If the parameter is empty, 
		if ( empty( $_aPostTypes ) ) return true;
		
		// If the parameter the post type are set and it's in the given post types, 
		if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $_aPostTypes ) ) return true;
		
		// Find the post type from the post ID.
		$this->_sCurrentPostType = isset( $this->_sCurrentPostType )
			? $this->_sCurrentPostType
			: ( isset( $_GET['post'] )
				? get_post_type( $_GET['post'] )
				: ''
			);
		
		// If the found post type is in the given post types,
		if ( isset( $_GET['post'], $_GET['action'] ) && in_array( $this->_sCurrentPostType, $_aPostTypes ) )		// edit post page
			return true;						
		
		// Otherwise,
		return false;
		
	}	
	
}
endif;