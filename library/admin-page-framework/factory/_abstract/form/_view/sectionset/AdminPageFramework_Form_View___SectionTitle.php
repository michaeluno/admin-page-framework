<?php
class AdminPageFramework_Form_View___SectionTitle extends AdminPageFramework_Form_View___Section_Base {
    public $aArguments = array('title' => null, 'tag' => null, 'section_index' => null,);
    public $aFieldsets = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $oMsg;
    public $aCallbacks = array('fieldset_output', 'is_fieldset_visible' => null,);
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aArguments, $this->aFieldsets, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->oMsg, $this->aCallbacks);
        $this->aArguments = $_aParameters[0] + $this->aArguments;
        $this->aFieldsets = $_aParameters[1];
        $this->aSavedData = $_aParameters[2];
        $this->aFieldErrors = $_aParameters[3];
        $this->aFieldTypeDefinitions = $_aParameters[4];
        $this->oMsg = $_aParameters[5];
        $this->aCallbacks = $_aParameters[6];
    }
    public function get() {
        return $this->_getSectionTitle($this->aArguments['title'], $this->aArguments['tag'], $this->aFieldsets, $this->aArguments['section_index'], $this->aFieldTypeDefinitions);
    }
    protected function _getSectionTitle($sTitle, $sTag, $aFieldsets, $iSectionIndex = null, $aFieldTypeDefinitions = array()) {
        $_aSectionTitleField = $this->_getSectionTitleField($aFieldsets, $iSectionIndex, $aFieldTypeDefinitions);
        return $_aSectionTitleField ? $this->getFieldsetOutput($_aSectionTitleField) : "<{$sTag}>" . $sTitle . "</{$sTag}>";
    }
    private function _getSectionTitleField(array $aFieldsetsets, $iSectionIndex, $aFieldTypeDefinitions) {
        foreach ($aFieldsetsets as $_aFieldsetset) {
            if ('section_title' !== $_aFieldsetset['type']) {
                continue;
            }
            $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput($_aFieldsetset, $iSectionIndex, $aFieldTypeDefinitions);
            return $_oFieldsetOutputFormatter->get();
        }
    }
}