<?php
abstract class AdminPageFramework_FormDefinition_Base extends AdminPageFramework_WPUtility {
    public function dropRepeatableElements(array $aOptions) {
        $_oFilterRepeatableElements = new AdminPageFramework_Modifier_FilterRepeatableElements($aOptions, $this->getElementAsArray($_POST, '__repeatable_elements_' . $this->sFieldsType));
        return $_oFilterRepeatableElements->get();
    }
    public function isSection($sID) {
        if ($this->isNumericInteger($sID)) {
            return false;
        }
        if (!array_key_exists($sID, $this->aSections)) {
            return false;
        }
        if (!array_key_exists($sID, $this->aFields)) {
            return false;
        }
        $_bIsSeciton = false;
        foreach ($this->aFields as $_sSectionID => $_aFields) {
            if ($_sSectionID == $sID) {
                $_bIsSeciton = true;
            }
            if (array_key_exists($sID, $_aFields)) {
                return false;
            }
        }
        return $_bIsSeciton;
    }
    public function getFieldsModel(array $aFields = array()) {
        $_aFieldsModel = array();
        $aFields = empty($aFields) ? $this->aFields : $aFields;
        foreach ($aFields as $_sSectionID => $_aFields) {
            if ($_sSectionID != '_default') {
                $_aFieldsModel[$_sSectionID] = $_aFields;
                continue;
            }
            foreach ($_aFields as $_sFieldID => $_aField) {
                $_aFieldsModel[$_aField['field_id']] = $_aField;
            }
        }
        return $_aFieldsModel;
    }
    public function applyFiltersToFields($oCaller, $sClassName) {
        foreach ($this->aConditionedFields as $_sSectionID => $_aSubSectionOrFields) {
            foreach ($_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField) {
                if ($this->isNumericInteger($_sIndexOrFieldID)) {
                    $_sSubSectionIndex = $_sIndexOrFieldID;
                    $_aFields = $_aSubSectionOrField;
                    $_sSectionSubString = $this->getAOrB('_default' == $_sSectionID, '', "_{$_sSectionID}");
                    foreach ($_aFields as $_aField) {
                        $this->aConditionedFields[$_sSectionID][$_sSubSectionIndex][$_aField['field_id']] = $this->addAndApplyFilter($oCaller, "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}", $_aField, $_sSubSectionIndex);
                    }
                    continue;
                }
                $_aField = $_aSubSectionOrField;
                $_sSectionSubString = $this->getAOrB('_default' == $_sSectionID, '', "_{$_sSectionID}");
                $this->aConditionedFields[$_sSectionID][$_aField['field_id']] = $this->addAndApplyFilter($oCaller, "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}", $_aField);
            }
        }
        $this->aConditionedFields = $this->addAndApplyFilter($oCaller, "field_definition_{$sClassName}", $this->aConditionedFields);
        $this->aConditionedFields = $this->formatFields($this->aConditionedFields, $this->sFieldsType, $this->sCapability);
    }
}