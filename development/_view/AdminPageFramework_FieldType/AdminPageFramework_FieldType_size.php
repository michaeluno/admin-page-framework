<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the size field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_size extends AdminPageFramework_FieldType_select {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'size', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'is_multiple'           => false,    
        'units'                 => null, // do not define units here since this will be merged with the user defined field array.
        'attributes'            => array(
            'size'      => array(
                'size'          => 10,
                'maxlength'     => 400,
                'min'           => null,
                'max'           => null,
            ),
            'unit'      => array(
                // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'multiple'      => null,
                'size'          => 1,
                'autofocusNew'  => null,
                'required'      => null,
            ),
            'optgroup'  => array(),
            'option'    => array(),     
        ),    
    );
    
    /**
     * Defines the default units.
     * 
     * This goes to the 'units' element of the field definition array.
     * 
     * @since       3.0.0
     */
    protected $aDefaultUnits = array(
        'px'    => 'px', // pixel
        '%'     => '%',  // percentage
        'em'    => 'em', // font size
        'ex'    => 'ex', // font height
        'in'    => 'in', // inch
        'cm'    => 'cm', // centimetre
        'mm'    => 'mm', // millimetre
        'pt'    => 'pt', // point
        'pc'    => 'pc', // pica
    );

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Size Field Type */
.admin-page-framework-field-size input {
    text-align: right;
}
.admin-page-framework-field-size select.size-field-select {
    vertical-align: 0px;     
}
.admin-page-framework-field-size label {
    width: auto;     
} 
.form-table td fieldset .admin-page-framework-field-size label {
    display: inline;
}
CSSRULES;
    }
    
    /**
     * Returns the output of the field type.
     *
     * Returns the size input fields. This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
     * Useful for theme developers.
     * 
     * @since       2.0.1
     * @since       2.1.5       Moved from AdminPageFramework_FormField. Changed the name from getSizeField().
     * @since       3.0.0       Reconstructed entirely which involves dropping unnecessary parameters and renaming keys in the field definition array.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
    
        /* 1. Initial set-up of the field definition array */
        $aField['units'] = isset( $aField['units'] ) 
            ? $aField['units']
            : $this->aDefaultUnits;
    
        /* 2. Prepare attributes */
        
        /* 2-1. Base attributes */
        $aBaseAttributes = $aField['attributes'];
        unset( $aBaseAttributes['unit'], $aBaseAttributes['size'] ); 
        
        /* 2-2. Size attributes */     
        $aSizeAttributes = array(
            'type'  => 'number',
            'id'    => $aField['input_id'] . '_' . 'size',
            'name'  => $aField['_input_name'] . '[size]',
            'value' => isset( $aField['value']['size'] ) ? $aField['value']['size'] : '',
        ) 
        + $this->getFieldElementByKey( $aField['attributes'], 'size', $this->aDefaultKeys['attributes']['size'] )
        + $aBaseAttributes;
        
        /* 2-3. Size label attributes */     
        $aSizeLabelAttributes = array(
            'for'   => $aSizeAttributes['id'],
            'class' => $aSizeAttributes['disabled'] ? 'disabled' : null,
        );
        
        /* 2-4. Unit attributes */   
        $_bIsMultiple    = $aField['is_multiple'] 
            ? true 
            : ( $aField['attributes']['unit']['multiple'] ? true : false );
        $_aUnitAttributes = array(
            'type'      => 'select',
            'id'        => $aField['input_id'] . '_' . 'unit',
            'multiple'  => $_bIsMultiple ? 'multiple' : null,
            'name'      => $_bIsMultiple ? "{$aField['_input_name']}[unit][]" : "{$aField['_input_name']}[unit]",
            'value'     => isset( $aField['value']['unit'] ) ? $aField['value']['unit'] : '',
        )
        + $this->getFieldElementByKey( $aField['attributes'], 'unit', $this->aDefaultKeys['attributes']['unit'] )
        + $aBaseAttributes;
               
        // Create a select input object
        $_aUnitField = array( 
            'label' => $aField['units'],
        ) + $aField;
        $_aUnitField['attributes']['select'] =  $_aUnitAttributes;
        $_oUnitInput = new AdminPageFramework_Input_select( $_aUnitField );
        
        /* 3. Return the output */
        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                /* The size (number) part */
                . "<label " . $this->generateAttributes( $aSizeLabelAttributes ) . ">"
                    . $this->getFieldElementByKey( $aField['before_label'], 'size' )
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aSizeAttributes ) . " />" // this method is defined in the base class
                    . $this->getFieldElementByKey( $aField['after_input'], 'size' )
                . "</label>"
                /* The unit (select) part */
                . "<label " . $this->generateAttributes( 
                        array(
                            'for'       => $_aUnitAttributes['id'],
                            'class'     => $_aUnitAttributes['disabled'] ? 'disabled' : null,                        
                        ) 
                    ) 
                    . ">"
                    . $this->getFieldElementByKey( $aField['before_label'], 'unit' )
                    . $_oUnitInput->get()
                    . $this->getFieldElementByKey( $aField['after_input'], 'unit' )
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"     
            . "</div>"
            . $aField['after_label'];             
        
    }

}