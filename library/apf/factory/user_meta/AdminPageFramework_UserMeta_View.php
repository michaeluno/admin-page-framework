<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_UserMeta_View extends AdminPageFramework_UserMeta_Model {
    public function content($sContent)
    {
        return $sContent;
    }
    public function _replyToPrintFields()
    {
        $_aOutput = array();
        $_aOutput[] = $this->oForm->get();
        $_sOutput = $this->oUtil->addAndApplyFilters($this, 'content_' . $this->oProp->sClassName, $this->content(implode(PHP_EOL, $_aOutput)));
        $this->oUtil->addAndDoActions($this, 'do_' . $this->oProp->sClassName, $this);
        echo $_sOutput;
    }
}
