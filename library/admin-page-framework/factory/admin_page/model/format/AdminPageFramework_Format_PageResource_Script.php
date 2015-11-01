<?php
class AdminPageFramework_Format_PageResource_Script extends AdminPageFramework_Format_Base {
    static public $aStructure = array('src' => null, 'handle_id' => null, 'dependencies' => array(), 'version' => false, 'translation' => array(), 'in_footer' => false,);
    public $asSubject = '';
    public function __construct() {
        $_aParameters = func_get_args() + array($this->asSubject,);
        $this->asSubject = $_aParameters[0];
    }
    public function get() {
        return $this->_getFormatted($this->asSubject);
    }
    private function _getFormatted($asSubject) {
        $_aSubject = array();
        if (is_string($asSubject)) {
            $_aSubject['src'] = $asSubject;
        }
        return $_aSubject + self::$aStructure;
    }
}