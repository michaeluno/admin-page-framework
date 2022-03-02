<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Format_PageResource_Script extends AdminPageFramework_Format_Base {
    public static $aStructure = array( 'src' => null, 'handle_id' => null, 'dependencies' => array(), 'version' => false, 'translation' => array(), 'in_footer' => false, );
    public $asSubject = '';
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->asSubject, );
        $this->asSubject = $_aParameters[ 0 ];
    }
    public function get()
    {
        return $this->_getFormatted($this->asSubject);
    }
    private function _getFormatted($asSubject)
    {
        if (is_array($asSubject)) {
            return $asSubject + self::$aStructure;
        }
        $_aSubject = array();
        if (is_string($asSubject)) {
            $_aSubject[ 'src' ] = $asSubject;
        }
        return $_aSubject + self::$aStructure;
    }
}
