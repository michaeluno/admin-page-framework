<?php
class AdminPageFramework_Form_View___Attribute_SectionTable extends AdminPageFramework_Form_View___Attribute_Base {
    public $sContext = 'section_table';
    protected function _getAttributes() {
        return array('id' => 'section_table-' . $this->aArguments['_tag_id'], 'class' => $this->getClassAttribute('form-table', 'admin-page-framework-section-table'),);
    }
}