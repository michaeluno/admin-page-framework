<?php
if ( ! class_exists( 'AdminPageFramework_FormElement_Utility' ) ) :
/**
 * Provides public methods used outside the class definition.
 * 
 * @package			AdminPageFramework
 * @subpackage		Property
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormElement_Utility extends AdminPageFramework_WPUtility {
			
	/**
	 * Drops repeatable section elements from the given array.
	 * 
	 * This is used in the filtering method that merges user input data with the saved options. If the user input data includes repeatable sections
	 * and the user removed some elements, then the corresponding elements also need to be removed from the options array. Otherwise, the user's removing element
	 * remains in the saved option array as the framework performs recursive array merge.
	 * 
	 * @since			3.0.0
	 */
	public function dropRepeatableSections( $aOptions ) {

		foreach( $aOptions as $_sFieldOrSectionID => $_aSubSectionsOrFieldValue ) {
			
			if ( $this->isSection( $_sFieldOrSectionID ) ) continue;
			if ( ! is_array( $_aSubSectionsOrFieldValue ) ) continue;
			
			$_sSectionID = $_sFieldOrSectionID;

			$_aSubSections = $this->getIntegerElements( $_aSubSectionsOrFieldValue );
			if ( empty( $_aSubSections ) ) continue;		// means it's not a subsection
			
			// Now it's repeatable sections. So drop it.
			unset( $aOptions[ $_sSectionID ] );
			
		}
		
		return $aOptions;
		
	}			
			
	/**
	 * Determines whether the given ID is of a registered form section.
	 * 
	 * @since			3.0.0
	 */
	public function isSection( $sID ) {
		
		/* 
		 * Consider the possibility that the given ID may be used both for a section and a field.
		 * 1. Check if the given ID is not a section.
		 * 2. Parse stored fields and check their ID. If one matches, return false.
		 */
		
		if ( is_numeric( $sID ) && is_int( $sID + 0 ) ) return false;		// integer IDs are not accepted.
		
		// If the section ID is not registered, return false.
		if ( ! array_key_exists( $sID, $this->aSections ) ) return false;
		
		if ( array_key_exists( $sID, $this->aFields ) ) return false;
		
		$_bIsSeciton = false;
		foreach( $this->aFields as $_sSectionID => $_aFields ) {	// since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
			
			if ( $_sSectionID == $sID ) $_bIsSeciton = true;
			
			if ( array_key_exists( $sID, $_aFields ) ) return false;	// a field using the ID is found, and it precedes a section match.
			
		}
		
		return $_bIsSeciton;
		
	}	
	
	/**
	 * Returns the output of the title and description part of the given section by section ID.
	 * 
	 * @since			3.0.0
	 */ 
	public function getSectionHeader( $sSectionID ) {
		
		if ( ! isset( $this->aSections[ $sSectionID ] ) ) return '';
		
		$aOutput = array();
		$aOutput[] = $this->aSections[ $sSectionID ]['title'] ? "<h3 class='admin-page-framework-section-title'>" . $this->aSections[ $sSectionID ]['title'] . "</h3>" : '';
		$aOutput[] = $this->aSections[ $sSectionID ]['description'] ? "<p class='admin-page-framework-section-description'>" . $this->aSections[ $sSectionID ]['description'] . "</p>" : '';
		return implode( PHP_EOL, $aOutput );
		
	}
	
	/**
	 * Returns a fields model array that represents the structure of the array of saving data from the given fields definition array.
	 * 
	 * The passed fields array should be structured like the following.
	 * 
	 * 	array(  
	 * 		'_default'	=> array(		// _default is reserved for the system.
	 * 			'my_field_id' => array( .... ),
	 * 			'my_field_id2' => array( .... ),
	 * 		),
	 * 		'my_secion_id' => array(
	 * 			'my_field_id' => array( ... ),
	 * 			'my_field_id2' => array( ... ),
	 * 			'my_field_id3' => array( ... ),
	 * 	
	 * 		),
	 * 		'my_section_id2' => array(
	 * 			'my_field_id' => array( ... ),
	 * 		),
	 * 		...
	 * )
	 * 
	 * It will be converted to 
	 * 	array(  
	 * 		'my_field_id' => array( .... ),
	 * 		'my_field_id2' => array( .... ),
	 * 		'my_secion_id' => array(
	 * 			'my_field_id' => array( ... ),
	 * 			'my_field_id2' => array( ... ),
	 * 			'my_field_id3' => array( ... ),
	 * 	
	 * 		),
	 * 		'my_section_id2' => array(
	 * 			'my_field_id' => array( ... ),
	 * 		),
	 * 		...
	 * )
	 * 
	 * @remark			Just the _default section elements get extracted to the upper dimension.
	 * @since			3.0.0
	 */
	public function getFieldsModel( array $aFields=array() )  {
		
		$_aFieldsModel = array();
		$aFields = empty( $aFields ) ? $this->aFields : $aFields;
		foreach ( $aFields as $_sSectionID => $_aFields ) {

			if ( $_sSectionID != '_default' ) {
				$_aFieldsModel[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;	
				continue;
			}
			
			// For default field items.
			foreach( $_aFields as $_sFieldID => $_aField ) 
				$_aFieldsModel[ $_aField['field_id'] ] = $_aField;

		}
		return $_aFieldsModel;
	}
	
	
		/**
		 * Calculates the subtraction of two values with the array key of <em>order</em>
		 * 
		 * This is used to sort arrays.
		 * 
		 * @since			3.0.0			
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