<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_PageMetaBox extends AdminPageFramework_PageMetaBox_Controller {
    protected $_sStructureType = 'page_meta_box';
    public function __construct($sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework')
    {
        if (empty($asPageSlugs)) {
            return;
        }
        if (! $this->_isInstantiatable()) {
            return;
        }
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, get_class($this), $sCapability, $sTextDomain, $this->_sStructureType);
        $this->oProp->aPageSlugs = is_string($asPageSlugs) ? array( $asPageSlugs ) : $asPageSlugs;
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
}
