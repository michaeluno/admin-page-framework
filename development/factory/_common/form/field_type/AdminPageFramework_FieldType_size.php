<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
        'is_multiple'           => false, // indicates whether the select tag alloes multiple selections.
        'units'                 => null,  // do not define units here since this will be merged with the user defined field array.
        'attributes'            => array(
            'size'      => array(
                'min'           => null,
                'max'           => null,
                'style'         => 'width: 160px;',
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
     * @since       3.5.9       Supports multiple labels.
     * @return      string
     */
    protected function getField( $aField ) {
                
        // Set the default units.
        $aField[ 'units' ] = $this->getElement( 
            $aField, 
            'units', 
            $this->aDefaultUnits 
        );

        $_aOutput = array();
        foreach( ( array ) $aField[ 'label' ] as $_isKey => $_sLabel ) {
            $_aOutput[] = $this->_getFieldOutputByLabel( 
                $_isKey, 
                $_sLabel,
                $aField
            );
        }
        return implode( '', $_aOutput );
                  
        
    }
        /**
         * @since       3.5.9
         * @return      string
         */
        protected function _getFieldOutputByLabel( $isKey, $sLabel, array $aField ) {
            
            $_bMultiLabels      = is_array( $aField[ 'label' ] );
            $_sLabel            = $this->getElementByLabel( $aField[ 'label' ], $isKey, $aField[ 'label' ] );
            $aField[ 'value' ]  = $this->getElementByLabel( $aField[ 'value' ], $isKey, $aField[ 'label' ] );

            $_aBaseAttributes   = $_bMultiLabels
                ? array(
                        'name'  => $aField[ 'attributes' ][ 'name' ] . "[{$isKey}]",
                        'id'    => $aField[ 'attributes' ][ 'id' ] . "_{$isKey}",
                        'value' => $aField[ 'value' ],
                    ) 
                    + $aField[ 'attributes' ]
                : $aField[ 'attributes' ];
            unset( 
                $_aBaseAttributes[ 'unit' ], 
                $_aBaseAttributes[ 'size' ] 
            );            
                     
            $_aOutput = array(
                $this->getElementByLabel( $aField[ 'before_label' ], $isKey, $aField[ 'label' ] ),
                    "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width: " . $this->sanitizeLength( $aField[ 'label_min_width' ] ) . ";'>",
                        $this->_getNumberInputPart( $aField, $_aBaseAttributes, $isKey, is_array( $aField[ 'label' ] ) ),  // The size (number) part
                        $this->_getUnitSelectInput( $aField, $_aBaseAttributes, $isKey, is_array( $aField[ 'label' ] ) ),  // The unit (select) part
                    "</div>",
                $this->getElementByLabel( $aField[ 'after_label' ], $isKey, $aField[ 'label' ] )
            );
            return implode( '', $_aOutput );                
                
        }
            /**
             * Returns the HTML output of the number input part.
             * 
             * @since       3.5.3
             * @return      string      The number input output.
             */
            private function _getNumberInputPart( array $aField, array $aBaseAttributes, $isKey, $bMultiLabels ) {
                
                // Size and Size Label
                $_aSizeAttributes       = $this->_getSizeAttributes( 
                    $aField, 
                    $aBaseAttributes,
                    $bMultiLabels
                        ? $isKey
                        : ''
                );

                $_aSizeLabelAttributes  = array(
                    'for'   => $_aSizeAttributes[ 'id' ],
                    'class' => $_aSizeAttributes[ 'disabled' ] 
                        ? 'disabled' 
                        : null,
                );                  
                
                $_sLabel                = $this->getElementByLabel( 
                    $aField[ 'label' ], 
                    $isKey, 
                    $aField[ 'label' ]
                );
                return "<label " . $this->getAttributes( $_aSizeLabelAttributes ) . ">"
                    . $this->getElement( 
                        $aField, 
                        $bMultiLabels
                            ? array( 'before_label', $isKey, 'size' ) 
                            : array( 'before_label', 'size' ) 
                    )
                    . ( $aField['label'] && ! $aField[ 'repeatable' ]
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField[ 'label_min_width' ] ) . ";'>" 
                                . $_sLabel 
                            . "</span>"
                        : "" 
                    )
                    . "<input " . $this->getAttributes( $_aSizeAttributes ) . " />" 
                    . $this->getElement( 
                        $aField, 
                        $bMultiLabels
                            ? array( 'after_input', $isKey, 'size' ) 
                            : array( 'after_input', 'size' )
                    )
                . "</label>";            
                
            }
            
            /**
             * Returns the HTML output of the unit select input part.
             * 
             * @since       3.5.3
             * @return      string      The unit select input output.
             */
            private function _getUnitSelectInput( array $aField, array $aBaseAttributes, $isKey, $bMultiLabels ) {
                
                // Unit (select input)
                $_aUnitAttributes = $this->_getUnitAttributes( 
                    $aField, 
                    $aBaseAttributes,
                    $bMultiLabels
                        ? $isKey
                        : ''                    
                );
            
                $_oUnitInput = new AdminPageFramework_Input_select(
                    $_aUnitAttributes + array( 
                        // the class will use the 'select' key of the attribute array to construct the select input.
                        'select' => $_aUnitAttributes  
                    )
                );
                $_aLabels = $bMultiLabels
                    ? $this->getElement( 
                        $aField, 
                        array( 'units', $isKey ),
                        $aField[ 'units' ]  // default - if the above keys are not set
                    )
                    : $aField[ 'units' ];
                
                return "<label " . $this->getAttributes( 
                        array(
                            'for'       => $_aUnitAttributes[ 'id' ],
                            'class'     => $_aUnitAttributes[ 'disabled' ] 
                                ? 'disabled' 
                                : null, 
                        ) 
                    ) 
                    . ">"
                    . $this->getElement( 
                        $aField, 
                        $bMultiLabels
                            ? array( 'before_label', $isKey, 'unit' ) 
                            : array( 'before_label', 'unit' )                                            
                    )
                    . $_oUnitInput->get( $_aLabels )
                    . $this->getElement( 
                        $aField, 
                        $bMultiLabels
                            ? array( 'after_input', $isKey, 'unit' ) 
                            : array( 'after_input', 'unit' )                    
                    )
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>";
                
            }    
                /**
                 * Returns an unit attribute array.
                 * @since       3.5.3    
                 * @return      array       an unit attribute array
                 */
                private function _getUnitAttributes( array $aField, array $aBaseAttributes, $isLabelKey='' ) {
                    
                    $_bIsMultiple    = $aField[ 'is_multiple' ] 
                        ? true 
                        : $this->getElement( 
                            $aField,
                            '' === $isLabelKey
                                ? array( 'attributes', 'unit', 'multiple' )
                                : array( 'attributes', $isLabelKey, 'unit', 'multiple' ),
                            false // default
                        );
              
                    $_aSelectAttributes = array(
                        'type'      => 'select',
                        'id'        => $aField[ 'input_id' ] . ( '' === $isLabelKey ? '' : '_' . $isLabelKey ) . '_' . 'unit',
                        'multiple'  => $_bIsMultiple 
                            ? 'multiple' 
                            : null,
                        'name'      => $_bIsMultiple 
                            ? "{$aField['_input_name']}" . ( '' === $isLabelKey ? '' : '[' . $isLabelKey . ']' ) . "[unit][]" 
                            : "{$aField['_input_name']}" . ( '' === $isLabelKey ? '' : '[' . $isLabelKey . ']' ) . "[unit]",
                        'value'     => $this->getElement( 
                            $aField, 
                            array( 'value', 'unit' ),
                            ''
                        ),
                    )
                    + $this->getElement( 
                        $aField, 
                        '' === $isLabelKey
                            ? array( 'attributes', 'unit' )
                            : array( 'attributes', $isLabelKey, 'unit' ),
                        $this->aDefaultKeys['attributes']['unit'] 
                    )
                    + $aBaseAttributes;       
                    return $_aSelectAttributes;
                    
                }        
 
        
            /**
             * Returns an size attribute array.
             * @since       3.5.3    
             * @return      array       an size attribute array
             */
            private function _getSizeAttributes( array $aField, array $aBaseAttributes, $sLabelKey='' ) {

                return array(
                        'type'  => 'number',
                        'id'    => $aField['input_id'] . '_' . ( '' !== $sLabelKey ? $sLabelKey . '_' : '' ) . 'size',
                        'name'  => $aField[ '_input_name' ] . ( '' !== $sLabelKey ? "[{$sLabelKey}]" : '' ) . '[size]',
                        'value' => $this->getElement(
                            $aField,        // subject
                            array( 'value', 'size' ),   // dimensional keys
                            ''  // default
                        ),
                    ) 
                    + $this->getElementAsArray(
                        $aField, 
                        '' === $sLabelKey
                            ? array( 'attributes', 'size' )
                            : array( 'attributes', $sLabelKey, 'size' ),
                        $this->aDefaultKeys[ 'attributes' ][ 'size' ]
                    )
                    + $aBaseAttributes;        
                    
            }    
   
        
}
