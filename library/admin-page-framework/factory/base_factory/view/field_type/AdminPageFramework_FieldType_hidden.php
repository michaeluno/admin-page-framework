<?php
class AdminPageFramework_FieldType_hidden extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('hidden');
    protected $aDefaultKeys = array();
    protected function getField($aField) {
        return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength($aField['label_min_width']) . ";'>" . $aField['label'] . "</span>" : "") . "<input " . $this->generateAttributes($aField['attributes']) . " />" . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label'];
    }
}