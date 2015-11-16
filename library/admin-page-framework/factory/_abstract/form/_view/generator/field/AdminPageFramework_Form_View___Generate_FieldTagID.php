<?php
class AdminPageFramework_Form_View___Generate_FieldTagID extends AdminPageFramework_Form_View___Generate_Field_Base {
    public function get() {
        return $this->_getFiltered($this->_getBaseFieldTagID());
    }
    public function getModel() {
        return $this->get() . '__' . $this->sIndexMark;
    }
    protected function _getBaseFieldTagID() {
        $_sSectionIndex = isset($this->aArguments['_section_index']) ? '__' . $this->aArguments['_section_index'] : '';
        return $this->_isSectionSet() ? $this->aArguments['section_id'] . $_sSectionIndex . '_' . $this->aArguments['field_id'] : $this->aArguments['field_id'];
    }
}