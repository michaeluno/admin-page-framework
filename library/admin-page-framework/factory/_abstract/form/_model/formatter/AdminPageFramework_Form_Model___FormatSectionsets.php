<?php
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
        return $this->_getSectionsetsFormatted($this->aSectionsets, $this->sStructureType, $this->sCapability);
    }
    private function _getSectionsetsFormatted(array $aSectionsets, $sStructureType, $sCapability) {
        $_aNewSectionsets = array();
        foreach ($aSectionsets as $_sSectionID => $_aSection) {
            if (!is_array($_aSection)) {
                continue;
            }
            $_aSectionFormatter = new AdminPageFramework_Form_Model___FormatSectionset($_aSection, $sStructureType, $sCapability, count($_aNewSectionsets), $this->oCallerForm);
            $_aSection = $this->callBack($this->aCallbacks['sectionset_before_output'], array($_aSectionFormatter->get()));
            if (empty($_aSection)) {
                continue;
            }
            $_aNewSectionsets[$_sSectionID] = $_aSection;
        }
        uasort($_aNewSectionsets, array($this, 'sortArrayByKey'));
        return $_aNewSectionsets;
    }
}