<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     * @param       array       $aAttributes    The attribute array.  
     */    
    public function get( /* $sLabel, $aAttributes=array() */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 0 => '', 1 => array() );
        $_sLabel        = $_aParams[ 0 ];
        $_aAttributes   = $_aParams[ 1 ];
        
        // Output
        return 
            "<{$this->aOptions['input_container_tag']} " . $this->generateAttributes( $this->aOptions['input_container_attributes'] ) . ">"
                . "<input " . $this->generateAttributes( $_aAttributes ) . " />" 
            . "</{$this->aOptions['input_container_tag']}>"
            . "<{$this->aOptions['label_container_tag']} " . $this->generateAttributes( $this->aOptions['label_container_attributes'] ) . ">"
                . $_sLabel
            . "</{$this->aOptions['label_container_tag']}>"
            ;
        
    }    
    
    /**
     * Calculates and returns the attributes as an array.
     * 
     * @since       3.4.0
     */
    public function getAttributeArray( /* $sKey */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 0 => '', );
        $sKey           = $_aParams[ 0 ];        
        
        // Result
        return $this->getElement( $this->aField['attributes'], $sKey, array() )
            + array(
                'type'          => 'radio',
                'checked'       => isset( $this->aField['attributes']['value'] ) && $this->aField['attributes']['value'] == $sKey 
                    ? 'checked' 
                    : null,
                'value'         => $sKey,
                'id'            => $this->aField['input_id'] . '_' . $sKey,
                'data-default'  => $this->aField['default'],        // refered by the repeater script
                'data-id'       => $this->aField['input_id'],       // refered by the JavaScript scripts such as the revealer script.
            ) 
            + $this->aField['attributes']; 
        
    }
    
}