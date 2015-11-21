<?php
class AdminPageFramework_Model__FormRedirectHandler extends AdminPageFramework_WPUtility {
    public $oFactory;
    public function __construct($oFactory) {
        $this->oFactory = $oFactory;
        $this->_replyToCheckRedirects();
    }
    public function _replyToCheckRedirects() {
        if (!$this->_shouldProceed()) {
            return;
        }
        $_sTransient = 'apf_rurl' . md5(trim("redirect_{$this->oFactory->oProp->sClassName}_{$_GET['page']}"));
        $_aError = $this->oFactory->getFieldErrors();
        if (!empty($_aError)) {
            $this->deleteTransient($_sTransient);
            return;
        }
        $_sURL = $this->getTransient($_sTransient);
        if (false === $_sURL) {
            return;
        }
        $this->deleteTransient($_sTransient);
        $this->goToURL($_sURL);
    }
    private function _shouldProceed() {
        if (!$this->oFactory->_isInThePage()) {
            return false;
        }
        $_bsSettingsUpdatedFlag = $this->getElement($_GET, 'settings-updated', false);
        if (!$_bsSettingsUpdatedFlag) {
            return false;
        }
        $_sConfirmationType = $this->getElement($_GET, 'confirmation', '');
        return 'redirect' === $_sConfirmationType;
    }
}