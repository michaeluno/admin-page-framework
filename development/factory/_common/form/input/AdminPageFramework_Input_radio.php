<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form input element of radio buttons.
 * 
 * @package         AdminPageFramework
 * @subpackage      FormInput
 * @since           3.4.0       
 * @internal
 */
class AdminPageFramework_Input_radio extends AdminPageFramework_Input_Base {
    
    /**
     * Returns the output of the input element.
     * 
     * @since       3.4.0    
     * @param       string      $sLabel         The label text.
     * @param       array       $aAttributes    (optional) The attribute array. If set, it will be merged with the attribute set in the constructor.
     */    
    public function get( /* $sLabel, $aAttributes=array() */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 0 => '', 1 => array() );
        $_sLabel        = $_aParams[ 0 ];
        $_aAttributes   = $this->uniteArrays(
            $this->getElementAsArray( $_aParams, 1, array() ),
            $this->aAttributes 
        );
        
        // Output
        return 
            "<{$this->aOptions['input_container_tag']} " . $this->getAttributes( $this->aOptions['input_container_attributes'] ) . ">"
                . "<input " . $this->getAttributes( $_aAttributes ) . " />" 
            . "</{$this->aOptions['input_container_tag']}>"
            . "<{$this->aOptions['label_container_tag']} " . $this->getAttributes( $this->aOptions['label_container_attributes'] ) . ">"
                . $_sLabel
            . "</{$this->aOptions['label_container_tag']}>"
            ;
        
    }    
    
        
    /**
     * Generates an attribute array from the given key based on the attributes set in the constructor.
     * 
     * @return      array       The updated attribute array. 
     * @since       3.5.3
     * @param       string      $sKey       The array element key of the radio button. 
     * It is assumed that there is an array that holds multiple radio buttons and each of them has an array key.
     */
    public function getAttributesByKey( /* $sKey */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 0 => '', );
        $_sKey           = $_aParams[ 0 ];        
        
        // Result
        return $this->getElementAsArray( $this->aAttributes, $_sKey, array() )
            + array(
                'type'          => 'radio',
                'checked'       => isset( $this->aAttributes['value'] ) && $this->aAttributes['value'] == $_sKey 
                    ? 'checked' 
                    : null,
                'value'         => $_sKey,
                // 'id'            => $this->aField['input_id'] . '_' . $_sKey,
                'id'            => $this->getAttribute( 'id' ) . '_' . $_sKey,
                // 'data-default'  => $this->aField['default'],        // refered by the repeater script
                // 'data-id'       => $this->aField['input_id'],       // refered by the JavaScript scripts such as the revealer script.
                'data-id'       => $this->getAttribute( 'id' ),       // refered by the JavaScript scripts such as the revealer script.
            ) 
            + $this->aAttributes; 
            
    }
            
    
   
    
}