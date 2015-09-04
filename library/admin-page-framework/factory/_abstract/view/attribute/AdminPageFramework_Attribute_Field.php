<?php
class AdminPageFramework_Attribute_Field extends AdminPageFramework_Attribute_FieldContainer_Base {
    public $sContext = 'field';
    protected function _getAttributes() {
        return array('id' => $this->aArguments['_field_container_id'], 'data-type' => $this->aArguments['type'], 'class' => "admin-page-framework-field admin-page-framework-field-" . $this->aArguments['type'] . $this->getAOrB($this->aArguments['attributes']['disabled'], ' disabled', '') . $this->getAOrB($this->aArguments['_is_sub_field'], ' admin-page-framework-subfield', ''));
    }
}