<?php
class AdminPageFramework_Attribute_Fieldrow extends AdminPageFramework_Attribute_FieldContainer_Base {
    public $sContext = 'fieldrow';
    protected function _getFormattedAttributes() {
        $_aAttributes = parent::_getFormattedAttributes();
        if ($this->aArguments['hidden']) {
            $_aAttributes['style'] = $this->getStyleAttribute($this->getElement($_aAttributes, 'style', array()), 'display:none');
        }
        return $_aAttributes;
    }
}