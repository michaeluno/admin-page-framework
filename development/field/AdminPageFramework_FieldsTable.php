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
	 * A wrapper function of the do_settings_section() function.
	 * 
	 * @since			3.0.0
	 */
	public function doSettingsSections( $sPageSlug, $hfSectionCallback, $hfFieldCallback ) {

		if ( ! isset( $GLOBALS['wp_settings_sections'][ $sPageSlug ] ) ) return;

		foreach ( ( array ) $GLOBALS['wp_settings_sections'][ $sPageSlug ] as $aSection ) {
			
			if ( $aSection['title'] )
				echo "<h3>{$aSection['title']}</h3>\n";

			if ( $aSection['callback'] )
				call_user_func( $aSection['callback'], $aSection );

			if ( ! isset( $GLOBALS['wp_settings_fields'] ) ) continue; 
			if ( ! isset( $GLOBALS['wp_settings_fields'][ $sPageSlug ] ) ) continue;
			if ( ! isset( $GLOBALS['wp_settings_fields'][ $sPageSlug ][ $aSection['id'] ] ) ) continue;
			
			echo $this->getFieldsTable( $aFields, $hfFieldCallback );
			echo '<table class="form-table">';
			do_settings_fields(  $sPageSlug , $aSection['id'] );
			echo '</table>';
			
		}
		
	}	
	
	/**
	 * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table tag.
	 * 
	 * @since			3.0.0
	 */
	public function getFieldsTable( &$aFields, $hfCallback ) {
		
		$aOutput = array();
		$aOutput[] = '<table class="form-table">';
			$aOutput[] = $this->getFieldRows( $aFields, $hfCallback );
		$aOutput[] = '</table>';
		return implode( PHP_EOL, $aOutput );
		
	}

	/**
	 * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
	 * 
	 * @since			3.0.0	
	 */
	public function getFieldRows( &$aFields, $hfCallback ) {
		
		// if ( ! is_callable( $hfCallback ) ) return '';
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
				$aOutput[] = "<th>" . $this->getFieldTitle( $aField ) . "</th>";
				$aOutput[] = "<td>" . call_user_func( $hfCallback, $aField ) . "</td>";
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
			
			$aOutput = array( '<p>THIS IS A TEST</p>' );
			$aOutput[] = $this->getFieldTitle( $aField );
			$aOutput[] = call_user_func( $hfCallback, $aField );
			return implode( PHP_EOL, $aOutput );		
			
		}
	
		
		/**
		 * Returns the title part of the field output.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldTitle( &$aField ) {
			
			if ( ! $aField['show_title_column'] ) return '';
			return "<label for='{$aField['field_id']}'>"
				. "<a id='{$aField['field_id']}'></a>"
					. "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>"
						. $aField['title'] 
					. "</span>"
				. "</label>";
		
			
		}
	
}
endif;