<?php
if ( ! class_exists( 'SampleCustomFieldType' ) ) :
class SampleCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'sample', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark  $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        
        'attributes'    => array(
            'size'      => 10,
            'maxlength' => 400,
        ),    
        'label'         => array(),    // determines the elements of radio button.
        'reveal'        => array(),    // the keys should correspond the label key element.
        
    );

    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {}    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array(
            // array( 'src' => dirname( __FILE__ ) . '/js/jquery.knob.js', 'dependencies' => array( 'jquery' ) ),
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
        /*    The below function will be triggered when a new repeatable field is added. */
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

                        // var nodeFields = nodeField.closest( '.admin-page-framework-fields' );
                        
                        nodeField.nextAll().andSelf().each( function() {
                            
                            /* Update the hidden elements' ID */        
                            jQuery( this ).find( '.sample_hidden_element' ).incrementIDAttribute( 'id' );
                            
                            /* The checked states will be gone after updating the ID of radio buttons so re-check them again */    
                            jQuery( this ).find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'checked' );
                            
                            /* Rebind the event */    
                            jQuery( this ).find( 'input[type=radio]' ).change( function() {
                                jQuery( this ).closest( '.admin-page-framework-field' )
                                    .find( 'input[type=radio]' )
                                    .attr( 'checked', false );            
                                jQuery( this ).attr( 'checked', 'checked' );
                                revealSelection( jQuery( this ).attr( 'id' ) );
                            });
                        });                                
                    },
                    /**
                     * The repeatable field callback for the remove event.
                     * 
                     * @param    object    the field container element next to the removed field container.
                     * @param    string    the field type slug
                     * @param    string    the field container tag ID
                     * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */                            
                    removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID, sCallType ) {
                        
                        /* If it is not this field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;                        
                        
                        oNextFieldConainer.nextAll().andSelf().each( function() {
                            
                            /* Update the hidden elements' ID */        
                            jQuery( this ).find( '.sample_hidden_element' ).decrementIDAttribute( 'id' );
                                        
                            /* Rebind the event */    
                            jQuery( this ).find( 'input[type=radio]' ).change( function() {
                                jQuery( this ).closest( '.admin-page-framework-field' )
                                    .find( 'input[type=radio]' )
                                    .attr( 'checked', false );            
                                jQuery( this ).attr( 'checked', 'checked' );
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
     * Returns the output of the field type.
     * @return      string      The field output.
     */
    protected function getField( $aField ) { 

        $_aOutput   = array();
        $sValue     = $aField['attributes']['value'];
        foreach( $this->getAsArray( $aField['label'] ) as $sKey =>$sLabel ) {

            // Attributes
            $aInputAttributes = array(
                    'type'          => 'radio',
                    'checked'       => $sValue == $sKey ? 'checked' : null,
                    'value'         => $sKey,
                    'id'            => $aField['input_id'] . '_' . $sKey,
                    'data-default'  => $aField['default'],
                ) 
                + $this->getElement( $aField, array( 'attributes', $sKey ), $aField['attributes'] )
                + $aField['attributes'];
            $aLabelAttributes = array(
                'for'    => $aInputAttributes['id'],
                'class'  => $aInputAttributes['disabled'] ? 'disabled' : null,
            );
            
            // Output
            $_aOutput[] = $this->getElement( $aField, array( 'before_label', $sKey ) )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->generateAttributes( $aLabelAttributes ) . ">"
                        . $this->getElement( $aField, array( 'before_input', $sKey ) )
                        . "<span class='admin-page-framework-input-container'>"
                            . "<input " . $this->generateAttributes( $aInputAttributes ) . " />"
                        . "</span>"
                        . "<span class='admin-page-framework-input-label-string'>"
                            . $sLabel
                        . "</span>"    
                        . $this->getElement( $aField, array( 'after_input', $sKey ) )
                    . "</label>"
                . "</div>"
                . $this->getElement( $aField, array( 'after_label', $sKey ) )
                ;
                
        }
        
        // Hidden contents
        $_aOutput[] = $this->_getHiddenContents( $aField );
        
        // Revealer script
        $_aOutput[] = $this->getRevealerScript( 
            $aField['_field_container_id'], 
            $aField['input_id'] . '_' . $sValue 
        );
        
        // Result
        return implode( PHP_EOL, $_aOutput );
            
    }    
        /**
         * Returns a generated hidden HTML output which appears when a redio button is selected.
         * @since       3.5.3
         * @return      string      The generated hidden HTML output.
         */
        private function _getHiddenContents( array $aField ) {
            
            $_aOutput = array();
            foreach( $aField['reveal'] as $sKey => $sHiddenOutput ) {

                // the hidden array key should correspond to the label array.
                if ( ! isset( $aField['label'][ $sKey ] ) ) { 
                    continue;    
                }        
                $_aOutput[] =     /* Insert the output */
                    "<div " . $this->generateAttributes(
                        array(    
                            'style'    => 'display:none;',
                            'class'    => 'sample_hidden_element',
                            'id'       => "hidden-" . $aField['input_id'] . '_' . $sKey,    // hidden- + {input id}
                        )                    
                    ) . ">"
                        . $sHiddenOutput
                    . "</div>";
                    
            }
            return "<div class='sample_hidden_elements_container'>"
                    . implode( '', $_aOutput )
                . "</div>";            
            
        }
        /**
         * 
         * @return      string
         */
        private function getRevealerScript( $sFieldContainerID, $sDefaultSelectionID ) {
            return 
                "<script type='text/javascript'>
                    jQuery( document ).ready( function(){
                        jQuery( '#{$sFieldContainerID} input[type=radio]' ).change( function() {
                            jQuery( this ).closest( '.admin-page-framework-field' )
                                .find( 'input[type=radio]' )
                                .attr( 'checked', false );
                            jQuery( this ).attr( 'checked', 'checked' );
                            revealSelection( jQuery( this ).attr( 'id' ) );
                        });
                        revealSelection( '{$sDefaultSelectionID}' );    // do it for the default one
                    });                
                </script>";        
            
        }
    
}
endif;