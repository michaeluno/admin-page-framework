<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form input element of check boxes.
 * 
 * @package         AdminPageFramework
 * @subpackage      FormInput
 * @since           3.4.0       
 * @internal
 */
class AdminPageFramework_Input_checkbox extends AdminPageFramework_Input_Base {
    
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
                // the unchecked value must be set prior to the checkbox input field.
                . "<input " . $this->generateAttributes( 
                    array(
                        'type'      => 'hidden',
                        'class'     => $_aAttributes['class'],
                        'name'      => $_aAttributes['name'],
                        'value'     => '0',
                    ) 
                ) 
                . " />"
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
        $_sKey          = $_aParams[ 0 ];        
        
        // Result
        return 
            // Allows the user set attributes to overridden the system set attributes.
            $this->getElement( $this->aField['attributes'], $_sKey, array() )
            
            // The type needs to be specified since the postytpe field type extends this class. If not set, the 'posttype' will be passed to the type attribute.
            + array(
                'type'      => 'checkbox', 
                'id'        => $this->aField['input_id'] . '_' . $_sKey,
                'checked'   => $this->getCorrespondingArrayValue( $this->aField['attributes']['value'], $_sKey, null ) 
                    ? 'checked' 
                    : null,    // to not to set, pass null. An empty value '' will still set the attribute.
                'value'     => 1,   // this must be always 1 because the key value can be zero. In that case, the value always will be false and unchecked.
                'name'      => is_array( $this->aField['label'] ) 
                    ? "{$this->aField['attributes']['name']}[{$_sKey}]" 
                    : $this->aField['attributes']['name'],
                'data-id'   => $this->aField['input_id'],       // referenced by the JavaScript scripts such as the revealer script.
            )
            + $this->aField['attributes']
            ;

    }
    
}