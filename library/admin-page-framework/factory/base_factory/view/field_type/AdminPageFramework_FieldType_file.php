<?php
class AdminPageFramework_FieldType_file extends AdminPageFramework_FieldType_text {
    public $aFieldTypeSlugs = array('file',);
    protected $aDefaultKeys = array('attributes' => array('accept' => 'audio/*|video/*|image/*|MIME_type',),);
    protected function setUp() {
    }
    protected function getScripts() {
        return "";
    }
    protected function getStyles() {
        return "";
    }
    protected function getField($aField) {
        return parent::getField($aField) . $this->getHTMLTag('input', array('type' => 'hidden', 'value' => '', 'name' => $aField['attributes']['name'] . '[_dummy_value]',)) . $this->getHTMLTag('input', array('type' => 'hidden', 'name' => '__unset_' . $aField['_fields_type'] . '[' . $aField['_input_name_flat'] . '|_dummy_value' . ']', 'value' => $aField['_input_name_flat'] . '|_dummy_value', 'class' => 'unset-element-names element-address',));
    }
}