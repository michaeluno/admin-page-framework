<?php
class AdminPageFramework_Attribute_SectionTable extends AdminPageFramework_Attribute_Base {
    public $sContext = 'section_table';
    protected function _getAttributes() {
        return array('id' => 'section_table-' . $this->aArguments['_tag_id'], 'class' => $this->getClassAttribute('form-table', 'admin-page-framework-section-table'),);
    }
}