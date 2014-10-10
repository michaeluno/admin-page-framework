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
	 * Indicates whether the JavaScirpt script is inserted or not.
	 */
	private static $_bIsLoaded = false;
	
	/**
	 * Loads the field type necessary components.
	 */ 
	public function setUp() {
				
		if ( ! self::$_bIsLoaded ) {
            wp_enqueue_script( 'jquery' );
			self::$_bIsLoaded = add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddRevealerjQueryPlugin' ) );
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
			'id'	    => $aField['input_id'],
			'multiple'	=> $aField['is_multiple'] ? 'multiple' : $aField['attributes']['select']['multiple'],
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
            . $this->_getRevealerScript( $aField['input_id'] )
			. $this->_getConcealerScript( $aField['input_id'], $aField['label'], $aField['value'] )
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
		
		private function _getRevealerScript( $sInputID ) {
			return 
				"<script type='text/javascript' >
					jQuery( document ).ready( function(){
						jQuery( '#{$sInputID}' ).setRevealer();
					});				
				</script>";	
		}        
		private function _getConcealerScript( $sSelectorID, $aLabels, $asCurrentSelection ) {
			
            $_aCurrentSelection = $this->getAsArray( $asCurrentSelection );
            unset( $_aCurrentSelection['undefined'] );	// an internal reserved key	
            if( ( $_sKey = array_search( 'undefined' , $_aCurrentSelection) ) !== false ) {
                unset( $_aCurrentSelection[ $_sKey ] );
            }            
            $_sCurrentSelection = json_encode( $_aCurrentSelection );            
            
            unset( $aLabels['undefined'] );
            $aLabels    = array_keys( $aLabels );
			$_sLabels   = json_encode( $aLabels );	// encode it to be usable in JavaScript
			return 
				"<script type='text/javascript' class='admin-page-framework-revealer-field-type-concealer-script'>
					jQuery( document ).ready( function(){

						jQuery.each( {$_sLabels}, function( iIndex, sValue ) {

                            /* If it is a selected item, show it */
                            if ( jQuery.inArray( sValue, {$_sCurrentSelection} ) !== -1 ) { 
                                jQuery( sValue ).show();
                                return true;    // continue
                            }
                                                     
							jQuery( sValue ).hide();
                            
						});
                        jQuery( {$sSelectorID} ).trigger( 'change' );
					});				
				</script>";
				
		}

	/**
	 * Adds the revealer jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddRevealerjQueryPlugin() {
		        
		$sScript = "
		( function ( $ ) {
		    
            /**
             * Stores revealer settings
             */ 
            $.fn.aRevealerSettings = {};
            
            /**
             * Binds the revealer event to the element.
             */
			$.fn.setRevealer = function() {

                var _aSettings = {};
				this.change( function() {
                    
                    var _sTargetSelector    = jQuery( this ).val();
                    var _oElementToReveal   = jQuery( _sTargetSelector );                   
                    var sLastRevealedSelector = _aSettings.hasOwnProperty( 'last_revealed_selector' ) 
                        ? _aSettings['last_revealed_selector'] 
                        : undefined;
                    _aSettings['last_revealed_selector'] = _sTargetSelector;
                    
                    // Hide the previously hidden element.
                    $( sLastRevealedSelector ).hide();	
                    
                    if ( 'undefined' === _sTargetSelector ) { 
                        return; 
                    }
                    _oElementToReveal.show();                                       
                    
				});
                
			};
                        
		}( jQuery ));";
		
		echo "<script type='text/javascript' class='admin-page-framework-revealer-jQuery-plugin'>{$sScript}</script>";
		
	}		
	
}
endif;