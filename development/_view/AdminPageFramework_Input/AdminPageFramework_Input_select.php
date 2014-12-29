<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form input element of drop-down list.
 * 
 * @package         AdminPageFramework
 * @subpackage      FormInput
 * @since           3.4.0       
 * @internal
 */
class AdminPageFramework_Input_select extends AdminPageFramework_Input_Base {
    
    /**
     * Represents the structure of the options array.
     * 
     * @since       3.4.0
     */
    public $aStructureOptions = array(
        'input_container_tag'          => 'span',
        'input_container_attributes'    => array(
            'class' => 'admin-page-framework-input-container',
        ),
        'label_container_tag'          => 'span',
        'label_container_attributes'    => array(
            'class' => 'admin-page-framework-input-label-string',
        ),        
    );
    
    /**
     * Returns the output of the input element.
     * 
     * @remark       This method should be overridden in each extended class.
     * @since        3.4.0     
     */    
    public function get() {

        $_bIsMultiple          = $this->aField['is_multiple'] 
            ? true
            : ( $this->aField['attributes']['select']['multiple'] ? true : false );
        $_aSelectTagAttributes = $this->uniteArrays(
            $this->aField['attributes']['select'],      // allowing the user set attributes override the system set attributes.
            array(
                'id'        => $this->aField['input_id'],
                'multiple'  => $_bIsMultiple ? 'multiple' : null,
                'name'      => $_bIsMultiple ? "{$this->aField['_input_name']}[]" : $this->aField['_input_name'] ,
                'data-id'   => $this->aField['input_id'],       // referenced by the JavaScript scripts such as the revealer script.
            )            
        );
     
        return  
            "<{$this->aOptions['input_container_tag']} " . $this->generateAttributes( $this->aOptions['input_container_attributes'] ) . ">"
                . "<select " . $this->generateAttributes( $_aSelectTagAttributes ) . " >"
                    . $this->_getDropDownList( 
                        $this->aField['input_id'], 
                        $this->getAsArray( $this->aField['label'] ),
                        $this->aField['attributes']
                    )
                . "</select>"
            . "</{$this->aOptions['input_container_tag']}>"
            ;
            
    }
    
       /**
         * Returns the option tags of the select field.
         * 
         * @since       2.0.0
         * @since       2.0.1       Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
         * @since       2.1.5       Added the $tag_id parameter.
         * @since       3.0.0       Reconstructed entirely.
         * @since       3.4.0  
         * @internal
         * @param       string      $sInputID           The input ID that will be the base of each generated option tag ID.
         * @param       array       $aLabels            The array holding labels.
         * @param       array       $aAttributes        The attribute arrays. Accepts the following arguments.
         * - optgroup
         * - option
         */     
        private function _getDropDownList( $sInputID, array $aLabels, array $aAttributes ) {
            
            $_aOutput   = array();
            $_aValues   = $this->getAsArray( $aAttributes['value'] );

            foreach( $aLabels as $__sKey => $__asLabel ) {
                
                // For the optgroup tag,
                if ( is_array( $__asLabel ) ) {
                
                    $_aOptGroupAttributes = isset( $aAttributes['optgroup'][ $__sKey ] ) && is_array( $aAttributes['optgroup'][ $__sKey ] )
                        ? $aAttributes['optgroup'][ $__sKey ] + $aAttributes['optgroup']
                        : $aAttributes['optgroup'];
                        
                    $_aOutput[] = "<optgroup label='{$__sKey}'" . $this->generateAttributes( $_aOptGroupAttributes ) . ">"
                            . $this->_getDropDownList( $sInputID, $__asLabel, $aAttributes )
                        . "</optgroup>";
                    continue;
                    
                }
                
                // For the option tag,
                $_sLabel    = $__asLabel;
                $_aValues   = isset( $aAttributes['option'][ $__sKey ]['value'] )
                    ? $aAttributes['option'][ $__sKey ]['value']
                    : $_aValues;
                $_aOutput[] = $this->_getOptionTag( 
                    $_sLabel,   // the text label the user sees to be selected
                    array(      // the attributes array. Here the id and value etc. are set.
                        'id'        => $sInputID . '_' . $__sKey,
                        'value'     => $__sKey,
                        'selected'  => in_array( ( string ) $__sKey, $_aValues ) 
                            ? 'selected' 
                            : null,                                        
                    ) + ( isset( $aAttributes['option'][ $__sKey ] ) && is_array( $aAttributes['option'][ $__sKey ] )
                        ? $aAttributes['option'][ $__sKey ] + $aAttributes['option']
                        : $aAttributes['option'] )     
                );
                    
            }
            return implode( PHP_EOL, $_aOutput );    
            
        }
            /**
             * 
             * @sicne       3.4.0
             */
            private function _getOptionTag( $sLabel, array $aAttributes=array() ) {
              
                return "<option " . $this->generateAttributes( $aAttributes ) . " >"    
                        . $sLabel
                    . "</option>";
                    
            }
    
}