<?php
class GeometryCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'size'					=> 1,
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
			"http://maps.googleapis.com/maps/api/js?sensor=false",	// load this first
			dirname( __FILE__ ) . '/js/jquery-gmaps-latlon-picker.js',	// load this next - a file path can be passed, ( as well as a url )
		);
	}	

	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array(
			dirname( __FILE__ ) . '/css/jquery-gmaps-latlon-picker.css',	// a file path can be passed, ( as well as a url )
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
			.admin-page-framework-input-label-container.geometry, 
			.geometry .admin-page-framework-input-container
			{
				width: 100%;
			}
			.geometry .inline-label-text {
				min-width:120px; 
				display: inline-block;
			}
			.geometry label {
				display: inline-block;
				margin-bottom: 0.5em;
			}
			.geometry label.update-button {
				display: inline;
			}
			.geometry .map {
				margin-bottom: 1em;
			}
			.geometry .update-button {
				float: right;
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
					
		if ( isset( $vValue['latitude'], $vValue['longitude'] ) )
			$vValue = array( $vValue );

		foreach( ( array ) $vValue as $sKey => $aValue ) {
			$sClassAttribute = $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] );
			$sName = is_array( $aField['label'] ) ? "{$field_name}[{$sKey}]" : "{$field_name}";
			$nLatitude = isset( $aValue['latitude'] ) ? $aValue['latitude'] : 20;
			$nLongitude = isset( $aValue['longitude'] ) ? $aValue['longitude'] : 20; 
			$nElevation = isset( $aValue['elevation'] ) ? $aValue['elevation'] : null; 
			$sPlaceName = isset( $aValue['place_name'] ) ? $aValue['place_name'] : ''; 
			$sDisabled = ( $this->getCorrespondingArrayValue( $aField['is_disabled'], $sKey ) ? "disabled='Disabled' " : '' );
			$aOutput[] = 
				"<div class='{$field_class_selector}' id='field-{$tag_id}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container geometry'>"
						. $this->getCorrespondingArrayValue( $aField['before_input_tag'], $sKey, $_aDefaultKeys['before_input_tag'] ) 
						. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
							? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['label_min_width'], $sKey, $_aDefaultKeys['label_min_width'] ) . "px;'>{$sLabel}</span>" 
							: "" 
						)
						. "<div class='admin-page-framework-input-container'>"
							. "<div class='gllpLatlonPicker'>"
								. "<div class='gllpMap map'>Google Maps</div>"
								. "<label for='{$tag_id}_{$sKey}_button' class='update-button'>"
									. "<input type='button' class='gllpUpdateButton button button-small' id='{$tag_id}_{$sKey}_button' value='" . __( 'Update Map', 'admin-page-framework-demo' ) . "' {$sDisabled} />"
								. "</label>"								
								. "<label for='{$tag_id}_{$sKey}_latitude'>"
									. "<span class='inline-label-text'>" . __( 'Latitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLatitude {$sClassAttribute}' id='{$tag_id}_{$sKey}_latitude' name='{$sName}[latitude]' value='{$nLatitude}' {$sDisabled}/>"
								. "</label><br />"
								. "<label for='{$tag_id}_{$sKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Longitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLongitude {$sClassAttribute}' id='{$tag_id}_{$sKey}_longitude' name='{$sName}[longitude]' value='{$nLongitude}' {$sDisabled}/>"
								. "</label><br />"
								. "<label for='{$tag_id}_{$sKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Elevation', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpElevation {$sClassAttribute}' id='{$tag_id}_{$sKey}_elevation' name='{$sName}[elevation]' value='{$nElevation}' {$sDisabled}/> " . __( "metres", "admin-page-framework-demo" )
								. "</label><br />"								
								. "<label for='{$tag_id}_{$sKey}_name'>"
									. "<span class='inline-label-text'>" . __( 'Place Name', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLocationName {$sClassAttribute}' id='{$tag_id}_{$sKey}_place_name' name='{$sName}[place_name]' value='{$sPlaceName}' {$sDisabled}/> "
								. "</label><br />"
								. "<label for='{$tag_id}_{$sKey}_zoom'>"
									. "<span class='inline-label-text'>" . __( 'zoom', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpZoom' id='{$tag_id}_{$sKey}_zoom' value='3'/>"
								. "</label><br />"
							. "</div>"							
						. "</div>"
						. $this->getCorrespondingArrayValue( $aField['after_input_tag'], $sKey, $_aDefaultKeys['after_input_tag'] )
						. "</label>"
					. "</div>"
				
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$tag_id}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
		}
					
		return "<div class='admin-page-framework-field-geometry' id='{$tag_id}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}	
	
}