<?php
abstract class AdminPageFramework_Widget_View extends AdminPageFramework_Widget_Model {
    public function content($sContent, $aArguments, $aFormData) {
        return $sContent;
    }
    public function _printWidgetForm() {
        $_oFieldsTable = new AdminPageFramework_FormPart_Table($this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg);
        echo $_oFieldsTable->getFormTables($this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array($this, '_replyToGetSectionHeaderOutput'), array($this, '_replyToGetFieldOutput'));
    }
    public function _replyToGetSectionHeaderOutput($sSectionDescription, $aSection) {
        return $this->oUtil->addAndApplyFilters($this, array('section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id']), $sSectionDescription);
    }
}