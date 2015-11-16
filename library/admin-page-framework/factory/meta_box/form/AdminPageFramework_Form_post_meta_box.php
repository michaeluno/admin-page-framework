<?php
class AdminPageFramework_Form_post_meta_box extends AdminPageFramework_Form_Meta {
    public $sStructureType = 'post_meta_box';
    public function construct() {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources() {
        $_oCSS = new AdminPageFramework_Form_View___CSS_meta_box;
        $this->addResource('inline_styles', $_oCSS->get());
    }
}