<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Model__FormSubmission__Validator__ContactFormConfirm extends AdminPageFramework_Model__FormSubmission__Validator__ContactForm {
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 40;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory)
    {
        if (! $this->_shouldProceed($oFactory, $aSubmits)) {
            return;
        }
        $this->oFactory->setLastInputs($aInputs);
        $this->oFactory->oProp->_bDisableSavingOptions = true;
        add_filter("options_update_status_{$this->oFactory->oProp->sClassName}", array( $this, '_replyToSetStatus' ));
        $_oException = new Exception('aReturn');
        $_oException->aReturn = $this->_confirmSubmitButtonAction($this->getElement($aSubmitInformation, 'input_name'), $this->getElement($aSubmitInformation, 'section_id'), 'email');
        throw $_oException;
    }
    protected function _shouldProceed($oFactory, $aSubmits)
    {
        if ($oFactory->hasFieldError()) {
            return false;
        }
        return ( bool ) $this->_getPressedSubmitButtonData($aSubmits, 'confirming_sending_email');
    }
    public function _replyToSetStatus($aStatus)
    {
        return array( 'confirmation' => 'email' ) + $aStatus;
    }
}
