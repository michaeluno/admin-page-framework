<?php
class AdminPageFramework_Modifier_SortInput extends AdminPageFramework_Modifier_Base {
    public $aInput = array();
    public $aSortDimensionalKeys = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aInput, $this->aSortDimensionalKeys,);
        $this->aInput = $_aParameters[0];
        $this->aSortDimensionalKeys = array_unique($_aParameters[1]);
    }
    public function get() {
        foreach ($this->aSortDimensionalKeys as $_sFlatFieldAddress) {
            $_aDimensionalKeys = explode('|', $_sFlatFieldAddress);
            $_aDynamicElements = $this->getElement($this->aInput, $_aDimensionalKeys);
            if (!is_array($_aDynamicElements)) {
                continue;
            }
            $this->setMultiDimensionalArray($this->aInput, $_aDimensionalKeys, array_values($_aDynamicElements));
        }
        return $this->aInput;
    }
}