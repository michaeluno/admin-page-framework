<?php
abstract class AdminPageFramework_Form_Base extends AdminPageFramework_WPUtility {
    static public $_aResources = array('inline_styles' => array(), 'inline_styles_ie' => array(), 'inline_scripts' => array(), 'src_styles' => array(), 'src_scripts' => array(),);
    public function isSection($sID) {
        if ($this->isNumericInteger($sID)) {
            return false;
        }
        if (!array_key_exists($sID, $this->aSectionsets)) {
            return false;
        }
        if (!array_key_exists($sID, $this->aFieldsets)) {
            return false;
        }
        $_bIsSeciton = false;
        foreach ($this->aFieldsets as $_sSectionID => $_aFields) {
            if ($_sSectionID == $sID) {
                $_bIsSeciton = true;
            }
            if (array_key_exists($sID, $_aFields)) {
                return false;
            }
        }
        return $_bIsSeciton;
    }
    public function canUserView($sCapability) {
        if (!$sCapability) {
            return true;
        }
        return ( boolean )current_user_can($sCapability);
    }
    public function isInThePage() {
        static $_bInThePage;
        return isset($_bInThePage) ? $_bInThePage : $this->callBack($this->aCallbacks['is_in_the_page'], true);
    }
    public function callBack($oCallable, $asParameters) {
        $_aParameters = self::getAsArray($asParameters, true);
        $_mDefaultValue = self::getElement($_aParameters, 0);
        return is_callable($oCallable) ? call_user_func_array($oCallable, $_aParameters) : $_mDefaultValue;
    }
    public function __toString() {
        return $this->getObjectInfo($this);
    }
}