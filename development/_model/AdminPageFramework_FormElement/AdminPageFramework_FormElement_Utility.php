<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
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
	 * Drops repeatable section and field elements from the given array.
	 * 
	 * This is used in the filtering method that merges user input data with the saved options. If the user input data includes repeatable sections
	 * and the user removed some elements, then the corresponding elements also need to be removed from the options array. Otherwise, the user's removing element
	 * remains in the saved option array as the framework performs recursive array merge.
	 *  
	 * @remark			The options array structure is slightly different from the fields array. An options array does not have '_default' section keys.
	 * @since			3.0.0
	 */
	public function dropRepeatableElements( array $aOptions ) {

		foreach( $aOptions as $_sFieldOrSectionID => $_aSectionOrFieldValue ) {
			
			// If it's a section
			if ( $this->isSection( $_sFieldOrSectionID ) ) {
				
				$_aFields = $_aSectionOrFieldValue;
				$_sSectionID = $_sFieldOrSectionID;		
				if ( $this->isRepeatableSection( $_sSectionID ) ) {
					unset( $aOptions[ $_sSectionID ] );				
					continue;
				}
				
				if ( ! is_array( $_aFields ) ) continue;	// an error may occur with an empty _default element.
				
				// At this point, it is ensured that it's not a repeatable section. 
				foreach( $_aFields as $_sFieldID => $_aField ) {
					
					if ( $this->isRepeatableField( $_sFieldID, $_sSectionID ) ) {
						
						unset( $aOptions[ $_sSectionID ][ $_sFieldID ] );
						continue;
					}

				}
				
				continue;
			}
			

			// It's a field saved in the root dimension, which corresponds to the '_default' section of the stored registered fields array.
			$_sFieldID = $_sFieldOrSectionID;			
			if ( $this->isRepeatableField( $_sFieldID, '_default' ) ) 
				unset( $aOptions[ $_sFieldID ] );

		
		}
		return $aOptions;
		
	}	
		/**
		 * Checks whether a section is repeatable from the given section ID.
		 * 
		 * @since			3.0.0
		 */
		private function isRepeatableSection( $sSectionID ) {
			
			return isset( $this->aSections[ $sSectionID ]['repeatable'] ) && $this->aSections[ $sSectionID ]['repeatable'];
			
		}
		
		/**
		 * Checks whether a field is repeatable from the given field ID.
		 * 
		 * @since			3.0.0
		 */		
		private function isRepeatableField( $sFieldID, $sSectionID ) {
			
			return ( isset( $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] ) && $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] );
			
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
		if ( ! array_key_exists( $sID, $this->aFields ) ) return false;	// the fields array's first dimension is also filled with the keys of section ids.
		
		$_bIsSeciton = false;
		foreach( $this->aFields as $_sSectionID => $_aFields ) {	// since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
			
			if ( $_sSectionID == $sID ) $_bIsSeciton = true;
			
			if ( array_key_exists( $sID, $_aFields ) ) return false;	// a field using the ID is found, and it precedes a section match.
			
		}
		
		return $_bIsSeciton;
		
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
	
	
	/**
	 * Applies filters to each conditioned field definition array.
	 * 
	 * @since			3.0.2
	 */
	public function applyFiltersToFields( $oCaller, $sClassName ) {
		
		foreach( $this->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) {
						
			foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
				
				// If it is a sub-section array.
				if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) {
					$_sSubSectionIndex = $_sIndexOrFieldID;
					$_aFields = $_aSubSectionOrField;
					$_sSectionSubString = $_sSectionID == '_default' ? '' : "_{$_sSectionID}";
					foreach( $_aFields as $_aField ) {
						$this->aConditionedFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $this->addAndApplyFilter(
							$oCaller,
							"field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}",
							$_aField,
							$_sSubSectionIndex
						);	
					}
					continue;
					
				}
				
				// Otherwise, insert the formatted field definition array.
				$_aField = $_aSubSectionOrField;
				$_sSectionSubString = $_sSectionID == '_default' ? '' : "_{$_sSectionID}";
				$this->aConditionedFields[ $_sSectionID ][ $_aField['field_id'] ] = $this->addAndApplyFilter(
					$oCaller,
					"field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}",
					$_aField		
				);
				
			}
			
		}		
		
	}

	
}
endif;