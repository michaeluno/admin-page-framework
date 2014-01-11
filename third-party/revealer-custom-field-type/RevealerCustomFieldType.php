<?php
class RevealerCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'revealer', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		
		'is_multiple'	=> '',
		'attributes'	=> array(
			'select'	=> array(
				'size'	=> 1,
				'autofocusNew'	=> '',
				// 'form'	=> 		// this is still work in progress
				'multiple'	=> '',	// set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
				'required'	=> '',		
			),
			'optgroup'	=> array(),
			'option'	=> array(),
		),		
	);

	/**
	 * Loads the field type necessary components.
	 */ 
	public function setUp() {}	

	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 */
	protected function getEnqueuingScripts() { 
		return array(
			// array( 'src'	=> dirname( __FILE__ ) . '/js/jquery.knob.js', 'dependencies'	=> array( 'jquery' ) ),
		);
	}
	
	/**
	 * Returns an array holding the urls of enqueuing styles.
	 */
	protected function getEnqueuingStyles() { 
		return array();
	}			


	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 

		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		/*	The below function will be triggered when a new repeatable field is added. */
		return "
			jQuery( document ).ready( function(){
				
				revealSelection = function( sSelectedInputID ) {
					jQuery( '#hidden-' + sSelectedInputID ).siblings().hide();
					jQuery( '#hidden-' + sSelectedInputID ).show();
				}
				
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( nodeField, sFieldType, sFieldTagID ) {
			
						/* If it is not this field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

						/* If the input tag is not found, do nothing  */
						var nodeRadioButtons = nodeField.find( 'input[type=radio]' );
						if ( nodeRadioButtons.length <= 0 ) return;

						/* Update the hidden elements' ID */				
						nodeField.find( '.sample_hidden_element' ).incrementIDAttribute( 'id' );
						
						/* The checked states will be gone after updating the ID of radio buttons so re-check them again */	
						nodeField.closest( '.admin-page-framework-fields' )
							.find( 'input[type=radio][checked=checked]' )
							.attr( 'checked', 'Checked' );
						
						/* Rebind the event */
						jQuery.each( nodeRadioButtons, function( i, val ) {
							jQuery( this ).change( function() {
								jQuery( this ).closest( '.admin-page-framework-field' )
									.find( 'input[type=radio]' )
									.attr( 'checked', false );			
								jQuery( this ).attr( 'checked', 'Checked' );
								revealSelection( jQuery( this ).attr( 'id' ) );
							});
						});
								
					},
					
				});
			});		
		
		" . PHP_EOL;
		
	}

	/**
	 * Returns IE specific CSS rules.
	 */
	protected function getIEStyles() { return ''; }

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	protected function getStyles() {
		return "";
	}

	
	/**
	 * Returns the output of the geometry custom field type.
	 * 
	 */
	/**
	 * Returns the output of the field type.
	 */
	protected function getField( $aField ) { 
				
		$aSelectAttributes = array(
			'id'	=>	$aField['input_id'],
			'multiple'	=>	$aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
		) + $aField['attributes']['select'];
		$aSelectAttributes['name'] = empty( $aSelectAttributes['multiple'] ) ? $aField['field_name'] : "{$aField['field_name']}[]";

		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: {$aField['label_min_width']}px;'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. "<span class='admin-page-framework-input-container'>"
						. "<select " . $this->generateAttributes( $aSelectAttributes ) . " >"
							. $this->_getOptionTags( $aField['input_id'], $aField['attributes'], $aField['label'] )
						. "</select>"
					. "</span>"
					. $aField['after_input']
					. "<div class='repeatable-field-buttons'></div>"	// the repeatable field buttons will be replaced with this element.
				. "</label>"					
			. "</div>"
			. $aField['after_label']
			. $this->getRevealerScript( $aField['input_id'] )
			;
		
	}
		protected function _getOptionTags( $sInputID, &$aAttributes, $aLabel ) {
			
			$aOutput = array();
			$aValue = ( array ) $aAttributes['value'];

			foreach( $aLabel as $sKey => $asLabel ) {
				
				// For the optgroup tag,
				if ( is_array( $asLabel ) ) {	// optgroup
				
					$aOptGroupAttributes = isset( $aAttributes['optgroup'][ $sKey ] ) && is_array( $aAttributes['optgroup'][ $sKey ] )
						? $aAttributes['optgroup'][ $sKey ] + $aAttributes['optgroup']
						: $aAttributes['optgroup'];
						
					$aOutput[] = 
						"<optgroup label='{$sKey}'" . $this->generateAttributes( $aOptGroupAttributes ) . ">"
						. $this->_getOptionTags( $sInputID, $aAttributes, $asLabel )
						. "</optgroup>";
					continue;
					
				}
				
				// For the option tag,
				$aValue = isset( $aAttributes['option'][ $sKey ]['value'] )
					? $aAttributes['option'][ $sKey ]['value']
					: $aValue;
				
				$aOptionAttributes = array(
					'id'	=> $sInputID . '_' . $sKey,
					'value'	=> $sKey,
					'selected'	=> in_array( ( string ) $sKey, $aValue ) ? 'Selected' : '',
				) + ( isset( $aAttributes['option'][ $sKey ] ) && is_array( $aAttributes['option'][ $sKey ] )
					? $aAttributes['option'][ $sKey ] + $aAttributes['option']
					: $aAttributes['option']
				);

				$aOutput[] =
					"<option " . $this->generateAttributes( $aOptionAttributes ) . " >"	
						. $asLabel
					. "</option>";
					
			}
			return implode( PHP_EOL, $aOutput );	
			
		}
		
		private function getRevealerScript( $sInputID, $sDefaultSelectionID='' ) {
			return 
				"<script type='text/javascript' class=''>
					jQuery( document ).ready( function(){
						jQuery( '#{$sInputID}' ).change( function() {
							// jQuery( this ).closest( '.admin-page-framework-field' )
								// .find( 'select' )
								// .attr( 'selected', false );
							// jQuery( this ).attr( 'selected', 'Selected' );
							revealSelection( jQuery( this ).attr( 'id' ) );
						});
						// revealSelection( '{$sDefaultSelectionID}' );	// do it for the default one
					});				
				</script>";		
			
		}
	
}