<?php
if ( ! class_exists( 'AdminPageFramework_FormTable' ) ) :
/**
 * Provides methods to render setting fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable {
	
	/**
	 * Returns a set of HTML table outputs consisting of form sections and fields.
	 * 
	 * @since			3.0.0
	 */
	public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
		
		$aOutput = array();
		foreach( $aFieldsInSections as $_sSectionID => $aSubSectionsOrFields ) {
			
			if ( ! isset( $aSections[ $_sSectionID ] ) ) continue;
			
			// For repeatable sections,
			if ( $this->hasSubSections( $aSubSectionsOrFields ) ) {
				
				$aSubSections = $this->getSubSections( $aSubSectionsOrFields );		// will include the main section as well.
				
				// Get the heading title and description
				if ( is_callable( $hfSectionCallback ) ) 
					$aOutput[] = call_user_func_array( $hfSectionCallback, array( $_sSectionID ) );	// the section title and the description							
					
				// Get the section tables.
				foreach( $aSubSections as $_iIndex => $aSubSection )
					$aOutput[] = $this->getFormTable( $_sSectionID . '_' . $_iIndex, $aFields, $hfFieldCallback );
					
				continue;
				
			}
			
			// Otherwise, it's an fields-array.
			$aFields = $aSubSectionsOrFields;
			
			// The head part of the section
			if ( $_sSectionID != '_default' && is_callable( $hfSectionCallback ) ) {
				$aOutput[] = call_user_func_array( $hfSectionCallback, array( $_sSectionID ) );	// the section title and the description			
				$aOutput[] = $this->getRepeatableSectionsEnablerScript( $aSections[ $_sSectionID ] );
			}
			// The section table (main content)
			$aOutput[] = $this->getFormTable( $_sSectionID . '_' . '0',  $aFields, $hfFieldCallback );
			
		}
		return implode( PHP_EOL, $aOutput );
		
	}
		/**
		 * Returns the enabler script for repeatable sections.
		 * @since			3.0.0
		 */
		private function getRepeatableSectionsEnablerScript( $aSection ) {
			
			if ( ! $aSection['repeatable'] ) return '';
			
			return '<pre>The repeatable section script will be inserted here.</pre>';
			
			
		}
		/**
		 * Determines whether the given sections array holds sub-sections.
		 * 
		 * @since			3.0.0
		 */
		private function hasSubSections( $aSectionElements ) {
			
			$aKeys = array_keys( $aSectionElements );
			foreach( $aKeys as $isKey ) {
				
				if ( ! is_numeric( $isKey ) ) continue;
				
				$ifKey = $isKey + 0;	// could be a float.
				
				if ( is_int( $ifKey ) ) return true;
				
			}
			
			return false;
			
		}
	
		/**
		 * Re-composes section definition arrays with sub-sections.
		 * 
		 * @since			3.0.0
		 */
		private function getSubSections( $aSectionElements ) {
			
			/* The passed array structure looks like this
			 array( 
				0 => array(
					'field_id_1' => array( ... ),
					'field_id_2' => array( ... ),
					'field_id_3' => array( ... ),
				), 
				1 => array(
					'field_id_1' => array( ... ),
					'field_id_2' => array( ... ),
					'field_id_3' => array( ... ),				
				),
				'field_id_1' => array( ... ),
				'field_id_2' => array( ... ),
				'field_id_3' => array( ... ),				
			 )
			 
			 It will be converted to to
			 array(
				0 => array(
					'field_id_1' => array( ... ),
					'field_id_2' => array( ... ),
					'field_id_3' => array( ... ),
				), 
				1 => array(
					'field_id_1' => array( ... ),
					'field_id_2' => array( ... ),
					'field_id_3' => array( ... ),				
				),				
				2 => array(
					'field_id_1' => array( ... ),
					'field_id_2' => array( ... ),
					'field_id_3' => array( ... ),				
				),
			 )
			 */
			$aSubSections = AdminPageFramework_Utility::getIntegerElements( $aSectionElements );
			$aMainSection = AdminPageFramework_Utility::invertCastArrayContents( $aSectionElements, $aSubSections );
			foreach( $aSubSections as &$_aSubSection ) 
				$_aSubSection = AdminPageFramework_Utility::uniteArrays( $_aSubSection, $aMainSection );
				
			array_unshift( $aSubSections, $aMainSection );	// insert the main section to the beginning of the array.
			return $aSectionElements;			
			
		}
	/**
	 * Returns a single HTML table output of a set of fields generated from the given field definition arrays.
	 * 
	 * @since			3.0.0
	 */
	public function getFormTable( $sID, $aFields, $hfFieldCallback ) {

		if ( count( $aFields ) <= 0 ) return '';
	
		$_sAttributes = AdminPageFramework_WPUtility::generateAttributes(  
			array( 
				'id' => 'fields_table-' . $sID,
				'class' => 'form-table admin-page-framework-fields-table',
			)
		);
		$aOutput = array();
		$aOutput[] = "<table {$_sAttributes}>";
			$aOutput[] = $this->getFieldRows( $aFields, $hfFieldCallback );
		$aOutput[] = "</table>";
		return implode( PHP_EOL, $aOutput );
		
	}

	/**
	 * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
	 * 
	 * @since			3.0.0	
	 */
	public function getFieldRows( $aFields, $hfCallback ) {
		
		if ( ! is_callable( $hfCallback ) ) return '';
		$aOutput = array();
		foreach( $aFields as $aField ) 
			$aOutput[] = $this->getFieldRow( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
		
		/**
		 * Returns the field output enclosed in a table row.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldRow( $aField, $hfCallback ) {
			
			$aOutput = array();
			$_sAttributes = $this->getAttributes( 
				$aField,
				array( 
					'id' => 'fieldrow-' . AdminPageFramework_InputField::_getInputTagID( $aField ),
					'valign' => 'top',
				)
			);
			$aOutput[] = "<tr {$_sAttributes}>";
				if ( $aField['show_title_column'] )
					$aOutput[] = "<th>" . $this->getFieldTitle( $aField ) . "</th>";
				$aOutput[] = "<td>" . call_user_func_array( $hfCallback, array( $aField ) ) . "</td>";
			$aOutput[] = "</tr>";
			return implode( PHP_EOL, $aOutput );
				
		}
	
	/**
	 * Returns a set of fields output from the given field definition array.
	 * 
	 * @remark			This is similar to getFieldRows() but without the enclosing table row tag. Used for taxonomy fields.
	 * @since			3.0.0
	 */
	public function getFields( $aFields, $hfCallback ) {
		
		if ( ! is_callable( $hfCallback ) ) return '';
		$aOutput = array();
		foreach( $aFields as $aField ) 
			$aOutput[] = $this->getField( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
	
		/**
		 * Returns the given field output without a table row tag.
		 * @since			3.0.0
		 */
		protected function getField( $aField, $hfCallback )  {
			
			$aOutput = array();
			$aOutput[] = "<div " . $this->getAttributes( $aField ) . ">";
			if ( $aField['show_title_column'] )
				$aOutput[] = $this->getFieldTitle( $aField );
			$aOutput[] = call_user_func_array( $hfCallback, array( $aField ) );
			$aOutput[] = "</div>";
			return implode( PHP_EOL, $aOutput );		
			
		}
	
		/**
		 * Generates attributes of the field container tag.
		 * 
		 * @since			3.0.0
		 */
		protected function getAttributes( $aField, $aAttributes=array() ) {
			
			$_aAttributes = $aAttributes + ( isset( $aField['attributes']['fieldrow'] ) ? $aField['attributes']['fieldrow'] : array() );
			
			if ( $aField['hidden'] )	// Prepend the visibility CSS property.
				$_aAttributes['style'] = 'display:none;' . ( isset( $_aAttributes['style'] ) ? $_aAttributes['style'] : '' );
			
			return AdminPageFramework_WPUtility::generateAttributes( $_aAttributes );
			
		}
		
		/**
		 * Returns the title part of the field output.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldTitle( $aField ) {
			
			return "<label for='{$aField['field_id']}'>"
				. "<a id='{$aField['field_id']}'></a>"
					. "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>"
						. $aField['title'] 
					. "</span>"
				. "</label>";
		
			
		}
	
}
endif;