<?php
class AdminPageFramework_Form_View___Generate_FieldName extends AdminPageFramework_Form_View___Generate_Field_Base {
    public function get() {
        return $this->_getFiltered($this->_getFieldName());
    }
    public function getModel() {
        return $this->get() . '[' . $this->sIndexMark . ']';
    }
    protected function _getFieldName() {
        $_sSectionIndex = isset($this->aArguments['section_id'], $this->aArguments['_section_index']) ? "[{$this->aArguments['_section_index']}]" : "";
        return $this->getAOrB($this->_isSectionSet(), $this->aArguments['section_id'] . $_sSectionIndex . "[" . $this->aArguments['field_id'] . "]", $this->aArguments['field_id']);
    }
}