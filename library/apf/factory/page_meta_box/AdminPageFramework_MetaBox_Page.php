<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_MetaBox_Page extends AdminPageFramework_PageMetaBox {
    public function __construct($sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework')
    {
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
        $this->oUtil->showDeprecationNotice('The class, ' . __CLASS__ . ',', 'AdminPageFramework_PageMetaBox');
    }
}
