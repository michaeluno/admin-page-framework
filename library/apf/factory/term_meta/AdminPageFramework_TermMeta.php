<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_TermMeta extends AdminPageFramework_TermMeta_Controller {
    protected $_sStructureType = 'term_meta';
    public function __construct($asTaxonomySlug, $sCapability='manage_options', $sTextDomain='admin-page-framework')
    {
        if (empty($asTaxonomySlug)) {
            return;
        }
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, get_class($this), $sCapability, $sTextDomain, $this->_sStructureType);
        $this->oProp->aTaxonomySlugs = ( array ) $asTaxonomySlug;
        parent::__construct($this->oProp);
    }
}
