<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the select field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_select extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'select', );
    
    /**
     * Defines the default key-values of this field type. 
     */
    protected $aDefaultKeys = array(
        'label'             => array(),
        'is_multiple'       => false,
        'attributes'        => array(
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
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Select Field Type */
.admin-page-framework-field-select .admin-page-framework-input-label-container {
    vertical-align: top; 
}
.admin-page-framework-field-select .admin-page-framework-input-label-container {
    padding-right: 1em;
}
CSSRULES;
    }
    
    
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5
     * @since       3.0.0       Removed unnecessary parameters.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
            
        $_oSelectInput = new AdminPageFramework_Input_select( $aField );
        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . $_oSelectInput->get()
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"     
            . "</div>"
            . $aField['after_label'];         
        
    }
        
}