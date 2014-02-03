<?php
if ( ! class_exists( 'AdminPageFramework_FieldsTable' ) ) :
/**
 * Provides methods to render setting fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Field
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FieldsTable {
	
	/**
	 * Returns a set of HTML table outputs consisting of form sections and fields.
	 * 
	 * @since			3.0.0
	 */
	public function getFieldsTables( &$aSections, $hfSectionCallback, $hfFieldCallback ) {
		
		$aOutput = array();
		foreach( $aSections as $_sSectionID => $aFields ) {
			if ( is_callable( $hfSectionCallback ) ) 
				$aOutput[] = call_user_func_array( $hfSectionCallback, array( $_sSectionID ) );	// the section title and the description			
			$aOutput[] = $this->getFieldsTable( $aFields, $hfSectionCallback, $hfFieldCallback );
		}
		return implode( PHP_EOL, $aOutput );
		
	}
	
	/**
	 * Returns a single HTML table output of a set of fields generated from the given field definition arrays.
	 * 
	 * @since			3.0.0
	 */
	public function getFieldsTable( &$aFields, $hfSectionCallback, $hfFieldCallback ) {

		$aOutput = array();
		$aOutput[] = '<table class="form-table">';
			$aOutput[] = $this->getFieldRows( $aFields, $hfFieldCallback );
		$aOutput[] = '</table>';
		return implode( PHP_EOL, $aOutput );
		
	}

	/**
	 * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
	 * 
	 * @since			3.0.0	
	 */
	public function getFieldRows( &$aFields, $hfCallback ) {
		
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
		protected function getFieldRow( &$aField, $hfCallback ) {
			
			$aOutput = array();
			$aOutput[] = "<tr valign='top'>";
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
	public function getFields( &$aFields, $hfCallback ) {
		
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
		protected function getField( &$aField, $hfCallback )  {
			
			$aOutput = array();
			if ( $aField['show_title_column'] )
				$aOutput[] = $this->getFieldTitle( $aField );
			$aOutput[] = call_user_func_array( $hfCallback, array( $aField ) );
			return implode( PHP_EOL, $aOutput );		
			
		}
	
		
		/**
		 * Returns the title part of the field output.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldTitle( &$aField ) {
			
			return "<label for='{$aField['field_id']}'>"
				. "<a id='{$aField['field_id']}'></a>"
					. "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>"
						. $aField['title'] 
					. "</span>"
				. "</label>";
		
			
		}
	
}
endif;