<?php
class AdminPageFramework_FormPart_FieldsetRow extends AdminPageFramework_FormPart_TableRow {
    protected function _getRow(array $aFieldset, $hfCallback) {
        if ('section_title' === $aFieldset['type']) {
            return '';
        }
        $_oFieldrowAttribute = new AdminPageFramework_Attribute_Fieldrow($aFieldset);
        return $this->_getFieldByContainer($aFieldset, $hfCallback, array('open_main' => "<div " . $_oFieldrowAttribute->get() . ">", 'close_main' => "</div>",));
    }
}