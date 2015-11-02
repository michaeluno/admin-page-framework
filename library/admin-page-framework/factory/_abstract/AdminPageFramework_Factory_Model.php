<?php
abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router {
    protected function _setUp() {
        $this->setUp();
    }
    static private $_aFieldTypeDefinitions = array();
    protected function _loadFieldTypeDefinitions() {
        if (empty(self::$_aFieldTypeDefinitions)) {
            self::$_aFieldTypeDefinitions = AdminPageFramework_FieldTypeRegistration::register(array(), $this->oProp->sClassName, $this->oMsg);
        }
        $this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilters($this, array('field_types_admin_page_framework', "field_types_{$this->oProp->sClassName}",), self::$_aFieldTypeDefinitions);
    }
    protected function _registerFields(array $aFields) {
        foreach ($aFields as $_sSecitonID => $_aFields) {
            $_bIsSubSectionLoaded = false;
            foreach ($_aFields as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField) {
                if ($this->oUtil->isNumericInteger($_iSubSectionIndexOrFieldID)) {
                    if ($_bIsSubSectionLoaded) {
                        continue;
                    }
                    $_bIsSubSectionLoaded = true;
                    foreach ($_aSubSectionOrField as $_aField) {
                        $this->_registerField($_aField);
                    }
                    continue;
                }
                $_aField = $_aSubSectionOrField;
                $this->_registerField($_aField);
            }
        }
    }
    protected function _registerField(array $aField) {
        AdminPageFramework_FieldTypeRegistration::_setFieldResources($aField, $this->oProp, $this->oResource);
        if ($aField['help']) {
            $this->oHelpPane->_addHelpTextForFormFields($aField['title'], $aField['help'], $aField['help_aside']);
        }
        $_oCallableDoOnRegistration = $this->oUtil->getElement($this->oProp->aFieldTypeDefinitions, array($aField['type'], 'hfDoOnRegistration'));
        if (is_callable($_oCallableDoOnRegistration)) {
            call_user_func_array($_oCallableDoOnRegistration, array($aField));
        }
    }
    public function getSavedOptions() {
        return $this->oProp->aOptions;
    }
    public function getFieldErrors() {
        return $this->_getFieldErrors();
    }
    public function _getFieldErrors($sID = 'deprecated', $bDelete = true) {
        static $_aFieldErrors;
        $_sTransientKey = "apf_field_erros_" . get_current_user_id();
        $_sID = md5($this->oProp->sClassName);
        $_aFieldErrors = isset($_aFieldErrors) ? $_aFieldErrors : $this->oUtil->getTransient($_sTransientKey);
        if ($bDelete) {
            add_action('shutdown', array($this, '_replyToDeleteFieldErrors'));
        }
        return $this->oUtil->getElementAsArray($_aFieldErrors, $_sID, array());
    }
    protected function _isValidationErrors() {
        $_aFieldErrors = $this->oUtil->getElement($GLOBALS, array('aAdminPageFramework', 'aFieldErrors'));
        return !empty($_aFieldErrors) ? $_aFieldErrors : $this->oUtil->getTransient("apf_field_erros_" . get_current_user_id());
    }
    public function _replyToDeleteFieldErrors() {
        $this->oUtil->deleteTransient("apf_field_erros_" . get_current_user_id());
    }
    public function _replyToSaveFieldErrors() {
        if (!isset($GLOBALS['aAdminPageFramework']['aFieldErrors'])) {
            return;
        }
        $this->oUtil->setTransient("apf_field_erros_" . get_current_user_id(), $GLOBALS['aAdminPageFramework']['aFieldErrors'], 300);
    }
    public function _replyToSaveNotices() {
        if (!isset($GLOBALS['aAdminPageFramework']['aNotices'])) {
            return;
        }
        if (empty($GLOBALS['aAdminPageFramework']['aNotices'])) {
            return;
        }
        $this->oUtil->setTransient('apf_notices_' . get_current_user_id(), $GLOBALS['aAdminPageFramework']['aNotices']);
    }
    public function _setLastInput(array $aLastInput) {
        return $this->oUtil->setTransient('apf_tfd' . md5('temporary_form_data_' . $this->oProp->sClassName . get_current_user_id()), $aLastInput, 60 * 60);
    }
    protected function _getSortedInputs(array $aInput) {
        $_aDynamicFieldAddressKeys = array_unique(array_merge($this->oUtil->getElementAsArray($_POST, '__repeatable_elements_' . $this->oProp->sFieldsType, array()), $this->oUtil->getElementAsArray($_POST, '__sortable_elements_' . $this->oProp->sFieldsType, array())));
        if (empty($_aDynamicFieldAddressKeys)) {
            return $aInput;
        }
        $_oInputSorter = new AdminPageFramework_Modifier_SortInput($aInput, $_aDynamicFieldAddressKeys);
        return $_oInputSorter->get();
    }
}