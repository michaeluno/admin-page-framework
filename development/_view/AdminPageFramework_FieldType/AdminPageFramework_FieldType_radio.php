<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_radio extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'radio' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'label'         => array(),
        'attributes'    => array(),
    );
 
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Radio Field Type */
.admin-page-framework-field input[type='radio'] {
    margin-right: 0.5em;
}     
.admin-page-framework-field-radio .admin-page-framework-input-label-container {
    padding-right: 1em;
}     
.admin-page-framework-field-radio .admin-page-framework-input-container {
    display: inline;
}     
.admin-page-framework-field-radio .admin-page-framework-input-label-string  {
    display: inline; /* radio labels should not fold(wrap) after the check box */
}
CSSRULES;
    }

    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     */ 
    protected function getScripts() {

        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "     
            jQuery( document ).ready( function(){
                jQuery().registerAPFCallback( {     
                    added_repeatable_field: function( nodeField, sFieldType, sFieldTagID, sCallType ) {
         
                        /* If it is not the field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
                                                    
                        /* the checked state of radio buttons somehow lose their values so re-check them again */    
                        nodeField.closest( '.admin-page-framework-fields' )
                            .find( 'input[type=radio][checked=checked]' )
                            .attr( 'checked', 'checked' );
                            
                        /* Rebind the checked attribute updater */
                        nodeField.find( 'input[type=radio]' ).change( function() {
                            jQuery( this ).closest( '.admin-page-framework-field' )
                                .find( 'input[type=radio]' )
                                .attr( 'checked', false );
                            jQuery( this ).attr( 'checked', 'checked' );
                        });

                    }
                });
            });
        ";     
    }     
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
        
        $_aOutput   = array();
        $_sValue    = $aField['attributes']['value'];
        
        foreach( $aField['label'] as $_sKey => $_sLabel ) {

            /* Prepare attributes */
            $_aInputAttributes = array(
                'type'          => 'radio',
                'checked'       => $_sValue == $_sKey ? 'checked' : null,
                'value'         => $_sKey,
                'id'            => $aField['input_id'] . '_' . $_sKey,
                'data-default'  => $aField['default'],
            ) 
            + $this->getFieldElementByKey( $aField['attributes'], $_sKey, $aField['attributes'] )
            + $aField['attributes'];
            $_aLabelAttributes = array(
                'for'   => $_aInputAttributes['id'],
                'class' => $_aInputAttributes['disabled'] ? 'disabled' : null,
            );

            /* Insert the output */
            $_aOutput[] = 
                $this->getFieldElementByKey( $aField['before_label'], $_sKey )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->generateAttributes( $_aLabelAttributes ) . ">"
                        . $this->getFieldElementByKey( $aField['before_input'], $_sKey )
                        . "<span class='admin-page-framework-input-container'>"
                            . "<input " . $this->generateAttributes( $_aInputAttributes ) . " />" // this method is defined in the utility class    
                        . "</span>"
                        . "<span class='admin-page-framework-input-label-string'>"
                            . $_sLabel
                        . "</span>"    
                        . $this->getFieldElementByKey( $aField['after_input'], $_sKey )
                    . "</label>"
                . "</div>"
                . $this->getFieldElementByKey( $aField['after_label'], $_sKey )
                ;
                
        }
        $_aOutput[] = $this->_getUpdateCheckedScript( $aField['_field_container_id'] );
        return implode( PHP_EOL, $_aOutput );
            
    }
        /**
         * Returns the JavaScript script that updates the checked attribute of radio buttons when the user select one.
         * This helps repeatable field script that duplicate the last checked item.
         * @sinec       3.0.0
         */
        private function _getUpdateCheckedScript( $sFieldContainerID ) {
            return 
                "<script type='text/javascript' class='radio-button-checked-attribute-updater'>
                    jQuery( document ).ready( function(){
                        jQuery( '#{$sFieldContainerID} input[type=radio]' ).change( function() {
                            jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' ).attr( 'checked', false );
                            jQuery( this ).attr( 'checked', 'checked' );
                        });
                    });     
                </script>";     
            
        }    
}
endif;