<?php
if ( ! class_exists( 'AdminPageFramework_Base' ) ) :
/**
 * Defines common properties and methods shared with the AdminPageFramework classes for pages.
 *
 * @abstract
 * @since			3.0.0		
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AdminPageFramework_Base {
	
	/**
    * The common properties shared among sub-classes. 
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the name from $oProps and moved from the main class.
	* @access			protected
	* @var				object			an instance of AdminPageFramework_Property_Page will be assigned in the constructor.
    */		
	protected $oProp;	
	
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
	* @var				object			an instance of AdminPageFramework_Link will be assigned in the constructor.
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
		$this->oProp = new AdminPageFramework_Property_Page( $this, get_class( $this ), $sOptionKey, $sCapability );
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oPageLoadInfo = AdminPageFramework_PageLoadInfo_Page::instantiate( $this->oProp, $this->oMsg );
		$this->oHelpPane = new AdminPageFramework_HelpPane_Page( $this->oProp );
		$this->oLink = new AdminPageFramework_Link( $this->oProp, $sCallerPath, $this->oMsg );
		$this->oHeadTag = new AdminPageFramework_HeadTag_Page( $this->oProp );
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oDebug = new AdminPageFramework_Debug;		
		
	}
	
	/* Methods that should be defined in the user's class. */
	public function setUp() {}
	
	/* Defined in AdminPageFramework */
	public function addHelpTab() {}
	public function enqueueStyles() {}
	public function enqueueStyle() {}
	public function enqueueScripts() {}
	public function enqueueScript() {}
	public function addLinkToPluginDescription() {}
	public function addLinkToPluginTitle() {}
	public function setCapability() {}
	public function setFooterInfoLeft() {}
	public function setFooterInfoRight() {}
	public function setAdminNotice() {}
	public function setDisallowedQueryKeys() {}
	
	/* Defined in AdminPageFramework_Page */
	public function addInPageTabs() {}
	public function addInPageTab() {}
	public function setPageTitleVisibility() {}
	public function setPageHeadingTabsVisibility() {}
	public function setInPageTabsVisibility() {}
	public function setInPageTabTag() {}
	public function setPageHeadingTabTag() {}
	
	/* Defined in AdminPageFramework_Menu */
	public function setRootMenuPage() {}
	public function setRootMenuPageBySlug() {}
	public function addSubMenuItems() {}
	public function addSubMenuItem() {}
	public function addSubMenuLink() {}	
	public function addSubMenuPages() {}
	public function addSubMenuPage() {}
	
	/* Defined in AdminPageFramework_Setting */
	public function setSettingNotice() {}
	public function addSettingSections() {}
	public function addSettingSection() {}
	public function removeSettingSections() {}	
	public function addSettingFields() {}
	public function addSettingField() {}
	public function removeSettingFields() {}
	public function setFieldErrors() {}
	public function getFieldValue() {}
	

}
endif;