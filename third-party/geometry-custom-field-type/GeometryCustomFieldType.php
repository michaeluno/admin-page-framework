<?php
class GeometryCustomFieldType extends AdminPageFramework_CustomFieldType {
		
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'vSize'					=> 1,
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
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
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
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
					
		if ( isset( $vValue['latitude'], $vValue['longitude'] ) )
			$vValue = array( $vValue );

		foreach( ( array ) $vValue as $strKey => $arrValue ) {
			$strClassAttribute = $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] );
			$strName = is_array( $arrField['vLabel'] ) ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}";
			$numLatitude = isset( $arrValue['latitude'] ) ? $arrValue['latitude'] : 20;
			$numLongitude = isset( $arrValue['longitude'] ) ? $arrValue['longitude'] : 20; 
			$numElevation = isset( $arrValue['elevation'] ) ? $arrValue['elevation'] : null; 
			$strPlaceName = isset( $arrValue['place_name'] ) ? $arrValue['place_name'] : ''; 
			$strDisabled = ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' );
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container geometry'>"
						. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
						. ( ( $strLabel = $this->getCorrespondingArrayValue( $arrField['vLabel'], $strKey, $arrDefaultKeys['vLabel'] ) ) 
							? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>{$strLabel}</span>" 
							: "" 
						)
						. "<div class='admin-page-framework-input-container'>"
							. "<div class='gllpLatlonPicker'>"
								. "<div class='gllpMap map'>Google Maps</div>"
								. "<label for='{$strTagID}_{$strKey}_button' class='update-button'>"
									. "<input type='button' class='gllpUpdateButton button button-small' id='{$strTagID}_{$strKey}_button' value='" . __( 'Update Map', 'admin-page-framework-demo' ) . "' {$strDisabled} />"
								. "</label>"								
								. "<label for='{$strTagID}_{$strKey}_latitude'>"
									. "<span class='inline-label-text'>" . __( 'Latitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLatitude {$strClassAttribute}' id='{$strTagID}_{$strKey}_latitude' name='{$strName}[latitude]' value='{$numLatitude}' {$strDisabled}/>"
								. "</label><br />"
								. "<label for='{$strTagID}_{$strKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Longitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLongitude {$strClassAttribute}' id='{$strTagID}_{$strKey}_longitude' name='{$strName}[longitude]' value='{$numLongitude}' {$strDisabled}/>"
								. "</label><br />"
								. "<label for='{$strTagID}_{$strKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Elevation', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpElevation {$strClassAttribute}' id='{$strTagID}_{$strKey}_elevation' name='{$strName}[elevation]' value='{$numElevation}' {$strDisabled}/> " . __( "metres", "admin-page-framework-demo" )
								. "</label><br />"								
								. "<label for='{$strTagID}_{$strKey}_name'>"
									. "<span class='inline-label-text'>" . __( 'Place Name', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLocationName {$strClassAttribute}' id='{$strTagID}_{$strKey}_place_name' name='{$strName}[place_name]' value='{$strPlaceName}' {$strDisabled}/> "
								. "</label><br />"
								. "<label for='{$strTagID}_{$strKey}_zoom'>"
									. "<span class='inline-label-text'>" . __( 'zoom', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpZoom' id='{$strTagID}_{$strKey}_zoom' value='3'/>"
								. "</label><br />"
							. "</div>"							
						. "</div>"
						. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
		}
					
		return "<div class='admin-page-framework-field-geometry' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}	
	
}