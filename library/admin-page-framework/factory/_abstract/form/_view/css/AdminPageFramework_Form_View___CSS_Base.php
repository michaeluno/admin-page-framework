<?php
class AdminPageFramework_Form_View___CSS_Base extends AdminPageFramework_WPUtility {
    public function get() {
        $_sCSSRules = $this->_get() . PHP_EOL;
        $_sCSSRules.= $this->_getVersionSpecific();
        return $this->isDebugMode() ? trim($_sCSSRules) : $this->minifyCSS($_sCSSRules);
    }
    protected function _get() {
        return '';
    }
    protected function _getVersionSpecific() {
        return '';
    }
}