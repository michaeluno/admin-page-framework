<?php
class AdminPageFramework_Form_View___Fieldset extends AdminPageFramework_Form_View___Fieldset_Base {
    public function get() {
        $_aOutputs = array();
        $_oFieldError = new AdminPageFramework_Form_View___Fieldset___FieldError($this->aErrors, $this->aField['_section_path_array'], $this->aField['_field_path_array'], $this->aField['error_message']);
        $_aOutputs[] = $_oFieldError->get();
        $_oFieldsFormatter = new AdminPageFramework_Form_Model___Format_Fields($this->aField, $this->aOptions);
        $_aFields = $_oFieldsFormatter->get();
        $_aOutputs[] = $this->_getFieldsOutput($_aFields, $this->aCallbacks);
        return $this->_getFinalOutput($this->aField, $_aOutputs, count($_aFields));
    }
    public function _getFieldOutput() {
        return $this->get();
    }
    private function _getFieldsOutput(array $aFields, array $aCallbacks = array()) {
        $_aOutput = array();
        foreach ($aFields as $_isIndex => $_aField) {
            $_aOutput[] = $this->_getEachFieldOutput($_aField, $_isIndex, $aCallbacks, $this->isLastElement($aFields, $_isIndex));
        }
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getEachFieldOutput(array $aField, $isIndex, array $aCallbacks, $bIsLastElement = false) {
        $_aFieldTypeDefinition = $this->_getFieldTypeDefinition($aField['type']);
        if (!is_callable($_aFieldTypeDefinition['hfRenderField'])) {
            return '';
        }
        $_oSubFieldFormatter = new AdminPageFramework_Form_Model___Format_EachField($aField, $isIndex, $aCallbacks, $_aFieldTypeDefinition);
        $aField = $_oSubFieldFormatter->get();
        $_oFieldAttribute = new AdminPageFramework_Form_View___Attribute_Field($aField);
        return $aField['before_field'] . "<div " . $_oFieldAttribute->get() . ">" . call_user_func_array($_aFieldTypeDefinition['hfRenderField'], array($aField)) . $this->_getUnsetFlagFieldInputTag($aField) . $this->_getDelimiter($aField, $bIsLastElement) . "</div>" . $aField['after_field'];
    }
    private function _getUnsetFlagFieldInputTag(array $aField) {
        if (false !== $aField['save']) {
            return '';
        }
        return $this->getHTMLTag('input', array('type' => 'hidden', 'name' => '__unset_' . $aField['_fields_type'] . '[' . $aField['_input_name_flat'] . ']', 'value' => $aField['_input_name_flat'], 'class' => 'unset-element-names element-address',));
    }
    private function _getFieldTypeDefinition($sFieldTypeSlug) {
        return $this->getElement($this->aFieldTypeDefinitions, $sFieldTypeSlug, $this->aFieldTypeDefinitions['default']);
    }
    private function _getDelimiter(array $aField, $bIsLastElement) {
        return $aField['delimiter'] ? "<div " . $this->getAttributes(array('class' => 'delimiter', 'id' => "delimiter-{$aField['input_id']}", 'style' => $this->getAOrB($bIsLastElement, "display:none;", ""),)) . ">" . $aField['delimiter'] . "</div>" : '';
    }
    private function _getFinalOutput(array $aFieldset, array $aFieldsOutput, $iFieldsCount) {
        $_oFieldsetAttributes = new AdminPageFramework_Form_View___Attribute_Fieldset($aFieldset);
        return $aFieldset['before_fieldset'] . "<fieldset " . $_oFieldsetAttributes->get() . ">" . $this->_getFieldsetContent($aFieldset, $aFieldsOutput, $iFieldsCount) . $this->_getExtras($aFieldset, $iFieldsCount) . "</fieldset>" . $aFieldset['after_fieldset'];
    }
    private function _getFieldsetContent($aFieldset, $aFieldsOutput, $iFieldsCount) {
        if (is_scalar($aFieldset['content'])) {
            return $aFieldset['content'];
        }
        $_oFieldsAttributes = new AdminPageFramework_Form_View___Attribute_Fields($aFieldset, array(), $iFieldsCount);
        return "<div " . $_oFieldsAttributes->get() . ">" . $aFieldset['before_fields'] . implode(PHP_EOL, $aFieldsOutput) . $aFieldset['after_fields'] . "</div>";
    }
    private function _getExtras($aField, $iFieldsCount) {
        $_aOutput = array();
        $_oFieldDescription = new AdminPageFramework_Form_View___Description($aField['description'], 'admin-page-framework-fields-description');
        $_aOutput[] = $_oFieldDescription->get();
        $_aOutput[] = $this->_getDynamicElementFlagFieldInputTag($aField);
        $_aOutput[] = $this->_getFieldScripts($aField, $iFieldsCount);
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getDynamicElementFlagFieldInputTag(array $aFieldset) {
        if ($aFieldset['repeatable']) {
            return $this->_getRepeatableFieldFlagTag($aFieldset);
        }
        if ($aFieldset['sortable']) {
            return $this->_getSortableFieldFlagTag($aFieldset);
        }
        return '';
    }
    private function _getRepeatableFieldFlagTag(array $aFieldset) {
        return $this->getHTMLTag('input', array('type' => 'hidden', 'name' => '__repeatable_elements_' . $aFieldset['_structure_type'] . '[' . $aFieldset['_field_address'] . ']', 'class' => 'element-address', 'value' => $aFieldset['_field_address'], 'data-field_address_model' => $aFieldset['_field_address_model'],));
    }
    private function _getSortableFieldFlagTag(array $aFieldset) {
        return $this->getHTMLTag('input', array('type' => 'hidden', 'name' => '__sortable_elements_' . $aFieldset['_structure_type'] . '[' . $aFieldset['_field_address'] . ']', 'class' => 'element-address', 'value' => $aFieldset['_field_address'], 'data-field_address_model' => $aFieldset['_field_address_model'],));
    }
    private function _getFieldScripts($aField, $iFieldsCount) {
        $_aOutput = array();
        $_aOutput[] = $aField['repeatable'] ? $this->_getRepeaterFieldEnablerScript('fields-' . $aField['tag_id'], $iFieldsCount, $aField['repeatable']) : '';
        $_aOutput[] = $aField['sortable'] && ($iFieldsCount > 1 || $aField['repeatable']) ? $this->_getSortableFieldEnablerScript('fields-' . $aField['tag_id']) : '';
        return implode(PHP_EOL, $_aOutput);
    }
}