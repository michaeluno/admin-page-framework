<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
    
        $aField['units'] = $this->getElement( $aField, 'units', $this->aDefaultUnits );
        
        // Base attributes
        $_aBaseAttributes       = $aField['attributes'];
        unset( $_aBaseAttributes['unit'], $_aBaseAttributes['size'] ); 
              
        /* 3. Return the output */
        return
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                . $this->_getNumberInputPart( $aField, $_aBaseAttributes )  // The size (number) part
                . $this->_getUnitSelectInput( $aField, $_aBaseAttributes )  // The unit (select) part
            . "</div>"
            . $aField['after_label'];             
        
    }
        /**
         * Returns the HTML output of the number input part.
         * 
         * @since       3.5.3
         * @return      string      The number input output.
         */
        private function _getNumberInputPart( array $aField, array $aBaseAttributes ) {
            
            // Size and Size Label
            $_aSizeAttributes       = $this->_getSizeAttributes( $aField, $aBaseAttributes );
            $_aSizeLabelAttributes  = array(
                'for'   => $_aSizeAttributes['id'],
                'class' => $_aSizeAttributes['disabled'] ? 'disabled' : null,
            );                  
            
            return "<label " . $this->generateAttributes( $_aSizeLabelAttributes ) . ">"
                . $this->getElement( $aField, array( 'before_label', 'size' ) )
                . ( $aField['label'] && ! $aField['repeatable']
                    ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" 
                            . $aField['label'] 
                        . "</span>"
                    : "" 
                )
                . "<input " . $this->generateAttributes( $_aSizeAttributes ) . " />" // this method is defined in the base class
                . $this->getElement( $aField, array( 'after_input', 'size' ) )
            . "</label>";            
            
        }
        
        /**
         * Returns the HTML output of the unit select input part.
         * 
         * @since       3.5.3
         * @return      string      The unit select input output.
         */
        private function _getUnitSelectInput( array $aField, array $aBaseAttributes ) {
            
            // Unit
            $_aUnitAttributes = $this->_getUnitAttributes( $aField, $aBaseAttributes );
        
            // Create a select input object
            $_oUnitInput = $this->_getUnitInputObject( $aField, $_aUnitAttributes );
            
            return "<label " . $this->generateAttributes( 
                    array(
                        'for'       => $_aUnitAttributes['id'],
                        'class'     => $_aUnitAttributes['disabled'] 
                            ? 'disabled' 
                            : null, 
                    ) 
                ) 
                . ">"
                . $this->getElement( $aField, array( 'before_label', 'unit' ) )
                . $_oUnitInput->get()
                . $this->getElement( $aField, array( 'after_input', 'unit' ) )
                . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
            . "</label>";
            
        }    
            /**
             * Returns an unit attribute array.
             * @since       3.5.3    
             * @return      array       an unit attribute array
             */
            private function _getUnitAttributes( array $aField, array $aBaseAttributes ) {
                
                $_bIsMultiple    = $aField['is_multiple'] 
                    ? true 
                    : ( $aField['attributes']['unit']['multiple'] 
                        ? true 
                        : false 
                    );
                return array(
                    'type'      => 'select',
                    'id'        => $aField['input_id'] . '_' . 'unit',
                    'multiple'  => $_bIsMultiple 
                        ? 'multiple' 
                        : null,
                    'name'      => $_bIsMultiple 
                        ? "{$aField['_input_name']}[unit][]" 
                        : "{$aField['_input_name']}[unit]",
                    'value'     => $this->getElement( 
                        $aField, 
                        array( 'value', 'unit' ), 
                        ''
                    ),
                )
                + $this->getElement( 
                    $aField, 
                    array( 'attributes', 'unit' ),
                    $this->aDefaultKeys['attributes']['unit'] 
                )
                + $aBaseAttributes;        
                
            }        
            /**
             * Returns a select input object for the unit select input part.
             * @since       3.5.3
             * @return      object      a select input object.
             */
            private function _getUnitInputObject( array $aField, array $aUnitAttributes ) {

                $_aUnitField = array( 
                    'label' => $aField['units'],
                ) + $aField;
                $_aUnitField['attributes']['select'] =  $aUnitAttributes;
                return new AdminPageFramework_Input_select( $_aUnitField );
            
            }             
        
    
        /**
         * Returns an size attribute array.
         * @since       3.5.3    
         * @return      array       an size attribute array
         */
        private function _getSizeAttributes( array $aField, array $aBaseAttributes ) {
            
            return array(
                    'type'  => 'number',
                    'id'    => $aField['input_id'] . '_' . 'size',
                    'name'  => $aField['_input_name'] . '[size]',
                    'value' => $this->getElement(
                        $aField,        // subject
                        array( 'value', 'size' ),   // dimensional keys
                        ''  // default
                    ),
                ) 
                + $this->getElement( 
                    $aField, 
                    array( 'attributes', 'size' ), 
                    $this->aDefaultKeys['attributes']['size'] 
                )
                + $aBaseAttributes;        
                
        }    
   
        
}