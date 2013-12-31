<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'radio' );
	
	/**
	 * Defines the default key-values of this field type. 
	 */
	protected $aDefaultKeys = array(
		'label'			=> array(),
		'attributes'	=> array(
		),
	);

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
		return "";		
	}

	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetScripts() {
		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		return "			
			/*	The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
				renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
													
						/* the jQuery clone() method looses the checked state of radio buttons so re-check them again */	
						node.closest( '.admin-page-framework-fields' )
							.find( 'input[type=radio][checked=checked]' )
							.attr( 'checked', 'checked' );
									
					}
				});
			});
		";				
	}		
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 * @since			3.0.0			Removed unnecessary parameters.
	 */
	public function replyToGetField( $aField ) {
		
		$aOutput = array();
		$sValue = $aField['attributes']['value'];
		foreach( $aField['label'] as $sKey =>$sLabel ) {
			
			$aInputAttributes = array(
				'type'	=> 'radio',
				'checked'	=> $sValue == $sKey ? 'checked' : '',
				'value' => $sKey,
				'id' => $aField['input_id'] . '_' . $sKey,
				'data-default' => $aField['default'],
			) + (	isset( $aField['attributes'][ $sKey ] ) && is_array( $aField['attributes'][ $sKey ] )
				? $aField['attributes'][ $sKey ] + $aField['attributes']
				: $aField['attributes']
			);
			$aOutput[] = 
				$aField['before_field']
				. "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: {$aField['label_min_width']}px;'>"
					. "<label for='{$aInputAttributes['id']}'>"
						. $aField['before_input_tag']
						. "<span class='admin-page-framework-input-container'>"
							. "<input " . $this->getHTMLTagAttributesFromArray( $aInputAttributes ) . " />"	// this method is defined in the utility class	
						. "</span>"
						. "<span class='admin-page-framework-input-label-string'>"
							. $sLabel
						. "</span>"	
						. $aField['after_input_tag']
					. "</label>"
				. "</div>"
				. $aField['after_field'];
				
		}
		return implode( PHP_EOL, $aOutput );
			
	}

}
endif;