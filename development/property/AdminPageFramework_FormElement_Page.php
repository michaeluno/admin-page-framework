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

	/*
	 * Extending the methods in the base class
	 */
	/**
	 * Formats the stored sections definition array.
	 * 
	 * @since			3.0.0
	 */
	public function formatSections( $sFieldsType, $sCapability, $sDefaultPageSlug, $sCurrentPageSlug, $sCurrentTabSlug ) {
		
		$_aNewSectionArray = array();
		foreach( $this->aSections as $_sSectionID => $_aSection ) {
			
			$_aSection = $this->uniteArrays(
				$_aSection,
				array( 
					'_fields_type' => $sFieldsType,
					'capability' => $sCapability,
					'page_slug'=> $sDefaultPageSlug,
				),
				self::$_aStructure_Section

			);

			// Set the order.
			$_aSection['order']	= is_numeric( $_aSection['order'] ) ? $_aSection['order'] : count( $_aNewSectionArray ) + 10;
			
			// Sanitize the IDs since they are used as a callback method name, the slugs as well.
			$_aSection['section_id'] = $this->oUtil->sanitizeSlug( $_aSection['section_id'] );
			$_aSection['page_slug'] = $this->oUtil->sanitizeSlug( $_aSection['page_slug'] );
			$_aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $_aSection['tab_slug'] );
			
			// Apply conditions
			if ( ! current_user_can( $_aSection['capability'] ) ) continue;
			if ( ! $_aSection['if'] ) continue;
			if ( ! $_aSection['page_slug'] ) continue;	
			if ( $GLOBALS['pagenow'] != 'options.php' && ! $sCurrentPageSlug || $sCurrentPageSlug !=  $_aSection['page_slug'] ) continue;	
			if ( ! $this->_isSectionOfCurrentTab( $_aSection, $sCurrentPageSlug, $sCurrentTabSlug ) ) continue;

			
			$_aNewSectionArray[ $_sSectionID ] = $_aSection;
			
			
		}
		uasort( $_aNewSectionArray, array( $this, '_sortByOrder' ) ); 
		$this->aSections = $_aNewSectionArray;
		
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
}
endif;