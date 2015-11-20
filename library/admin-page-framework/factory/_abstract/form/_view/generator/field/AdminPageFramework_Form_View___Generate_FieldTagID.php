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
        $_sSectionPart = implode('_', $this->aArguments['_section_path_array']);
        $_sFieldPart = implode('_', $this->aArguments['_field_path_array']);
        return $this->_isSectionSet() ? $_sSectionPart . $_sSectionIndex . '_' . $_sFieldPart : $_sFieldPart;
    }
}