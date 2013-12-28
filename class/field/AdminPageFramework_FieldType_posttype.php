<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_posttype' ) ) :
/**
 * Defines the posttype field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_posttype extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'aRemove'					=> array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of post type checklist check boxes.
	 * 
	 * @remark			the posttype checklist field does not support multiple elements by passing an array of labels.
	 * @since			2.0.0
	 * 
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
						
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $aField['aRemove'] ) as $sKey => $sValue ) {
			$sName = "{$field_name}[{$sKey}]";
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>"
						. "<label for='{$tag_id}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] )
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name='{$sName}' value='0' />"
								. "<input "
									. "id='{$tag_id}_{$sKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
									. "type='checkbox' "
									. "name='{$sName}'"
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $sKey, false ) == 1 ? "Checked " : '' )				
								. "/>"
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $sValue
							. "</span>"				
							. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-posttype' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}	
	
		/**
		 * A helper function for the above getPosttypeChecklistField method.
		 * 
		 * @since			2.0.0
		 * @since			2.1.1			Changed the returning array to have the labels in its element values.
		 * @since			2.1.5			Moved from AdminPageFramework_InputTag.
		 * @return			array			The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
		 */ 
		private function getPostTypeArrayForChecklist( $aRemoveNames, $aPostTypes=array() ) {
			
			foreach( get_post_types( '','objects' ) as $oPostType ) 
				if (  isset( $oPostType->name, $oPostType->label ) ) 
					$aPostTypes[ $oPostType->name ] = $oPostType->label;

			return array_diff_key( $aPostTypes, array_flip( $aRemoveNames ) );	

		}		
	
}
endif;