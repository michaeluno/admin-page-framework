<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Model__FormSubmission__Validator__Reset extends AdminPageFramework_Model__FormSubmission__Validator_Base
{
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 30;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory)
    {
        if (! $this->_shouldProceed($oFactory, $aSubmits)) {
            return;
        }
        $_sKeyToReset = $this->_getPressedSubmitButtonData($aSubmits, 'reset_key');
        $_sKeyToReset = trim($_sKeyToReset);
        if (! $_sKeyToReset) {
            return;
        }
        $_oException = new Exception('aReturn');
        $_oException->aReturn = $this->_resetOptions($_sKeyToReset, $aInputs, $aSubmitInformation);
        throw $_oException;
    }
    protected function _shouldProceed($oFactory, $aSubmits)
    {
        return ! $oFactory->hasFieldError();
    }
    private function _resetOptions($sKeyToReset, array $aInputs, array $aSubmitInformation)
    {
        $this->_doResetActions($sKeyToReset, $aInputs, $aSubmitInformation);
        if (! $this->oFactory->oProp->sOptionKey) {
            return array();
        }
        if (in_array($sKeyToReset, array( '1', ), true)) {
            delete_option($this->oFactory->oProp->sOptionKey);
            return array();
        }
        $_aDimensionalKeys = explode('|', $sKeyToReset);
        $this->unsetDimensionalArrayElement($this->oFactory->oProp->aOptions, $_aDimensionalKeys);
        $this->unsetDimensionalArrayElement($aInputs, $_aDimensionalKeys);
        update_option($this->oFactory->oProp->sOptionKey, $this->oFactory->oProp->aOptions);
        $this->oFactory->setSettingNotice($this->oFactory->oMsg->get('specified_option_been_deleted'), 'updated');
        return $aInputs;
    }
    private function _doResetActions($sKeyToReset, $aInputs, $aSubmitInformation)
    {
        $sKeyToReset = '1' === $sKeyToReset ? '' : $sKeyToReset;
        $_sPageSlug = $aSubmitInformation[ 'page_slug' ];
        $_sTabSlug = $aSubmitInformation[ 'tab_slug' ];
        $_sFieldID = $aSubmitInformation[ 'field_id' ];
        $_sSectionID = $aSubmitInformation[ 'section_id' ];
        $this->addAndDoActions($this->oFactory, array( $_sSectionID ? "reset_{$this->oFactory->oProp->sClassName}_{$_sSectionID}_{$_sFieldID}" : "reset_{$this->oFactory->oProp->sClassName}_{$_sFieldID}", $_sSectionID ? "reset_{$this->oFactory->oProp->sClassName}_{$_sSectionID}" : null, $_sTabSlug ? "reset_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}" : null, "reset_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}", "reset_{$this->oFactory->oProp->sClassName}", ), $sKeyToReset, $aInputs, $this->oFactory, $aSubmitInformation);
    }
}
