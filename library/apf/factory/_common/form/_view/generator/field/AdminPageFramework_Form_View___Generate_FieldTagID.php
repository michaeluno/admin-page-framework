<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___Generate_FieldTagID extends AdminPageFramework_Form_View___Generate_Field_Base {
    public function get()
    {
        return $this->_getFiltered($this->_getBaseFieldTagID());
    }
    public function getModel()
    {
        return $this->get() . '__' . $this->sIndexMark;
    }
    protected function _getBaseFieldTagID()
    {
        if ($this->aArguments[ '_parent_tag_id' ]) {
            return $this->aArguments[ '_parent_tag_id' ] . '_' . $this->aArguments[ 'field_id' ];
        }
        $_sSectionIndex = isset($this->aArguments[ '_section_index' ]) ? '__' . $this->aArguments[ '_section_index' ] : '';
        $_sSectionPart = implode('_', $this->aArguments[ '_section_path_array' ]);
        $_sFieldPart = implode('_', $this->aArguments[ '_field_path_array' ]);
        return $this->_isSectionSet() ? $_sSectionPart . $_sSectionIndex . '_' . $_sFieldPart : $_sFieldPart;
    }
}
