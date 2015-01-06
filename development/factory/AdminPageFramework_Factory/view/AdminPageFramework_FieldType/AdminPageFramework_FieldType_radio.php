<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

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

        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery().registerAPFCallback( {     
        added_repeatable_field: function( nodeField, sFieldType, sFieldTagID, sCallType ) {

            /* If it is not the field type, do nothing. */
            if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { return; }
                                        
            /* the checked state of radio buttons somehow lose their values when repeated so re-check them again */    
            nodeField.closest( '.admin-page-framework-fields' )
                .find( 'input[type=radio][checked=checked]' )
                .attr( 'checked', 'checked' );
                
            /* Rebind the checked attribute updater */
            // @todo: for nested fields, only apply to the direct child container elements.
            nodeField.find( 'input[type=radio]' ).change( function() {
                jQuery( this ).closest( '.admin-page-framework-field' )
                    .find( 'input[type=radio]' )
                    .attr( 'checked', false );
                jQuery( this ).attr( 'checked', 'checked' );
            });

        }
    });
});
JAVASCRIPTS;

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
        $_oRadio    = new AdminPageFramework_Input_radio( $aField );

        foreach( $aField['label'] as $_sKey => $_sLabel ) {

            /* Prepare attributes */
            $_aInputAttributes = $_oRadio->getAttributeArray( $_sKey );

            /* Insert the output */
            $_aOutput[] = 
                $this->getFieldElementByKey( $aField['before_label'], $_sKey )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->generateAttributes( 
                            array(
                                'for'   => $_aInputAttributes['id'],
                                'class' => $_aInputAttributes['disabled'] ? 'disabled' : null,                            
                            )
                        ) 
                    . ">"
                        . $this->getFieldElementByKey( $aField['before_input'], $_sKey )
                        . $_oRadio->get( $_sLabel, $_aInputAttributes )
                        . $this->getFieldElementByKey( $aField['after_input'], $_sKey )
                    . "</label>"
                . "</div>"
                . $this->getFieldElementByKey( $aField['after_label'], $_sKey )
                ;
                
        }

        $_aOutput[] = $this->_getUpdateCheckedScript( $aField['input_id'] );
        return implode( PHP_EOL, $_aOutput );
            
    }
        /**
         * Returns the JavaScript script that updates the checked attribute of radio buttons when the user select one.
         * This helps repeatable field script that duplicate the last checked item.
         * @since       3.0.0
         * @since       3.4.0       Changed the parameter to accept input id from the container tag id to prepare for the support of nested fields.
         */
        private function _getUpdateCheckedScript( $sInputID ) {

            $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    jQuery( 'input[type=radio][data-id=\"{$sInputID}\"]' ).change( function() {
        // Uncheck the other radio buttons
        jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio][data-id=\"{$sInputID}\"]' ).attr( 'checked', false );

        // Make sure the clicked item is checked
        jQuery( this ).attr( 'checked', 'checked' );
    });
});                 
JAVASCRIPTS;
            return 
                "<script type='text/javascript' class='radio-button-checked-attribute-updater'>"
                    . $_sScript
                . "</script>";
            
        }    
}