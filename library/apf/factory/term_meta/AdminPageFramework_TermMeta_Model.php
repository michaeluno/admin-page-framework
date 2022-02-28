<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_TermMeta_Model extends AdminPageFramework_TermMeta_Router {
    public function _replyToGetSavedFormData()
    {
        return array();
    }
    protected function _setOptionArray($iTermID=null, $_deprecated=null)
    {
        $this->oForm->aSavedData = $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->_getSavedTermMetas($iTermID, $this->oForm->aFieldsets));
    }
    private function _getSavedTermMetas($iTermID, array $aFieldsets)
    {
        $_oMetaData = new AdminPageFramework_TermMeta_Model___TermMeta($iTermID, $this->oForm->aFieldsets);
        return $_oMetaData->get();
    }
    public function _replyToValidateOptions($iTermID)
    {
        if (! $this->_shouldProceedValidation()) {
            return;
        }
        $_aSavedFormData = $this->_getSavedTermMetas($iTermID, $this->oForm->aFieldsets);
        $_aSubmittedFormData = $this->oForm->getSubmittedData($this->oForm->getHTTPRequestSanitized($_POST));
        $_aSubmittedFormData = $this->oUtil->addAndApplyFilters($this, 'validation_' . $this->oProp->sClassName, call_user_func_array(array( $this, 'validate' ), array( $_aSubmittedFormData, $_aSavedFormData, $this )), $_aSavedFormData, $this);
        $this->oForm->updateMetaDataByType($iTermID, $_aSubmittedFormData, $this->oForm->dropRepeatableElements($_aSavedFormData), $this->oForm->sStructureType);
    }
}
