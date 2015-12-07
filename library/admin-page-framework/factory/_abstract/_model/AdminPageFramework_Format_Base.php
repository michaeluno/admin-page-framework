<?php
abstract class AdminPageFramework_Format_Base extends AdminPageFramework_FrameworkUtility {
    static public $aStructure = array();
    public $aSubject = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubject,);
        $this->aSubject = $_aParameters[0];
    }
    public function get() {
        return $this->aSubject;
    }
}