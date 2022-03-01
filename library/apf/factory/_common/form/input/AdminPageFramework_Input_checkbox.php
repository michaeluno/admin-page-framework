<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Input_checkbox extends AdminPageFramework_Input_Base {
    public $aOptions = array( 'save_unchecked' => true, );
    public function get()
    {
        $_aParams = func_get_args() + array( 0 => '', 1 => array() );
        $_sLabel = $_aParams[ 0 ];
        $_aAttributes = $this->uniteArrays($this->getElementAsArray($_aParams, 1, array()), $this->aAttributes);
        return "<{$this->aOptions[ 'input_container_tag' ]} " . $this->getAttributes($this->aOptions[ 'input_container_attributes' ]) . ">" . $this->_getInputElements($_aAttributes, $this->aOptions) . "</{$this->aOptions[ 'input_container_tag' ]}>" . "<{$this->aOptions[ 'label_container_tag' ]} " . $this->getAttributes($this->aOptions[ 'label_container_attributes' ]) . ">" . $_sLabel . "</{$this->aOptions[ 'label_container_tag' ]}>" ;
    }
    private function _getInputElements($aAttributes, $aOptions)
    {
        $_sOutput = $this->aOptions[ 'save_unchecked' ] ? "<input " . $this->getAttributes(array( 'type' => 'hidden', 'class' => $aAttributes[ 'class' ], 'name' => $aAttributes[ 'name' ], 'value' => '0', )) . " />" : '';
        $_sOutput .= "<input " . $this->getAttributes($aAttributes) . " />";
        return $_sOutput;
    }
    public function getAttributesByKey()
    {
        $_aParams = func_get_args() + array( 0 => '', );
        $_sKey = $_aParams[ 0 ];
        $_bIsMultiple = '' !== $_sKey;
        return $this->getElement($this->aAttributes, $_sKey, array()) + array( 'type' => 'checkbox', 'id' => $this->aAttributes[ 'id' ] . '_' . $_sKey, 'checked' => $this->_getCheckedAttributeValue($_sKey), 'value' => 1, 'name' => $_bIsMultiple ? "{$this->aAttributes[ 'name' ]}[{$_sKey}]" : $this->aAttributes[ 'name' ], 'data-id' => $this->aAttributes[ 'id' ], ) + $this->aAttributes ;
    }
    private function _getCheckedAttributeValue($_sKey)
    {
        $_aValueDimension = '' === $_sKey ? array( 'value' ) : array( 'value', $_sKey );
        return $this->getElement($this->aAttributes, $_aValueDimension) ? 'checked' : null;
    }
}
