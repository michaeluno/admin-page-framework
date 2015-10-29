<?php
class AdminPageFramework_Modifier_FilterRepeatableElements extends AdminPageFramework_Modifier_Base {
    public $aSubject = array();
    public $aDimensionalKeys = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubject, $this->aDimensionalKeys,);
        $this->aSubject = $_aParameters[0];
        $this->aDimensionalKeys = array_unique($_aParameters[1]);
    }
    public function get() {
        foreach ($this->aDimensionalKeys as $_sFlatFieldAddress) {
            $this->unsetDimensionalArrayElement($this->aSubject, explode('|', $_sFlatFieldAddress));
        }
        return $this->aSubject;
    }
}