<?php 
/**
 * Admin Page Framework
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes. 
 * The framework uses the built-in WordPress Settings API so it respects the WordPress standard form layout design.
 * 
 * @author				Michael Uno <michael@michaeluno.jp>
 * @copyright			Michael Uno
 * @license				GPLv2 or later
 * @see					http://wordpress.org/plugins/admin-page-framework/
 * @see					https://github.com/michaeluno/admin-page-framework
 * @link				http://en.michaeluno.jp/admin-page-framework
 * @package				Admin Page Framework
 * @remarks				To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remarks				Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * @remarks				The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @version				3.0.0b
 */
/*
	Library Name: Admin Page Framework
	Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
	Author:  Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.0.0b
	Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
	Description: Provides simpler means of building administration pages for plugin and theme developers.
*/

if ( ! function_exists( 'includeAdminPageFramework' ) ) :
function includeAdminPageFramework() {
	
	$sDirPath = dirname( __FILE__ );
	include_once( $sDirPath . '/utility/AdminPageFramework_RegisterClasses.php' );
	new AdminPageFramework_RegisterClasses( $sDirPath );
	
}	
endif;
includeAdminPageFramework();


if ( ! class_exists( 'AdminPageFramework_CustomSubmitFields' ) ) :
/**
 * Provides helper methods that deal with custom submit fields and retrieve custom key elements.
 *
 * @abstract
 * @since			2.0.0
 * @remark			The classes that extend this include ExportOptions, ImportOptions, and Redirect.
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
abstract class AdminPageFramework_CustomSubmitFields {
	 
	public function __construct( $aPostElement ) {
		
		$this->aPostElement = $aPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	/**
	 * Retrieves the value of the specified element key.
	 * 
	 * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getElement( $aElement, $aElementKey, $sElementKey='format' ) {
			
		$sFirstDimensionKey = $aElementKey[ 0 ];
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) || ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return 'ERROR_A';

		/* For single element, e.g.
		 * <input type="hidden" name="__import[import_single][import_option_key]" value="APF_GettingStarted">
		 * <input type="hidden" name="__import[import_single][format]" value="array">
		 * */	
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) && ! is_array( $aElement[ $sFirstDimensionKey ][ $sElementKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ];

		/* For multiple elements, e.g.
		 * <input type="hidden" name="__import[import_multiple][import_option_key][2]" value="APF_GettingStarted.txt">
		 * <input type="hidden" name="__import[import_multiple][format][2]" value="array">
		 * */
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B';
		$sKey = $aElementKey[ 1 ];
		if ( isset( $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ] ) )
			return $aElement[ $sFirstDimensionKey ][ $sElementKey ][ $sKey ];
			
		return 'ERROR_C';	// Something wrong happened.
		
	}	
	
	/**
	 * Retrieves an array consisting of two values.
	 * 
	 * The first element is the fist dimension's key and the second element is the second dimension's key.
	 * @since			2.0.0
	 */
	protected function getElementKey( $aElement, $sFirstDimensionKey ) {
		
		if ( ! isset( $aElement[ $sFirstDimensionKey ] ) ) return;
		
		// Set the first element the field ID.
		$aEkementKey = array( 0 => $sFirstDimensionKey );

		// For single export buttons, e.g. name="__import[submit][import_single]" 		
		if ( ! is_array( $aElement[ $sFirstDimensionKey ] ) ) return $aEkementKey;
		
		// For multiple ones, e.g. name="__import[submit][import_multiple][1]" 		
		foreach( $aElement[ $sFirstDimensionKey ] as $k => $v ) {
			
			// Only the pressed export button's element is submitted. In other words, it is necessary to check only one item.
			$aEkementKey[] = $k;
			return $aEkementKey;			
				
		}		
	}
		
	public function getFieldID() {
		
		// e.g.
		// single:		name="__import[submit][import_single]"
		// multiple:	name="__import[submit][import_multiple][1]"
		
		if ( isset( $this->sFieldID ) && $this->sFieldID  ) return $this->sFieldID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->aPostElement['submit'] as $sKey => $v ) {	// $this->aPostElement should have been set in the constructor.
			$this->sFieldID = $sKey;
			return $this->sFieldID;
		}
	}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_ImportOptions' ) ) :
/**
 * Provides methods to import option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ImportOptions extends AdminPageFramework_CustomSubmitFields {
	
	/* Example of $_FILES for a single import field. 
		Array (
			[__import] => Array (
				[name] => Array (
				   [import_single] => APF_GettingStarted_20130709 (1).json
				)
				[type] => Array (
					[import_single] => application/octet-stream
				)
				[tmp_name] => Array (
					[import_single] => Y:\wamp\tmp\php7994.tmp
				)
				[error] => Array (
					[import_single] => 0
				)
				[size] => Array (
					[import_single] => 715
				)
			)
		)
	*/
	
	public function __construct( $aFilesImport, $aPostImport ) {

		// Call the parent constructor. This must be done before the getFieldID() method that uses the $aPostElement property.
		parent::__construct( $aPostImport );
	
		$this->aFilesImport = $aFilesImport;
		$this->aPostImport = $aPostImport;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostImport['submit'], $this->sFieldID );
			
	}
	
	private function getElementInFilesArray( $aFilesImport, $aElementKey, $sElementKey='error' ) {

		$sElementKey = strtolower( $sElementKey );
		$sFieldID = $aElementKey[ 0 ];	// or simply assigning $this->sFieldID would work as well.
		if ( ! isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) ) return 'ERROR_A: The given key does not exist.';
	
		// For single export buttons, e.g. $_FILES[__import][ $sElementKey ][import_single] 
		if ( isset( $aFilesImport[ $sElementKey ][ $sFieldID ] ) && ! is_array( $aFilesImport[ $sElementKey ][ $sFieldID ] ) )
			return $aFilesImport[ $sElementKey ][ $sFieldID ];
			
		// For multiple import buttons, e.g. $_FILES[__import][ $sElementKey ][import_multiple][2]
		if ( ! isset( $aElementKey[ 1 ] ) ) return 'ERROR_B: the sub element is not set.';
		$sKey = $aElementKey[ 1 ];		
		if ( isset( $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ] ) )
			return $aPostImport[ $sElementKey ][ $sFieldID ][ $sKey ];

		// Something wrong happened.
		return 'ERROR_C: unexpected problem occurred.';
		
	}	
		
	public function getError() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'error' );
		
	}
	public function getType() {
		
		return $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'type' );
		
	}
	public function getImportData() {
		
		// Retrieve the uploaded file path.
		$sFilePath = $this->getElementInFilesArray( $this->aFilesImport, $this->aElementKey, 'tmp_name' );
		
		// Read the file contents.
		$vData = file_exists( $sFilePath ) ? file_get_contents( $sFilePath, true ) : false;
		
		return $vData;
		
	}
	public function formatImportData( &$vData, $sFormatType=null ) {
		
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->getFormatType();
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				return;	// do nothing
			case 'json':	// for json.
				$vData = json_decode( ( string ) $vData, true );	// the second parameter indicates to decode it as array.
				return;
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				$vData = maybe_unserialize( trim( $vData ) );
				return;
		}		
	
	}
	public function getFormatType() {
					
		$this->sFormatType = isset( $this->sFormatType ) && $this->sFormatType 
			? $this->sFormatType
			: $this->getElement( $this->aPostImport, $this->aElementKey, 'format' );

		return $this->sFormatType;
		
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostImport, $this->aElementKey, $sKey );
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_ExportOptions' ) ) :
/**
 * Provides methods to export option data.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_ExportOptions extends AdminPageFramework_CustomSubmitFields {

	public function __construct( $aPostExport, $sClassName ) {
		
		// Call the parent constructor.
		parent::__construct( $aPostExport );
		
		// Properties
		$this->aPostExport = $aPostExport;
		$this->sClassName = $sClassName;	// will be used in the getTransientIfSet() method.
		// $this->sPageSlug = $sPageSlug;
		// $this->sTabSlug = $sTabSlug;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->sFieldID = $this->getFieldID();
		$this->aElementKey = $this->getElementKey( $aPostExport['submit'], $this->sFieldID );
		
		// Set the file name to download and the format type. Also find whether the exporting data is set in transient.
		$this->sFileName = $this->getElement( $aPostExport, $this->aElementKey, 'file_name' );
		$this->sFormatType = $this->getElement( $aPostExport, $this->aElementKey, 'format' );
		$this->bIsDataSet = $this->getElement( $aPostExport, $this->aElementKey, 'transient' );
	
	}
	
	public function getTransientIfSet( $vData ) {
		
		if ( $this->bIsDataSet ) {
			$sKey = $this->aElementKey[1];
			$sTransient = isset( $this->aElementKey[1] ) ? "{$this->sClassName}_{$this->sFieldID}_{$this->aElementKey[1]}" : "{$this->sClassName}_{$this->sFieldID}";
			$tmp = get_transient( md5( $sTransient ) );
			if ( $tmp !== false ) {
				$vData = $tmp;
				delete_transient( md5( $sTransient ) );
			}
		}
		return $vData;
	}
	
	public function getFileName() {
		return $this->sFileName;
	}
	public function getFormat() {
		return $this->sFormatType;
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $sKey ) {
		
		return $this->getElement( $this->aPostExport, $this->aElementKey, $sKey );
		
	}	

	/**
	 * Performs exporting data.
	 * 
	 * @since			2.0.0
	 */ 
	public function doExport( $vData, $sFileName=null, $sFormatType=null ) {

		/* 
		 * Sample HTML elements that triggers the method.
		 * e.g.
		 * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
		 * <input type="hidden" name="__export[export_sinble][format]" value="json">
		 * <input id="export_and_import_export_sinble_0" 
		 *  type="submit" 
		 *  name="__export[submit][export_sinble]" 
		 *  value="Export Options">
		*/	
		$sFileName = isset( $sFileName ) ? $sFileName : $this->sFileName;
		$sFormatType = isset( $sFormatType ) ? $sFormatType : $this->sFormatType;
							
		// Do export.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $sFileName );
		switch ( strtolower( $sFormatType ) ) {
			case 'text':	// for plain text.
				if ( is_array( $vData ) || is_object( $vData ) ) {
					$oDebug = new AdminPageFramework_Debug;
					$sData = $oDebug->getArray( $vData );
					die( $sData );
				}
				die( $vData );
			case 'json':	// for json.
				die( json_encode( ( array ) $vData ) );
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				die( serialize( ( array ) $vData  ));
		}
	}
}
endif;

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

if ( ! class_exists( 'AdminPageFramework_Link_PostType' ) ) :
/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since			2.0.0
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link_PostType extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the information to embed into the page footer.
	 * @since			2.0.0
	 * @remark			This is accessed from the AdminPageFramework_PostType class.
	 */ 
	public $aFooterInfo = array(
		'sLeft' => '',
		'sRight' => '',
	);
	
	public function __construct( $sPostTypeSlug, $sCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->sPostTypeSlug = $sPostTypeSlug;
		$this->sCallerPath = file_exists( $sCallerPath ) ? $sCallerPath : $this->getCallerPath();
		$this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath ); 
		$this->aLibraryInfo = $this->getLibraryInfo();
		
		$this->oMsg = $oMsg;
		
		$this->sSettingPageLinkTitle = $this->oMsg->__( 'manage' );
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->aScriptInfo, $this->aFooterInfo['sLeft'] );
		$this->setFooterInfoRight( $this->aLibraryInfo, $this->aFooterInfo['sRight'] );
		
		// For the plugin listing page
		if ( $this->aScriptInfo['type'] == 'plugin' )
			add_filter( 
				'plugin_action_links_' . plugin_basename( $this->aScriptInfo['sPath'] ),
				array( $this, 'addSettingsLinkInPluginListingPage' ), 
				20 	// set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
			);	
		
		// For post type posts listing table page ( edit.php )
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->sPostTypeSlug )
			add_action( 'get_edit_post_link', array( $this, 'addPostTypeQueryInEditPostLink' ), 10, 3 );
		
	}
	
	/*
	 * Callback methods
	 */ 
	/**
	 * Adds the <em>post_type</em> query key and value in the link url.
	 * 
	 * This is used to make it easier to detect if the linked page belongs to the post type created with this class.
	 * So it can be used to embed footer links.
	 * 
	 * @since			2.0.0
	 * @remark			e.g. http://.../wp-admin/post.php?post=180&action=edit -> http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]
	 * @remark			A callback for the <em>get_edit_post_link</em> hook.
	 */	 
	public function addPostTypeQueryInEditPostLink( $sURL, $iPostID=null, $sContext=null ) {
		return add_query_arg( array( 'post' => $iPostID, 'action' => 'edit', 'post_type' => $this->sPostTypeSlug ), $sURL );	
	}	
	public function addSettingsLinkInPluginListingPage( $aLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$aLinks,
			"<a href='edit.php?post_type={$this->sPostTypeSlug}'>" . $this->sSettingPageLinkTitle . "</a>"
		); 
		return $aLinks;		
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $sLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->sPostTypeSlug )
			return $sLinkHTML;	// $sLinkHTML is given by the hook.

		if ( empty( $this->aScriptInfo['sName'] ) ) return $sLinkHTML;
					
		return $this->aFooterInfo['sLeft'];
		
	}
	public function addInfoInFooterRight( $sLinkHTML='' ) {

		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->sPostTypeSlug )
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
			
		return $this->aFooterInfo['sRight'];		
			
	}
}
endif;
 
if ( ! class_exists( 'AdminPageFramework_Link' ) ) :
/**
 * Provides methods for HTML link elements for admin pages created by the framework, except the pages of custom post types.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since			2.0.0
 * @extends			AdminPageFramework_Link_Base
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 */ 
	private $sCallerPath;
	
	/**
	 * The property object, commonly shared.
	 * @since			2.0.0
	 */ 
	private $oProp;
	
	public function __construct( &$oProp, $sCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProp = $oProp;
		$this->sCallerPath = file_exists( $sCallerPath ) ? $sCallerPath : $this->getCallerPath();
		$this->oProp->aScriptInfo = $this->getCallerInfo( $this->sCallerPath ); 
		$this->oProp->aLibraryInfo = $this->getLibraryInfo();
		$this->oMsg = $oMsg;
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->oProp->aScriptInfo, $this->oProp->aFooterInfo['sLeft'] );
		$this->setFooterInfoRight( $this->oProp->aLibraryInfo, $this->oProp->aFooterInfo['sRight'] );
	
		if ( $this->oProp->aScriptInfo['type'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ) , array( $this, 'addSettingsLinkInPluginListingPage' ) );

	}

	
	/**	
	 * 
	 * @since			2.0.0
	 * @since			2.1.4			Changed to be static since it is used from multiple classes.
	 * @remark			The scope is public because this is accessed from an extended class.
	 */ 
	public static $_aStructure_SubMenuLink = array(		
		'title' => null,
		'href' => null,
		'sCapability' => null,
		'order' => null,
		'type' => 'link',
		'fShowPageHeadingTab' => true,
		'fShowInMenu' => true,
	);

	public function addSubMenuLink( $sMenuTitle, $sURL, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		
		$iCount = count( $this->oProp->aPages );
		$this->oProp->aPages[ $sURL ] = array(  
			'title'		=> $sMenuTitle,
			'title'		=> $sMenuTitle,	// used for the page heading tabs.
			'href'			=> $sURL,
			'type'			=> 'link',	// this is used to compare with the 'page' type.
			'sCapability'		=> isset( $sCapability ) ? $sCapability : $this->oProp->sCapability,
			'order'			=> is_numeric( $nOrder ) ? $nOrder : $iCount + 10,
			'fShowPageHeadingTab'	=> $bShowPageHeadingTab,
			'fShowInMenu'		=> $bShowInMenu,
		);	
			
	}
			
	/*
	 * Methods for embedding links 
	 */ 	
	public function addLinkToPluginDescription( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProp->aPluginDescriptionLinks[] = $linkss;
		else
			$this->oProp->aPluginDescriptionLinks = array_merge( $this->oProp->aPluginDescriptionLinks , $linkss );
	
		add_filter( 'plugin_row_meta', array( $this, 'addLinkToPluginDescription_Callback' ), 10, 2 );

	}
	public function addLinkToPluginTitle( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProp->aPluginTitleLinks[] = $linkss;
		else
			$this->oProp->aPluginTitleLinks = array_merge( $this->oProp->aPluginTitleLinks, $linkss );
		
		add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ), array( $this, 'AddLinkToPluginTitle_Callback' ) );

	}
	
	/*
	 * Callback methods
	 */ 
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
		
		if ( empty( $this->oProp->aScriptInfo['sName'] ) ) return $sLinkHTML;
		
		return $this->oProp->aFooterInfo['sLeft'];

	}
	public function addInfoInFooterRight( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkTHML is given by the hook.
			
		return $this->oProp->aFooterInfo['sRight'];
			
	}
	
	public function addSettingsLinkInPluginListingPage( $aLinks ) {
		
		// For a custom root slug,
		$sLinkURL = preg_match( '/^.+\.php/', $this->oProp->aRootMenu['sPageSlug'] ) 
			? add_query_arg( array( 'page' => $this->oProp->sDefaultPageSlug ), admin_url( $this->oProp->aRootMenu['sPageSlug'] ) )
			: "admin.php?page={$this->oProp->sDefaultPageSlug}";
		
		array_unshift(	
			$aLinks,
			'<a href="' . $sLinkURL . '">' . $this->oMsg->__( 'settings' ) . '</a>'
		); 
		return $aLinks;
		
	}	
	
	public function addLinkToPluginDescription_Callback( $aLinks, $sFile ) {

		if ( $sFile != plugin_basename( $this->oProp->aScriptInfo['sPath'] ) ) return $aLinks;
		
		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProp->aPluginDescriptionLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $aLinks ) {

		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProp->aPluginTitleLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}		
}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Base' ) ) :
/**
 * Collects data of page loads in admin pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
abstract class AdminPageFramework_PageLoadInfo_Base {
	
	function __construct( $oProp, $oMsg ) {
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			
			$this->oProp = $oProp;
			$this->oMsg = $oMsg;
			$this->nInitialMemoryUsage = memory_get_usage();
			add_action( 'admin_menu', array( $this, 'replyToSetPageLoadInfoInFooter' ), 999 );	// must be loaded after the sub pages are registered
						
		}

	}
	
	/**
	 * @remark			Should be overridden in an extended class.
	 */
	public function replyToSetPageLoadInfoInFooter() {}
		
	/**
	 * Display gathered information.
	 *
	 * @access			public
	 */
	public function replyToGetPageLoadInfo( $sFooterHTML ) {
		
		// Get values we're displaying
		$nSeconds 				= timer_stop(0);
		$nQueryCount 			= get_num_queries();
		$memory_usage 			= round( $this->convert_bytes_to_hr( memory_get_usage() ), 2 );
		$memory_peak_usage 		= round( $this->convert_bytes_to_hr( memory_get_peak_usage() ), 2 );
		$memory_limit 			= round( $this->convert_bytes_to_hr( $this->let_to_num( WP_MEMORY_LIMIT ) ), 2 );
		$sInitialMemoryUsage	= round( $this->convert_bytes_to_hr( $this->nInitialMemoryUsage ), 2 );
				
		$sOutput = 
			"<div id='admin-page-framework-page-load-stats'>"
				. "<ul>"
					. "<li>" . sprintf( $this->oMsg->__( 'queries_in_seconds' ), $nQueryCount, $nSeconds ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'out_of_x_memory_used' ), $memory_usage, $memory_limit, round( ( $memory_usage / $memory_limit ), 2 ) * 100 . '%' ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'peak_memory_usage' ), $memory_peak_usage ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->__( 'initial_memory_usage' ), $sInitialMemoryUsage ) . "</li>"
				. "</ul>"
			. "</div>";
		return $sFooterHTML . $sOutput;
		
	}

	/**
	 * let_to_num function.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer
	 *
	 * @access public
	 * @param $size
	 * @return int
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function let_to_num( $size ) {
		$l 		= substr( $size, -1 );
		$ret 	= substr( $size, 0, -1 );
		switch( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	/**
	 * convert_bytes_to_hr function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function convert_bytes_to_hr( $bytes ) {
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log( $bytes, 1024 );
		$power = ( int ) $log;
		$size = pow( 1024, $log - $power );
		return $size . $units[ $power ];
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_Page' ) ) :
/**
 * Collects data of page loads of the added pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_PageLoadInfo_Page extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProp, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_Page ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_Page( $oProp, $oMsg );
		return self::$_oInstance;
		
	}		
	
	/**
	 * Sets the hook if the current page is one of the framework's added pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {
		
		// For added pages
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		if ( $this->oProp->isPageAdded( $sCurrentPageSlug ) ) 
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadInfo' ), 999 );
	
	}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_PageLoadInfo_PostType' ) ) :
/**
 * Collects data of page loads of the added post type pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AdminPageFramework_PageLoadInfo_PostType extends AdminPageFramework_PageLoadInfo_Base {
	
	private static $_oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProp, $oMsg ) {
		
		if ( ! isset( self::$_oInstance ) && ! ( self::$_oInstance instanceof AdminPageFramework_PageLoadInfo_PostType ) ) 
			self::$_oInstance = new AdminPageFramework_PageLoadInfo_PostType( $oProp, $oMsg );
		return self::$_oInstance;
		
	}	

	/**
	 * Sets the hook if the current page is one of the framework's added post type pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {

		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// For post type pages
		if ( isset( $_GET['post_type'], $this->oProp->sPostType ) && $_GET['post_type'] == $this->oProp->sPostType )
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadInfo' ), 999 );
		
	}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldTypeDefinition_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AdminPageFramework_InputFieldTypeDefinition_Base extends AdminPageFramework_Utility {
	
	protected static $_aDefaultKeys = array(
		'vValue'				=> null,				// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'default'				=> null,				// ( array or string )
		'repeatable'			=> false,
		'class_attribute'		=> '',					// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'label'				=> '',					// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'delimiter'			=> '',
		'vDisable'				=> false,				// ( array or boolean ) This value indicates whether the set field is disabled or not. 
		'vReadOnly'				=> false,				// ( array or boolean ) sets the readonly attribute to text and textarea input fields.
		'vBeforeInputTag'		=> '',
		'vAfterInputTag'		=> '',				
		'labelMinWidth'		=> 140,
		
		// Mandatory keys.
		'field_id' => null,		
		
		// For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings.
		'page_slug' => null,
		'section_id' => null,
		'sBeforeField' => null,
		'sAfterField' => null,	
	);	
	
	protected $oMsg;
	
	function __construct( $sClassName, $sFieldTypeSlug, $oMsg=null, $bAutoRegister=true ) {
			
		$this->sFieldTypeSlug = $sFieldTypeSlug;
		$this->sClassName = $sClassName;
		$this->oMsg	= $oMsg;
		
		// This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
		if ( $bAutoRegister )
			add_filter( "field_types_{$sClassName}", array( $this, 'replyToRegisterInputFieldType' ) );
	
	}	
	
	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		$aFieldDefinitions[ $this->sFieldTypeSlug ] = $this->getDefinitionArray();
		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the field type definition array.
	 * 
	 * @remark			The scope is public since AdminPageFramework_FieldType class allows the user to use this method.
	 * @since			2.1.5
	 */
	public function getDefinitionArray() {
		
		return array(
			'hfRenderField' => array( $this, "replyToGetInputField" ),
			'hfGetScripts' => array( $this, "replyToGetInputScripts" ),
			'hfGetStyles' => array( $this, "replyToGetInputStyles" ),
			'hfGetIEStyles' => array( $this, "replyToGetInputIEStyles" ),
			'hfFieldLoader' => array( $this, "replyToFieldLoader" ),
			'aEnqueueScripts' => $this->getEnqueuingScripts(),	// urls of the scripts
			'aEnqueueStyles' => $this->getEnqueuingStyles(),	// urls of the styles
			'aDefaultKeys' => $this->getDefaultKeys() + self::$_aDefaultKeys, 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function replytToGetInputField() { return ''; }	// should return the field output
	public function replyToGetInputScripts() { return ''; }	// should return the script
	public function replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function replyToGetInputStyles() { return ''; }	// should return the style
	public function replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getDefaultKeys() { return array(); }
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_InputFieldTypeDefinition_Base {}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_default' ) ) :
/**
 * Defines the default field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_default extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * This one is triggered when the called field type is unknown. This does not insert the input tag but just renders the value stored in the $vValue variable.
	 * 
	 * @since			2.1.5				
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. $sValue
							. "</div>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-default' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_text' ) ) :
/**
 * Defines the text field type.
 * 
 * Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defeined.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_text extends AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', ) as $sTextTypeSlug )
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();

		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 30,
			'vMaxLength'			=> 400,
		);	
	}
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
		$bMultiple = is_array( $aFields );
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, 30 ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='{$aField['type']}' "	// text, password, etc.
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				)
			;
				
		return "<div class='admin-page-framework-field-text' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";

	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_number' ) ) :
/**
 * Defines the number, and range field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_number extends AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( array( 'number', 'range' ) as $sTextTypeSlug ) 
			$aFieldDefinitions[ $sTextTypeSlug ] = $this->getDefinitionArray();
		return $aFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vMin'				=> null,
			'vMax'				=> null,
			'vStep'				=> null,
			'size'				=> 30,
			'vMaxLength'		=> 400,
		);	
	}
	
	/**
	 * Returns the output of the number input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {
		
		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, 30 ) . "' "
								. "type='{$aField['type']}' "
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
								. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
								. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);				
					
		return "<div class='admin-page-framework-field-number' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_textarea' ) ) :
/**
 * Defines the textarea field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_textarea extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'rows'					=> 4,
			'cols'					=> 80,
			'vRich'					=> false,
			'vMaxLength'			=> 400,
		);	
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"	/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}		
		" . PHP_EOL;		
	}	
		
	/**
	 * Returns the output of the textarea input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];			
		$bSingle = ! is_array( $aFields );
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) {
			
			$aRichEditorSettings = $bSingle
				? $aField['vRich']
				: $this->getCorrespondingArrayValue( $aField['vRich'], $sKey, null );
				
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}' >"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. ( ! empty( $aRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
								? wp_editor( 
									$this->getCorrespondingArrayValue( $vValue, $sKey, null ), 
									"{$sTagID}_{$sKey}",  
									$this->uniteArrays( 
										( array ) $aRichEditorSettings,
										array(
											'wpautop' => true, // use wpautop?
											'media_buttons' => true, // show insert/upload button(s)
											'textarea_name' => is_array( $aFields ) ? "{$sFieldName}[{$sKey}]" : $sFieldName , // set the textarea name to something different, square brackets [] can be used here
											'textarea_rows' => $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ),
											'tabindex' => '',
											'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
											'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
											'editor_class' => $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ), // add extra class(es) to the editor textarea
											'teeny' => false, // output the minimal editor config used in Press This
											'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
											'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
											'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
										)
									)
								) . $this->getScriptForRichEditor( "{$sTagID}_{$sKey}" )
								: "<textarea id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, '' ) . "' "
									. "rows='" . $this->getCorrespondingArrayValue( $aField['rows'], $sKey, $_aDefaultKeys['rows'] ) . "' "
									. "cols='" . $this->getCorrespondingArrayValue( $aField['cols'], $sKey, $_aDefaultKeys['cols'] ) . "' "
									. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
									. "type='{$aField['type']}' "
									. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
								. ">"
									. $this->getCorrespondingArrayValue( $vValue, $sKey, null )
								. "</textarea>"
							)
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		

	}	
		/**
		 * A helper function for the above getTextAreaField() method.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */	
		private function getScriptForRichEditor( $sIDSelector ) {

			// id: wp-sample_rich_textarea_0-wrap
			return "<script type='text/javascript'>
				jQuery( '#wp-{$sIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$sIDSelector}-wrap' ).appendTo( '#field-{$sIDSelector}' );
					jQuery( '#wp-{$sIDSelector}-wrap' ).show();
				})
			</script>";		
			
		}	
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_color' ) ) :
/**
 * Defines the color field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_color extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'vMaxLength'			=> 400,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 * 
	 * Loads necessary files of the color field type.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramework_MetaBox. Changed the name from enqueueColorFieldScript().
	 * @see				http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
	 */ 
	public function replyToFieldLoader() {
		
		// If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
		if ( version_compare( $GLOBALS['wp_version'], '3.5', '>=' ) ) {
			//Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
		//If the WordPress version is less than 3.5 load the older farbtasic color picker.
		else {
			//As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
		}	
		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Color Picker */
			.repeatable .colorpicker {
				display: inline;
			}
			.admin-page-framework-field-color .wp-picker-container {
				vertical-align: middle;
			}
			.admin-page-framework-field-color .ui-widget-content {
				border: none;
				background: none;
				color: transparent;
			}
			.admin-page-framework-field-color .ui-slider-vertical {
				width: inherit;
				height: auto;
				margin-top: -11px;
			}	
			" . PHP_EOL;		
	}	
	
	/**
	 * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
	 * @since			2.0.0
	 * @since			2.1.3			Changed to define a global function literal that registers the given input field as a color picker.
	 * @since			2.1.5			Changed the name from getColorPickerScript().
	 * @var				string
	 * @remark			It is accessed from the main class and meta box class.
	 * @remark			This is made to be a method rather than a property because in the future a variable may need to be used in the script code like the above image selector script.
	 * @access			public	
	 * @internal
	 * @return			string			The image selector script.
	 */ 
	public function replyToGetInputScripts() {
		return "
			registerAPFColorPickerField = function( sInputID ) {
				'use strict';
				// This if statement checks if the color picker element exists within jQuery UI
				// If it does exist then we initialize the WordPress color picker on our text input field
				if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
					var myColorPickerOptions = {
						defaultColor: false,	// you can declare a default color here, or in the data-default-color attribute on the input				
						change: function(event, ui){},	// a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/			
						clear: function() {},	// a callback to fire when the input is emptied or an invalid color
						hide: true,	// hide the color picker controls on load
						palettes: true	// show a group of common colors beneath the square or, supply an array of colors to customize further
					};			
					jQuery( '#' + sInputID ).wpColorPicker( myColorPickerOptions );
				}
				else {
					// We use farbtastic if the WordPress color picker widget doesn't exist
					jQuery( '#color_' + sInputID ).farbtastic( '#' + sInputID );
				}
			}
		";		
	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];
	
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
	
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"					
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$sTagID}_{$sKey}' "
								. "class='input_color " . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "	// text
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . ( $this->getCorrespondingArrayValue( $vValue, $sKey, 'transparent' ) ) . "' "
								. "color='" . ( $this->getCorrespondingArrayValue( $vValue, $sKey, 'transparent' ) ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
						. "<div class='colorpicker' id='color_{$sTagID}_{$sKey}' rel='{$sTagID}_{$sKey}'></div>"	// this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x )
						. $this->getColorPickerEnablerScript( "{$sTagID}_{$sKey}" )
					. "</div>"
				. "</div>"	// admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-color' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";	
		
	}
		/**
		 * A helper function for the above getColorField() method to add a script to enable the color picker.
		 */
		private function getColorPickerEnablerScript( $sInputID ) {
			return
				"<script type='text/javascript' class='color-picker-enabler-script'>
					jQuery( document ).ready( function(){
						registerAPFColorPickerField( '{$sInputID}' );
					});
				</script>";
		}	

	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_image' ) ) :
/**
 * Defines the image field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_image extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'attributes_to_capture'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'size'									=> 60,
			'vMaxLength'							=> 400,
			'vImagePreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();	
	}	
	/**
	 * Enqueues scripts and styles for the media uploader.
	 * 
	 * @remark			Used by the image and media field types.
	 * @since			2.1.5
	 */
	protected function enqueueMediaUploader() {
		
		// add_filter( 'gettext', array( $this, 'replyToReplacingThickBoxText' ) , 1, 2 );
		add_filter( 'media_upload_tabs', array( $this, 'replyToRemovingMediaLibraryTab' ) );
		
		wp_enqueue_script( 'jquery' );			
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	
		if ( function_exists( 'wp_enqueue_media' ) ) 	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else		
			wp_enqueue_script( 'media-upload' );
			
	}
		/**
		 * Removes the From URL tab from the media uploader.
		 * 
		 * since			2.1.3
		 * since			2.1.5			Moved from AdminPageFramework_Setting. Changed the name from removeMediaLibraryTab() to replyToRemovingMediaLibraryTab().
		 * @remark			A callback for the <em>media_upload_tabs</em> hook.	
		 */
		public function replyToRemovingMediaLibraryTab( $aTabs ) {
			
			if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
			
			if ( ! $_REQUEST['enable_external_source'] )
				unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
			
			return $aTabs;
			
		}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {		
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	
			. $this->getScript_ImageSelector( 
				"admin_page_framework", 
				$this->oMsg->__( 'upload_image' ),
				$this->oMsg->__( 'use_this_image' )
		);
	}
		/**
		 * Returns the JavaScript script that creates a custom media uploader object.
		 * 
		 * @remark			Used by the image and media field types.
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_Property_Base.
		 */
		protected function getScript_CustomMediaUploaderObject() {
			
			 $bLoaded = isset( $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] )
				? $GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] : false;
			
			if( ! function_exists( 'wp_enqueue_media' ) || $bLoaded )	// means the WordPress version is 3.4.x or below
				return "";
			
			$GLOBALS['aAdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] = true;
			
			// Global function literal
			return "
				getAPFCustomMediaUploaderSelectObject = function() {
					return wp.media.view.MediaFrame.Select.extend({

						initialize: function() {
							wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

							_.defaults( this.options, {
								multiple:  true,
								editing:   false,
								state:    'insert'
							});

							this.createSelection();
							this.createStates();
							this.bindHandlers();
							this.createIframeStates();
						},

						createStates: function() {
							var options = this.options;

							// Add the default states.
							this.states.add([
								// Main states.
								new wp.media.controller.Library({
									id:         'insert',
									title:      'Insert Media',
									priority:   20,
									toolbar:    'main-insert',
									filterable: 'image',
									library:    wp.media.query( options.library ),
									multiple:   options.multiple ? 'reset' : false,
									editable:   true,

									// If the user isn't allowed to edit fields,
									// can they still edit it locally?
									allowLocalEdits: true,

									// Show the attachment display settings.
									displaySettings: true,
									// Update user settings when users adjust the
									// attachment display settings.
									displayUserSettings: true
								}),

								// Embed states.
								new wp.media.controller.Embed(),
							]);


							if ( wp.media.view.settings.post.featuredImageId ) {
								this.states.add( new wp.media.controller.FeaturedImage() );
							}
						},

						bindHandlers: function() {
							// from Select
							this.on( 'router:create:browse', this.createRouter, this );
							this.on( 'router:render:browse', this.browseRouter, this );
							this.on( 'content:create:browse', this.browseContent, this );
							this.on( 'content:render:upload', this.uploadContent, this );
							this.on( 'toolbar:create:select', this.createSelectToolbar, this );
							//

							this.on( 'menu:create:gallery', this.createMenu, this );
							this.on( 'toolbar:create:main-insert', this.createToolbar, this );
							this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
							this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
							this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

							var handlers = {
									menu: {
										'default': 'mainMenu'
									},

									content: {
										'embed':          'embedContent',
										'edit-selection': 'editSelectionContent'
									},

									toolbar: {
										'main-insert':      'mainInsertToolbar'
									}
								};

							_.each( handlers, function( regionHandlers, region ) {
								_.each( regionHandlers, function( callback, handler ) {
									this.on( region + ':render:' + handler, this[ callback ], this );
								}, this );
							}, this );
						},

						// Menus
						mainMenu: function( view ) {
							view.set({
								'library-separator': new wp.media.View({
									className: 'separator',
									priority: 100
								})
							});
						},

						// Content
						embedContent: function() {
							var view = new wp.media.view.Embed({
								controller: this,
								model:      this.state()
							}).render();

							this.content.set( view );
							view.url.focus();
						},

						editSelectionContent: function() {
							var state = this.state(),
								selection = state.get('selection'),
								view;

							view = new wp.media.view.AttachmentsBrowser({
								controller: this,
								collection: selection,
								selection:  selection,
								model:      state,
								sortable:   true,
								search:     false,
								dragInfo:   true,

								AttachmentView: wp.media.view.Attachment.EditSelection
							}).render();

							view.toolbar.set( 'backToLibrary', {
								text:     'Return to Library',
								priority: -100,

								click: function() {
									this.controller.content.mode('browse');
								}
							});

							// Browse our library of attachments.
							this.content.set( view );
						},

						// Toolbars
						selectionStatusToolbar: function( view ) {
							var editable = this.state().get('editable');

							view.set( 'selection', new wp.media.view.Selection({
								controller: this,
								collection: this.state().get('selection'),
								priority:   -40,

								// If the selection is editable, pass the callback to
								// switch the content mode.
								editable: editable && function() {
									this.controller.content.mode('edit-selection');
								}
							}).render() );
						},

						mainInsertToolbar: function( view ) {
							var controller = this;

							this.selectionStatusToolbar( view );

							view.set( 'insert', {
								style:    'primary',
								priority: 80,
								text:     'Select Image',
								requires: { selection: true },

								click: function() {
									var state = controller.state(),
										selection = state.get('selection');

									controller.close();
									state.trigger( 'insert', selection ).reset();
								}
							});
						},

						featuredImageToolbar: function( toolbar ) {
							this.createSelectToolbar( toolbar, {
								text:  'Set Featured Image',
								state: this.options.state || 'upload'
							});
						},

						mainEmbedToolbar: function( toolbar ) {
							toolbar.view = new wp.media.view.Toolbar.Embed({
								controller: this,
								text: 'Insert Image'
							});
						}		
					});
				}
			";
		}	
		/**
		 * Returns the image selector JavaScript script to be loaded in the head tag of the created admin pages.
		 * @var				string
		 * @remark			It is accessed from the main class and meta box class.
		 * @remark			Moved to the base class since 2.1.0.
		 * @access			private	
		 * @internal
		 * @return			string			The image selector script.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_Property_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
		 */		
		private function getScript_ImageSelector( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( sRawHTML ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'img', sHTML ).attr( 'src' );
							var alt = jQuery( 'img', sHTML ).attr( 'alt' );
							var title = jQuery( 'img', sHTML ).attr( 'title' );
							var width = jQuery( 'img', sHTML ).attr( 'width' );
							var height = jQuery( 'img', sHTML ).attr( 'height' );
							var classes = jQuery( 'img', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
							var sCaption = sRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
								.replace( /<a.*?>(.*?)<\/a>/m, '' );
							var align = sRawHTML.replace( /^.*?\[\w+.*?\salign=([\'\"])(.*?)[\'\"]\s.+$/mg, '$2' );	//\'\" syntax fixer
							var link = jQuery( sHTML ).find( 'a:first' ).attr( 'href' );

							// Escape the strings of some of the attributes.
							var sCaption = jQuery( '<div/>' ).text( sCaption ).html();
							var sAlt = jQuery( '<div/>' ).text( alt ).html();
							var title = jQuery( '<div/>' ).text( title ).html();						
							
							// If the user wants to save relevant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );
							jQuery( '#' + field_id + '_width' ).val( width );
							jQuery( '#' + field_id + '_height' ).val( height );
							jQuery( '#' + field_id + '_caption' ).val( sCaption );
							jQuery( '#' + field_id + '_alt' ).val( sAlt );
							jQuery( '#' + field_id + '_title' ).val( title );						
							jQuery( '#' + field_id + '_align' ).val( align );						
							jQuery( '#' + field_id + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + field_id ).attr( 'alt', alt );
							jQuery( '#image_preview_' + field_id ).attr( 'title', title );
							jQuery( '#image_preview_' + field_id ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + field_id ).attr( 'data-id', id );
							jQuery( '#image_preview_' + field_id ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + field_id ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + field_id ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + sInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( custom_uploader ) {
							custom_uploader.open();
							return;
						}					
						
						// Store the original select object in a global variable
						oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						var custom_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							library     : { type : 'image' },
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( sInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( sInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
											
						});
						
						// Open the uploader dialog
						custom_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( sInputID, image ) {

						// Escape the strings of some of the attributes.
						var sCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var sAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var title = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + sInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						jQuery( 'input#' + sInputID + '_id' ).val( image.id );
						jQuery( 'input#' + sInputID + '_width' ).val( image.width );
						jQuery( 'input#' + sInputID + '_height' ).val( image.height );
						jQuery( 'input#' + sInputID + '_caption' ).val( sCaption );
						jQuery( 'input#' + sInputID + '_alt' ).val( sAlt );
						jQuery( 'input#' + sInputID + '_title' ).val( title );
						jQuery( 'input#' + sInputID + '_align' ).val( image.align );
						jQuery( 'input#' + sInputID + '_link' ).val( image.link );
						
						// Update up the preview
						jQuery( '#image_preview_' + sInputID ).attr( 'data-id', image.id );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-width', image.width );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-height', image.height );
						jQuery( '#image_preview_' + sInputID ).attr( 'data-caption', sCaption );
						jQuery( '#image_preview_' + sInputID ).attr( 'alt', sAlt );
						jQuery( '#image_preview_' + sInputID ).attr( 'title', title );
						jQuery( '#image_preview_' + sInputID ).attr( 'src', image.url );
						jQuery( '#image_preview_container_' + sInputID ).show();				
						
					}
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
			"/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 1em;
				margin-bottom: 1em;
				display: block; 
			}		
			@media only screen and ( max-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			} 
			@media only screen and ( max-width: 900px ) {
				.admin-page-framework-field .image_preview {
					max-width: 440px;
				}
			}	
			@media only screen and ( max-width: 600px ) {
				.admin-page-framework-field .image_preview {
					max-width: 300px;
				}
			}		
			@media only screen and ( max-width: 480px ) {
				.admin-page-framework-field .image_preview {
					max-width: 240px;
				}
			}
			@media only screen and ( min-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			}		 
			.admin-page-framework-field .image_preview img {		
				width: auto;
				height: auto; 
				max-width: 100%;
				display: block;
			}
		/* Image Uploader Button */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
			}
			.select_image.button.button-small {
				vertical-align: baseline;
			}			
		" . PHP_EOL;	
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"					
					. $this->getImageInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$sTagID}'>" 
				. implode( PHP_EOL, $aOutput ) 
			. "</div>";		
		
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() method to return input elements.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added some parameters.
		 */
		private function getImageInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['attributes_to_capture'] );
			
			// The URL input field is mandatory as the preview element uses it.
			$aOutputs = array(
				( $sLabel && ! $aField['repeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
					: ''
				)			
				. "<input id='{$sTagID}_{$sKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . ( $iCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $sImageURL = $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['attributes_to_capture'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$sTagID}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getImageInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$sTagID}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $aField['vImagePreview'], $sKey, true )
					? "<div id='image_preview_container_{$sTagID}_{$sKey}' "
							. "class='image_preview' "
							. "style='" . ( $sImageURL ? "" : "display : none;" ) . "'"
						. ">"
							. "<img src='{$sImageURL}' "
								. "id='image_preview_{$sTagID}_{$sKey}' "
							. "/>"
						. "</div>"
					: "" )
				. $this->getImageUploaderButtonScript( "{$sTagID}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false );
			
		}
		/**
		 * A helper function for the above getImageInputTags() method that retrieve the specified input field value.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField
		 */
		private function getImageInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AdminPageFramework_InputField.
		 */
		private function getImageUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_image_{$sInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->__( 'select_image' )
				."</a>";
			
			$sScript = "
				if ( jQuery( 'a#select_image_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$sScript .="
					jQuery( document ).ready( function(){			
						setAPFImageUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $sScript . "</script>" . PHP_EOL;

		}	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_media' ) ) :
/**
 * Defines the media field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_media extends AdminPageFramework_InputFieldType_image {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'attributes_to_capture'					=> array(),
			'size'									=> 60,
			'vMaxLength'							=> 400,
			'sTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'sLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'allow_external_source' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	// defined in the parent class
			. $this->getScript_MediaUploader(
				"admin_page_framework", 
				$this->oMsg->__( 'upload_file' ),
				$this->oMsg->__( 'use_this_file' )
			);
	}	
		/**
		 * Returns the media uploader JavaScript script to be loaded in the head tag of the created admin pages.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from ... Chaned the name from getMediaUploaderScript().
		 */
		private function getScript_MediaUploader( $sReferrer, $sThickBoxTitle, $sThickBoxButtonUseThis ) {
			
			if ( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_media' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_file_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );					
							tb_show( '{$sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$sThickBoxButtonUseThis}&amp;type=media&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( sRawHTML, param ) {

							var sHTML = '<div>' + sRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'a', sHTML ).attr( 'href' );
							var classes = jQuery( 'a', sHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
						
							// If the user wants to save relavant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );			
								
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
				
			return "
			jQuery( document ).ready( function(){		
				// Global Function Literal 
				setAPFMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_media_' + sInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_media_' + sInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( media_uploader ) {
							media_uploader.open();
							return;
						}		
						
						// Store the original select object in a global variable
						oAPFOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalMediaUploaderSelectObject;
						var media_uploader = wp.media({
							title: '{$sThickBoxTitle}',
							button: {
								text: '{$sThickBoxButtonUseThis}'
							},
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						media_uploader.on( 'close', function() {

							var state = media_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( sInputID, image );
							} else {
								
								var selection = media_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( sInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + sInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var sInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( sInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalMediaUploaderSelectObject;	
							
						});
						
						// Open the uploader dialog
						media_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( sInputID, image ) {
									
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( '#' + sInputID ).val( image.url );		// the url field is mandatory so  it does not have the suffix.
						jQuery( '#' + sInputID + '_id' ).val( image.id );				
						jQuery( '#' + sInputID + '_caption' ).val( jQuery( '<div/>' ).text( image.caption ).html() );				
						jQuery( '#' + sInputID + '_description' ).val( jQuery( '<div/>' ).text( image.description ).html() );				
						
					}
				}		
				
			});";
		}
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Media Uploader Button */
			.admin-page-framework-field-media input {
				margin-right: 0.5em;
			}
			.select_media.button.button-small {
				vertical-align: baseline;
			}		
		";
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];			
		$bMultipleFields = is_array( $aFields );	
		$bRepeatable = $aField['repeatable'];			
			
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] =
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"					
					. $this->getMediaInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-media' id='{$sTagID}'>" 
				. implode( PHP_EOL, $aOutput ) 
			. "</div>";		
			
	}
		/**
		 * A helper function for the above getImageField() method to return input elements.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaInputTags( $vValue, $aField, $sFieldName, $sTagID, $sKey, $sLabel, $bMultipleFields, $_aDefaultKeys ) {
	
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$iCountAttributes = count( ( array ) $aField['attributes_to_capture'] );	
			
			// The URL input field is mandatory as the preview element uses it.
			$aOutputs = array(
				( $sLabel && ! $aField['repeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>" 
					: ''
				)
				. "<input id='{$sTagID}_{$sKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . ( $iCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $this->getMediaInputValue( $vValue, $sKey, $bMultipleFields, $iCountAttributes ? 'url' : '', $_aDefaultKeys ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $aField['attributes_to_capture'] as $sAttribute )
				$aOutputs[] = 
					"<input id='{$sTagID}_{$sKey}_{$sAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $bMultipleFields ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}" ) . "[{$sAttribute}]' " 
						. "value='" . $this->getMediaInputValue( $vValue, $sKey, $bMultipleFields, $sAttribute, $_aDefaultKeys  ) . "' "
						. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container media-field'>"
					. "<label for='{$sTagID}_{$sKey}' >"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] )
						. implode( PHP_EOL, $aOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. $this->getMediaUploaderButtonScript( "{$sTagID}_{$sKey}", $aField['repeatable'] ? true : false, $aField['allow_external_source'] ? true : false );
			
		}
		/**
		 * A helper function for the above getMediaInputTags() method that retrieve the specified input field value.
		 * @since			2.1.3
		 */
		private function getMediaInputValue( $vValue, $sKey, $bMultipleFields, $sCaptureAttribute, $_aDefaultKeys ) {	

			$vValue = $bMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $sKey, $_aDefaultKeys['default'] )
				: ( isset( $vValue ) ? $vValue : $_aDefaultKeys['default'] );

			return $sCaptureAttribute
				? ( isset( $vValue[ $sCaptureAttribute ] ) ? $vValue[ $sCaptureAttribute ] : "" )
				: $vValue;
			
		}		
		/**
		 * A helper function for the above getMediaInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaUploaderButtonScript( $sInputID, $bRpeatable, $bExternalSource ) {
			
			$sButton ="<a id='select_media_{$sInputID}' "
						. "href='#' "
						. "class='select_media button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $bExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->__( 'select_file' )
				."</a>";
			
			$sScript = "
				if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
					jQuery( 'input#{$sInputID}' ).after( \"{$sButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$sScript .="
					jQuery( document ).ready( function(){			
						setAPFMediaUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $sScript . "</script>" . PHP_EOL;

		}	
		
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_select extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 1,
			'vMultiple'				=> false,				// ( array or boolean ) This value indicates whether the select tag should have the multiple attribute or not.
			'vWidth'				=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $aField['label'] ) ) return;	

		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels = $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label ) {
			
			$bMultiple = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] );
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<select id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "
									. ( $bMultiple ? "multiple='Multiple' " : '' )
									. "name=" . ( $bSingle ? "'{$sFieldName}" : "'{$sFieldName}[{$sKey}]" ) . ( $bMultiple ? "[]' " : "' " )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. "size=" . ( $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) ) . " "
									. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
								. ">"
									. $this->getOptionTags( $label, $vValue, $sTagID, $sKey, $bSingle, $bMultiple )
								. "</select>"
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $sTagID, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
						. "value='{$sKey}' "
						. (	$bMultiple 
							? ( in_array( $sKey, $aValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $sLabel
					. "</option>";
			}
			return implode( '', $aOutput );
		}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_radio extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $aField['label'] ) ) return;	
		
		$bSingle = ( $this->getArrayDimension( ( array ) $aField['label'] ) == 1 );
		$aLabels =  $bSingle ? array( $aField['label'] ) : $aField['label'];
		foreach( $aLabels as $sKey => $label )  
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. $this->getRadioTags( $aField, $vValue, $label, $sFieldName, $sTagID, $sKey, $bSingle, $_aDefaultKeys )				
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-radio' id='{$sTagID}'>" 
				. implode( '', $aOutput )
			. "</div>";
		
	}
		/**
		 * A helper function for the <em>getRadioField()</em> method.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from AdminPageFramework_InputField. Added the $aField, $sFieldName, $_aDefaultKeys, $sTagID, and $vValue parameter.
		 */ 
		private function getRadioTags( $aField, $vValue, $aLabels, $sFieldName, $sTagID, $sIterationID, $bSingle, $_aDefaultKeys ) {
			
			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) 
				$aOutput[] = 
					"<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sIterationID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='radio' "
									. "value='{$sKey}' "
									. "name=" . ( ! $bSingle  ? "'{$sFieldName}[{$sIterationID}]' " : "'{$sFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? 'Checked ' : '' )
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>";

			return implode( '', $aOutput );
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_checkbox' ) ) :
/**
 * Defines the checkbox field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_checkbox extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		

		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"	
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name=" .  ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// checkbox
									. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, null ) == 1 ? "Checked " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-checkbox' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";	
	
	}

}
endif;


if ( ! class_exists( 'AdminPageFramework_InputFieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_size extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size_units'				=> array(	// the default unit size array.
				'px'	=> 'px',	// pixel
				'%'		=> '%',		// percentage
				'em'	=> 'em',	// font size
				'ex'	=> 'ex',	// font height
				'in'	=> 'in',	// inch
				'cm'	=> 'cm',	// centimetre
				'mm'	=> 'mm',	// millimetre
				'pt'	=> 'pt',	// point
				'pc'	=> 'pc',	// pica
			),
			'size'						=> 10,
			'vUnitSize'					=> 1,
			'vMaxLength'				=> 400,
			'vMin'						=> null,
			'vMax'						=> null,
			'vStep'						=> null,
			'vMultiple'					=> false,
			'vWidth'					=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		" . PHP_EOL;
	}
	
	/**
	 * Returns the output of the field type.
	 *
	 * Returns the size input fields. This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
	 * Useful for theme developers.
	 * 
	 * @since			2.0.1
	 * @since			2.1.5			Moved from AdminPageFramework_InputField. Changed the name from getSizeField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
				
		$bSingle = ! is_array( $aField['label'] );
		$bIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $aField['size_units'] ) == 1 );
		$aSizeUnits = isset( $aField['size_units'] ) && is_array( $aField['size_units'] ) && $bIsSizeUnitForSingle 
			? $aField['size_units']
			: $_aDefaultKeys['size_units'];		
		
		foreach( ( array ) $aField['label'] as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<label for='{$sTagID}_{$sKey}'>"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. ( $sLabel 
							? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel ."</span>"
							: "" 
						)
						. "<input id='{$sTagID}_{$sKey}' "	// number field
							// . "style='text-align: right;'"
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $aField['vMaxLength'], $sKey, $_aDefaultKeys['vMaxLength'] ) . "' "
							. "type='number' "	// number
							. "name=" . ( $bSingle ? "'{$sFieldName}[size]' " : "'{$sFieldName}[{$sKey}][size]' " )
							. "value='" . ( $bSingle ? $this->getCorrespondingArrayValue( $vValue['size'], $sKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'size', '' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $aField['vReadOnly'], $sKey ) ? "readonly='readonly' " : '' )
							. "min='" . $this->getCorrespondingArrayValue( $aField['vMin'], $sKey, $_aDefaultKeys['vMin'] ) . "' "
							. "max='" . $this->getCorrespondingArrayValue( $aField['vMax'], $sKey, $_aDefaultKeys['vMax'] ) . "' "
							. "step='" . $this->getCorrespondingArrayValue( $aField['vStep'], $sKey, $_aDefaultKeys['vStep'] ) . "' "					
						. "/>"
					. "</label>"
						. "<select id='{$sTagID}_{$sKey}' class='size-field-select'"	// select field
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "
							. ( ( $bMultipleOptions = $this->getCorrespondingArrayValue( $aField['vMultiple'], $sKey, $_aDefaultKeys['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $bSingle ? "'{$sFieldName}[unit]" : "'{$sFieldName}[{$sKey}][unit]" ) . ( $bMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $aField['vUnitSize'], $sKey, $_aDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $sWidth = $this->getCorrespondingArrayValue( $aField['vWidth'], $sKey, $_aDefaultKeys['vWidth'] ) ) ? "style='width:{$sWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$bSingle ? $aSizeUnits : $this->getCorrespondingArrayValue( $aField['size_units'], $sKey, $aSizeUnits ),
							$bSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $sKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $sKey, array() ), 'unit', 'px' ),
							$sTagID,
							$sKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$bMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);			

		return "<div class='admin-page-framework-field-size' id='{$sTagID}'>" 
			. implode( '', $aOutput )
		. "</div>";
		
	}
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $sTagID parameter. Moved from AdminPageFramwrodk_InputField.
		 */ 
		private function getOptionTags( $aLabels, $vValue, $sTagID, $sIterationID, $bSingle, $bMultiple=false ) {	

			$aOutput = array();
			foreach ( $aLabels as $sKey => $sLabel ) {
				$aValue = $bSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $sIterationID, array() ) ;
				$aOutput[] = "<option "
						. "id='{$sTagID}_{$sIterationID}_{$sKey}' "
						. "value='{$sKey}' "
						. (	$bMultiple 
							? ( in_array( $sKey, $aValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $sIterationID, null ) == $sKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $sLabel
					. "</option>";
			}
			return implode( '', $aOutput );
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_hidden' ) ) :
/**
 * Defines the hidden field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_hidden extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @remark			The user needs to assign the value to either the default key or the vValue key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='{$aField['type']}' "	// hidden
									. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
									. "value='" . $sValue  . "' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
								. "/>"
							. "</div>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-hidden' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_file' ) ) :
/**
 * Defines the file field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_file extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vAcceptAttribute'				=> 'audio/*|video/*|image/*|MIME_type',
			// 'size'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
					
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
							. ( $sLabel && ! $aField['repeatable'] ?
								"<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>" . $sLabel . "</span>"
								: ""
							)
							. "<input "
								. "id='{$sTagID}_{$sKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
								. "type='{$aField['type']}' "	// file
								. "name=" . ( is_array( $aFields ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $aFields, $sKey ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
	}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_posttype' ) ) :
/**
 * Defines the posttype field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_posttype extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'aRemove'					=> array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of post type checklist check boxes.
	 * 
	 * @remark			the posttype checklist field does not support multiple elements by passing an array of labels.
	 * @since			2.0.0
	 * 
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
						
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $aField['aRemove'] ) as $sKey => $sValue ) {
			$sName = "{$sFieldName}[{$sKey}]";
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<label for='{$sTagID}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] )
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name='{$sName}' value='0' />"
								. "<input "
									. "id='{$sTagID}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='checkbox' "
									. "name='{$sName}'"
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, false ) == 1 ? "Checked " : '' )				
								. "/>"
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sValue
							. "</span>"				
							. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-posttype' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}	
	
		/**
		 * A helper function for the above getPosttypeChecklistField method.
		 * 
		 * @since			2.0.0
		 * @since			2.1.1			Changed the returning array to have the labels in its element values.
		 * @since			2.1.5			Moved from AdminPageFramework_InputTag.
		 * @return			array			The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
		 */ 
		private function getPostTypeArrayForChecklist( $aRemoveNames, $aPostTypes=array() ) {
			
			foreach( get_post_types( '','objects' ) as $oPostType ) 
				if (  isset( $oPostType->name, $oPostType->label ) ) 
					$aPostTypes[ $oPostType->name ] = $oPostType->label;

			return array_diff_key( $aPostTypes, array_flip( $aRemoveNames ) );	

		}		
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_taxonomy' ) ) :
/**
 * Defines the taxonomy field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_taxonomy extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'taxonomy_slugs'					=> 'category',			// ( string ) This is for the taxonomy field type.
			'height'						=> '250px',				// for the taxonomy checklist field type, since 2.1.1.
			'sWidth'						=> '100%',				// for the taxonomy checklist field type, since 2.1.1.		
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 * 
	 * Returns the JavaScript script of the taxonomy field type.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AdminPageFramework_Property_Base().
	 */ 
	public function replyToGetInputScripts() {
		return "
			jQuery( document ).ready( function() {
				jQuery( '.tab-box-container' ).each( function() {
					jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
						
						if ( i == 0 )
							jQuery( this ).addClass( 'active' );
							
						jQuery( this ).click( function( e ){
								 
							// Prevents jumping to the anchor which moves the scroll bar.
							e.preventDefault();
							
							// Remove the active tab and set the clicked tab to be active.
							jQuery( this ).siblings( 'li.active' ).removeClass( 'active' );
							jQuery( this ).addClass( 'active' );
							
							// Find the element id and select the content element with it.
							var thisTab = jQuery( this ).find( 'a' ).attr( 'href' );
							active_content = jQuery( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' ); 
							active_content.siblings().css( 'display', 'none' );
							
						});
					});			
				});
			});
		";
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Taxonomy Field Type */
			.admin-page-framework-field .taxonomy-checklist li { 
				margin: 8px 0 8px 20px; 
			}
			.admin-page-framework-field div.taxonomy-checklist {
				padding: 8px 0 8px 10px;
				margin-bottom: 20px;
			}
			.admin-page-framework-field .taxonomy-checklist ul {
				list-style-type: none;
				margin: 0;
			}
			.admin-page-framework-field .taxonomy-checklist ul ul {
				margin-left: 1em;
			}
			.admin-page-framework-field .taxonomy-checklist-label {
				/* margin-left: 0.5em; */
			}		
		/* Tabbed box */
			.admin-page-framework-field .tab-box-container.categorydiv {
				max-height: none;
			}
			.admin-page-framework-field .tab-box-tab-text {
				display: inline-block;
			}
			.admin-page-framework-field .tab-box-tabs {
				line-height: 12px;
				margin-bottom: 0;
			
			}
			.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
				display: inline;
				border-color: #dfdfdf #dfdfdf #fff;
				margin-bottom: 0;
				padding-bottom: 1px;
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-container { 
				position: relative; width: 100%; 

			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 0 0 20px; 
				border: 1px solid #dfdfdf; 
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-contents { 
				overflow: hidden; 
				overflow-x: hidden; 
				position: relative; 
				top: -1px; 
				height: 300px;  
			}
			.admin-page-framework-field .tab-box-content { 
				height: 300px;
				display: none; 
				overflow: auto; 
				display: block; 
				position: relative; 
				overflow-x: hidden;
			}
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target { 
				display: block; 
			}			
		" . PHP_EOL;
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return 	".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		";	

	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of taxonomy checklist check boxes.
	 * 
	 * @remark			Multiple fields are not supported.
	 * @remark			Repeater fields are not supported.
	 * @since			2.0.0
	 * @since			2.1.1			The checklist boxes are rendered in a tabbed single box.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		$aTabs = array();
		$aCheckboxes = array();
		foreach( ( array ) $aField['taxonomy_slugs'] as $sKey => $sTaxonomySlug ) {
			$sActive = isset( $sActive ) ? '' : 'active';	// inserts the active class selector into the first element.
			$aTabs[] = 
				"<li class='tab-box-tab'>"
					. "<a href='#tab-{$sKey}'>"
						. "<span class='tab-box-tab-text'>" 
							. $this->getCorrespondingArrayValue( empty( $aField['label'] ) ? null : $aField['label'], $sKey, $this->getLabelFromTaxonomySlug( $sTaxonomySlug ) )
						. "</span>"
					."</a>"
				."</li>";
			$aCheckboxes[] = 
				"<div id='tab-{$sKey}' class='tab-box-content' style='height: {$aField['height']};'>"
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $aField['taxonomy_slugs'] ) ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}",   // name of the input
							'selected' => $this->getSelectedKeyArray( $vValue, $sKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $sTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
							'sTagID' => $sTagID,
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
				. "</div>";
		}
		$sTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( '', $aTabs ) . "</ul>";
		$sContents = 
			"<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$aField['height']};'>"
					. implode( '', $aCheckboxes )
				. "</div>"
			. "</div>";
			
		$sOutput = 
			"<div id='{$sTagID}' class='{$sFieldClassSelector} admin-page-framework-field-taxonomy tab-box-container categorydiv' style='max-width:{$aField['sWidth']};'>"
				. $sTabs . PHP_EOL
				. $sContents . PHP_EOL
			. "</div>";

		return $sOutput;

	}	
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$sKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function getSelectedKeyArray( $vValue, $sKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$iArrayDimension = $this->getArrayDimension( ( array ) $vValue );
					
			if ( $iArrayDimension == 1 )
				$aKeys = $vValue;
			else if ( $iArrayDimension == 2 )
				$aKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $sKey, false );
				
			return array_keys( $aKeys, true );
		
		}
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function getLabelFromTaxonomySlug( $sTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $sTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}
	
}
endif;
if ( ! class_exists( 'AdminPageFramework_InputFieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_submit extends AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(		
			'class_attribute'					=> 'button button-primary',
			'redirect_url'							=> null,
			'links'								=> null,
			'is_reset'							=> null,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		

		
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			$sRedirectURL = $this->getCorrespondingArrayValue( $aField['redirect_url'], $sKey, $_aDefaultKeys['redirect_url'] );
			$sLinkURL = $this->getCorrespondingArrayValue( $aField['links'], $sKey, $_aDefaultKeys['links'] );
			$sResetKey = $this->getCorrespondingArrayValue( $aField['is_reset'], $sKey, $_aDefaultKeys['is_reset'] );
			$bResetConfirmed = $this->checkConfirmationDisplayed( $sResetKey, $sFieldNameFlat ); 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][input_id]' "
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][field_id]' "
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__submit[{$sTagID}_{$sKey}][name]' "
						. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
					. "/>" 						
					// for the redirect_url key
					. ( $sRedirectURL 
						? "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sRedirectURL . "' "
						. "/>" 
						. "<input type='hidden' "
							. "name='__redirect[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}" : "'" )
						. "/>" 
						: "" 
					)
					// for the links key
					. ( $sLinkURL 
						? "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][url]' "
							. "value='" . $sLinkURL . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__link[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: "" 
					)
					// for the is_reset key
					. ( $sResetKey && ! $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sFieldNameFlat . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset_confirm[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. ( $sResetKey && $bResetConfirmed
						? "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][key]' "
							. "value='" . $sResetKey . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset[{$sTagID}_{$sKey}][name]' "
							. "value='{$sFieldNameFlat}" . ( is_array( $vValue ) ? "|{$sKey}'" : "'" )
						. "/>" 
						: ""
					)
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='{$aField['type']}' "	// submit
							. "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'submit' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-submit' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the is_reset key is set.
		 * 
		 */
		private function checkConfirmationDisplayed( $sResetKey, $sFlatFieldName ) {
				
			if ( ! $sResetKey ) return false;
			
			$bResetConfirmed =  get_transient( md5( "reset_confirm_" . $sFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $bResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $sFlatFieldName ) );
				
			return $bResetConfirmed;
			
		}

	/*
	 *	Shared Methods 
	 */
	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 * 
	 * @remark			Used by the import and submit field types.
	 * @since			2.0.0
	 * @since			2.1.5			Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_InputField.
	 */ 
	protected function getInputFieldNameFlat( $aField ) {	
	
		return isset( $aField['sOptionKey'] ) // the meta box class does not use the option key
			? "{$aField['sOptionKey']}|{$aField['page_slug']}|{$aField['section_id']}|{$aField['field_id']}"
			: $aField['field_id'];
		
	}			
	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>label</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @remark			The submit, import, and export field types use this method.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
	 */ 
	protected function getInputFieldValueFromLabel( $aField ) {	
		
		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		if ( isset( $aField['label'] ) ) return $aField['label'];
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_export' ) ) :
/**
 * Defines the export field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_export extends AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'export_data'					=> null,	// ( array or string or object ) This is for the export field type. 			
			'export_format'					=> 'array',	// ( array or string )	for the export field type. Do not set a default value here. Currently array, json, and text are supported.
			'export_file_name'				=> null,	// ( array or string )	for the export field type. Do not set a default value here.
			'class_attribute'				=> 'button button-primary',	// ( array or string )	
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
				
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		
		// If vValue is not an array and the export data set, set the transient. ( it means single )
		if ( isset( $aField['export_data'] ) && ! is_array( $vValue ) )
			set_transient( md5( "{$aField['sClassName']}_{$aField['field_id']}" ), $aField['export_data'], 60*2 );	// 2 minutes.
		
		foreach( ( array ) $vValue as $sKey => $sValue ) {
			
			$sExportFormat = $this->getCorrespondingArrayValue( $aField['export_format'], $sKey, $_aDefaultKeys['export_format'] );
			
			// If it's one of the multiple export buttons and the export data is explictly set for the element, store it as transient in the option table.
			$bIsDataSet = false;
			if ( isset( $vValue[ $sKey ] ) && isset( $aField['export_data'][ $sKey ] ) ) {
				set_transient( md5( "{$aField['sClassName']}_{$aField['field_id']}_{$sKey}" ), $aField['export_data'][ $sKey ], 60*2 );	// 2 minutes.
				$bIsDataSet = true;
			}
			
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"					
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][file_name]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['export_file_name'], $sKey, $this->generateExportFileName( $aField['sOptionKey'], $sExportFormat ) )
					. "' />"
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $sExportFormat
					. "' />"				
					. "<input type='hidden' "
						. "name='__export[{$aField['field_id']}][transient]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . ( $bIsDataSet ? 1 : 0 )
					. "' />"				
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the export button is a custom submit button.
							// . "name=" . ( is_array( $aField['label'] ) ? "'{$sFieldName}[{$sKey}]' " : "'{$sFieldName}' " )
							. "name='__export[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'export_options' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
									
		}
					
		return "<div class='admin-page-framework-field-export' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";		
	
	}
	
		/**
		 * A helper function for the above method.
		 * 
		 * @remark			Currently only array, text or json is supported.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AdminPageFramework_InputField class.
		 */ 
		private function generateExportFileName( $sOptionKey, $sExportFormat='text' ) {
				
			switch ( trim( strtolower( $sExportFormat ) ) ) {
				case 'text':	// for plain text.
					$sExt = "txt";
					break;
				case 'json':	// for json.
					$sExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$sExt = "txt";
					break;
			}		
				
			return $sOptionKey . '_' . date("Ymd") . '.' . $sExt;
			
		}

}
endif;

if ( ! class_exists( 'AdminPageFramework_InputFieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AdminPageFramework_InputFieldType_import extends AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'class_attribute'					=> 'import button button-primary',	// ( array or string )	
			'vAcceptAttribute'					=> 'audio/*|video/*|image/*|MIME_type',
			'class_attributeUpload'				=> 'import',
			'vImportOptionKey'					=> null,	// ( array or string )	for the import field type. The default value is the set option key for the framework.
			'vImportFormat'						=> 'array',	// ( array or string )	for the import field type.
			'vMerge'							=> false,	// ( array or boolean ) [2.1.5+] for the import field
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
	
		$vValue = $this->getInputFieldValueFromLabel( $aField );
		$sFieldNameFlat = $this->getInputFieldNameFlat( $aField );
		foreach( ( array ) $vValue as $sKey => $sValue ) 
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][input_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$sTagID}_{$sKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][field_id]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='{$aField['field_id']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][do_merge]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vMerge'], $sKey, $_aDefaultKeys['vMerge'] ) . "' "
					. "/>"							
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][import_option_key]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportOptionKey'], $sKey, $aField['sOptionKey'] )
					. "' />"
					. "<input type='hidden' "
						. "name='__import[{$aField['field_id']}][format]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $aField['vImportFormat'], $sKey, $_aDefaultKeys['vImportFormat'] )	// array, text, or json.
					. "' />"			
					. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$sTagID}_{$sKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attributeUpload'], $sKey, $_aDefaultKeys['class_attributeUpload'] ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $aField['vAcceptAttribute'], $sKey, $_aDefaultKeys['vAcceptAttribute'] ) . "' "
							. "type='file' "	// upload field. the file type will be stored in $_FILE
							. "name='__import[{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$sTagID}_{$sKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$aField['field_id']}]" . ( is_array( $aField['label'] ) ? "[{$sKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, $this->oMsg->__( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_BuiltinInputFieldTypeDefinitions' ) ) :
/**
 * Provides means to define custom input fields not only by the framework but also by the user.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 * @since			2.1.6			Changed the name from AdminPageFramework_InputFieldTypeDefinitions
 */
class AdminPageFramework_BuiltinInputFieldTypeDefinitions  {
	
	/**
	 * Holds the default input field labels
	 * 
	 * @since			2.1.5
	 */
	protected static $aDefaultFieldTypeSlugs = array(
		'default' => array( 'default' ),	// undefined ones will be applied 
		'text' => array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ),
		'number' => array( 'number', 'range' ),
		'textarea' => array( 'textarea' ),
		'radio' => array( 'radio' ),
		'checkbox' => array( 'checkbox' ),
		'select' => array( 'select' ),
		'hidden' => array( 'hidden' ),
		'file' => array( 'file' ),
		'submit' => array( 'submit' ),
		'import' => array( 'import' ),
		'export' => array( 'export' ),
		'image' => array( 'image' ),
		'media' => array( 'media' ),
		'color' => array( 'color' ),
		'taxonomy' => array( 'color' ),
		'posttype' => array( 'posttype' ),
		'size' => array( 'size' ),
	);	
	
	function __construct( &$aFieldTypeDefinitions, $sExtendedClassName, $oMsg ) {
		foreach( self::$aDefaultFieldTypeSlugs as $sFieldTypeSlug => $aSlugs ) {
			$sInstantiatingClassName = "AdminPageFramework_InputFieldType_{$sFieldTypeSlug}";
			if ( class_exists( $sInstantiatingClassName ) ) {
				$oFieldType = new $sInstantiatingClassName( $sExtendedClassName, $sFieldTypeSlug, $oMsg, false );	// passing false for the forth parameter disables auto-registering.
				foreach( $aSlugs as $sSlug )
					$aFieldTypeDefinitions[ $sSlug ] = $oFieldType->getDefinitionArray();
			}
		}
	}
}


endif;

if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_InputField extends AdminPageFramework_Utility {
		
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $bIsMetaBox = false;
		
	protected static $_aStructure_FieldDefinition = array(
		'hfRenderField' => null,
		'hfGetScripts' => null,
		'hfGetStyles' => null,
		'hfGetIEStyles' => null,
		'hfFieldLoader' => null,
		'aEnqueueScripts' => null,
		'aEnqueueStyles' => null,
		'aDefaultKeys' => null,
	);
	
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldDefinition, &$oMsg ) {
			
		$this->aField = $aField + $aFieldDefinition['aDefaultKeys'] + self::$_aStructure_FieldDefinition;	// better not to merge recursively because some elements are array by default, not as multiple elements.
		$this->aFieldDefinition = $aFieldDefinition;
		$this->aOptions = $aOptions;
		$this->aErrors = $aErrors ? $aErrors : array();
		$this->oMsg = $oMsg;
			
		$this->sFieldName = $this->getInputFieldName();
		$this->sTagID = $this->getInputTagID( $aField );
		$this->vValue = $this->getInputFieldValue( $aField, $aOptions );
		
		// Global variable
		$GLOBALS['aAdminPageFramework']['aFieldFlags'] = isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'] )
			? $GLOBALS['aAdminPageFramework']['aFieldFlags'] 
			: array();
		
	}	
		
	private function getInputFieldName( $aField=null ) {
		
		$aField = isset( $aField ) ? $aField : $this->aField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $aField['sName'] ) ) return $aField['sName'];
		
		return isset( $aField['sOptionKey'] ) // the meta box class does not use the option key
			? "{$aField['sOptionKey']}[{$aField['page_slug']}][{$aField['section_id']}][{$aField['field_id']}]"
			: $aField['field_id'];
		
	}

	private function getInputFieldValue( &$aField, $aOptions ) {	

		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $aField['page_slug'], $aField['section_id'] ) ) {			
		
			$vValue = $this->getInputFieldValueFromOptionTable( $aField, $aOptions );
			if ( $vValue != '' ) return $vValue;
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			$vValue = $this->getInputFieldValueFromPostTable( $_GET['post'], $aField );
			if ( $vValue != '' ) return $vValue;
			
		}
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}	
	private function getInputFieldValueFromOptionTable( &$aField, &$aOptions ) {
		
		if ( ! isset( $aOptions[ $aField['page_slug'] ][ $aField['section_id'] ][ $aField['field_id'] ] ) )
			return;
						
		$vValue = $aOptions[ $aField['page_slug'] ][ $aField['section_id'] ][ $aField['field_id'] ];
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
			
		
	}	
	private function getInputFieldValueFromPostTable( $iPostID, &$aField ) {
		
		$vValue = get_post_meta( $iPostID, $aField['field_id'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
		
	}
		
	private function getInputTagID( $aField )  {
		
		// For Settings API's form fields should have these key values.
		if ( isset( $aField['section_id'], $aField['field_id'] ) )
			return "{$aField['section_id']}_{$aField['field_id']}";
			
		// For meta box form fields,
		if ( isset( $aField['field_id'] ) ) return $aField['field_id'];
		if ( isset( $aField['sName'] ) ) return $aField['sName'];	// the name key is for the input name attribute but it's better than nothing.
		
		// Not Found - it's not a big deal to have an empty value for this. It's just for the anchor link.
		return '';
			
	}		
	
	
	/** 
	 * Retrieves the input field HTML output.
	 * @since			2.0.0
	 * @since			2.1.6			Moved the repeater script outside the fieldset tag.
	 */ 
	public function getInputField( $sFieldType ) {
		
		// Prepend the field error message.
		$sOutput = isset( $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->aField['sError']}" . $this->aErrors[ $this->aField['section_id'] ][ $this->aField['field_id'] ] . "</span><br />"
			: '';		
		
		// Prepeare the field class selector 
		$this->sFieldClassSelector = $this->aField['repeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Add new elements
		$this->aField['sFieldName'] = $this->sFieldName;
		$this->aField['sTagID'] = $this->sTagID;
		$this->aField['sFieldClassSelector'] = $this->sFieldClassSelector;

		// Get the field output.
		$sOutput .= call_user_func_array( 
			$this->aFieldDefinition['hfRenderField'], 
			array( $this->vValue, $this->aField, $this->aOptions, $this->aErrors, $this->aFieldDefinition )
		);			
				
		// Add the description
		$sOutput .= ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>"
			: '';
			
		// Add the repeater script
		$sOutput .= $this->aField['repeatable']
			? $this->getRepeaterScript( $this->sTagID, count( ( array ) $this->vValue ) )
			: '';
			
		return $this->getRepeaterScriptGlobal( $this->sTagID )
			. "<fieldset>"
				. "<div class='admin-page-framework-fields'>"
					. $this->aField['sBeforeField'] 
					. $sOutput
					. $this->aField['sAfterField']
				. "</div>"
			. "</fieldset>";
		
	}
	
	/**
	 * Sets or return the flag that indicates whether the creating fields are for meta boxes or not.
	 * 
	 * If the parameter is not set, it will return the stored value. Otherwise, it will set the value.
	 * 
	 * @since			2.1.2
	 */
	public function isMetaBox( $bTrueOrFalse=null ) {
		
		if ( isset( $bTrueOrFalse ) ) 
			$this->bIsMetaBox = $bTrueOrFalse;
			
		return $this->bIsMetaBox;
		
	}
	
	/**
	 * Indicates whether the repeatable fields script is called or not.
	 * 
	 * @since			2.1.3
	 */
	private $bIsRepeatableScriptCalled = false;
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	private function getRepeaterScript( $sTagID, $iFieldCount ) {

		$sAdd = $this->oMsg->__( 'add' );
		$sRemove = $this->oMsg->__( 'remove' );
		$sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$sButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$sAdd}' data-id='{$sTagID}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$sRemove}' {$sVisibility} data-id='{$sTagID}'>-</a>"
			. "</div>";

		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
				
					// Adds the buttons
					jQuery( '#{$sTagID} .admin-page-framework-field' ).append( \"{$sButtons}\" );
					
					// Update the fields
					updateAPFRepeatableFields( '{$sTagID}' );
					
				});
			</script>";
		
	}

	/**
	 * Returns the script that will be referred multiple times.
	 * since			2.1.3
	 */
	private function getRepeaterScriptGlobal( $sID ) {

		if ( $this->bIsRepeatableScriptCalled ) return '';
		$this->bIsRepeatableScriptCalled = true;
		return 
		"<script type='text/javascript'>
			jQuery( document ).ready( function() {
				
				// Global function literals
				
				// This function modifies the ids and names of the tags of input, textarea, and relevant tags for repeatable fields.
				updateAPFIDsAndNames = function( element, fIncrementOrDecrement ) {

					var updateID = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
							return '_' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}
					var updateName = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
							return '[' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}					
				
					element.attr( 'id', function( index, name ) { return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'name', function( index, name ){ return updateName( index, name ) } );
					
					// Color Pickers
					var nodeColorInput = element.find( 'input.input_color' );
					if ( nodeColorInput.length > 0 ) {
						
							var previous_id = nodeColorInput.attr( 'id' );
							
							if ( fIncrementOrDecrement > 0 ) {	// Add
					
								// For WP 3.5+
								var nodeNewColorInput = nodeColorInput.clone();	// re-clone without bind events.
								
								// For WP 3.4.x or below
								var sInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent';
								var sInputStyle = sInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
								
								nodeNewColorInput.val( sInputValue );	// set the default value	
								nodeNewColorInput.attr( 'style', sInputStyle );	// remove the background color set to the input field ( for WP 3.4.x or below )						 
								
								var nodeFarbtastic = element.find( '.colorpicker' );
								var nodeNewFarbtastic = nodeFarbtastic.clone();	// re-clone without bind elements.
								
								// Remove the old elements
								nodeIris = jQuery( '#' + previous_id ).closest( '.wp-picker-container' );	
								if ( nodeIris.length > 0 ) {	// WP 3.5+
									nodeIris.remove();	
								} else {
									jQuery( '#' + previous_id ).remove();	// WP 3.4.x or below
									element.find( '.colorpicker' ).remove();	// WP 3.4.x or below
								}
							
								// Add the new elements
								element.prepend( nodeNewFarbtastic );
								element.prepend( nodeNewColorInput );
								
							}
							
							element.find( '.colorpicker' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
							element.find( '.colorpicker' ).attr( 'rel', function( index, name ){ return updateID( index, name ) } );					

							// Renew the color picker script
							var cloned_id = element.find( 'input.input_color' ).attr( 'id' );
							registerAPFColorPickerField( cloned_id );					
					
					}

					// Image uploader buttons and image preview elements
					image_uploader_button = element.find( '.select_image' );
					if ( image_uploader_button.length > 0 ) {
						var previous_id = element.find( '.image-field input' ).attr( 'id' );
						image_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview img' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( image_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( image_uploader_button ).attr( 'data-enable_external_source' );
							setAPFImageUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Media uploader buttons
					media_uploader_button = element.find( '.select_media' );
					if ( media_uploader_button.length > 0 ) {
						var previous_id = element.find( '.media-field input' ).attr( 'id' );
						media_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( media_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( media_uploader_button ).attr( 'data-enable_external_source' );
							setAPFMediaUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Date pickers - somehow it needs to destroy the both previous one and the added one and assign the new date pickers 
					var date_picker_script = element.find( 'script.date-picker-enabler-script' );
					if ( date_picker_script.length > 0 ) {
						var previous_id = date_picker_script.attr( 'data-id' );
						date_picker_script.attr( 'data-id', function( index, name ){ return updateID( index, name ) } );

						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker( 'destroy' ); 
						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});						
						jQuery( '#' + previous_id ).datepicker( 'destroy' ); //here
						jQuery( '#' + previous_id ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});												
					}				
									
				}
				
				// This function is called from the updateAPFRepeatableFields() and from the media uploader for multiple file selections.
				addAPFRepeatableField = function( sFieldContainerID ) {	

					var field_container = jQuery( '#' + sFieldContainerID );
					var field_delimiter_id = sFieldContainerID.replace( 'field-', 'delimiter-' );
					var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
					
					var field_new = field_container.clone( true );
					var delimiter_new = field_delimiter.clone( true );
					var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;
			
					field_new.find( 'input,textarea' ).val( '' );	// empty the value		
					field_new.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
					field_new.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
					delimiter_new.insertAfter( target_element );	// add the delimiter
					field_new.insertAfter( target_element );		// add the cloned new field element

					// Increment the names and ids of the next following siblings.
					target_element.nextAll().each( function() {
						updateAPFIDsAndNames( jQuery( this ), true );
					});

					var remove_buttons =  field_container.closest( '.admin-page-framework-fields' ).find( '.repeatable-field-remove' );
					if ( remove_buttons.length > 1 ) 
						remove_buttons.show();				
					
					// Return the newly created element
					return field_new;
					
				}
				
				updateAPFRepeatableFields = function( sID ) {
				
					// Add button behaviour
					jQuery( '#' + sID + ' .repeatable-field-add' ).click( function() {
						
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						addAPFRepeatableField( field_container.attr( 'id' ) );
						return false;
						
					});		
					
					// Remove button behaviour
					jQuery( '#' + sID + ' .repeatable-field-remove' ).click( function() {
						
						// Need to remove two elements: the field container and the delimiter element.
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						var field_container_id = field_container.attr( 'id' );				
						var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
						var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
						var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;

						// Decrement the names and ids of the next following siblings.
						target_element.nextAll().each( function() {
							updateAPFIDsAndNames( jQuery( this ), false );	// the second parameter value indicates it's for decrement.
						});

						field_delimiter.remove();
						field_container.remove();
						
						var fieldsCount = jQuery( '#' + sID + ' .repeatable-field-remove' ).length;
						if ( fieldsCount == 1 ) {
							jQuery( '#' + sID + ' .repeatable-field-remove' ).css( 'display', 'none' );
						}
						return false;
					});
									
				}
			});
		</script>";
	}
	
}
endif;

if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see				Walker : wp-includes/class-wp-walker.php
 * @see				Walker_Category : wp-includes/category-template.php
 * @since			2.0.0
 * @since			2.1.5			Added the sTagID key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends			Walker_Category
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
		
	function start_el( &$sOutput, $oCategory, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0 ) {
		
		/*	
		 	$aArgs keys:
			'show_option_all' => '', 
			'show_option_none' => __('No categories'),
			'orderby' => 'name', 
			'order' => 'ASC',
			'style' => 'list',
			'show_count' => 0, 
			'hide_empty' => 1,
			'use_desc_for_title' => 1, 
			'child_of' => 0,
			'feed' => '', 
			'feed_type' => '',
			'feed_image' => '', 
			'exclude' => '',
			'exclude_tree' => '', 
			'current_category' => 0,
			'hierarchical' => true, 
			'title_li' => __( 'Categories' ),
			'echo' => 1, 
			'depth' => 0,
			'taxonomy' => 'category'	// 'post_tag' or any other registered taxonomy slug will work.

			[class] => categories
			[has_children] => 1
		*/
		
		$aArgs = $aArgs + array(
			'name' 		=> null,
			'disabled'	=> null,
			'selected'	=> array(),
			'sTagID'	=> null,
		);
		
		$iID = $oCategory->term_id;
		$sTaxonomy = empty( $aArgs['taxonomy'] ) ? 'category' : $aArgs['taxonomy'];
		$sChecked = in_array( $iID, ( array ) $aArgs['selected'] )  ? 'Checked' : '';
		$sDisabled = $aArgs['disabled'] ? 'disabled="Disabled"' : '';
		$sClass = 'category-list';
		$sID = "{$aArgs['sTagID']}_{$sTaxonomy}_{$iID}";
		$sOutput .= "\n"
			. "<li id='list-{$sID}' $sClass>" 
				. "<label for='{$sID}' class='taxonomy-checklist-label'>"
					. "<input value='0' type='hidden' name='{$aArgs['name']}[{$iID}]' />"
					. "<input id='{$sID}' value='1' type='checkbox' name='{$aArgs['name']}[{$iID}]' {$sChecked} {$sDisabled} />"
					. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
				. "</label>";	
			// no need to close </li> since it is dealt in end_el().
			
	}
}
endif;

if ( ! class_exists( 'AdminPageFramework_PostType' ) ) :
/**
 * Provides methods for registering custom post types.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>cell_ + post type + _ + column key</code> – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p> 
 * 
 * @abstract
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Post Type
 */
abstract class AdminPageFramework_PostType {	

	// Objects
	/**
	 * @since			2.0.0
	 * @internal
	 */ 
	protected $oUtil;
	/**
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected $oLink;
		
	/**
	* Constructs the class object, AdminPageFramework_PostType.
	* 
	* <h4>Example</h4>
	* <code>new APF_PostType( 
	* 	'apf_posts', 	// post type slug
	* 	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* 		'labels' => array(
	* 			'name' => 'Admin Page Framework',
	* 			'singular_name' => 'Admin Page Framework',
	* 			'add_new' => 'Add New',
	* 			'add_new_item' => 'Add New APF Post',
	* 			'edit' => 'Edit',
	* 			'edit_item' => 'Edit APF Post',
	* 			'new_item' => 'New APF Post',
	* 			'view' => 'View',
	* 			'view_item' => 'View APF Post',
	* 			'search_items' => 'Search APF Post',
	* 			'not_found' => 'No APF Post found',
	* 			'not_found_in_trash' => 'No APF Post found in Trash',
	* 			'parent' => 'Parent APF Post'
	* 		),
	* 		'public' => true,
	* 		'menu_position' => 110,
	* 		'supports' => array( 'title' ),
	* 		'taxonomies' => array( '' ),
	* 		'menu_icon' => null,
	* 		'has_archive' => true,
	* 		'show_admin_column' => true,	// for custom taxonomies
	* 	)		
	* );</code>
	* @since			2.0.0
	* @since			2.1.6			Added the $sTextDomain parameter.
	* @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* @param			string			$sPostType			The post type slug.
	* @param			array			$aArgs				The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
	* @param			string			$sCallerPath			The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
	* @param			string			$sTextDomain			The text domain of the caller script.
	* @return			void
	*/
	public function __construct( $sPostType, $aArgs=array(), $sCallerPath=null, $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oProp = new AdminPageFramework_Property_PostType( $this );
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oHeadTag = new AdminPageFramework_HeadTag_PostType( $this->oProp );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_PostType::instantiate( $this->oProp, $this->oMsg );
		
		// Properties
		$this->oProp->sPostType = $this->oUtil->sanitizeSlug( $sPostType );
		$this->oProp->aPostTypeArgs = $aArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->oProp->sClassName = get_class( $this );
		$this->oProp->sClassHash = md5( $this->oProp->sClassName );
		$this->oProp->aColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',		// Checkbox for bulk actions. 
			'title'			=> $this->oMsg->__( 'title' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> $this->oMsg->__( 'author' ), 	// Post author.
			// 'categories'	=> $this->oMsg->__( 'categories' ),	// Categories the post belongs to. 
			// 'tags'		=> $this->oMsg->__( 'tags' ), 		//	Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> $this->oMsg->__( 'date' ), 		// The date and publish status of the post. 
		);			
		$this->oProp->sCallerPath = $sCallerPath;
		
		add_action( 'init', array( $this, 'registerPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		
		if ( $this->oProp->sPostType != '' && is_admin() ) {			
		
			add_action( 'admin_enqueue_scripts', array( $this, 'disableAutoSave' ) );
			
			// For table columns
			add_filter( "manage_{$this->oProp->sPostType}_posts_columns", array( $this, 'setColumnHeader' ) );
			add_filter( "manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, 'setSortableColumns' ) );
			add_action( "manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, 'setColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, 'addAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, 'addTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, 'setTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			
			// Links
			$this->oLink = new AdminPageFramework_Link_PostType( $this->oProp->sPostType, $this->oProp->sCallerPath, $this->oMsg );
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		}
	
		$this->oUtil->addAndDoAction( $this, "{$this->oProp->sPrefix_Start}{$this->oProp->sClassName}" );
		
	}
	
	/*
	 * Extensible methods
	 */

	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 		$this->setAutoSave( false );
	* 		$this->setAuthorTableFilter( true );
	* 		$this->addTaxonomy( 
	* 			'sample_taxonomy', // taxonomy slug
	* 			array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* 				'labels' => array(
	* 					'name' => 'Genre',
	* 					'add_new_item' => 'Add New Genre',
	* 					'new_item_name' => "New Genre"
	* 				),
	* 				'show_ui' => true,
	* 				'show_tagcloud' => false,
	* 				'hierarchical' => true,
	* 				'show_admin_column' => true,
	* 				'show_in_nav_menus' => true,
	* 				'show_table_filter' => true,	// framework specific key
	* 				'show_in_sidebar_menus' => false,	// framework specific key
	* 			)
	* 		);
	* 	}</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method in their class definition.
	* @remark			A callback for the <em>wp_loaded</em> hook.
	*/
	public function setUp() {}	
		
	/*
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @remark			The user may use this method.
	 */
	public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 	
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 */
	public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version/strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );
	}		
	
	
	/**
	 * Defines the column header items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_{post type}_post)_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 * @return			void
	 */ 
	public function setColumnHeader( $aColumnHeaders ) {
		return $this->oProp->aColumnHeaders;
	}	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 */ 
	public function setSortableColumns( $aColumns ) {
		return $this->oProp->aColumnSortable;
	}
	
	/*
	 * Front-end methods
	 */
	/**
	* Enables or disables the auto-save feature in the custom post type's post submission page.
	* 
	* <h4>Example</h4>
	* <code>$this->setAutoSave( false );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$bEnableAutoSave			If true, it enables the auto-save; othwerwise, it disables it.
	* return			void
	*/ 
	protected function setAutoSave( $bEnableAutoSave=True ) {
		$this->oProp->bEnableAutoSave = $bEnableAutoSave;		
	}
	
	/**
	* Adds a custom taxonomy to the class post type.
	* <h4>Example</h4>
	* <code>$this->addTaxonomy( 
	*		'sample_taxonomy', // taxonomy slug
	*		array(			// argument
	*			'labels' => array(
	*				'name' => 'Genre',
	*				'add_new_item' => 'Add New Genre',
	*				'new_item_name' => "New Genre"
	*			),
	*			'show_ui' => true,
	*			'show_tagcloud' => false,
	*			'hierarchical' => true,
	*			'show_admin_column' => true,
	*			'show_in_nav_menus' => true,
	*			'show_table_filter' => true,	// framework specific key
	*			'show_in_sidebar_menus' => false,	// framework specific key
	*		)
	*	);</code>
	* 
	* @see				http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* @since			2.0.0
	* @param			string			$sTaxonomySlug			The taxonomy slug.
	* @param			array			$aArgs					The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
	* @return			void
	*/ 
	protected function addTaxonomy( $sTaxonomySlug, $aArgs ) {
		
		$sTaxonomySlug = $this->oUtil->sanitizeSlug( $sTaxonomySlug );
		$this->oProp->aTaxonomies[ $sTaxonomySlug ] = $aArgs;	
		if ( isset( $aArgs['show_table_filter'] ) && $aArgs['show_table_filter'] )
			$this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug;
		if ( isset( $aArgs['show_in_sidebar_menus'] ) && ! $aArgs['show_in_sidebar_menus'] )
			$this->oProp->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = "edit.php?post_type={$this->oProp->sPostType}";
				
		if ( count( $this->oProp->aTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, 'registerTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->oProp->aTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, 'removeTexonomySubmenuPages' ), 999 );		
			
	}	

	/**
	* Sets whether the author dropdown filter is enabled/disabled in the post type post list table.
	* 
	* <h4>Example</h4>
	* <code>this->setAuthorTableFilter( true );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$bEnableAuthorTableFileter			If true, it enables the author filter; otherwise, it disables it.
	* @return			void
	*/ 
	protected function setAuthorTableFilter( $bEnableAuthorTableFileter=false ) {
		$this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
	}
	
	/**
	 * Sets the post type arguments.
	 * 
	 * This is only necessary if it is not set to the constructor.
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	 * @param			array			$aArgs			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the <em>register_post_type()</em> function.
	 * @return			void
	 */ 
	protected function setPostTypeArgs( $aArgs ) {
		$this->oProp->aPostTypeArgs = $aArgs;
	}
	
	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $sHTML, $bAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.
			$this->oLink->aFooterInfo['sLeft'] = $bAppend 
				? $this->oLink->aFooterInfo['sLeft'] . $sHTML
				: $sHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$sHTML			The HTML code to insert.
	 * @param			boolean			$bAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */		
	protected function setFooterInfoRight( $sHTML, $bAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.	
			$this->oLink->aFooterInfo['sRight'] = $bAppend 
				? $this->oLink->aFooterInfo['sRight'] . $sHTML
				: $sHTML;
	}

	/**
	 * Sets the given screen icon to the post type screen icon.
	 * 
	 * @since			2.1.3
	 * @since			2.1.6				The $sSRC parameter can accept file path.
	 */
	private function getStylesForPostTypeScreenIcon( $sSRC ) {
		
		$sNone = 'none';
		
		$sSRC = $this->oUtil->resolveSRC( $sSRC );
		
		return "#post-body-content {
				margin-bottom: 10px;
			}
			#edit-slug-box {
				display: {$sNone};
			}
			#icon-edit.icon32.icon32-posts-" . $this->oProp->sPostType . " {
				background: url('" . $sSRC . "') no-repeat;
				background-size: 32px 32px;
			}			
		";		
		
	}
	
	/*
	 * Callback functions
	 */
	public function addStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProp->sPostType )
			return;

		// If the screen icon url is specified
		if ( isset( $this->oProp->aPostTypeArgs['screen_icon'] ) && $this->oProp->aPostTypeArgs['screen_icon'] )
			$this->oProp->sStyle = $this->getStylesForPostTypeScreenIcon( $this->oProp->aPostTypeArgs['screen_icon'] );
			
		$this->oProp->sStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
		
		// Print out the filtered styles.
		if ( ! empty( $this->oProp->sStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
				. $this->oProp->sStyle
				. "</style>";			
		
	}
	
	public function registerPostType() {

		register_post_type( $this->oProp->sPostType, $this->oProp->aPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->oProp->sPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProp->sPostType}", true );
		}

	}	

	public function registerTaxonomies() {
		
		foreach( $this->oProp->aTaxonomies as $sTaxonomySlug => $aArgs ) 
			register_taxonomy(
				$sTaxonomySlug,
				$this->oProp->sPostType,
				$aArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	public function removeTexonomySubmenuPages() {
		
		foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug )
			remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );
		
	}
	
	public function disableAutoSave() {
		
		if ( $this->oProp->bEnableAutoSave ) return;
		if ( $this->oProp->sPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Adds a dorpdown list to filter posts by author, placed above the post type listing table.
	 */ 
	public function addAuthorTableFilter() {
		
		if ( ! $this->oProp->bEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->oProp->sPostType ) ) ) )
			return;
		
		wp_dropdown_users( array(
			'show_option_all'	=> 'Show all Authors',
			'show_option_none'	=> false,
			'name'			=> 'author',
			'selected'		=> ! empty( $_GET['author'] ) ? $_GET['author'] : 0,
			'include_selected'	=> false
		));
			
	}
	
	/**
	 * Adds drop-down lists to filter posts by added taxonomies, placed above the post type listing table.
	 */ 
	public function addTaxonomyTableFilter() {
		
		if ( $GLOBALS['typenow'] != $this->oProp->sPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->oProp->sPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySulg ) {
			
			if ( ! in_array( $sTaxonomySulg, $this->oProp->aTaxonomyTableFilters ) ) continue;
			
			$oTaxonomy = get_taxonomy( $sTaxonomySulg );
 
			// If there is no added term, skip.
			if ( wp_count_terms( $oTaxonomy->name ) == 0 ) continue; 			

			// This function will echo the drop down list based on the passed array argument.
			wp_dropdown_categories( array(
				'show_option_all' => $this->oMsg->__( 'show_all' ) . ' ' . $oTaxonomy->label,
				'taxonomy' 	  => $sTaxonomySulg,
				'name' 		  => $oTaxonomy->name,
				'orderby' 	  => 'name',
				'selected' 	  => intval( isset( $_GET[ $sTaxonomySulg ] ) ),
				'hierarchical' 	  => $oTaxonomy->hierarchical,
				'show_count' 	  => true,
				'hide_empty' 	  => false,
				'hide_if_empty'	=> false,
				'echo'	=> true,	// this make the function print the output
			) );
			
		}
	}
	public function setTableFilterQuery( $oQuery=null ) {
		
		if ( 'edit.php' != $GLOBALS['pagenow'] ) return $oQuery;
		
		if ( ! isset( $GLOBALS['typenow'] ) ) return $oQuery;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $sTaxonomySlug ) {
			
			if ( ! in_array( $sTaxonomySlug, $this->oProp->aTaxonomyTableFilters ) ) continue;
			
			$sVar = &$oQuery->query_vars[ $sTaxonomySlug ];
			if ( ! isset( $sVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $sVar, $sTaxonomySlug );
			if ( is_object( $oTerm ) )
				$sVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	public function setColumnCell( $sColumnTitle, $iPostID ) { 
	
		// foreach ( $this->oProp->aColumnHeaders as $sColumnHeader => $sColumnHeaderTranslated ) 
			// if ( $sColumnHeader == $sColumnTitle ) 
			
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "{$this->oProp->sPrefix_Cell}{$this->oProp->sPostType}_{$sColumnTitle}", $sCell='', $iPostID );
				  
	}
	
	/*
	 * Magic method - this prevents PHP's not-a-valid-callback errors.
	*/
	public function __call( $sMethodName, $aArgs=null ) {	
		if ( substr( $sMethodName, 0, strlen( $this->oProp->sPrefix_Cell ) ) == $this->oProp->sPrefix_Cell ) return $aArgs[0];
		if ( substr( $sMethodName, 0, strlen( "style_" ) )== "style_" ) return $aArgs[0];
	}
	
}
endif;


if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * Provides methods for creating meta boxes.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>extended class name + _ + field_ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>style_ + extended class name</code> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>  
 * 
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AdminPageFramework_MetaBox {
	
	// Objects
	/**
	* @internal
	* @since			2.0.0
	*/ 	
	protected $oDebug;
	/**
	* @internal
	* @since			2.0.0
	*/ 		
	protected $oUtil;
	/**
	* @since			2.0.0
	* @internal
	*/ 		
	protected $oMsg;
	/**
	 * @since			2.1.5
	 * @internal
	 */
	protected $oHeadTag;
	
	/**
	 * Constructs the class object instance of AdminPageFramework_MetaBox.
	 * 
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 * @since			2.0.0
	 * @param			string			$sMetaBoxID			The meta box ID.
	 * @param			string			$sTitle				The meta box title.
	 * @param			string|array	$vPostTypes				( optional ) The post type(s) that the meta box is associated with.
	 * @param			string			$sContext				( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$sPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$sCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$sTextDomain			( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $sMetaBoxID, $sTitle, $vPostTypes=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oProp = new AdminPageFramework_Property_MetaBox( $this );
		$this->oHeadTag = new AdminPageFramework_HeadTag_MetaBox( $this->oProp );
		$this->oHelpPane = new AdminPageFramework_HelpPane_MetaBox( $this->oProp );
			
		// Properties
		$this->oProp->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID );
		$this->oProp->sTitle = $sTitle;
		$this->oProp->aPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->oProp->sContext = $sContext;	//  'normal', 'advanced', or 'side' 
		$this->oProp->sPriority = $sPriority;	// 	'high', 'core', 'default' or 'low'
		$this->oProp->sClassName = get_class( $this );
		$this->oProp->sClassHash = md5( $this->oProp->sClassName );
		$this->oProp->sCapability = $sCapability;
				
		if ( is_admin() ) {
			
			add_action( 'wp_loaded', array( $this, 'replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
							
			if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
	
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->oProp->sPrefixStart}{$this->oProp->sClassName}" );
		
	}

	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>	public function setUp() {		
	* 	$this->addSettingFields(
	* 		array(
	* 			'field_id'		=> 'sample_metabox_text_field',
	* 			'title'			=> 'Text Input',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'text',
	* 		),
	* 		array(
	* 			'field_id'		=> 'sample_metabox_textarea_field',
	* 			'title'			=> 'Textarea',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'textarea',
	* 			'default'			=> 'This is a default text.',
	* 		)
	* 	);		
	* }</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method.
	* @return			void
	*/	 
	public function setUp() {}
	
	/*
	 * Help Pane
	 */
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addHelpText( 
	 *		__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
	 *		__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
	 * @remark			The user may use this method to add contextual help text.
	 */ 
	public function addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
		$this->oHelpPane->_addHelpText( $sHTMLContent, $sHTMLSidebarContent );
	}
	
	/*
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @remark			The user may use this method.
	 */
	public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 	
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 */
	public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $aSRCs, $aPostTypes, $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version/strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		array( 'my_post_type_slug' ),
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
	}	
		
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 */
	public function replyToLoadDefaultFieldTypeDefinitions() {
		
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AdminPageFramework_BuiltinInputFieldTypeDefinitions( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );		
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$this->oProp->aFieldTypeDefinitions
		);				
		
	}
		
	/**
	* Adds the given field array items into the field array property. 
	* 
	* <h4>Example</h4>
	* <code>    $this->addSettingFields(
    *     array(
    *         'field_id'        => 'sample_metabox_text_field',
    *         'title'          => 'Text Input',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'text',
    *     ),
    *     array(
    *         'field_id'        => 'sample_metabox_textarea_field',
    *         'title'          => 'Textarea',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'textarea',
    *         'default'          => 'This is a default text.',
    *     )
    * );</code>
	* 
	* @since			2.0.0
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array			$aField1			The field array.
	* @param			array			$aField2			Another field array.
	* @param			array			$_and_more			Add more fields arrays as many as necessary to the next parameters.
	* @return			void
	*/ 
	protected function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {

		foreach( func_get_args() as $aField ) 
			$this->addSettingField( $aField );
		
	}	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/		
	protected function addSettingField( $aField ) {

		if ( ! is_array( $aField ) ) return;
		
		$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $aField['fIf'] ) return;
							
		// Load head tag elements for fields.
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProp->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProp->aPostTypes ) )		// edit post page
			)
		) {
			// Set relevant scripts and styles for the input field.
			$this->setFieldHeadTagElements( $aField );

		}
		
		// For the contextual help pane,
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProp->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProp->aPostTypes ) )		// edit post page
			)
			&& $aField['help']
		) 			
			$this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );
				
		$this->oProp->aFields[ $aField['field_id'] ] = $aField;
	
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above _replyToRegisterSettings() method.
		 * 
		 * @since			2.1.5
		 */
		private function setFieldHeadTagElements( $aField ) {
			
			$sFieldType = $aField['type'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) && $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) return;
			$GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProp->aFieldTypeDefinitions[ $sFieldType ] ) ) return;

			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) )
				call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() );		
			
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) )
				$this->oProp->sScript .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() );
				
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) )
				$this->oProp->sStyle .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() );
				
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) )
				$this->oProp->sStyleIE .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() );					

			$this->oHeadTag->_enqueueStyles( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] );
			$this->oHeadTag->_enqueueScripts( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] );
					
		}		

	/**
	 * 
	 * since			2.1.3
	 */
	public function removeMediaLibraryTab( $aTabs ) {
		
		if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
		
		if ( ! $_REQUEST['enable_external_source'] )
			unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
		
		return $aTabs;
		
	}

	/**
 	 * Replaces the label text of a button used in the media uploader.
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.
	 */ 
	public function replaceThickBoxText( $sTranslated, $sText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $sTranslated;
		if ( $sText != 'Insert into Post' ) return $sTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $sTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProp->sThickBoxButtonUseThis ?  $this->oProp->sThickBoxButtonUseThis : $this->oMsg->__( 'use_this_image' );
		
	}
	
	/**
	 * Adds the defined meta box.
	 * 
	 * @since			2.0.0
	 * @remark			uses <em>add_meta_box()</em>.
	 * @remark			A callback for the <em>add_meta_boxes</em> hook.
	 * @return			void
	 */ 
	public function addMetaBox() {
		
		foreach( $this->oProp->aPostTypes as $sPostType ) 
			add_meta_box( 
				$this->oProp->sMetaBoxID, 		// id
				$this->oProp->sTitle, 	// title
				array( $this, 'echoMetaBoxContents' ), 	// callback
				$sPostType,		// post type
				$this->oProp->sContext, 	// context
				$this->oProp->sPriority,	// priority
				$this->oProp->aFields	// argument
			);
			
	}	
	
	/**
	 * Echoes the meta box contents.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>add_meta_box()</em> method.
	 * @param			object			$oPost			The object of the post associated with the meta box.
	 * @param			array			$vArgs			The array of arguments.
	 * @return			void
	 */ 
	public function echoMetaBoxContents( $oPost, $vArgs ) {	
		
		// Use nonce for verification
		$sOut = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
		
		// Begin the field table and loop
		$sOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;
			
			// get value of this field if it exists for this post
			$sStoredValue = get_post_meta( $oPost->ID, $aField['field_id'], true );
			$aField['vValue'] = $sStoredValue ? $sStoredValue : $aField['vValue'];
			
			// Check capability. If the access level is not sufficient, skip.
			$aField['sCapability'] = isset( $aField['sCapability'] ) ? $aField['sCapability'] : $this->oProp->sCapability;
			if ( ! current_user_can( $aField['sCapability'] ) ) continue; 			
			
			// Begin a table row. 
			
			// If it's a hidden input type, do now draw a table row
			if ( $aField['type'] == 'hidden' ) {
				$sOut .= "<tr><td style='height: 0; padding: 0; margin: 0; line-height: 0;'>"
					. $this->getFieldOutput( $aField )
					. "</td></tr>";
				continue;
			}
			$sOut .= "<tr>";
			if ( ! $aField['show_inpage_tabTitleColumn'] )
				$sOut .= "<th><label for='{$aField['field_id']}'>"
						. "<a id='{$aField['field_id']}'></a>"
						. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
						. $aField['title'] 
						. "</span>"
						. "</label></th>";		
			$sOut .= "<td>";
			$sOut .= $this->getFieldOutput( $aField );
			$sOut .= "</td>";
			$sOut .= "</tr>";
			
		} // end foreach
		$sOut .= '</table>'; // end table
		echo $sOut;
		
	}
	private function setOptionArray( $iPostID, $aFields ) {
		
		if ( ! is_array( $aFields ) ) return;
		
		foreach( $aFields as $iIndex => $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;

			$this->oProp->aOptions[ $iIndex ] = get_post_meta( $iPostID, $aField['field_id'], true );
			
		}
	}	
	private function getFieldOutput( $aField ) {

		// Set the input field name which becomes the option key of the custom meta field of the post.
		$aField['sName'] = isset( $aField['sName'] ) ? $aField['sName'] : $aField['field_id'];

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).
		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions[ $sFieldType ], $this->oMsg );	// currently the error array is not supported for meta-boxes
		$oField->isMetaBox( true );
		$sFieldOutput = $oField->getInputField( $sFieldType );	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProp->sClassName . '_' . 'field_' . $aField['field_id'],	// this filter will be deprecated
				'field_' . $this->oProp->sClassName . '_' . $aField['field_id']	// field_ + {extended class name} + _ {field id}
			),
			$sFieldOutput,
			$aField // the field array
		);		
						
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 */
	public function saveMetaBoxFields( $iPostID ) {
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) return;
			
		// Check permissions
		if ( in_array( $_POST['post_type'], $this->oProp->aPostTypes )   
			&& ( ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) || ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$aInput = array();
		foreach( $this->oProp->aFields as $aField ) 
			$aInput[ $aField['field_id'] ] = isset( $_POST[ $aField['field_id'] ] ) ? $_POST[ $aField['field_id'] ] : null;
			
		// Prepare the old value array.
		$aOriginal = array();
		foreach ( $aInput as $sFieldID => $v )
			$aOriginal[ $sFieldID ] = get_post_meta( $iPostID, $sFieldID, true );
					
		// Apply filters to the array of the submitted values.
		$aInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $aInput, $aOriginal );

		// Loop through fields and save the data.
		foreach ( $aInput as $sFieldID => $vValue ) {
			
			// $sOldValue = get_post_meta( $iPostID, $sFieldID, true );			
			$sOldValue = isset( $aOriginal[ $sFieldID ] ) ? $aOriginal[ $sFieldID ] : null;
			if ( ! is_null( $vValue ) && $vValue != $sOldValue ) {
				update_post_meta( $iPostID, $sFieldID, $vValue );
				continue;
			} 
			// if ( '' == $sNewValue && $sOldValue ) 
				// delete_post_meta( $iPostID, $aField['field_id'], $sOldValue );
			
		} // end foreach
		
	}	
	
	/*
	 * Magic method
	*/
	function __call( $sMethodName, $aArgs=null ) {	
		
		// the start_ action hook.
		if ( $sMethodName == $this->oProp->sPrefixStart . $this->oProp->sClassName ) return;

		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProp->sClassName . '_' ) ) == 'field_' . $this->oProp->sClassName . '_' )
			return $aArgs[ 0 ];
		
		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( $this->oProp->sClassName . '_' . 'field_' ) ) == $this->oProp->sClassName . '_' . 'field_' )
			return $aArgs[ 0 ];

		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		
			
		// the script_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "script_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "style_{$this->oProp->sClassName}" ) ) == "style_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}" ) ) == "validation_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];				
			
	}
}
endif;