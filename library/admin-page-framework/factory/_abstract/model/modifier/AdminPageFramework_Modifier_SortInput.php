<?php
class AdminPageFramework_Modifier_SortInput extends AdminPageFramework_Modifier_Base {
    public $aInput = array();
    public $aFieldAddresses = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aInput, $this->aFieldAddresses,);
        $this->aInput = $_aParameters[0];
        $this->aFieldAddresses = $_aParameters[1];
    }
    public function get() {
        foreach ($this->_getFormattedDimensionalKeys($this->aFieldAddresses) as $_sFlatFieldAddress) {
            $_aDimensionalKeys = explode('|', $_sFlatFieldAddress);
            $_aDynamicElements = $this->getElement($this->aInput, $_aDimensionalKeys);
            if (!is_array($_aDynamicElements)) {
                continue;
            }
            $this->setMultiDimensionalArray($this->aInput, $_aDimensionalKeys, array_values($_aDynamicElements));
        }
        return $this->aInput;
    }
    private function _getFormattedDimensionalKeys($aFieldAddresses) {
        $aFieldAddresses = $this->getAsArray($aFieldAddresses);
        $aFieldAddresses = array_unique($aFieldAddresses);
        arsort($aFieldAddresses);
        return $aFieldAddresses;
    }
}