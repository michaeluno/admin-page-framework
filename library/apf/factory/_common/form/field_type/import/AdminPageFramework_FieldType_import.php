<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_import extends AdminPageFramework_FieldType_submit {
    public $aFieldTypeSlugs = array( 'import', );
    protected $aDefaultKeys = array( 'option_key' => null, 'format' => 'json', 'is_merge' => false, 'attributes' => array( 'class' => 'button button-primary', 'file' => array( 'accept' => 'audio/*|video/*|image/*|MIME_type', 'class' => 'import', 'type' => 'file', ), 'submit' => array( 'class' => 'import button button-primary', 'type' => 'submit', ), ), );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'admin-page-framework-field-type-import', 'src' => dirname(__FILE__) . '/js/import.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'translation_var' => 'AdminPageFrameworkImportFieldType', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'label' => array( 'noFile' => $this->oMsg->get('import_no_file'), ), ), ), );
    }
    protected function getField($aField)
    {
        $aField[ 'attributes'][ 'name' ] = "__import[submit][{$aField[ 'input_id' ]}]";
        $aField[ 'label' ] = $aField[ 'label' ] ? $aField[ 'label' ] : $this->oMsg->get('import');
        return parent::getField($aField);
    }
    protected function _getExtraFieldsBeforeLabel(&$aField)
    {
        return "<label>" . "<input " . $this->getAttributes(array( 'id' => "{$aField[ 'input_id' ]}_file", 'type' => 'file', 'name' => "__import[{$aField[ 'input_id' ]}]", ) + $aField[ 'attributes' ][ 'file' ]) . " />" . "</label>";
    }
    protected function _getExtraInputFields(&$aField)
    {
        $aHiddenAttributes = array( 'type' => 'hidden', );
        return "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][input_id]", 'value' => $aField['input_id'], ) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][field_id]", 'value' => $aField['field_id'], ) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][section_id]", 'value' => isset($aField['section_id']) && $aField['section_id'] != '_default' ? $aField['section_id'] : '', ) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][is_merge]", 'value' => $aField['is_merge'], ) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][option_key]", 'value' => $aField['option_key'], ) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array( 'name' => "__import[{$aField['input_id']}][format]", 'value' => $aField['format'], ) + $aHiddenAttributes) . "/>" ;
    }
}
