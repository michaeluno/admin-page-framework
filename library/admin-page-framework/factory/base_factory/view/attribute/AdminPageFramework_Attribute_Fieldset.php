<?php
class AdminPageFramework_Attribute_Fieldset extends AdminPageFramework_Attribute_FieldContainer_Base {
    public $sContext = 'fieldset';
    protected function _getAttributes() {
        return array('id' => $this->sContext . '-' . $this->aArguments['tag_id'], 'class' => 'admin-page-framework-' . $this->sContext, 'data-field_id' => $this->aArguments['tag_id'],);
    }
}