<?php
class AdminPageFramework_Form_View___Generate_FieldAddress extends AdminPageFramework_Form_View___Generate_FlatFieldName {
    public function get() {
        return $this->_getFlatFieldName();
    }
    public function getModel() {
        return $this->get() . '|' . $this->sIndexMark;
    }
}