<?php
class AdminPageFramework_Model_FormSubmission_Validator_ContactFormConfirm extends AdminPageFramework_Model_FormSubmission_Validator_ContactForm {
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 40;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory) {
        if ($this->oFactory->hasFieldError()) {
            return;
        }
        $_bConfirmingToSendEmail = ( bool )$this->_getPressedSubmitButtonData($aSubmits, 'confirming_sending_email');
        if (!$_bConfirmingToSendEmail) {
            return;
        }
        $this->oFactory->_setLastInput($aInputs);
        $this->oFactory->oProp->_bDisableSavingOptions = true;
        add_filter("options_update_status_{$this->oFactory->oProp->sClassName}", array($this, '_replyToSetStatus'));
        $_oException = new Exception('aReturn');
        $_oException->aReturn = $this->_confirmSubmitButtonAction($this->getElement($aSubmitInformation, 'input_name'), $this->getElement($aSubmitInformation, 'section_id'), 'email');
        throw $_oException;
    }
    public function _replyToSetStatus($aStatus) {
        return array('confirmation' => 'email') + $aStatus;
    }
}