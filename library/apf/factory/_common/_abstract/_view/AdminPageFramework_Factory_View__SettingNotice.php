<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Factory_View__SettingNotice extends AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory, $sActionHookName='admin_notices')
    {
        $this->oFactory = $oFactory;
        add_action($sActionHookName, array( $this, '_replyToPrintSettingNotice' ));
    }
    public function _replyToPrintSettingNotice()
    {
        if (! $this->_shouldProceed()) {
            return;
        }
        $this->oFactory->oForm->printSubmitNotices();
    }
    private function _shouldProceed()
    {
        if (! $this->oFactory->isInThePage()) {
            return false;
        }
        if ($this->hasBeenCalled(__METHOD__)) {
            return false;
        }
        return isset($this->oFactory->oForm);
    }
}
