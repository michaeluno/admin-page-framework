<?php
abstract class AdminPageFramework_Controller_Form extends AdminPageFramework_Form_View {
    public function addSettingSections() {
        foreach (func_get_args() as $asSection) {
            $this->addSettingSection($asSection);
        }
        $this->_sTargetTabSlug = null;
        $this->_sTargetSectionTabSlug = null;
    }
    public function addSettingSection($asSection) {
        if (!is_array($asSection)) {
            $this->_sTargetPageSlug = is_string($asSection) ? $asSection : $this->_sTargetPageSlug;
            return;
        }
        $aSection = $asSection;
        $this->_sTargetPageSlug = $this->oUtil->getElement($aSection, 'page_slug', $this->_sTargetPageSlug);
        $this->_sTargetTabSlug = $this->oUtil->getElement($aSection, 'tab_slug', $this->_sTargetTabSlug);
        $this->_sTargetSectionTabSlug = $this->oUtil->getElement($aSection, 'section_tab_slug', $this->_sTargetSectionTabSlug);
        $aSection = $this->oUtil->uniteArrays($aSection, array('page_slug' => $this->_sTargetPageSlug, 'tab_slug' => $this->_sTargetTabSlug, 'section_tab_slug' => $this->_sTargetSectionTabSlug,));
        $aSection['page_slug'] = $aSection['page_slug'] ? $this->oUtil->sanitizeSlug($aSection['page_slug']) : ($this->oProp->sDefaultPageSlug ? $this->oProp->sDefaultPageSlug : null);
        $aSection['tab_slug'] = $this->oUtil->sanitizeSlug($aSection['tab_slug']);
        $aSection['section_tab_slug'] = $this->oUtil->sanitizeSlug($aSection['section_tab_slug']);
        if (!$aSection['page_slug']) {
            return;
        }
        $this->oForm->addSection($aSection);
    }
    public function removeSettingSections() {
        foreach (func_get_args() as $_sSectionID) {
            $this->oForm->removeSection($_sSectionID);
        }
    }
    public function addSettingFields() {
        foreach (func_get_args() as $aField) {
            $this->addSettingField($aField);
        }
    }
    public function addSettingField($asField) {
        $this->oForm->addField($asField);
    }
    public function removeSettingFields($sFieldID1, $sFieldID2 = null, $_and_more) {
        foreach (func_get_args() as $_sFieldID) {
            $this->oForm->removeField($_sFieldID);
        }
    }
    public function getValue() {
        $_aParams = func_get_args();
        $_aDimensionalKeys = $_aParams + array(null, null);
        $_mDefault = null;
        if (is_array($_aDimensionalKeys[0])) {
            $_mDefault = $_aDimensionalKeys[1];
            $_aDimensionalKeys = $_aDimensionalKeys[0];
        }
        return AdminPageFramework_WPUtility::getOption($this->oProp->sOptionKey, empty($_aParams) ? null : $_aDimensionalKeys, $_mDefault, $this->getSavedOptions() + $this->oProp->getDefaultOptions($this->oForm->aFields));
    }
    public function getFieldValue($sFieldID, $sSectionID = '') {
        trigger_error('Admin Page Framework: ' . ' : ' . sprintf(__('The method is deprecated: %1$s. Use %2$s instead.', $this->oProp->sTextDomain), __METHOD__, 'getValue()'), E_USER_WARNING);
        $_aOptions = $this->oUtil->uniteArrays($this->oProp->aOptions, $this->oProp->getDefaultOptions($this->oForm->aFields));
        if (!$sSectionID) {
            if (array_key_exists($sFieldID, $_aOptions)) {
                return $_aOptions[$sFieldID];
            }
            foreach ($_aOptions as $aOptions) {
                if (array_key_exists($sFieldID, $aOptions)) {
                    return $aOptions[$sFieldID];
                }
            }
        }
        if ($sSectionID) {
            if (array_key_exists($sSectionID, $_aOptions) && array_key_exists($sFieldID, $_aOptions[$sSectionID])) {
                return $_aOptions[$sSectionID][$sFieldID];
            }
        }
        return null;
    }
}