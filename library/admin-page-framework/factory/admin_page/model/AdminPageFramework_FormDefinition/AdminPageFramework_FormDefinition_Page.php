<?php
class AdminPageFramework_FormDefinition_Page extends AdminPageFramework_FormDefinition {
    protected $sDefaultPageSlug;
    protected $sOptionKey;
    protected $sClassName;
    protected $sCurrentPageSlug;
    protected $sCurrentTabSlug;
    public function isPageAdded($sPageSlug) {
        foreach ($this->aSections as $_sSectionID => $_aSection) {
            if (isset($_aSection['page_slug']) && $sPageSlug == $_aSection['page_slug']) {
                return true;
            }
        }
        return false;
    }
    public function getFieldsByPageSlug($sPageSlug, $sTabSlug = '') {
        return $this->castArrayContents($this->getSectionsByPageSlug($sPageSlug, $sTabSlug), $this->aFields);
    }
    public function getSectionsByPageSlug($sPageSlug, $sTabSlug = '') {
        $_aSections = array();
        foreach ($this->aSections as $_sSecitonID => $_aSection) {
            if ($sTabSlug && $_aSection['tab_slug'] != $sTabSlug) {
                continue;
            }
            if ($_aSection['page_slug'] != $sPageSlug) {
                continue;
            }
            $_aSections[$_sSecitonID] = $_aSection;
        }
        uasort($_aSections, array($this, '_sortByOrder'));
        return $_aSections;
    }
    public function getPageSlugBySectionID($sSectionID) {
        return isset($this->aSections[$sSectionID]['page_slug']) ? $this->aSections[$sSectionID]['page_slug'] : null;
    }
    public function setDefaultPageSlug($sDefaultPageSlug) {
        $this->sDefaultPageSlug = $sDefaultPageSlug;
    }
    public function setOptionKey($sOptionKey) {
        $this->sOptionKey = $sOptionKey;
    }
    public function setCallerClassName($sClassName) {
        $this->sClassName = $sClassName;
    }
    public function setCurrentPageSlug($sCurrentPageSlug) {
        $this->sCurrentPageSlug = $sCurrentPageSlug;
    }
    public function setCurrentTabSlug($sCurrentTabSlug) {
        $this->sCurrentTabSlug = $sCurrentTabSlug;
    }
    protected function formatSection(array $aSection, $sFieldsType, $sCapability, $iCountOfElements, $oCaller) {
        $aSection = $aSection + array('_fields_type' => $sFieldsType, 'capability' => $sCapability, 'page_slug' => $this->sDefaultPageSlug,);
        return parent::formatSection($aSection, $sFieldsType, $sCapability, $iCountOfElements, $oCaller);
    }
    protected function formatField($aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject) {
        $_aField = parent::formatField($aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject);
        if (!$_aField) {
            return;
        }
        $_aField['option_key'] = $this->sOptionKey;
        $_aField['class_name'] = $this->sClassName;
        $_aField['page_slug'] = $this->getElement($this->aSections, array($_aField['section_id'], 'page_slug'), null);
        $_aField['tab_slug'] = $this->getElement($this->aSections, array($_aField['section_id'], 'tab_slug'), null);
        $_aField['section_title'] = $this->getElement($this->aSections, array($_aField['section_id'], 'title'), null);
        return $_aField;
    }
    protected function getConditionedSection(array $aSection) {
        if (!current_user_can($aSection['capability'])) {
            return array();
        }
        if (!$aSection['if']) {
            return array();
        }
        if (!$aSection['page_slug']) {
            return array();
        }
        if ('options.php' != $this->getPageNow() && $this->sCurrentPageSlug != $aSection['page_slug']) {
            return array();
        }
        if (!$this->_isSectionOfCurrentTab($aSection, $this->sCurrentPageSlug, $this->sCurrentTabSlug)) {
            return array();
        }
        return $aSection;
    }
    private function _isSectionOfCurrentTab(array $aSection, $sCurrentPageSlug, $sCurrentTabSlug) {
        if ($aSection['page_slug'] != $sCurrentPageSlug) {
            return false;
        }
        return ($aSection['tab_slug'] == $sCurrentTabSlug);
    }
    public function getPageOptions($aOptions, $sPageSlug) {
        $_aOtherPageOptions = $this->getOtherPageOptions($aOptions, $sPageSlug);
        return $this->invertCastArrayContents($aOptions, $_aOtherPageOptions);
    }
    public function getPageOnlyOptions($aOptions, $sPageSlug) {
        $_aStoredOptionsOfThePage = array();
        foreach ($this->aFields as $_sSectionID => $_aSubSectionsOrFields) {
            if (!$this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
                continue;
            }
            $this->_setPageOnlyOptions($_aStoredOptionsOfThePage, $aOptions, $_aSubSectionsOrFields, $sPageSlug, $_sSectionID);
        }
        return $_aStoredOptionsOfThePage;
    }
    private function _setPageOnlyOptions(array & $_aStoredOptionsOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug, $_sSectionID) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aField) {
            if ($this->isNumericInteger($_sFieldID)) {
                if (array_key_exists($_sSectionID, $aOptions)) {
                    $_aStoredOptionsOfThePage[$_sSectionID] = $aOptions[$_sSectionID];
                }
                continue;
            }
            if ($sPageSlug !== $_aField['page_slug']) {
                continue;
            }
            if ('_default' !== $_aField['section_id']) {
                if (array_key_exists($_aField['section_id'], $aOptions)) {
                    $_aStoredOptionsOfThePage[$_aField['section_id']] = $aOptions[$_aField['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aField['field_id'], $aOptions)) {
                $_aStoredOptionsOfThePage[$_aField['field_id']] = $aOptions[$_aField['field_id']];
            }
        }
    }
    public function getOtherPageOptions($aOptions, $sPageSlug) {
        $_aStoredOptionsNotOfThePage = array();
        foreach ($this->aFields as $_sSectionID => $_aSubSectionsOrFields) {
            if ($this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
                continue;
            }
            $this->_setOtherPageOptions($_aStoredOptionsNotOfThePage, $aOptions, $_aSubSectionsOrFields, $sPageSlug);
        }
        return $_aStoredOptionsNotOfThePage;
    }
    private function _setOtherPageOptions(array & $_aStoredOptionsNotOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aField) {
            if ($this->isNumericInteger($_sFieldID)) {
                continue;
            }
            if ($sPageSlug === $_aField['page_slug']) {
                continue;
            }
            if ('_default' !== $_aField['section_id']) {
                if (array_key_exists($_aField['section_id'], $aOptions)) {
                    $_aStoredOptionsNotOfThePage[$_aField['section_id']] = $aOptions[$_aField['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aField['field_id'], $aOptions)) {
                $_aStoredOptionsNotOfThePage[$_aField['field_id']] = $aOptions[$_aField['field_id']];
            }
        }
    }
    public function getOtherTabOptions($aOptions, $sPageSlug, $sTabSlug) {
        $_aStoredOptionsNotOfTheTab = array();
        foreach ($this->aFields as $_sSectionID => $_aSubSectionsOrFields) {
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
            $_aField = $_aSubSectionOrField;
            if ($_aField['section_id'] !== '_default') {
                if (array_key_exists($_aField['section_id'], $aOptions)) {
                    $_aStoredOptionsNotOfTheTab[$_aField['section_id']] = $aOptions[$_aField['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aField['field_id'], $aOptions)) {
                $_aStoredOptionsNotOfTheTab[$_aField['field_id']] = $aOptions[$_aField['field_id']];
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
        foreach ($this->aFields as $_sSectionID => $_aSubSectionsOrFields) {
            if (!$this->_isThisSectionSetToThisTab($_sSectionID, $sPageSlug, $sTabSlug)) {
                continue;
            }
            $this->_setTabOnlyOptions($_aStoredOptionsOfTheTab, $aOptions, $_aSubSectionsOrFields, $_sSectionID);
        }
        return $_aStoredOptionsOfTheTab;
    }
    private function _setTabOnlyOptions(array & $_aStoredOptionsOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $_sSectionID) {
        foreach ($_aSubSectionsOrFields as $_sFieldID => $_aField) {
            if ($this->isNumericInteger($_sFieldID)) {
                if (array_key_exists($_sSectionID, $aOptions)) {
                    $_aStoredOptionsOfTheTab[$_sSectionID] = $aOptions[$_sSectionID];
                }
                continue;
            }
            if ('_default' !== $_aField['section_id']) {
                if (array_key_exists($_aField['section_id'], $aOptions)) {
                    $_aStoredOptionsOfTheTab[$_aField['section_id']] = $aOptions[$_aField['section_id']];
                }
                continue;
            }
            if (array_key_exists($_aField['field_id'], $aOptions)) {
                $_aStoredOptionsOfTheTab[$_aField['field_id']] = $aOptions[$_aField['field_id']];
                continue;
            }
        }
    }
    private function _isThisSectionSetToThisPage($_sSectionID, $sPageSlug) {
        if (!isset($this->aSections[$_sSectionID]['page_slug'])) {
            return false;
        }
        return ($sPageSlug === $this->aSections[$_sSectionID]['page_slug']);
    }
    private function _isThisSectionSetToThisTab($_sSectionID, $sPageSlug, $sTabSlug) {
        if (!$this->_isThisSectionSetToThisPage($_sSectionID, $sPageSlug)) {
            return false;
        }
        if (!isset($this->aSections[$_sSectionID]['tab_slug'])) {
            return false;
        }
        return ($sTabSlug === $this->aSections[$_sSectionID]['tab_slug']);
    }
}