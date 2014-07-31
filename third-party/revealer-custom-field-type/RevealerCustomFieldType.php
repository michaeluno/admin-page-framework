<?php
if ( ! class_exists( 'RevealerCustomFieldType' ) ) :
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
	public function setUp() {
		
		static $_bIsLoaded; 
		
		if ( ! $_bIsLoaded ) {
			add_action( 'admin_footer', array( $this, '_replyToAddRevealerjQueryPlugin' ) );
			$_bIsLoaded = true;
		}
		
	}	

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
		return "";
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
		$aSelectAttributes['name'] = empty( $aSelectAttributes['multiple'] ) ? $aField['_input_name'] : "{$aField['_input_name']}[]";

		return
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
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
			. $this->getConcealerScript( $aField['label'] )
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
		
		private function getRevealerScript( $sInputID ) {
			return 
				"<script type='text/javascript' >
					jQuery( document ).ready( function(){
						jQuery( '#{$sInputID}' ).reveal();
					});				
				</script>";	
		}
		private function getConcealerScript( $aLabels ) {
			
			unset( $aLabels['undefined'] );	// this is an internal reserved key	
			
			$aLabels = json_encode( array_keys( $aLabels ) );	// encode it to be usable in JavaScript
			return 
				"<script type='text/javascript' class='admin-page-framework-revealer-field-type-concealer-script'>
					jQuery( document ).ready( function(){
						jQuery.each( {$aLabels}, function( sKey, sValue ) {
							jQuery( sValue ).hide();
						});
					});				
				</script>";
				
		}

	/**
	 * Adds the revealer jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddRevealerjQueryPlugin() {
		
		$sScript = "
		(function ( $ ) {
		
			$.fn.reveal = function() {
				
				var aSettings = [];
				this.change( function() {
					
					var _sTargetSelector = jQuery( this ).val();
					var nodeElementToReveal = jQuery( _sTargetSelector );
					if ( _sTargetSelector == 'undefined' ) return;
					
					var sLastRevealedSelector = aSettings.hasOwnProperty( 'last_revealed_id' ) ? aSettings['last_revealed_id'] : undefined;
					aSettings['last_revealed_id'] = _sTargetSelector;
					$( sLastRevealedSelector ).hide();	// hide the previously hidden element.
					nodeElementToReveal.show();
				});
				
			};

		}( jQuery ));";
		
		echo "<script type='text/javascript' class='admin-page-framework-revealer-jQuery-plugin'>{$sScript}</script>";
		
	}		
	
}
endif;