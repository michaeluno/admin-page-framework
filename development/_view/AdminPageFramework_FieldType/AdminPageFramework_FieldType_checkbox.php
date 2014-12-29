<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the checkbox field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_checkbox extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'checkbox' );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
        'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here
    );
        
    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     */ 
    protected function getScripts() {
        new AdminPageFramework_Script_CheckboxSelector;
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    // Add the buttons.
    jQuery( '.admin-page-framework-checkbox-container[data-select_all_button]' ).each( function(){
        jQuery( this ).before( '<div class=\"select_all_button_container\" onclick=\"jQuery( this ).selectALLAPFCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_all_button' ) + '</a></div>' );
    });            
    jQuery( '.admin-page-framework-checkbox-container[data-select_none_button]' ).each( function(){
        jQuery( this ).before( '<div class=\"select_none_button_container\" onclick=\"jQuery( this ).deselectAllAPFCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_none_button' ) + '</a></div>' );
    });
});
JAVASCRIPTS;

    }    

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Checkbox field type */
.select_all_button_container, 
.select_none_button_container
{
    display: inline-block;
    margin-bottom: 0.4em;
}
.admin-page-framework-checkbox-label {
    margin-top: 0.1em;
}
.admin-page-framework-field input[type='checkbox'] {
    margin-right: 0.5em;
}     
.admin-page-framework-field-checkbox .admin-page-framework-input-label-container {
    padding-right: 1em;
}
.admin-page-framework-field-checkbox .admin-page-framework-input-label-string  {
    display: inline; /* Checkbox labels should not fold(wrap) after the check box */
}
CSSRULES;

    }
    
    /**
     * The class selector to indicate that the input tag is a admin page framework checkbox.
     * 
     * This selector is used for the repeatable and sortable field scripts.
     * @since   3.1.7
     */
    protected $_sCheckboxClassSelector = 'apf_checkbox';
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.0     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

        $_aOutput   = array();
        // $_asValue   = $aField['attributes']['value'];
        $_oCheckbox = new AdminPageFramework_Input_checkbox( $aField );
        
        foreach( $this->getAsArray( $aField['label'] ) as $_sKey => $_sLabel ) {
            
            $_aInputAttributes = $_oCheckbox->getAttributeArray( $_sKey );
            $_aInputAttributes['class'] = $this->generateClassAttribute( $_aInputAttributes['class'], $this->_sCheckboxClassSelector );
                   
            $_aOutput[] =
                $this->getFieldElementByKey( $aField['before_label'], $_sKey )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->generateAttributes( 
                        array(
                            'for'   => $_aInputAttributes['id'],
                            'class' => $_aInputAttributes['disabled'] ? 'disabled' : null,
                        ) 
                    ) 
                    . ">"
                        . $this->getFieldElementByKey( $aField['before_input'], $_sKey )
                        . $_oCheckbox->get( $_sLabel, $_aInputAttributes )
                        . $this->getFieldElementByKey( $aField['after_input'], $_sKey )
                    . "</label>"     
                . "</div>"
                . $this->getFieldElementByKey( $aField['after_label'], $_sKey );
                
        }    
        
        $_aCheckboxContainerAttributes = array(
            'class'                     => 'admin-page-framework-checkbox-container',
            'data-select_all_button'    => $aField['select_all_button'] 
                ? ( ! is_string( $aField['select_all_button'] ) ? $this->oMsg->get( 'select_all' ) : $aField['select_all_button'] )
                : null,
            'data-select_none_button'   => $aField['select_none_button'] 
                ? ( ! is_string( $aField['select_none_button'] ) ? $this->oMsg->get( 'select_none' ) : $aField['select_none_button'] )
                : null,
        );
        
        return "<div " . $this->generateAttributes( $_aCheckboxContainerAttributes ) . ">"
                . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . implode( PHP_EOL, $_aOutput )
            . "</div>";
            
    }    
    
}