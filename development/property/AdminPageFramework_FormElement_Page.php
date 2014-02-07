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
		
		foreach( $this->aSections as $_aSection ) 
			if ( $_aSection['page_slug'] == $sPageSlug ) return true;
			
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


}
endif;