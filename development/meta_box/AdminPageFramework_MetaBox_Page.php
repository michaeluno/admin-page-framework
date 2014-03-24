<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
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
	 * 
	 * @param			string			$sMetaBoxID			The meta box ID to be created.
	 * @param			string			$sTitle				The meta box title.
	 * @param			array|string	$asPageSlugs		the page slug(s) that the meta box belongs to. If the element is an array, it will be considered as a tab array.
	 *	<code>
		$asPageSlugs = array(			
			'settings' => array( 	// if the key is not numeric and the value is an array, it will be considered as a tab array.
				'help', 		// enabled in the tab whose slug is 'help' which belongs to the page whose slug is 'settings'
				'about', 		// enabled in the tab whose slug is 'about' which belongs to the page whose slug is 'settings'
				'general',		// enabled in the tab whose slug is 'general' which belongs to the page whose slug is 'settings'
			),
			'manage',	// if the numeric key with a string value is given, the condition applies to the page slug of this string value.
		);
	 *	</code>
	 * @param			string			$sContext			The context, either 'normal', 'advanced', or 'side'.
	 * @param			string			$sPriority			The priority, either 'high', 'core', 'default' or 'low'.
	 * @param			string			$sCapability		The capability. See <a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Roles and Capabilities</a>.
	 */
	function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {		
		
		if ( empty( $asPageSlugs ) ) return;
		
		/* The property object needs to be done first */
		$this->oProp = new AdminPageFramework_Property_MetaBox_Page( $this, get_class( $this ), $sCapability );		
		
		parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
		
		$this->oProp->aPageSlugs = is_string( $asPageSlugs ) ? array( $asPageSlugs ) : $asPageSlugs;	// must be set before the isInThePage() method is used.
		$this->oProp->sFieldsType = self::$_sFieldsType;		
		
		if ( $this->_isInThePage() ) :
		
			/* These classes use methods that determine the current tab and page slugs based from the added pages. */
			$this->oHeadTag = new AdminPageFramework_HeadTag_MetaBox_Page( $this->oProp );
			$this->oHelpPane = new AdminPageFramework_HelpPane_MetaBox( $this->oProp );		
			

			$this->oForm = new AdminPageFramework_FormElement( $this->oProp->sFieldsType, $sCapability );

			/* Validation hook */
			foreach( $this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug ) {
				
				if ( is_string( $_asTabArrayOrPageSlug ) ) {				
					$_sPageSlug = $_asTabArrayOrPageSlug;
					add_filter( "validation_saved_options_{$_sPageSlug}", array( $this, '_replyToFilterPageOptions' ) );
					add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
					continue;
				}
				
				// At this point, the array key is the page slug.
				$_sPageSlug = $_sIndexOrPageSlug;
				$_aTabs = $_asTabArrayOrPageSlug;
				add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
				foreach( $_aTabs as $_sTabSlug )
					add_filter( "validation_saved_options_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptions' ) );
				
			}
		
		endif;
		
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
	
	}

		/**
		 * Determines whether the meta box belongs to the loading page.
		 * 
		 * @since			3.0.3
		 * @internal
		 */
		protected function _isInThePage() {
				
			if ( in_array( $GLOBALS['pagenow'], array( 'options.php' ) ) ) {
				return true;
			}
				
			if ( ! isset( $_GET['page'] ) )	{
				return false;
			}
				
			return in_array( $_GET['page'], $this->oProp->aPageSlugs );
			
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
	 * Filters the page option array.
	 * 
	 * This is triggered from the system validation method of the main class with the validation_saved_options_{page slug} filter hook.
	 * 
	 * @since			3.0.0
	 * @param			array
	 */
	public function _replyToFilterPageOptions( $aPageOptions ) {
		
		return $this->oForm->dropRepeatableElements( $aPageOptions );
		
	}
	
	/**
	 * Validates the submitted option values.
	 * 
	 * @internal
	 * @sicne			3.0.0
	 * @param			array			$aNewPageOptions			The array holing the field values of the page sent from the framework page class (the main class).
	 * @param			array			$aOldPageOptions			The array holing the saved options of the page. Note that this will be empty if non of generic page fields are created.
	 */
	public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions ) {
		
		// The field values of this class will not be included in the parameter array. So get them.
		$_aFieldsModel = $this->oForm->getFieldsModel();
		$_aNewMetaBoxInput = $this->oUtil->castArrayContents( $_aFieldsModel, $_POST );
		$_aOldMetaBoxInput = $this->oUtil->castArrayContents( $_aFieldsModel, $aOldPageOptions );

		// Apply filters - third party scripts will have access to the input.
		$_aNewMetaBoxInput = stripslashes_deep( $_aNewMetaBoxInput );	// fixes magic quotes
		$_aNewMetaBoxInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aNewMetaBoxInput, $_aOldMetaBoxInput );
	
		// Now merge the input values with the passed page options.
		return $this->oUtil->uniteArrays( $_aNewMetaBoxInput, $aNewPageOptions );
				
	}
	
	/**
	 * Registers form fields and sections.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToRegisterFormElements() {
				
		// Schedule to add head tag elements and help pane contents.		
		if ( ! $this->_isInThePage() ) return;
		
		// Format the fields array.
		$this->oForm->format();
		$this->oForm->applyConditions();	// will create the conditioned elements.
		// $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
		
		// Add the repeatable section elements to the fields definition array.
		$this->oForm->setDynamicElements( $this->oProp->aOptions );	// will update $this->oForm->aConditionedFields
						
		$this->_registerFields( $this->oForm->aConditionedFields );

	}		
				
}
endif;