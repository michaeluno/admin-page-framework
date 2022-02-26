<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Form_View___Generate_Section_Base extends AdminPageFramework_Form_View___Generate_Base {
    public $aArguments = array();
    public $hfCallback = null;
    public $sIndexMark = '___i___';
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->hfCallback, );
        $this->aArguments = $_aParameters[ 0 ];
        $this->hfCallback = $_aParameters[ 1 ];
    }
    public function getModel()
    {
        return '';
    }
    protected function _getFiltered($sSubject)
    {
        return is_callable($this->hfCallback) ? call_user_func_array($this->hfCallback, array( $sSubject, $this->aArguments, )) : $sSubject;
    }
    protected function _getInputNameConstructed($aParts)
    {
        $_sName = array_shift($aParts);
        foreach ($aParts as $_sPart) {
            $_sName .= '[' . $_sPart . ']';
        }
        return $_sName;
    }
}
