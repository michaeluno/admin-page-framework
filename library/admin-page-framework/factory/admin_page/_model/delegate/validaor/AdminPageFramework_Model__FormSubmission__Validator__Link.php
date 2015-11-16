<?php
class AdminPageFramework_Model__FormSubmission__Validator__Link extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    public $sActionHookPrefix = 'try_validation_before_';
    public $iHookPriority = 30;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory) {
        $_sLinkURL = $this->_getPressedSubmitButtonData($aSubmits, 'href');
        if (!$_sLinkURL) {
            return;
        }
        $this->goToURL($_sLinkURL);
    }
}