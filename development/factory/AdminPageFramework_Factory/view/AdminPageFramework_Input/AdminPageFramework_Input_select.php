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
            foreach( $aLabels as $__sKey => $__asLabel ) {
                
                // For the optgroup tag,
                if ( is_array( $__asLabel ) ) {
                    $_aOutput[] = $this->_getOptGroup(
                        $aAttributes, 
                        $sInputID, 
                        $__sKey, 
                        $__asLabel
                    );
                    continue;
                }
                
                // The option tag,
                $_aOutput[] = $this->_getOptionTag( 
                    $__asLabel,   // the text label the user sees to be selected
                    $this->_getOptionTagAttributes( 
                        $aAttributes, 
                        $sInputID, 
                        $__sKey,
                        $this->getAsArray( $aAttributes['value'] )
                    ) 
                );
                    
            }
            return implode( PHP_EOL, $_aOutput );    
            
        }
            /**
             * Returns an HTML output of optgroup tag.
             * @since       3.5.3
             * @return      string      an HTML output of optgroup tag.
             */
            private function _getOptGroup( array $aAttributes, $sInputID, $sKey, $asLabel ) {
             
                $_aOptGroupAttributes = isset( $aAttributes['optgroup'][ $sKey ] ) && is_array( $aAttributes['optgroup'][ $sKey ] )
                    ? $aAttributes['optgroup'][ $sKey ] + $aAttributes['optgroup']
                    : $aAttributes['optgroup'];
                $_aOptGroupAttributes = array(
                    'label' => $sKey,
                ) + $_aOptGroupAttributes;
                return "<optgroup " . $this->generateAttributes( $_aOptGroupAttributes ) . ">"
                        . $this->_getDropDownList( $sInputID, $asLabel, $aAttributes )
                    . "</optgroup>";
             
            }
        
            /**
             * 
             * @since        3.5.3
             */
            private function _getOptionTagAttributes( array $aAttributes, $sInputID, $sKey, $aValues ) {
            
                $aValues   = isset( $aAttributes['option'][ $sKey ]['value'] )
                    ? $aAttributes['option'][ $sKey ]['value']
                    : $aValues;            
            
                return array(      
                        'id'        => $sInputID . '_' . $sKey,
                        'value'     => $sKey,
                        'selected'  => in_array( ( string ) $sKey, $aValues ) 
                            ? 'selected' 
                            : null,                                        
                    ) + ( isset( $aAttributes['option'][ $sKey ] ) && is_array( $aAttributes['option'][ $sKey ] )
                        ? $aAttributes['option'][ $sKey ] + $aAttributes['option']
                        : $aAttributes['option'] );
            
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