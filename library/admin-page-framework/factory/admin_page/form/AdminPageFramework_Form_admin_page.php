<?php
class AdminPageFramework_Form_admin_page extends AdminPageFramework_Form {
    public function getPageOptions($aOptions, $sPageSlug) {
        $_aOtherPageOptions = $this->getOtherPageOptions($aOptions, $sPageSlug);
        return $this->invertCastArrayContents($aOptions, $_aOtherPageOptions);
    }
    public function getPageOnlyOptions($aOptions, $sPageSlug) {
        $_aStoredOptionsOfThePage = array();
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if (!$this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
                continue;
            }
            $this->_setPageOnlyOptions($_aStoredOptionsOfThePage, $aOptions, $_aSubSectionsOrFields, $sPageSlug, $_sSectionID);
        }
        return $_aStoredOptionsOfThePage;
    }
    private function _setPageOnlyOptions(array & $_aStoredOptionsOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug, $_sSectionID) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aFieldset) {
            if ($this->isNumericInteger($_sFieldID)) {
                if (array_key_exists($_sSectionID, $aOptions)) {
                    $_aStoredOptionsOfThePage[$_sSectionID] = $aOptions[$_sSectionID];
                }
                continue;
            }
            $_aFieldset = $_aFieldset + array('section_id' => null, 'field_id' => null, 'page_slug' => null,);
            if ($sPageSlug !== $_aFieldset['page_slug']) {
                continue;
            }
            if ('_default' !== $_aFieldset['section_id']) {
                if (array_key_exists($_aFieldset['section_id'], $aOptions)) {
                    $_aStoredOptionsOfThePage[$_aFieldset['section_id']] = $aOptions[$_aFieldset['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aFieldset['field_id'], $aOptions)) {
                $_aStoredOptionsOfThePage[$_aFieldset['field_id']] = $aOptions[$_aFieldset['field_id']];
            }
        }
    }
    public function getOtherPageOptions($aOptions, $sPageSlug) {
        $_aStoredOptionsNotOfThePage = array();
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if ($this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
                continue;
            }
            $this->_setOtherPageOptions($_aStoredOptionsNotOfThePage, $aOptions, $_aSubSectionsOrFields, $sPageSlug);
        }
        return $_aStoredOptionsNotOfThePage;
    }
    private function _setOtherPageOptions(array & $_aStoredOptionsNotOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aFieldset) {
            if ($this->isNumericInteger($_sFieldID)) {
                continue;
            }
            if ($sPageSlug === $_aFieldset['page_slug']) {
                continue;
            }
            if ('_default' !== $_aFieldset['section_id']) {
                if (array_key_exists($_aFieldset['section_id'], $aOptions)) {
                    $_aStoredOptionsNotOfThePage[$_aFieldset['section_id']] = $aOptions[$_aFieldset['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aFieldset['field_id'], $aOptions)) {
                $_aStoredOptionsNotOfThePage[$_aFieldset['field_id']] = $aOptions[$_aFieldset['field_id']];
            }
        }
    }
    public function getOtherTabOptions($aOptions, $sPageSlug, $sTabSlug) {
        $_aStoredOptionsNotOfTheTab = array();
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if ($this->_isThisSectionSetToThisTab($_sSectionID, $sPageSlug, $sTabSlug)) {
                continue;
            }
            $this->_setOtherTabOptions($_aStoredOptionsNotOfTheTab, $aOptions, $_aSubSectionsOrFields, $_sSectionID);
        }
        return $_aStoredOptionsNotOfTheTab;
    }
    private function _setOtherTabOptions(array & $_aStoredOptionsNotOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $_sSectionID) {
        foreach ($_aSubSectionsOrFields as $_isSubSectionIndexOrFieldID => $_aSubSectionOrField) {
            if ($this->isNumericInteger($_isSubSectionIndexOrFieldID)) {
                if (array_key_exists($_sSectionID, $aOptions)) {
                    $_aStoredOptionsNotOfTheTab[$_sSectionID] = $aOptions[$_sSectionID];
                }
                continue;
            }
            $_aFieldset = $_aSubSectionOrField;
            if ($_aFieldset['section_id'] !== '_default') {
                if (array_key_exists($_aFieldset['section_id'], $aOptions)) {
                    $_aStoredOptionsNotOfTheTab[$_aFieldset['section_id']] = $aOptions[$_aFieldset['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aFieldset['field_id'], $aOptions)) {
                $_aStoredOptionsNotOfTheTab[$_aFieldset['field_id']] = $aOptions[$_aFieldset['field_id']];
            }
        }
    }
    public function getTabOptions($aOptions, $sPageSlug, $sTabSlug = '') {
        $_aOtherTabOptions = $this->getOtherTabOptions($aOptions, $sPageSlug, $sTabSlug);
        return $this->invertCastArrayContents($aOptions, $_aOtherTabOptions);
    }
    public function getTabOnlyOptions(array $aOptions, $sPageSlug, $sTabSlug = '') {
        $_aStoredOptionsOfTheTab = array();
        if (!$sTabSlug) {
            return $_aStoredOptionsOfTheTab;
        }
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if (!$this->_isThisSectionSetToThisTab($_sSectionID, $sPageSlug, $sTabSlug)) {
                continue;
            }
            $this->_setTabOnlyOptions($_aStoredOptionsOfTheTab, $aOptions, $_aSubSectionsOrFields, $_sSectionID);
        }
        return $_aStoredOptionsOfTheTab;
    }
    private function _setTabOnlyOptions(array & $_aStoredOptionsOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $_sSectionID) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aFieldset) {
            if ($this->isNumericInteger($_sFieldID)) {
                if (array_key_exists($_sSectionID, $aOptions)) {
                    $_aStoredOptionsOfTheTab[$_sSectionID] = $aOptions[$_sSectionID];
                }
                continue;
            }
            if ('_default' !== $_aFieldset['section_id']) {
                if (array_key_exists($_aFieldset['section_id'], $aOptions)) {
                    $_aStoredOptionsOfTheTab[$_aFieldset['section_id']] = $aOptions[$_aFieldset['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aFieldset['field_id'], $aOptions)) {
                $_aStoredOptionsOfTheTab[$_aFieldset['field_id']] = $aOptions[$_aFieldset['field_id']];
                continue;
            }
        }
    }
    private function _isThisSectionSetToThisPage($_sSectionID, $sPageSlug) {
        if (!isset($this->aSectionsets[$_sSectionID]['page_slug'])) {
            return false;
        }
        return ($sPageSlug === $this->aSectionsets[$_sSectionID]['page_slug']);
    }
    private function _isThisSectionSetToThisTab($_sSectionID, $sPageSlug, $sTabSlug) {
        if (!$this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
            return false;
        }
        if (!isset($this->aSectionsets[$_sSectionID]['tab_slug'])) {
            return false;
        }
        return ($sTabSlug === $this->aSectionsets[$_sSectionID]['tab_slug']);
    }
}