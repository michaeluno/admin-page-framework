<?php
class AdminPageFramework_Form_taxonomy_field extends AdminPageFramework_Form {
    public $sStructureType = 'taxonomy_field';
    public function get() {
        $this->sCapability = $this->callback($this->aCallbacks['capability'], '');
        if (!$this->canUserView($this->sCapability)) {
            return '';
        }
        $this->_formatElementDefinitions($this->aSavedData);
        $_oFieldsets = new AdminPageFramework_Form_View___FieldsetRows($this->getElementAsArray($this->aFieldsets, '_default'), null, $this->aSavedData, $this->getFieldErrors(), $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldsets->get();
    }
}