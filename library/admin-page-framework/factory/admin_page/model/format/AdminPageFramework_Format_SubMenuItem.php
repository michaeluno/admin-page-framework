<?php
class AdminPageFramework_Format_SubMenuItem extends AdminPageFramework_Format_Base {
    static public $aStructure = array();
    public $aSubMenuItem = array();
    public $oFactory;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubMenuItem, $this->oFactory,);
        $this->aSubMenuItem = $_aParameters[0];
        $this->oFactory = $_aParameters[1];
    }
    public function get() {
        $_aSubMenuItem = $this->getAsArray($this->aSubMenuItem);
        if (isset($_aSubMenuItem['page_slug'])) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuPage($_aSubMenuItem, $this->oFactory);
            return $_oFormatter->get();
        }
        if (isset($_aSubMenuItem['href'])) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuLink($_aSubMenuItem, $this->oFactory);
            return $_oFormatter->get();
        }
        return array();
    }
}