<?php
class DialCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'size'					=> 10,
			'max_length'			=> 400,
			'data_attribute'		=> array(),		// the array holds the data for the data-{...} attribute of the input tag.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		
	}	
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			dirname( __FILE__ ) . '/js/jquery.knob.js',
		);
	}	

	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
		); 
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
		return "
			.admin-page-framework-field-dial .admin-page-framework-input-label-container {
				padding-right: 1em;
				padding-bottom: 2em;
			}
			.admin-page-framework-field-dial .admin-page-framework-input-label-string {
				vertical-align: top;
			}
		
		";		
	}

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the geometry custom field type.
	 * 
	 */
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		$aFields = $aField['repeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $aField['label'];		
		
		foreach( ( array ) $aFields as $sKey => $sLabel ) 
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$tag_id}_{$sKey}'>"
							. $this->getCorrespondingArrayValue( $aField['before_input'], $sKey, $_aDefaultKeys['before_input'] ) 
							. ( $sLabel && ! $aField['repeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>" . $sLabel . "</span>"
								: "" 
							)
							. "<input id='{$tag_id}_{$sKey}' "
								. "class='knob " . $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $aField['size'], $sKey, $_aDefaultKeys['size'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $aField['max_length'], $sKey, $_aDefaultKeys['max_length'] ) . "' "
								. "type='text' "
								. "name=" . ( is_array( $aFields ) ? "'{$field_name}[{$sKey}]' " : "'{$field_name}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $sKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $aField['is_read_only'], $sKey ) ? "readonly='readonly' " : '' )
								. $this->getDataAttributes( $aField, $sKey, $_aDefaultKeys )
							. "/>"
							. $this->getCorrespondingArrayValue( $aField['after_input'], $sKey, $_aDefaultKeys['after_input'] )
						. "</label>"
					. "</div>"	// end of label container
					. $this->getDialEnablerScript( "{$tag_id}_{$sKey}" )					
				. "</div>"	// end of admin-page-framework-field
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);

		return "<div class='admin-page-framework-field-dial' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
			
	}	
	
	/**
	 * A helper function for the above method.
	 */
	private function getDataAttributes( $aField, $sKey, $_aDefaultKeys ) {
		
		$aDataAttribute = isset( $aField['data_attribute'][ $sKey ] )
			? $this->uniteArrays( ( array ) $aField['data_attribute'][ $sKey ], $_aDefaultKeys['data_attribute'] )
			: $aField['data_attribute'];
			
		return $this->convertArrayToDataAttributes( $aDataAttribute );
		
	}
	
	/**
	 * Generates the data-{...} attributes from the given array.
	 * 
	 */
	private function convertArrayToDataAttributes( $aAssociativeArray ) {
				
		$aDataAttributes = array();
		foreach( $aAssociativeArray as $sKey => $sValue ) 
			if ( isset( $sValue ) )
				$aDataAttributes[] = "data-{$sKey}='{$sValue}'";
			
		return implode( ' ', $aDataAttributes );
		
	}
	
	private function getDialEnablerScript( $sInputID ) {
			return 
				"<script type='text/javascript' class='dial-enabler-script'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sInputID}' ).knob();
					});
				</script>";		
		
	}
	
}