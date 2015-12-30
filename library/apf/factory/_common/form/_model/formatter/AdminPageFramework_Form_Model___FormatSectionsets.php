<?php
/**
 Admin Page Framework v3.7.8b02 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Form_Model___FormatSectionsets extends AdminPageFramework_Form_Base {
    public $sStructureType = '';
    public $aSectionsets = array();
    public $sCapability = '';
    public $aCallbacks = array('sectionset_before_output' => null);
    public $oCallerForm;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSectionsets, $this->sStructureType, $this->sCapability, $this->aCallbacks, $this->oCallerForm);
        $this->aSectionsets = $_aParameters[0];
        $this->sStructureType = $_aParameters[1];
        $this->sCapability = $_aParameters[2];
        $this->aCallbacks = $_aParameters[3];
        $this->oCallerForm = $_aParameters[4];
    }
    public function get() {
        if (empty($this->aSectionsets)) {
            return array();
        }
        $_aSectionsets = $this->_getSectionsetsFormatted(array(), $this->aSectionsets, array(), $this->sCapability);
        return $_aSectionsets;
    }
    private function _getSectionsetsFormatted($_aNewSectionsets, $aSectionsetsToParse, $aSectionPath, $sCapability) {
        foreach ($aSectionsetsToParse as $_sSectionPath => $_aSectionset) {
            if (!is_array($_aSectionset)) {
                continue;
            }
            $_aSectionPath = array_merge($aSectionPath, array($_aSectionset['section_id']));
            $_sSectionPath = implode('|', $_aSectionPath);
            $_aSectionsetFormatter = new AdminPageFramework_Form_Model___FormatSectionset($_aSectionset, $_sSectionPath, $this->sStructureType, $sCapability, count($_aNewSectionsets), $this->oCallerForm);
            $_aSectionset = $this->callBack($this->aCallbacks['sectionset_before_output'], array($_aSectionsetFormatter->get()));
            if (empty($_aSectionset)) {
                continue;
            }
            $_aNewSectionsets[$_sSectionPath] = $_aSectionset;
            $_aNewSectionsets = $this->_getNestedSections($_aNewSectionsets, $_aSectionset, $_aSectionPath, $_aSectionset['capability']);
        }
        uasort($_aNewSectionsets, array($this, 'sortArrayByKey'));
        return $_aNewSectionsets;
    }
    private function _getNestedSections($aSectionsetsToEdit, $aSectionset, $aSectionPath, $sCapability) {
        if (!$this->_hasNestedSections($aSectionset)) {
            return $aSectionsetsToEdit;
        }
        return $this->_getSectionsetsFormatted($aSectionsetsToEdit, $aSectionset['content'], $aSectionPath, $sCapability);
    }
    private function _hasNestedSections($aSectionset) {
        $aSectionset = $aSectionset + array('content' => null);
        if (!is_array($aSectionset['content'])) {
            return false;
        }
        $_aContents = $aSectionset['content'];
        $_aFirstItem = $this->getFirstElement($_aContents);
        return is_scalar($this->getElement($_aFirstItem, 'section_id', null));
    }
}