<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Model__FormRedirectHandler extends AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory)
    {
        $this->oFactory = $oFactory;
        $this->_replyToCheckRedirects();
    }
    public function _replyToCheckRedirects()
    {
        if (! $this->_shouldProceed()) {
            return;
        }
        $_sPageSlug = $this->getHTTPQueryGET('page', '');
        $_sTransient = 'apf_rurl' . md5(trim("redirect_{$this->oFactory->oProp->sClassName}_{$_sPageSlug}"));
        $_aError = $this->oFactory->getFieldErrors();
        if (! empty($_aError)) {
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
    private function _shouldProceed()
    {
        if (! $this->oFactory->isInThePage()) {
            return false;
        }
        if (! $this->getHTTPQueryGET('settings-updated', false)) {
            return false;
        }
        return 'redirect' === $this->getHTTPQueryGET('confirmation', '');
    }
}
