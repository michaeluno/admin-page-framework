<?php
class AdminPageFramework_Form_View___Section extends AdminPageFramework_WPUtility {
    public $aSectionset = array();
    public $aFieldsetsPerSection = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array();
    public $oMsg;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSectionset, $this->aFieldsetsPerSection, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg,);
        $this->aSectionset = $this->getAsArray($_aParameters[0]);
        $this->aFieldsetsPerSection = $this->getAsArray($_aParameters[1]);
        $this->aSavedData = $this->getAsArray($_aParameters[2]);
        $this->aFieldErrors = $this->getAsArray($_aParameters[3]);
        $this->aFieldTypeDefinitions = $this->getAsArray($_aParameters[4]);
        $this->aCallbacks = $this->getAsArray($_aParameters[5]) + $this->aCallbacks;
        $this->oMsg = $_aParameters[6];
    }
    public function get() {
        $_iSectionIndex = $this->aSectionset['_index'];
        $_oTableCaption = new AdminPageFramework_Form_View___SectionCaption($this->aSectionset, $_iSectionIndex, $this->aFieldsetsPerSection, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        $_oSectionTableAttributes = new AdminPageFramework_Form_View___Attribute_SectionTable($this->aSectionset);
        $_oSectionTableBodyAttributes = new AdminPageFramework_Form_View___Attribute_SectionTableBody($this->aSectionset);
        $_aOutput = array();
        $_aOutput[] = "<table " . $_oSectionTableAttributes->get() . ">" . $_oTableCaption->get() . "<tbody " . $_oSectionTableBodyAttributes->get() . ">" . $this->_getSectionContent($_iSectionIndex) . "</tbody>" . "</table>";
        $_oSectionTableContainerAttributes = new AdminPageFramework_Form_View___Attribute_SectionTableContainer($this->aSectionset);
        return "<div " . $_oSectionTableContainerAttributes->get() . ">" . implode(PHP_EOL, $_aOutput) . "</div>";
    }
    private function _getSectionContent($_iSectionIndex) {
        if ($this->aSectionset['content']) {
            return "<tr>" . "<td>" . $this->aSectionset['content'] . "</td>" . "</tr>";
        }
        $_oFieldsets = new AdminPageFramework_Form_View___FieldsetRows($this->aFieldsetsPerSection, $_iSectionIndex, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldsets->get();
        return $this->getFieldsetRows($this->aFieldsetsPerSection, $_iSectionIndex);
    }
    public function getFieldsetRows(array $aFieldsetsPerSection, $iSectionIndex = null) {
        $_aOutput = array();
        foreach ($aFieldsetsPerSection as $_aFieldset) {
            $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput($_aFieldset, $iSectionIndex, $this->aFieldTypeDefinitions);
            $_aFieldset = $_oFieldsetOutputFormatter->get();
            $_oFieldsetRow = new AdminPageFramework_Form_View___FieldsetTableRow($_aFieldset, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
            $_aOutput[] = $_oFieldsetRow->get();
        }
        return implode(PHP_EOL, $_aOutput);
    }
}