<?php
if ( ! class_exists( 'AdminPageFramework_Property_Page' ) ) :
/**
 * Provides the space to store the shared properties.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Property_Base
 */
class AdminPageFramework_Property_Page extends AdminPageFramework_Property_Base {
			
	/**
	 * Stores framework's instantiated object name.
	 * 
	 * @since			2.0.0
	 */ 
	public $sClassName;	
	
	/**
	 * Stores the md5 hash string of framework's instantiated object name.
	 * @since			2.1.1
	 */
	public $sClassHash;
	
	/**
	 * Stores the access level to the root page. 
	 * 
	 * When sub pages are added and the capability value is not provided, this will be applied.
	 * 
	 * @since			2.0.0
	 */ 	
	public $sCapability = 'manage_options';	
	
	/**
	 * Stores the tag for the page heading navigation bar.
	 * @since			2.0.0
	 */ 
	public $sPageHeadingTabTag = 'h2';

	/**
	 * Stores the tag for the in-page tab navigation bar.
	 * @since			2.0.0
	 */ 
	public $sInPageTabTag = 'h3';
	
	/**
	 * Stores the default page slug.
	 * @since			2.0.0
	 */ 	
	public $sDefaultPageSlug;
		
	// Container arrays.
	/**
	 * A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
	 * @since			2.0.0
	 */ 	
	public $aPages = array(); 

	/**
	 * Stores the hidden page slugs.
	 * @since			2.1.4
	 */
	public $aHiddenPages = array();
	
	/**
	 * Stores the registered sub menu pages.
	 * 
	 * Unlike the above $aPages that holds the pages to be added, this stores the added pages. This is referred when adding a help section.
	 * 
	 * @since			2.1.0
	 */ 
	public $aRegisteredSubMenuPages = array();
	
	/**
	 * Stores the root menu item information for one set root menu item.
	 * @since			2.0.0
	 */ 		
	public $aRootMenu = array(
		'sTitle' => null,				// menu label that appears on the menu list
		'sPageSlug' => null,			// menu slug that identifies the menu item
		'sIcon16x16' => null,		// the associated icon that appears beside the label on the list
		'iPosition'	=> null,		// determines the position of the menu
		'fCreateRoot' => null,			// indicates whether the framework should create the root menu or not.
	); 
	
	/**
	 * Stores in-page tabs.
	 * @since			2.0.0
	 */ 	
	public $aInPageTabs = array();				
	
	/**
	 * Stores the default in-page tab.
	 * @since			2.0.0
	 */ 		
	public $aDefaultInPageTabs = array();			
		
	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
	 * @since			2.0.0
	 */ 			
	public $aPluginDescriptionLinks = array(); 

	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
	 * @since			2.0.0
	 */ 			
	public $aPluginTitleLinks = array();			
	
	/**
	 * Stores the information to insert into the page footer.
	 * @since			2.0.0
	 */ 			
	public $aFooterInfo = array(
		'sLeft' => '',
		'sRight' => '',
	);
		
	// Settings API
	// public $aOptions;			// Stores the framework's options. Do not even declare the property here because the __get() magic method needs to be triggered when it accessed for the first time.

	/**
	 * The instantiated class name will be assigned in the constructor if the first parameter is not set.
	 * @since			2.0.0
	 */ 				
	public $sOptionKey = '';		

	/**
	 * Stores form sections.
	 * @since			2.0.0
	 */ 					
	public $aSections = array();
	
	/**
	 * Stores form fields
	 * @since			2.0.0
	 */ 					
	public $aFields = array();

	/**
	 * Stores contextual help tabs.
	 * @since			2.1.0
	 */ 	
	public $aHelpTabs = array();
	
	/**
	 * Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
	 * @since			2.0.0
	 */ 					
	public $sFormEncType = 'multipart/form-data';	
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @internal
	 */ 	
	public $sThickBoxButtonUseThis = '';
	
	// Flags	
	/**
	 * Decides whether the setting form tag is rendered or not.	
	 * 
	 * This will be enabled when a settings section and a field is added.
	 * @since			2.0.0
	 */ 						
	public $bEnableForm = false;			
	
	/**
	 * Indicates whether the page title should be displayed.
	 * @since			2.0.0
	 */ 						
	public $bShowPageTitle = true;	
	
	/**
	 * Indicates whether the page heading tabs should be displayed.
	 * @since			2.0.0
	 * @remark			Used by the setPageHeadingTabsVisibility() method.
	 */ 	
	public $bShowPageHeadingTabs = true;

	/**
	 * Indicates whether the in-page tabs should be displayed.
	 * 
	 * This sets globally among the script using the framework. 
	 * 
	 * @since			2.1.2
	 * @remark			Used by the setInPageTabsVisibility() method.
	 */
	public $bShowInPageTabs = true;

	/**
	 * Stores the set administration notices.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the style handle ID which is automatically generated.
	 * @since			2.1.2
	 */
	public $aAdminNotices	= array();
	
	/**
	 * Stores the disallowed query keys in the links generated by the main class of the framework.
	 * 
	 * @remark			Currently this does not take effect on the meta box and post type classes of the framework.
	 * @since			2.1.2
	 */
	public $aDisallowedQueryKeys	= array( 'settings-updated' );
	
	/**
	 * Construct the instance of AdminPageFramework_Property_Page class object.
	 * 
	 * @remark			Used by the setInPageTabsVisibility() method.
	 * @since			2.0.0
	 * @since			2.1.5			The $oCaller parameter was added.
	 * @return			void
	 */ 
	public function __construct( $oCaller, $sCallerPath, $sClassName, $sOptionKey, $sCapability='manage_options' ) {
		
		parent::__construct( $oCaller, $sCallerPath, $sClassName );
		
		$this->sOptionKey = $sOptionKey ? $sOptionKey : $sClassName;
		$this->sCapability = empty( $sCapability ) ? $this->sCapability : $sCapability;
				
		// The capability for the settings. $this->sOptionKey is the part that is set in the settings_fields() function.
		// This prevents the "Cheatin' huh?" message.
		add_filter( "option_page_capability_{$this->sOptionKey}", array( $this, '_replyToGetCapability' ) );
		
	}
	
	/*
	 * Magic methods
	 * */
	public function &__get( $sName ) {
		
		// If $this->aOptions is called for the first time, retrieve the option data from the database and assign to the property.
		// Once this is done, calling $this->aOptions will not trigger the __get() magic method any more.
		// Without the the ampersand in the method name, it causes a PHP warning.
		if ( $sName == 'aOptions' ) {
			$this->aOptions = $this->getOptions();
			return $this->aOptions;	
		}
		
		// For regular undefined items, 
		return null;
		
	}
	
	/*
	 * Utility methods
	 * */
	
	/**
	 * Checks if the given page slug is one of the pages added by the framework.
	 * @since			2.0.0
	 * @since			2.1.0			Set the default value to the parameter and if the parameter value is empty, it applies the current $_GET['page'] value.
	 * @return			boolean			Returns true if it is of framework's added page; otherwise, false.
	 */
	public function isPageAdded( $sPageSlug='' ) {	
		
		$sPageSlug = ! empty( $sPageSlug ) ? $sPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
		return ( array_key_exists( trim( $sPageSlug ), $this->aPages ) )
			? true
			: false;
	}
	
	/**
	 * Retrieves the default in-page tab from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public and moved from the AdminPageFramework_Page class since this method is used by the AdminPageFramework_HeadTab class as well.
	 * @internal
	 * @remark			Used in the __call() method in the main class.
	 * @return			string			The default in-page tab slug if found; otherwise, an empty string.
	 */ 		
	public function getDefaultInPageTab( $sPageSlug ) {
	
		if ( ! $sPageSlug ) return '';		
		return isset( $this->aDefaultInPageTabs[ $sPageSlug ] ) 
			? $this->aDefaultInPageTabs[ $sPageSlug ]
			: '';

	}	
	
	public function getOptions() {
		
		$vOptions = get_option( $this->sOptionKey );
		if ( empty( $vOptions ) ) return array();		// casting array causes a 0 key element when the value is empty. So this way it can be avoided
		if ( is_array( $vOptions ) ) return $vOptions;	// if it's array, no problem.		
		return ( array ) $vOptions;	// finally cast array.
		
	}
	
	/*
	 * callback methods
	 */ 
	public function _replyToGetCapability() {
		return $this->sCapability;
	}	
		
}
endif;