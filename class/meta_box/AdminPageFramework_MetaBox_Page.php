<?php
if ( ! class_exists( 'AdminPageFramework_MetaBox_Page' ) ) :
/**
 * Provides methods for creating meta boxes in pages added by the framework.
 *
 * @abstract
 * @since			3.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_MetaBox_Base {
	
	/**
	 * 
	 * @since			3.0.0
	 */
	function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {		

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
	public function enqueueStyles( $aSRCs, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * @remark			The user may use this method.
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$sPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
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
	 * @remark			The user may use this method.
	 * @since			3.0.0			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $sPageSlug='', $sTabSlug='', $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $sPageSlug, $sTabSlug, $aCustomArgs );
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @since			3.0.0			The scope changed to public to indicate the users will use.
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/		
	public function addSettingField( array $aField ) {

		$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		
		// Check the mandatory keys are set.
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $aField['if'] ) return;
							
		// Load head tag elements for fields.
		if ( $this->_isMetaBoxPage( isset( $_GET['page'] ) ? $_GET['page'] : null ) ) 
			$this->_setFieldHeadTagElements( $aField );	// Set relevant scripts and styles for the input field.
		
		// For the contextual help pane,
		if ( $this->_isMetaBoxPage( isset( $_GET['page'] ) ? $_GET['page'] : null ) && $aField['help'] )
			$this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );

		$this->oProp->aFields[ $aField['field_id'] ] = $aField;
	
	}
		/**
		 * Checks if the currently loading page is in the pages specified for this meta box.
		 * @since			3.0.0
		 */
		private function _isMetaBoxPage( $sPageSlug ) {
			
			if ( ! isset( $sPageSlug ) ) return false;
			
			if ( in_array( $sPageSlug, $this->oProp->aPageSlugs ) )
				return true;
			
			return false;
		}
	
	/**
	 * Returns the field output.
	 * @since			3.0.0
	 */
	protected function getFieldOutput( $aField ) {
		
		/* Since meta box fields don't have the option_key key which is required to compose the name attribute in the regular pages. */
		$sOptionKey = $this->_getOptionKey();
		$aField['option_key'] = $sOptionKey ? $sOptionKey : null;
		$aField['page_slug'] = isset( $_GET['page'] ) ? $_GET['page'] : '';	// set an empty string to make it yield true for isset() so that saved options will be cheched.

		return parent::getFieldOutput( $aField );
		
	}
	
	/**
	 * Returns the currently loading page's option key if the page has the admin page object.
	 * @since			3.0.0
	 */
	private function _getOptionkey() {
		
		if ( ! isset( $_GET['page'] ) ) return null;
		
		return $this->oProp->getOptionKey( $_GET['page'] );
		
	}
	
		
	/**
	 * Sets the given field type's enqueuing scripts and styles.
	 * 
	 * A helper function for the above addSettingField() method.
	 * 
	 * @since			2.1.5
	 */
	protected function _setFieldHeadTagElements( array $aField ) {
		
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
				
		foreach( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] as $asSource ) {
			if ( is_string( $asSource ) )
				$this->oHeadTag->_enqueueStyle( $asSource );
			else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )
				$this->oHeadTag->_enqueueStyle( $asSource[ 'src' ], '', '', $asSource );
		}
		foreach( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] as $asSource ) {
			if ( is_string( $asSource ) )
				$this->oHeadTag->_enqueueScript( $asSource );
			else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )
				$this->oHeadTag->_enqueueScript( $asSource[ 'src' ], '', '', $asSource );
		}							
			
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
				$this->oProp->aFields	// argument
			);		
			
		}
		
}
endif;