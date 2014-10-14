<?php
if ( ! class_exists( 'DialCustomFieldType' ) ) :
class DialCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'dial', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        
        'attributes'    => array(
            'size'      => 10,
            'maxlength' => 400,
        ),    
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
            array( 'src' => dirname( __FILE__ ) . '/js/jquery.knob.js', 'dependencies' => array( 'jquery' ) ),
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
                jQuery().registerAPFCallback( {                
                
                    /**
                     * The repeatable field callback.
                     * 
                     * @param    object    oCopied
                     * @param    string    the field type slug
                     * @param    string    the field container tag ID
                     * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */
                    added_repeatable_field: function( oCopied, sFieldType, sFieldTagID, iCallType ) {            
                        /* If it is not this field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) {
                            return;
                        }

                        /* If the input tag is not found, do nothing  */
                        var oDialInput = oCopied.find( 'input.knob' );
                        if ( oDialInput.length <= 0 ) {
                            return;
                        }                                            
                        
                        // Find the wrapper element
                        var oWrapper = oDialInput.closest( 'label' ).children( 'div' );
                        
                        // Not sure why but it needs to be cloned again (the framework repeater script clones it internally though)
                        var oClone = oDialInput.clone();    
                        jQuery( oWrapper ).replaceWith( oClone );
                        
                        // Create the dial(knob).
                        oClone.knob();
                        
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
        return "
            .admin-page-framework-field-dial .admin-page-framework-input-label-container {
                padding-right: 1em;
                padding-bottom: 2em;
            }
            .sortable .admin-page-framework-field-dial .admin-page-framework-input-label-container {
                padding-right: 0;
                padding-bottom: 0;                
            }
            .admin-page-framework-field-dial .admin-page-framework-input-label-string {
                vertical-align: top;
            }
            .admin-page-framework-field-dial input[type='text'].knob {
                display: inline-block;
            }            
        ";
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 
            
        $aInputAttributes = array(
            'type'    => 'text',
        ) + $aField['attributes'];
        $aInputAttributes['class'] .= ' knob';

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $this->getDialEnablerScript( $aField['input_id'] )
            . $aField['after_label'];
        
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
endif;