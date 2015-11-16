<?php
class AdminPageFramework_Form_Model___FormatFieldsets extends AdminPageFramework_Form_Base {
    public $aSectionsets = array();
    public $aFieldsets = array();
    public $sStructureType = '';
    public $sCapability = '';
    public $aCallbacks = array('fieldset_before_output' => null);
    public $aSavedData = array();
    public $oCallerForm;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aFieldsets, $this->aSectionsets, $this->sStructureType, $this->aSavedData, $this->sCapability, $this->aCallbacks, $this->oCallerForm,);
        $this->aFieldsets = $_aParameters[0];
        $this->aSectionsets = $_aParameters[1];
        $this->sStructureType = $_aParameters[2];
        $this->aSavedData = $_aParameters[3];
        $this->sCapability = $_aParameters[4];
        $this->aCallbacks = $_aParameters[5];
        $this->oCallerForm = $_aParameters[6];
    }
    public function get() {
        $this->aFieldsets = $this->_getFieldsetsFormatted($this->aFieldsets, $this->aSectionsets, $this->sStructureType, $this->sCapability);
        return $this->_getDynamicElementsAdded();
    }
    private function _getDynamicElementsAdded() {
        $_oDynamicElements = new AdminPageFramework_Form_Model___FormatDynamicElements($this->aSectionsets, $this->aFieldsets, $this->aSavedData);
        return $_oDynamicElements->get();
    }
    private function _getFieldsetsFormatted(array $aFieldsets, array $aSectionsets, $sStructureType, $sCapability) {
        $_aNewFieldsets = array();
        foreach ($aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if (!isset($aSectionsets[$_sSectionID])) {
                continue;
            }
            $sCapability = $this->getElement($aSectionsets[$_sSectionID], 'capability', $sCapability);
            $_aNewFieldsets[$_sSectionID] = $this->getElementAsArray($_aNewFieldsets, $_sSectionID, array());
            $_abSectionRepeatable = $aSectionsets[$_sSectionID]['repeatable'];
            if (count($this->getIntegerKeyElements($_aSubSectionsOrFields)) || $_abSectionRepeatable) {
                foreach ($this->numerizeElements($_aSubSectionsOrFields) as $_iSectionIndex => $_aFieldsets) {
                    foreach ($_aFieldsets as $_aFieldset) {
                        $_iCountElement = count($this->getElementAsArray($_aNewFieldsets, array($_sSectionID, $_iSectionIndex), array()));
                        $_aFieldset = $this->_getFieldsetFormatted($_aFieldset, $aSectionsets, $sStructureType, $sCapability, $_iCountElement, $_iSectionIndex, $_abSectionRepeatable, $this->oCallerForm);
                        if (!empty($_aFieldset)) {
                            $_aNewFieldsets[$_sSectionID][$_iSectionIndex][$_aFieldset['field_id']] = $_aFieldset;
                        }
                    }
                    uasort($_aNewFieldsets[$_sSectionID][$_iSectionIndex], array($this, 'sortArrayByKey'));
                }
                continue;
            }
            $_aSectionedFields = $_aSubSectionsOrFields;
            foreach ($_aSectionedFields as $_sFieldID => $_aFieldset) {
                $_iCountElement = count($this->getElementAsArray($_aNewFieldsets, $_sSectionID, array()));
                $_aFieldset = $this->_getFieldsetFormatted($_aFieldset, $aSectionsets, $sStructureType, $sCapability, $_iCountElement, null, $_abSectionRepeatable, $this->oCallerForm);
                if (!empty($_aFieldset)) {
                    $_aNewFieldsets[$_sSectionID][$_aFieldset['field_id']] = $_aFieldset;
                }
            }
            uasort($_aNewFieldsets[$_sSectionID], array($this, 'sortArrayByKey'));
        }
        $this->_sortFieldsBySectionsOrder($_aNewFieldsets, $aSectionsets);
        return $this->callBack($this->aCallbacks['fieldsets_after_formatting'], array($_aNewFieldsets, $aSectionsets));
    }
    private function _sortFieldsBySectionsOrder(array & $aFieldsets, array $aSections) {
        if (empty($aSections) || empty($aFieldsets)) {
            return;
        }
        $_aSortedFields = array();
        foreach ($aSections as $_sSectionID => $_aSeciton) {
            if (isset($aFieldsets[$_sSectionID])) {
                $_aSortedFields[$_sSectionID] = $aFieldsets[$_sSectionID];
            }
        }
        $aFieldsets = $_aSortedFields;
    }
    private function _getFieldsetFormatted($aFieldset, $aSectionsets, $sStructureType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject) {
        if (!isset($aFieldset['field_id'], $aFieldset['type'])) {
            return;
        }
        $_oFieldsetFormatter = new AdminPageFramework_Form_Model___Format_Fieldset($aFieldset, $sStructureType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject);
        $_aFieldset = $this->callBack($this->aCallbacks['fieldset_before_output'], array($_oFieldsetFormatter->get(), $aSectionsets));
        return $this->callBack($this->aCallbacks['fieldset_after_formatting'], array($_aFieldset, $aSectionsets));
    }
}