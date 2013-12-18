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
	public function replyToGetInputField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$sFieldName = $aField['sFieldName'];
		$sTagID = $aField['sTagID'];
		$sFieldClassSelector = $aField['sFieldClassSelector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
					
		if ( isset( $vValue['latitude'], $vValue['longitude'] ) )
			$vValue = array( $vValue );

		foreach( ( array ) $vValue as $sKey => $aValue ) {
			$sClassAttribute = $this->getCorrespondingArrayValue( $aField['class_attribute'], $sKey, $_aDefaultKeys['class_attribute'] );
			$sName = is_array( $aField['label'] ) ? "{$sFieldName}[{$sKey}]" : "{$sFieldName}";
			$nLatitude = isset( $aValue['latitude'] ) ? $aValue['latitude'] : 20;
			$nLongitude = isset( $aValue['longitude'] ) ? $aValue['longitude'] : 20; 
			$nElevation = isset( $aValue['elevation'] ) ? $aValue['elevation'] : null; 
			$sPlaceName = isset( $aValue['place_name'] ) ? $aValue['place_name'] : ''; 
			$sDisabled = ( $this->getCorrespondingArrayValue( $aField['vDisable'], $sKey ) ? "disabled='Disabled' " : '' );
			$aOutput[] = 
				"<div class='{$sFieldClassSelector}' id='field-{$sTagID}_{$sKey}'>"
					. "<div class='admin-page-framework-input-label-container geometry'>"
						. $this->getCorrespondingArrayValue( $aField['vBeforeInputTag'], $sKey, $_aDefaultKeys['vBeforeInputTag'] ) 
						. ( ( $sLabel = $this->getCorrespondingArrayValue( $aField['label'], $sKey, $_aDefaultKeys['label'] ) ) 
							? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $aField['labelMinWidth'], $sKey, $_aDefaultKeys['labelMinWidth'] ) . "px;'>{$sLabel}</span>" 
							: "" 
						)
						. "<div class='admin-page-framework-input-container'>"
							. "<div class='gllpLatlonPicker'>"
								. "<div class='gllpMap map'>Google Maps</div>"
								. "<label for='{$sTagID}_{$sKey}_button' class='update-button'>"
									. "<input type='button' class='gllpUpdateButton button button-small' id='{$sTagID}_{$sKey}_button' value='" . __( 'Update Map', 'admin-page-framework-demo' ) . "' {$sDisabled} />"
								. "</label>"								
								. "<label for='{$sTagID}_{$sKey}_latitude'>"
									. "<span class='inline-label-text'>" . __( 'Latitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLatitude {$sClassAttribute}' id='{$sTagID}_{$sKey}_latitude' name='{$sName}[latitude]' value='{$nLatitude}' {$sDisabled}/>"
								. "</label><br />"
								. "<label for='{$sTagID}_{$sKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Longitude', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLongitude {$sClassAttribute}' id='{$sTagID}_{$sKey}_longitude' name='{$sName}[longitude]' value='{$nLongitude}' {$sDisabled}/>"
								. "</label><br />"
								. "<label for='{$sTagID}_{$sKey}_longitude'>"
									. "<span class='inline-label-text'>" . __( 'Elevation', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpElevation {$sClassAttribute}' id='{$sTagID}_{$sKey}_elevation' name='{$sName}[elevation]' value='{$nElevation}' {$sDisabled}/> " . __( "metres", "admin-page-framework-demo" )
								. "</label><br />"								
								. "<label for='{$sTagID}_{$sKey}_name'>"
									. "<span class='inline-label-text'>" . __( 'Place Name', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpLocationName {$sClassAttribute}' id='{$sTagID}_{$sKey}_place_name' name='{$sName}[place_name]' value='{$sPlaceName}' {$sDisabled}/> "
								. "</label><br />"
								. "<label for='{$sTagID}_{$sKey}_zoom'>"
									. "<span class='inline-label-text'>" . __( 'zoom', 'admin-page-framework-demo' ) . "</span>"
									. "<input type='text' class='gllpZoom' id='{$sTagID}_{$sKey}_zoom' value='3'/>"
								. "</label><br />"
							. "</div>"							
						. "</div>"
						. $this->getCorrespondingArrayValue( $aField['vAfterInputTag'], $sKey, $_aDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				
				. "</div>"
				. ( ( $sDelimiter = $this->getCorrespondingArrayValue( $aField['delimiter'], $sKey, $_aDefaultKeys['delimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$sTagID}_{$sKey}'>" . $sDelimiter . "</div>"
					: ""
				);
		}
					
		return "<div class='admin-page-framework-field-geometry' id='{$sTagID}'>" 
				. implode( '', $aOutput ) 
			. "</div>";
		
	}	
	
}