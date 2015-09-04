<?php
abstract class AdminPageFramework_Format_FormField_Base extends AdminPageFramework_Format_Base {
    protected function _isSectionSet(array $aField) {
        return isset($aField['section_id']) && $aField['section_id'] && '_default' !== $aField['section_id'];
    }
}