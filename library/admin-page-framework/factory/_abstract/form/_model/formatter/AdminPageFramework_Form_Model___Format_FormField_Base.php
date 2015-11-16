<?php
abstract class AdminPageFramework_Form_Model___Format_FormField_Base extends AdminPageFramework_Format_Base {
    protected function _isSectionSet(array $aField) {
        return isset($aField['section_id']) && $aField['section_id'] && '_default' !== $aField['section_id'];
    }
}