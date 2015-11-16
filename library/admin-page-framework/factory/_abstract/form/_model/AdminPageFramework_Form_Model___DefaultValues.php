<?php
class AdminPageFramework_Form_Model___DefaultValues extends AdminPageFramework_WPUtility {
    public $aFieldsets = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aFieldsets,);
        $this->aFieldsets = $_aParameters[0];
    }
    public function get() {
        $_aDefaultOptions = array();
        foreach ($this->aFieldsets as $_sSectionID => $_aFieldsetsPerSection) {
            foreach ($_aFieldsetsPerSection as $_sFieldID => $_aFieldset) {
                $_vDefault = $this->_getDefautValue($_aFieldset);
                if (isset($_aFieldset['section_id']) && $_aFieldset['section_id'] != '_default') {
                    $_aDefaultOptions[$_aFieldset['section_id']][$_sFieldID] = $_vDefault;
                } else {
                    $_aDefaultOptions[$_sFieldID] = $_vDefault;
                }
            }
        }
        return $_aDefaultOptions;
    }
    private function _getDefautValue($aFieldset) {
        $_aSubFields = $this->getIntegerKeyElements($aFieldset);
        if (count($_aSubFields) == 0) {
            return $this->getElement($aFieldset, 'value', $this->getElement($aFieldset, 'default', null));
        }
        $_aDefault = array();
        array_unshift($_aSubFields, $aFieldset);
        foreach ($_aSubFields as $_iIndex => $_aField) {
            $_aDefault[$_iIndex] = $this->getElement($_aField, 'value', $this->getElement($_aField, 'default', null));
        }
        return $_aDefault;
    }
}