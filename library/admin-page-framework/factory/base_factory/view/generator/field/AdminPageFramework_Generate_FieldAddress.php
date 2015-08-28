<?php
class AdminPageFramework_Generate_FieldAddress extends AdminPageFramework_Generate_FlatFieldName {
    public function get() {
        return $this->_getFlatFieldName();
    }
    public function getModel() {
        return $this->get() . '|' . $this->sIndexMark;
    }
}