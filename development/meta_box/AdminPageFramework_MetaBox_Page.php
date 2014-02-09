<?php
if ( ! class_exists( 'AdminPageFramework_MetaBox_Page' ) ) :
/**
 * Provides methods for creating meta boxes in pages added by the framework.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><strong>start_{extended class name}</strong> – triggered at the end of the class constructor.</li>
 * 	<li><strong>do_{extended class name}</strong> – triggered when the meta box gets rendered.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><strong>field_types_{extended class name}</strong> – receives the field type definition array. The first parameter: the field type definition array.</li>
 * 	<li><strong>field_{extended class name}_{field ID}</strong> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><strong>content_{extended class name}</strong> – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 * 	<li><strong>style_common_{extended class name}</strong> –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_ie_common_{extended class name}</strong> –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_{extended class name}</strong> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_ie_{extended class name}</strong> –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>script_common_{extended class name}</strong> – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>script_{extended class name}</strong> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>validation_{extended class name}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * 
 * @abstract
 * @since			3.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @package			AdminPageFramework
 * @subpackage		MetaBox
 */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_MetaBox_Base {

	/**
	 * Defines the fields type.
	 * @since			3.0.0
	 * @internal
	 */
	static protected $_sFieldsType = 'page_meta_box';
	
	/**
	 * Registers necessary hooks and internal properties.
	 * 
	 * <h4>Examples</h4>
	 * <code>
	 * 	new APF_MetaBox_For_Pages_Normal(
	 * 		'apf_metabox_for_pages_normal',		// meta box id
	 * 		__( 'Sample Meta Box For Admin Pages Inserted in Normal Area' ),	// title
	 * 		'apf_first_page',	// page slugs
	 * 		'normal',	// context
	 * 		'default'	// priority
	 * 	);
	 * 	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
	 * 	new APF_MetaBox_For_Pages_Advanced(
	 * 		'apf_metabox_for_pages_advanced',	// meta box id
	 * 		__( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ),	// title
	 * 		'apf_first_page',	// page slugs
	 * 		'advanced',		// context
	 * 		'default'	// priority
	 * 	);	
	 * 	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );
	 * 	new APF_MetaBox_For_Pages_Side(
	 * 		'apf_metabox_for_pages_side',	// meta box id
	 * 		__( 'Sample Meta Box For Admin Pages Inserted in Advanced Area' ),	// title
	 * 		array( 'apf_first_page', 'apf_second_page' ),	// page slugs - setting multiple slugs is possible
	 * 		'side',		// context
	 * 		'default'	// priority
	 * 	);		
	 * </code>
	 * @since			3.0.0
	 */
	function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {		
		
		if ( empty( $asPageSlugs ) ) return;
		
		/* 		
		$asPageSlugs = array(			
			'settings' => array( 	// if the key is not numeric and the value is an array, it will be considered as a tab array.
				'help', 		// enabled in the tab whose slug is 'help' which belongs to the page whose slug is 'settings'
				'about', 		// enabled in the tab whose slug is 'about' which belongs to the page whose slug is 'settings'
				'general',		// enabled in the tab whose slug is 'general' which belongs to the page whose slug is 'settings'
			),
			'manage',	// if the numeric key with a string value is given, the condition applies to the page slug of this string value.
		); 
		*/
		
		/* The property object needs to be done first */
		$this->oProp = new AdminPageFramework_Property_MetaBox_Page( $this, get_class( $this ), $sCapability );		
		
		parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
		
		/* These class uses methods that determines the current tab and page slugs based from the added pages. */
		$this->oHeadTag = new AdminPageFramework_HeadTag_MetaBox_Page( $this->oProp );
		$this->oHelpPane = new AdminPageFramework_HelpPane_MetaBox( $this->oProp );		
		
		$this->oProp->aPageSlugs = is_string( $asPageSlugs ) ? array( $asPageSlugs ) : $asPageSlugs;
		$this->oProp->sFieldsType = self::$_sFieldsType;
		$this->oForm = new AdminPageFramework_FormElement( $this->oProp->sFieldsType, $sCapability );
		
		/* Validation hook */
		foreach( $this->oProp->aPageSlugs as $sPageSlug )
			add_filter( "validation_{$sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
		
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
	
	}

	/*
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 */
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueScripts( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * @since			3.0.0			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	
	/**
	 * Returns the field output.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	protected function getFieldOutput( $aField ) {
		
		/* Since meta box fields don't have the option_key key which is required to compose the name attribute in the regular pages. */
		$sOptionKey = $this->_getOptionKey();
		$aField['option_key'] = $sOptionKey ? $sOptionKey : null;
		$aField['page_slug'] = isset( $_GET['page'] ) ? $_GET['page'] : '';	// set an empty string to make it yield true for isset() so that saved options will be checked.

		return parent::getFieldOutput( $aField );
		
	}
	
	/**
	 * Returns the currently loading page's option key if the page has the admin page object.
	 * @since			3.0.0
	 * @internal
	 */
	private function _getOptionkey() {
		return isset( $_GET['page'] ) 
			? $this->oProp->getOptionKey( $_GET['page'] )
			: null;
	}
	
		
	/**
	 * Adds the defined meta box.
	 * 
	 * @internal
	 * @since			3.0.0
	 * @remark			uses <em>add_meta_box()</em>.
	 * @remark			Before this method is called, the pages and in-page tabs need to be registered already.
	 * @remark			A callback for the <em>add_meta_boxes</em> hook.
	 * @return			void
	 */ 
	public function _replyToAddMetaBox() {
		
		foreach( $this->oProp->aPageSlugs as $sKey => $asPage ) {
			
			if ( is_string( $asPage ) )  {
				$this->_addMetaBox( $asPage );
				continue;
			}
			if ( ! is_array( $asPage ) ) continue;
			
			$sPageSlug = $sKey;
			foreach( $asPage as $sTabSlug ) {
				
				if ( ! $this->oProp->isCurrentTab( $sTabSlug ) ) continue;
				
				$this->_addMetaBox( $sPageSlug );
				
			}
			
		}
				
	}	
		/**
		 * Adds meta box with the given page slug.
		 * @since			3.0.0
		 * @internal
		 */
		private function _addMetaBox( $sPageSlug ) {
			
			add_meta_box( 
				$this->oProp->sMetaBoxID, 		// id
				$this->oProp->sTitle, 	// title
				array( $this, '_replyToPrintMetaBoxContents' ), 	// callback
				$this->oProp->_getScreenIDOfPage( $sPageSlug ),		// screen ID
				$this->oProp->sContext, 	// context
				$this->oProp->sPriority,	// priority
				null	// argument	// deprecated
			);		
			
		}
		
	/**
	 * Validates the submitted option values.
	 * 
	 * @internal
	 * @sicne			3.0.0
	 * @param			array			The array holing the field values of the page sent from the framework page class (the main class).
	 * @param			array			The array holing the saved options of the page.
	 */
	public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions ) {
		
		// The field values of this class will not be included in the parameter array. So get them.
		$_aFieldsModel = $this->oForm->getFieldsModel();
		$_aNewInput = $this->oUtil->castArrayContents( $_aFieldsModel, $_POST );
		$_aOldInput = $this->oUtil->castArrayContents( $_aFieldsModel, $aOldPageOptions );

		// Apply filters - third party scripts will have access to the input.
		$_aNewInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aNewInput, $_aOldInput );
		
		// Now merge the input values with the passed page options.
		return $this->oUtil->uniteArrays( $_aNewInput, $aNewPageOptions );
				
	}
	
	/**
	 * Registers form fields and sections.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToRegisterFormElements() {
				
		// Schedule to add head tag elements and help pane contents.
		if ( ! isset( $_GET['page'] ) ) return;
		if ( ! $this->_isMetaBoxPage( $_GET['page'] ) ) return;
		
		// Format the fields array.
		$this->oForm->format();
		$_aFields = $this->oForm->applyConditions();
		$this->_registerFields( $_aFields );

	}		
		/**
		 * Checks if the currently loading page is in the pages specified for this meta box.
		 * @since			3.0.0
		 */
		private function _isMetaBoxPage( $sPageSlug ) {
			
			if ( ! $sPageSlug ) return false;
			
			if ( in_array( $sPageSlug, $this->oProp->aPageSlugs ) )
				return true;
			
			return false;
		}	
}
endif;