<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___Attribute_SectionTable extends AdminPageFramework_Form_View___Attribute_Base {
    public $sContext = 'section_table';
    protected function _getAttributes()
    {
        return array( 'id' => 'section_table-' . $this->aArguments[ '_tag_id' ], 'class' => $this->getClassAttribute('form-table', 'admin-page-framework-section-table'), );
    }
}
