<?php
abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router {
    public function __construct($oProp) {
        parent::__construct($oProp);
        add_filter('field_types_admin_page_framework', array($this, '_replyToFilterFieldTypeDefinitions'));
    }
    protected function _setUp() {
        $this->setUp();
    }
    public function _replyToFieldsetReourceRegistration($aFieldset) {
        $aFieldset = $aFieldset + array('help' => null, 'title' => null, 'help_aside' => null,);
        if (!$aFieldset['help']) {
            return;
        }
        $this->oHelpPane->_addHelpTextForFormFields($aFieldset['title'], $aFieldset['help'], $aFieldset['help_aside']);
    }
    public function _replyToFilterFieldTypeDefinitions($aFieldTypeDefinitions) {
        return $this->oUtil->addAndApplyFilters($this, "field_types_{$this->oProp->sClassName}", $aFieldTypeDefinitions);
    }
    public function _replyToModifySectionsets($aSectionsets) {
        return $this->oUtil->addAndApplyFilter($this, "sections_{$this->oProp->sClassName}", $aSectionsets);
    }
    public function _replyToModifyFieldsets($aFieldsets, $aSectionsets) {
        foreach ($aFieldsets as $_sSectionID => $_aFields) {
            $aFieldsets[$_sSectionID] = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}_{$_sSectionID}", $_aFields);
        }
        $aFieldsets = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}", $aFieldsets);
        if (count($aFieldsets)) {
            $this->oProp->bEnableForm = true;
        }
        return $aFieldsets;
    }
    public function _replyToModifyFieldsetsDefinitions($aFieldsets) {
        return $this->oUtil->addAndApplyFilter($this, "field_definition_{$this->oProp->sClassName}", $aFieldsets);
    }
    public function _replyToModifyFieldsetDefinition($aFieldset) {
        $_sFieldPart = '_' . implode('_', $aFieldset['_field_path_array']);
        $_sSectionPart = implode('_', $aFieldset['_section_path_array']);
        $_sSectionPart = $this->oUtil->getAOrB('_default' === $_sSectionPart, '', '_' . $_sSectionPart);
        return $this->oUtil->addAndApplyFilter($this, "field_definition_{$this->oProp->sClassName}{$_sSectionPart}{$_sFieldPart}", $aFieldset, $aFieldset['_subsection_index']);
    }
    public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets) {
    }
    public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets) {
        if (empty($aFieldset)) {
            return $aFieldset;
        }
        return $aFieldset;
    }
    public function _replyToFormatSectionsetDefinition($aSectionset) {
        if (empty($aSectionset)) {
            return $aSectionset;
        }
        $aSectionset = $aSectionset + array('_fields_type' => $this->oProp->_sPropertyType, '_structure_type' => $this->oProp->_sPropertyType,);
        return $aSectionset;
    }
    public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed) {
        return $this->_isInThePage();
    }
    public function _replyToGetCapabilityForForm($sCapability) {
        return $this->oProp->sCapability;
    }
    public function _replyToGetSavedFormData() {
        return $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->oProp->aOptions);
    }
    public function getSavedOptions() {
        return $this->oForm->aSavedData;
    }
    public function getFieldErrors() {
        return $this->_getFieldErrors();
    }
    public function _getFieldErrors($sID = 'deprecated', $bDelete = true) {
        return $this->oForm->getFieldErrors();
    }
    protected function _isValidationErrors() {
        $_aFieldErrors = $this->oUtil->getElement($GLOBALS, array('aAdminPageFramework', 'aFieldErrors'));
        return !empty($_aFieldErrors) ? $_aFieldErrors : $this->oUtil->getTransient("apf_field_erros_" . get_current_user_id());
    }
    public function _replyToSaveFieldErrors() {
        if (!isset($GLOBALS['aAdminPageFramework']['aFieldErrors'])) {
            return;
        }
        $this->oUtil->setTransient("apf_field_erros_" . get_current_user_id(), $GLOBALS['aAdminPageFramework']['aFieldErrors'], 300);
    }
    public function _setLastInputs(array $aLastInputs) {
        return $this->oUtil->setTransient('apf_tfd' . md5('temporary_form_data_' . $this->oProp->sClassName . get_current_user_id()), $aLastInputs, 60 * 60);
    }
    public function _setLastInput($aLastInputs) {
        return $this->_setLastInputs($aLastInputs);
    }
}