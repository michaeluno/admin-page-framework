<?php
if ( ! class_exists( 'AdminPageFramework_Base' ) ) :
/**
 * Defines common properties and methods shared with the AdminPageFramework classes for pages.
 *
 * @abstract
 * @since			3.0.0		
 * @package			AdminPageFramework
 * @subpackage		Page
 * @internal
 */
abstract class AdminPageFramework_Base {
	
	/**
	 * Stores the prefixes of the filters used by this framework.
	 * 
	 * This must not use the private scope as the extended class accesses it, such as 'start_' and must use the public since another class uses this externally.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public from protected since the HeadTag class accesses it.
	 * @since			3.0.0			Moved from AdminPageFramework_Page. Changed the scope to protected as the head tag class no longer access this property.
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 
	protected static $_aHookPrefixes = array(	
		'start_'			=> 'start_',
		'load_'				=> 'load_',
		'do_before_'		=> 'do_before_',
		'do_after_'			=> 'do_after_',
		'do_form_'			=> 'do_form_',
		'do_'				=> 'do_',
		'submit_'			=> 'submit_',			// 3.0.0+
		'content_top_'		=> 'content_top_',
		'content_bottom_'	=> 'content_bottom_',
		'content_'			=> 'content_',
		'validation_'		=> 'validation_',
		'export_name'		=> 'export_name',
		'export_format' 	=> 'export_format',
		'export_'			=> 'export_',
		'import_name'		=> 'import_name',
		'import_format'		=> 'import_format',
		'import_'			=> 'import_',
		'style_common_ie_'	=> 'style_common_ie_',
		'style_common_'		=> 'style_common_',
		'style_ie_'			=> 'style_ie_',
		'style_'			=> 'style_',
		'script_'			=> 'script_',
		
		'field_'			=> 'field_',
		'section_head_'		=> 'section_head_',		// 3.0.0+ Changed from 'section_'
		'fields_'			=> 'fields_',
		'sections_'			=> 'sections_',
		'pages_'			=> 'pages_',
		'tabs_'				=> 'tabs_',
		
		'field_types_'		=> 'field_types_',
	);
	
	/**
    * The common properties shared among sub-classes. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the name from $oProps and moved from the main class. Changed the scope to public as all instantiated class became to be stored in the global aAdminPageFramework variable.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Property_Page will be assigned in the constructor.
    */		
	public $oProp;
	
	/**
    * The object that provides the debug methods. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Moved from the main class.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Debug will be assigned in the constructor.
    */		
	protected $oDebug;
	
	/**
    * Provides the methods for text messages of the framework. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Moved from the main class.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Message will be assigned in the constructor.
    */	
	protected $oMsg;
	
	/**
    * Provides the methods for creating HTML link elements. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Moved from the main class.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Link_Page will be assigned in the constructor.
    */		
	protected $oLink;
	
	/**
    * Provides the utility methods. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Moved from the main class.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Utility will be assigned in the constructor.
    */			
	protected $oUtil;
	
	/**
	 * Provides the methods to insert head tag elements.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Moved from the main class.
	 * @access			protected
	 * @var				object			an instance of AdminPageFramework_HeadTag_Page will be assigned in the constructor.
	 */
	protected $oHeadTag;
	
	/**
	 * Inserts page load information into the footer area of the page. 
	 * 
	 * @since			2.1.7
	 * @since			3.0.0			Moved from the main class.
	 * @access			protected
	 * @var				object			
	 */
	protected $oPageLoadInfo;
	
	/**
	 * Provides methods to manipulate contextual help pane.
	 * 
	 * @since			3.0.0
	 * @access			protected
	 * @var				object			
	 */
	protected $oHelpPane;
	

	function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ) {
				
		// Objects
		$this->oProp = new AdminPageFramework_Property_Page( $this, $sCallerPath, get_class( $this ), $sOptionKey, $sCapability );
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_Page::instantiate( $this->oProp, $this->oMsg );
		$this->oHelpPane = new AdminPageFramework_HelpPane_Page( $this->oProp );
		$this->oLink = new AdminPageFramework_Link_Page( $this->oProp, $this->oMsg );
		$this->oHeadTag = new AdminPageFramework_HeadTag_Page( $this->oProp );
		$this->oUtil = new AdminPageFramework_WPUtility;
		$this->oDebug = new AdminPageFramework_Debug;		

		if ( $this->oProp->bIsAdmin )
			add_action( 'wp_loaded', array( $this, 'setUp' ) );		
		
	}

	/**#@+
	 *@internal
	 */	 
	
	/* Methods that should be defined in the user's class. */
	public function setUp() {}

	/* Defined in AdminPageFramework */
	public function addHelpTab( $aHelpTab ) {}
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {}
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {}
	public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {}
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {}
	public function addLinkToPluginDescription( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {}
	public function addLinkToPluginTitle( $sTaggedLinkHTML1, $sTaggedLinkHTML2=null, $_and_more=null ) {}
	public function setCapability( $sCapability ) {}
	public function setFooterInfoLeft( $sHTML, $bAppend=true ) {}
	public function setFooterInfoRight( $sHTML, $bAppend=true ) {}
	public function setAdminNotice( $sMessage, $sClassSelector='error', $sID='' ) {}
	public function setDisallowedQueryKeys( $asQueryKeys, $bAppend=true ) {}
	
	/* Defined in AdminPageFramework_Page */
	public function addInPageTabs( $aTab1, $aTab2=null, $_and_more=null ) {}
	public function addInPageTab( $asInPageTab ) {}
	public function setPageTitleVisibility( $bShow=true, $sPageSlug='' ) {}
	public function setPageHeadingTabsVisibility( $bShow=true, $sPageSlug='' ) {}
	public function setInPageTabsVisibility( $bShow=true, $sPageSlug='' ) {}
	public function setInPageTabTag( $sTag='h3', $sPageSlug='' ) {}
	public function setPageHeadingTabTag( $sTag='h2', $sPageSlug='' ) {}
	
	/* Defined in AdminPageFramework_Menu */
	public function setRootMenuPage( $sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null ) {}
	public function setRootMenuPageBySlug( $sRootMenuSlug ) {}
	public function addSubMenuItems( $aSubMenuItem1, $aSubMenuItem2=null, $_and_more=null ) {}
	public function addSubMenuItem( array $aSubMenuItem ) {}
	protected function addSubMenuLink( array $aSubMenuLink ) {}	
	protected function addSubMenuPages() {}	// no parameter
	protected function addSubMenuPage( array $aSubMenuPage ) {}
	
	/* Defined in AdminPageFramework_Setting */
	public function setSettingNotice( $sMsg, $sType='error', $sID=null, $bOverride=true ) {}
	public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {}
	public function addSettingSection( $asSection ) {}
	public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {}	
	public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {}
	public function addSettingField( $asField ) {}
	public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {}
	public function setFieldErrors( $aErrors, $sID=null, $nSavingDuration=300 ) {}
	public function getFieldValue( $sFieldID ) {}
	/**#@-*/    
	
	/* Shared methods */
	/**
	 * Calculates the subtraction of two values with the array key of <em>order</em>
	 * 
	 * This is used to sort arrays.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Moved from the property class.
	 * @remark			a callback method for uasort().
	 * @return			integer
	 * @internal
	 */ 
	public function _sortByOrder( $a, $b ) {
		return isset( $a['order'], $b['order'] )
			? $a['order'] - $b['order']
			: 1;
	}	

}
endif;