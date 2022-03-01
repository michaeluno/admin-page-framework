<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Controller_Form extends AdminPageFramework_View_Form {
    public function addSettingSections()
    {
        foreach (func_get_args() as $asSection) {
            $this->addSettingSection($asSection);
        }
        $this->_sTargetTabSlug = null;
        $this->_sTargetSectionTabSlug = null;
    }
    public function addSettingSection($asSection)
    {
        if (! is_array($asSection)) {
            $this->_sTargetPageSlug = is_string($asSection) ? $asSection : $this->_sTargetPageSlug;
            return;
        }
        $aSection = $asSection;
        $this->_sTargetPageSlug = $this->_getTargetPageSlug($aSection);
        $this->_sTargetTabSlug = $this->_getTargetTabSlug($aSection);
        $this->_sTargetSectionTabSlug = $this->oUtil->getElement($aSection, 'section_tab_slug', $this->_sTargetSectionTabSlug);
        $aSection = $this->oUtil->uniteArrays($aSection, array( 'page_slug' => $this->_sTargetPageSlug, 'tab_slug' => $this->_sTargetTabSlug, 'section_tab_slug' => $this->_sTargetSectionTabSlug, ));
        $aSection[ 'section_tab_slug' ] = $this->oUtil->sanitizeSlug($aSection[ 'section_tab_slug' ]);
        if (! $aSection[ 'page_slug' ]) {
            return;
        }
        $this->oForm->addSection($aSection);
    }
    private function _getTargetPageSlug($aSection)
    {
        $_sTargetPageSlug = $this->oUtil->getElement($aSection, 'page_slug', $this->_sTargetPageSlug);
        $_sTargetPageSlug = $_sTargetPageSlug ? $this->oUtil->sanitizeSlug($_sTargetPageSlug) : $this->oProp->getCurrentPageSlugIfAdded();
        return $_sTargetPageSlug;
    }
    private function _getTargetTabSlug($aSection)
    {
        $_sTargetTabSlug = $this->oUtil->getElement($aSection, 'tab_slug', $this->_sTargetTabSlug);
        $_sTargetTabSlug = $_sTargetTabSlug ? $this->oUtil->sanitizeSlug($aSection[ 'tab_slug' ]) : $this->oProp->getCurrentInPageTabSlugIfAdded();
        return $_sTargetTabSlug;
    }
    public function removeSettingSections()
    {
        foreach (func_get_args() as $_sSectionID) {
            $this->oForm->removeSection($_sSectionID);
        }
    }
    public function addSettingFields()
    {
        foreach (func_get_args() as $aField) {
            $this->addSettingField($aField);
        }
    }
    public function addSettingField($asField)
    {
        $this->oForm->addField($asField);
    }
    public function removeSettingFields($sFieldID1, $sFieldID2=null, $_and_more=null)
    {
        foreach (func_get_args() as $_sFieldID) {
            $this->oForm->removeField($_sFieldID);
        }
    }
    public function getValue()
    {
        $_aParams = func_get_args();
        $_aDimensionalKeys = $_aParams + array( null, null );
        $_mDefault = null;
        if (is_array($_aDimensionalKeys[ 0 ])) {
            $_mDefault = $_aDimensionalKeys[ 1 ];
            $_aDimensionalKeys = $_aDimensionalKeys[ 0 ];
        }
        return AdminPageFramework_WPUtility::getOption($this->oProp->sOptionKey, empty($_aParams) ? null : $_aDimensionalKeys, $_mDefault, $this->getSavedOptions() + $this->oForm->getDefaultFormValues());
    }
    public function getFieldValue($sFieldID, $sSectionID='')
    {
        $this->oUtil->showDeprecationNotice('The method,' . __METHOD__ . ',', 'getValue()');
        $_aOptions = $this->oUtil->uniteArrays($this->oProp->aOptions, $this->oForm->getDefaultFormValues());
        if (! $sSectionID) {
            if (array_key_exists($sFieldID, $_aOptions)) {
                return $_aOptions[ $sFieldID ];
            }
            foreach ($_aOptions as $aOptions) {
                if (array_key_exists($sFieldID, $aOptions)) {
                    return $aOptions[ $sFieldID ];
                }
            }
        }
        if ($sSectionID) {
            if (array_key_exists($sSectionID, $_aOptions) && array_key_exists($sFieldID, $_aOptions[ $sSectionID ])) {
                return $_aOptions[ $sSectionID ][ $sFieldID ];
            }
        }
        return null;
    }
}
