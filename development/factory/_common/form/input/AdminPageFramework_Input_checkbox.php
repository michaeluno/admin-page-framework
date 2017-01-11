<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to output form input element of check boxes.
 * 
 * @package         AdminPageFramework/Common/Form/Input
 * @since           3.4.0       
 * @internal
 */
class AdminPageFramework_Input_checkbox extends AdminPageFramework_Input_Base {
    
    /**
     * @since       3.8.8
     */
    public $aOptions = array(
        'save_unchecked'    => true,
    );
    
    /**
     * Returns the output of the input element.
     * 
     * @since       3.4.0    
     * @param       string      $sLabel         The label text.
     * @param       array       $aAttributes    (optional) The attribute array. If set, it will be merged with the attribute set in the constructor.
     */    
    public function get( /* $sLabel, $aAttributes=array() */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 
            0 => '',            // 1st parameter
            1 => array()        // 2nd parameter
        );
        $_sLabel        = $_aParams[ 0 ];       // first parameter

        // Attributes
        $_aAttributes   = $this->uniteArrays(   // second parameter
            $this->getElementAsArray( $_aParams, 1, array() ),
            $this->aAttributes
        );

        // Output
        return 
           "<{$this->aOptions[ 'input_container_tag' ]} " . $this->getAttributes( $this->aOptions[ 'input_container_attributes' ] ) . ">"
                . $this->_getInputElements( $_aAttributes, $this->aOptions )
            . "</{$this->aOptions[ 'input_container_tag' ]}>"
            . "<{$this->aOptions[ 'label_container_tag' ]} " . $this->getAttributes( $this->aOptions[ 'label_container_attributes' ] ) . ">"
                . $_sLabel
            . "</{$this->aOptions[ 'label_container_tag' ]}>"
        ;
                        
    }        
        /**
         * @since       3.8.8
         * @internal   
         * @return      string
         */
        private function _getInputElements( $aAttributes, $aOptions ) {
            $_sOutput = $this->aOptions[ 'save_unchecked' ]
                // the unchecked value must be set prior to the checkbox input field.
                ? "<input " . $this->getAttributes( 
                    array(
                        'type'      => 'hidden',
                        'class'     => $aAttributes[ 'class' ],
                        'name'      => $aAttributes[ 'name' ],
                        'value'     => '0',
                    ) 
                ) . " />"
                : '';
            $_sOutput .= "<input " . $this->getAttributes( $aAttributes ) . " />";
            return $_sOutput;
        }
        
    /**
     * Generates an attribute array from the given key based on the attributes set in the constructor.
     * 
     * @return      array       The updated attribute array. 
     * @since       3.5.3
     */
    public function getAttributesByKey( /* $sKey */ ) {
        
        // Parameters
        $_aParams       = func_get_args() + array( 0 => '', );
        $_sKey          = $_aParams[ 0 ];        
        $_bIsMultiple   = '' !== $_sKey;
        
        // Result
        return 
            // Allows the user set attributes to override the system set attributes.
            $this->getElement( $this->aAttributes, $_sKey, array() )
            
            // The type needs to be specified since the postytpe field type extends this class. If not set, the 'posttype' will be passed to the type attribute.
            + array(
                'type'      => 'checkbox', 
                'id'        => $this->aAttributes[ 'id' ] . '_' . $_sKey,
                'checked'   => $this->_getCheckedAttributeValue( $_sKey ),
                'value'     => 1,   // this must be always 1 because the key value can be zero. In that case, the value always will be false and unchecked.
                'name'      => $_bIsMultiple 
                    ? "{$this->aAttributes[ 'name' ]}[{$_sKey}]" 
                    : $this->aAttributes[ 'name' ],
                'data-id'   => $this->aAttributes[ 'id' ],       // referenced by the JavaScript scripts such as the revealer script.
            )
            + $this->aAttributes
            ;
            
    }
        /**
         * @since       3.7.0
         * @return      string|null        If checked, `checked`; otherwize, `null`
         */
        private function _getCheckedAttributeValue( $_sKey ) {
            
            $_aValueDimension = '' === $_sKey
                ? array( 'value' )
                : array( 'value', $_sKey );
            return $this->getElement( $this->aAttributes, $_aValueDimension )
                ? 'checked' 
                : null;    // to not to set, pass null. An empty value '' will still set the attribute.            
        
        }
        
}
