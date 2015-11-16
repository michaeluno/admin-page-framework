<?php
abstract class AdminPageFramework_Widget_View extends AdminPageFramework_Widget_Model {
    public function content($sContent, $aArguments, $aFormData) {
        return $sContent;
    }
    public function _printWidgetForm() {
        echo $this->oForm->get();
    }
}