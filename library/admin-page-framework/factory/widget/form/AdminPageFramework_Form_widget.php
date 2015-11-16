<?php
class AdminPageFramework_Form_widget extends AdminPageFramework_Form {
    public $sStructureType = 'widget';
    public function construct() {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources() {
        $_oCSS = new AdminPageFramework_Form_View___CSS_widget;
        $this->addResource('inline_styles', $_oCSS->get());
    }
}