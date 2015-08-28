<?php
class AdminPageFramework_Attribute_SectionTableBody extends AdminPageFramework_Attribute_Base {
    public $sContext = 'section_table_content';
    protected function _getAttributes() {
        return array('class' => $this->getAOrB($this->aArguments['_is_collapsible'], 'admin-page-framework-collapsible-section-content' . ' ' . 'admin-page-framework-collapsible-content' . ' ' . 'accordion-section-content', null),);
    }
}