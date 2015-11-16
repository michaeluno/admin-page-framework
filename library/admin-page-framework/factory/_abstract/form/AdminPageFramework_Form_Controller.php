<?php
class AdminPageFramework_Form_Controller extends AdminPageFramework_Form_View {
    public function addSection(array $aSectionset) {
        $aSectionset = $aSectionset + array('section_id' => null,);
        $aSectionset['section_id'] = $this->sanitizeSlug($aSectionset['section_id']);
        $this->aSectionsets[$aSectionset['section_id']] = $aSectionset;
        $this->aFieldsets[$aSectionset['section_id']] = $this->getElement($this->aFieldsets, $aSectionset['section_id'], array());
    }
    public function removeSection($sSectionID) {
        if ('_default' === $sSectionID) {
            return;
        }
        unset($this->aSectionsets[$sSectionID], $this->aFieldsets[$sSectionID]);
    }
    public function getResources($sKey) {
        return $this->getElement(self::$_aResources, $sKey);
    }
    public function setResources($sKey, $mValue) {
        return self::$_aResources[$sKey] = $mValue;
    }
    public function addResource($sKey, $sValue) {
        self::$_aResources[$sKey][] = $sValue;
    }
    protected $_sTargetSectionID = '_default';
    public function addField($asField) {
        if (!is_array($asField)) {
            $this->_sTargetSectionID = $this->getAOrB(is_string($asField), $asField, $this->_sTargetSectionID);
            return $this->_sTargetSectionID;
        }
        $_aField = $asField;
        $this->_sTargetSectionID = $this->getElement($_aField, 'section_id', $this->_sTargetSectionID);
        $_aField = array('_fields_type' => $this->aArguments['structure_type'], '_structure_type' => $this->aArguments['structure_type'],) + $_aField + array('section_id' => $this->_sTargetSectionID, 'class_name' => $this->aArguments['caller_id'],);
        if (!isset($_aField['field_id'], $_aField['type'])) {
            return null;
        }
        $_aField['field_id'] = $this->sanitizeSlug($_aField['field_id']);
        $_aField['section_id'] = $this->sanitizeSlug($_aField['section_id']);
        $this->aFieldsets[$_aField['section_id']][$_aField['field_id']] = $_aField;
        return $_aField;
    }
    public function removeField($sFieldID) {
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if (array_key_exists($sFieldID, $_aSubSectionsOrFields)) {
                unset($this->aFieldsets[$_sSectionID][$sFieldID]);
            }
            foreach ($_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields) {
                if ($this->isNumericInteger($_sIndexOrFieldID)) {
                    if (array_key_exists($sFieldID, $_aSubSectionOrFields)) {
                        unset($this->aFieldsets[$_sSectionID][$_sIndexOrFieldID]);
                    }
                    continue;
                }
            }
        }
    }
}