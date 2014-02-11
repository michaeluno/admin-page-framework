<?php
if ( ! class_exists( 'AdminPageFramework_FormElement_Page' ) ) :
/**
 * Provides methods that deal with field and section definition arrays specific to the ones that belong to generic pages created by the framework.
 * 
 * @package			AdminPageFramework
 * @subpackage		Property
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormElement_Page extends AdminPageFramework_FormElement {
	
	/**
	 * Stores the default the page slug.
	 * 
	 * @since			3.0.0
	 */
	protected $sDefaultPageSlug;
	
	/**
	 * Checks if the given page slug is added to a section.
	 * 
	 * @since			3.0.0
	 */
	public function isPageAdded( $sPageSlug ) {

		foreach( $this->aSections as $_sSectionID => $_aSection ) {
			if ( $_sSectionID == '_default' ) continue;
			if ( $_aSection['page_slug'] == $sPageSlug ) return true;			
		}
			
		return false;
		
	}
	
	/**
	 * Returns the registered field that belongs to the given page by slug.
	 * 
	 * @since			3.0.0
	 */
	public function getFieldsByPageSlug( $sPageSlug, $sTabSlug='' ) {
		
		return $this->castArrayContents( $this->getSectionsByPageSlug( $sPageSlug, $sTabSlug ), $this->aFields );
		
	}
	
	/**
	 * Returns the registered sections that belong to the given page by slug.
	 * @since			3.0.0.
	 */
	public function getSectionsByPageSlug( $sPageSlug, $sTabSlug='' ) {
		
		$_aSections = array();
		foreach( $this->aSections as $_sSecitonID => $_aSection ) {
			
			if ( $sTabSlug && $_aSection['tab_slug'] != $sTabSlug ) continue;
			
			if ( $_aSection['page_slug'] != $sPageSlug ) continue;
			
			$_aSections[ $_sSecitonID ] = $_aSection;
				
		}
		
		uasort( $_aSections, array( $this, '_sortByOrder' ) ); 
		return $_aSections;
	}
	
	
	/**
	 * Retrieves the page slug that the settings section belongs to.		
	 * 
	 * Used by fields type that require the page_slug key.
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 * @internal
	 */ 
	public function getPageSlugBySectionID( $sSectionID ) {
		return isset( $this->aSections[ $sSectionID ]['page_slug'] )
			? $this->aSections[ $sSectionID ]['page_slug']
			: null;			
	}	
	
	/**
	 * Sets the default page slug property.
	 * 
	 * @since			3.0.0
	 */
	public function setDefaultPageSlug( $sDefaultPageSlug ) {
		$this->sDefaultPageSlug = $sDefaultPageSlug;
	}
	
	/**
	 * Sets the option key.
	 * 
	 * Used by the field formatting method.
	 * 
	 * @since			3.0.0
	 */
	public function setOptionKey( $sOptionKey ) {
		$this->sOptionKey = $sOptionKey;
	}
	
	/**
	 * Sets the caller class name.
	 * 
	 * Used by the field formatting method.
	 * 
	 * @since			3.0.0
	 */
	public function setCallerClassName( $sClassName ) {
		$this->sClassName = $sClassName;		
	}
	
	/**
	 * Sets the current page slug.
	 * 
	 * Usd by the conditioning method for secitons.
	 * 
	 * @since			3.0.0
	 */
	public function setCurrentPageSlug( $sCurrentPageSlug ) {
		$this->sCurrentPageSlug = $sCurrentPageSlug;
	}
	
	/**
	 * Sets the current page slug.
	 * 
	 * Usd by the conditioning method for secitons.
	 * 
	 * @since			3.0.0
	 */
	public function setCurrentTabSlug( $sCurrentTabSlug ) {
		$this->sCurrentTabSlug = $sCurrentTabSlug;
	}	
		
	/*
	 * Extending the methods in the base class
	 */
		
	/**
	 * Returns the formatted section array.
	 * 
	 * @since			3.0.0
	 */
	protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements ) {
		
		$aSection = $this->uniteArrays(
			$aSection,
			array( 
				'_fields_type' => $sFieldsType,
				'capability' => $sCapability,
				'page_slug'	=> $this->sDefaultPageSlug,
			),
			self::$_aStructure_Section
		);
			
		$aSection['order'] = is_numeric( $aSection['order'] ) ? $aSection['order'] : $iCountOfElements + 10;
		return $aSection;
		
	}

	/**
	 * Returns the formatted field array.
	 * 
	 * Before callign this method, $sOptionKey and $sClassName properties must be set.
	 * 
	 * @since			3.0.0
	 */
	protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable ) {
		
		$_aField = parent::formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable );
		
		if ( ! $_aField ) return;
		$_aField['option_key'] = $this->sOptionKey;
		$_aField['class_name'] = $this->sClassName;
		$_aField['page_slug'] = isset( $this->aSections[ $_aField['section_id'] ]['page_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['page_slug'] : null;
		$_aField['tab_slug'] = isset( $this->aSections[ $_aField['section_id'] ]['tab_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['tab_slug'] : null;
		$_aField['section_title'] = isset( $this->aSections[ $_aField['section_id'] ]['title'] ) ? $this->aSections[ $_aField['section_id'] ]['title'] : null;	// used for the contextual help pane.
		return $_aField;
		
	}
	
	/**
	 * Applies the conditions to the given section.
	 * 
	 * Before calling this method, $sCurrentPageSlug and $sCurrentTabSlug properties must be set.
	 * 
	 * @since			3.0.0
	 */
	protected function getConditionedSection( array $aSection ) {

		// Check the conditions
		if ( ! current_user_can( $aSection['capability'] ) ) return;
		if ( ! $aSection['if'] ) return;	
		if ( ! $aSection['page_slug'] ) return;	
		if ( $GLOBALS['pagenow'] != 'options.php' && $this->sCurrentPageSlug != $aSection['page_slug'] ) return;	
		if ( ! $this->_isSectionOfCurrentTab( $aSection, $this->sCurrentPageSlug, $this->sCurrentTabSlug ) ) return;
		return $aSection;
		
	}
		/**
		 * Checks if the given section belongs to the currently loading tab.
		 * 
		 * @since			2.0.0
		 * @since			3.0.0			Moved from the setting class.
		 * @return			boolean			Returns true if the section belongs to the current tab page. Otherwise, false.
		 * @deprecated
		 */ 	
		private function _isSectionOfCurrentTab( $aSection, $sCurrentPageSlug, $sCurrentTabSlug ) {
			
			// Make sure if it's in the loading page.
			if ( $aSection['page_slug'] != $sCurrentPageSlug  ) return false;

			// If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
			if ( ! isset( $aSection['tab_slug'] ) ) return true;
										
			// If the checking tab slug and the current loading tab slug is the same, it should be registered.
			if ( $aSection['tab_slug'] == $sCurrentTabSlug )  return true;
			
			// Otherwise, false.
			return false;
			
		}	
		
	/**
	 * Returns the field definition array by applying conditions. 
	 * 
	 * This method is intended to be extended to let the extended class customize the conditions.
	 * 
	 * @since			3.0.0
	 */
	protected function getConditionedField( $aField ) {
		
		// Check capability. If the access level is not sufficient, skip.
		if ( ! current_user_can( $aField['capability'] ) ) return null;
		if ( ! $aField['if'] ) return null;		
		return $aField;
		
	}
}
endif;