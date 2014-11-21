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
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'select_type'   => 'select',        // accepts 'radio', 'checkbox'
        'is_multiple'   => false,
        'attributes'    => array(
            'select'    => array(
                'size'          => 1,
                'autofocusNew'  => null,
                'multiple'      => null,    // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'required'      => null,        
            ),
            'optgroup'  => array(),
            'option'    => array(),
        ),        
    );
    
    /**
     * Indicates whether the JavaScirpt script is inserted or not.
     */
    private static $_bIsLoaded = false;
    
    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {
                
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
            // array( 'src'    => dirname( __FILE__ ) . '/js/jquery.knob.js', 'dependencies'    => array( 'jquery' ) ),
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
        
        $_aOutput   = array();        
        $aField     = $this->_sanitizeInnerFieldArray( $aField );
        $_aOutput[] = $this->geFieldOutput( $aField );
        $_aOutput[] = $this->_getRevealerScript( $aField['input_id'] );
        switch( $aField['select_type'] ) {
            default:
            case 'select':
            case 'radio':                          
                $_aOutput[] = $this->_getConcealerScript( $aField['input_id'], $aField['label'], $aField['value'] );
                break;
                
            case 'checkbox':
                $_aSelections = is_array( $aField['value'] )
                    ? array_keys( array_filter( $aField['value'] ) )
                    : $aField['label'];                  
                $_aOutput[] = $this->_getConcealerScript( $aField['input_id'], $aField['label'], $_aSelections );
                break;
  
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
        
        /**
         * Sanitize (re-format) the field definition array to get the field output by the select type.
         * 
         * @since       3.4.0
         */
        private function _sanitizeInnerFieldArray( array $aField ) {
            
            // The revealer field type has its own description element.
            unset( $aField['description'] );
            
            // The revealer script of checkboxes needs the reference of the selector to reveal. 
            // For radio and select input types, the key of the label array can be used but for the checkbox input type, 
            // the value attribute needs to be always 1 (for cases of key of zero '0') so the selector needs to be separately stored.
            switch( $aField['select_type'] ) {
                default:
                case 'select':
                case 'radio': 
                    break;
                case 'checkbox':
                    foreach( $this->getAsArray( $aField['label'] ) as $_sSelector => $_sLabel ) {
                        $aField['attributes'][ $_sSelector ] = array(
                                'data-reveal'   => $_sSelector,
                            ) 
                            + $this->getElementAsArray( $aField['attributes'], $_sSelector, array() );
                    }
                    break;
      
            }
                
            // Set the select_type to the type argument.
            return array( 
                    'type' => $aField['select_type'] 
                ) + $aField;
            
        }
        
        private function _getRevealerScript( $sInputID ) {
            return 
                "<script type='text/javascript' >
                    jQuery( document ).ready( function(){
                        jQuery('*[data-id=\"{$sInputID}\"]').setRevealer();
                    });                
                </script>";    
        }        
        private function _getConcealerScript( $sSelectorID, $aLabels, $asCurrentSelection ) {
            
            $aLabels            = $this->getAsArray( $aLabels );
            $_aCurrentSelection = $this->getAsArray( $asCurrentSelection );
            unset( $_aCurrentSelection['undefined'] );    // an internal reserved key    
            if( ( $_sKey = array_search( 'undefined' , $_aCurrentSelection) ) !== false ) {
                unset( $_aCurrentSelection[ $_sKey ] );
            }            
            $_sCurrentSelection = json_encode( $_aCurrentSelection );            
            
            unset( $aLabels['undefined'] );
            $aLabels    = array_keys( $aLabels );
            $_sLabels   = json_encode( $aLabels );    // encode it to be usable in JavaScript
            return 
                "<script type='text/javascript' class='admin-page-framework-revealer-field-type-concealer-script'>
                    jQuery( document ).ready( function(){

                        jQuery.each( {$_sLabels}, function( iIndex, sValue ) {

                            /* If it is the selected item, show it */
                            if ( jQuery.inArray( sValue, {$_sCurrentSelection} ) !== -1 ) { 
                                jQuery( sValue ).show();
                                return true;    // continue
                            }
                            
                            jQuery( sValue ).hide();
                                
                        });
                        jQuery( 'select[data-id=\"{$sSelectorID}\"], input:checked[type=radio][data-id=\"{$sSelectorID}\"], input:checked[type=checkbox][data-id=\"{$sSelectorID}\"]' )
                            .trigger( 'change' );
                    });                
                </script>";
                
        }

    /**
     * Adds the revealer jQuery plugin.
     * @since            3.0.0
     */
    public function _replyToAddRevealerjQueryPlugin() {
                
        $_sScript = "
        ( function ( $ ) {
            
            /**
             * Binds the revealer event to the element.
             */
            $.fn.setRevealer = function() {

                var _sLastRevealedSelector;
                this.change( function() {

                    // For checkboxes       
                    if ( $( this ).is(':checkbox') ) {
                        var _sTargetSelector        = $( this ).data( 'reveal' );
                        var _oElementToReveal       = $( _sTargetSelector );
                        if ( $( this ).is( ':checked' ) ) {
                            _oElementToReveal.show();
                        } else {
                            _oElementToReveal.hide();    
                        }                      
                        return;
                    }
                    
                    // For other types (select and radio).
                    var _sTargetSelector        = $( this ).val();
                    var _oElementToReveal       = $( _sTargetSelector );

                    // Hide the previously hidden element.
                    $( _sLastRevealedSelector ).hide();    
                                        
                    // Store the last revealed item in the local and the outer local variables.
                    _sLastRevealedSelector = _sTargetSelector;
                    
                    if ( 'undefined' === _sTargetSelector ) { 
                        return; 
                    }
                    _oElementToReveal.show();                                       
                    
                });
                
            };
                        
        }( jQuery ));";
        
        echo "<script type='text/javascript' class='admin-page-framework-revealer-jQuery-plugin'>{$_sScript}</script>";
        
    }        
    
}
endif;