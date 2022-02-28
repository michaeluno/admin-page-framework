<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___Attribute_Field extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {
    public $sContext = 'field';
    protected function _getAttributes()
    {
        $_sFieldTypeSelector = $this->getAOrB($this->aArguments[ 'type' ], " admin-page-framework-field-{$this->aArguments[ 'type' ]}", '');
        $_sChildFieldSelector = $this->getAOrB($this->hasFieldDefinitionsInContent($this->aArguments), ' with-child-fields', ' without-child-fields');
        $_sNestedFieldSelector = $this->getAOrB($this->hasNestedFields($this->aArguments), ' with-nested-fields', ' without-nested-fields');
        $_sMixedFieldSelector = $this->getAOrB('inline_mixed' === $this->aArguments[ 'type' ], ' with-mixed-fields', ' without-mixed-fields');
        return array( 'id' => $this->aArguments[ '_field_container_id' ], 'data-type' => $this->aArguments[ 'type' ], 'class' => "admin-page-framework-field{$_sFieldTypeSelector}{$_sNestedFieldSelector}{$_sMixedFieldSelector}{$_sChildFieldSelector}" . $this->getAOrB($this->aArguments[ 'attributes' ][ 'disabled' ], ' disabled', '') . $this->getAOrB($this->aArguments[ '_is_sub_field' ], ' admin-page-framework-subfield', '') );
    }
}
