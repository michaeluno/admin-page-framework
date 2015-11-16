<?php
abstract class AdminPageFramework_Form_View___Attribute_FieldContainer_Base extends AdminPageFramework_Form_View___Attribute_Base {
    protected function _getFormattedAttributes() {
        $_aAttributes = $this->uniteArrays($this->getElementAsArray($this->aArguments, array('attributes', $this->sContext)), $this->aAttributes + $this->_getAttributes());
        $_aAttributes['class'] = $this->getClassAttribute($this->getElement($_aAttributes, 'class', array()), $this->getElement($this->aArguments, array('class', $this->sContext), array()));
        return $_aAttributes;
    }
}