<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_UserMeta extends AdminPageFramework_UserMeta_Controller {
    protected $_sStructureType = 'user_meta';
    public function __construct($sCapability='read', $sTextDomain='admin-page-framework')
    {
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, get_class($this), $sCapability, $sTextDomain, $this->_sStructureType);
        parent::__construct($this->oProp);
    }
}
