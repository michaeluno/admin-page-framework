<?php
class AdminPageFramework_Form_page_meta_box extends AdminPageFramework_Form {
    public $sStructureType = 'page_meta_box';
    public function construct() {
        add_filter('options_' . $this->aArguments['caller_id'], array($this, '_replyToSanitizeSavedFormData'), 5);
    }
    public function _replyToSanitizeSavedFormData($aSavedFormData) {
        return $this->castArrayContents($this->getDataStructureFromAddedFieldsets(), $aSavedFormData);
    }
}