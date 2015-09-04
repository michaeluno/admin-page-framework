<?php
class AdminPageFramework_FormPart_DebugInfo extends AdminPageFramework_FormPart_Base {
    public $sFieldsType = '';
    public function __construct() {
        $_aParameters = func_get_args() + array($this->sFieldsType,);
        $this->sFieldsType = $_aParameters[0];
    }
    public function get() {
        if (!$this->isDebugModeEnabled()) {
            return '';
        }
        if (!in_array($this->sFieldsType, array('widget', 'post_meta_box', 'page_meta_box', 'user_meta'))) {
            return '';
        }
        return "<div class='admin-page-framework-info'>" . 'Debug Info: ' . AdminPageFramework_Registry::NAME . ' ' . AdminPageFramework_Registry::getVersion() . "</div>";
    }
}