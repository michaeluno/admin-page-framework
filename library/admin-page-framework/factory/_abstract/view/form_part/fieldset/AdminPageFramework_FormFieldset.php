<?php
class AdminPageFramework_FormFieldset extends AdminPageFramework_FormFieldset_Base {
    public function get() {
        $_aOutput = array();
        $_sFieldError = $this->_getFieldError($this->aErrors, $this->aField['section_id'], $this->aField['field_id']);
        if ('' !== $_sFieldError) {
            $_aOutput[] = $_sFieldError;
        }
        $_oFieldsFormatter = new AdminPageFramework_Format_Fields($this->aField, $this->aOptions);
        $_aFields = $_oFieldsFormatter->get();
        $_aOutput[] = $this->_getFieldsOutput($_aFields, $this->aCallbacks);
        return $this->_getFinalOutput($this->aField, $_aOutput, count($_aFields));
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
        $_oSubFieldFormatter = new AdminPageFramework_Format_EachField($aField, $isIndex, $aCallbacks, $_aFieldTypeDefinition);
        $aField = $_oSubFieldFormatter->get();
        $_oFieldAttribute = new AdminPageFramework_Attribute_Field($aField);
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
        $_oFieldsetAttributes = new AdminPageFramework_Attribute_Fieldset($aFieldset);
        $_oFieldsAttributes = new AdminPageFramework_Attribute_Fields($aFieldset, array(), $iFieldsCount);
        return $aFieldset['before_fieldset'] . "<fieldset " . $_oFieldsetAttributes->get() . ">" . "<div " . $_oFieldsAttributes->get() . ">" . $aFieldset['before_fields'] . implode(PHP_EOL, $aFieldsOutput) . $aFieldset['after_fields'] . "</div>" . $this->_getExtras($aFieldset, $iFieldsCount) . "</fieldset>" . $aFieldset['after_fieldset'];
    }
    private function _getExtras($aField, $iFieldsCount) {
        $_aOutput = array();
        $_oFieldDescription = new AdminPageFramework_FormPart_Description($aField['description'], 'admin-page-framework-fields-description');
        $_aOutput[] = $_oFieldDescription->get();
        $_aOutput[] = $this->_getDynamicElementFlagFieldInputTag($aField);
        $_aOutput[] = $this->_getFieldScripts($aField, $iFieldsCount);
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getDynamicElementFlagFieldInputTag(array $aFieldset) {
        if (!$aFieldset['sortable'] && !$aFieldset['repeatable']) {
            return '';
        }
        return $this->getHTMLTag('input', array('type' => 'hidden', 'name' => '__dynamic_elements_' . $aFieldset['_fields_type'] . '[' . $aFieldset['_field_address'] . ']', 'class' => 'dynamic-element-names element-address', 'value' => $aFieldset['_field_address'], 'data-field_address_model' => $aFieldset['_field_address_model'],));
    }
    private function _getFieldScripts($aField, $iFieldsCount) {
        $_aOutput = array();
        $_aOutput[] = $aField['repeatable'] ? $this->_getRepeaterFieldEnablerScript('fields-' . $aField['tag_id'], $iFieldsCount, $aField['repeatable']) : '';
        $_aOutput[] = $aField['sortable'] && ($iFieldsCount > 1 || $aField['repeatable']) ? $this->_getSortableFieldEnablerScript('fields-' . $aField['tag_id']) : '';
        return implode(PHP_EOL, $_aOutput);
    }
    private function _getFieldError($aErrors, $sSectionID, $sFieldID) {
        if ($this->_hasFieldErrorsOfSection($aErrors, $sSectionID, $sFieldID)) {
            return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" . $aErrors[$sSectionID][$sFieldID] . "</span>";
        }
        if ($this->_hasFieldError($aErrors, $sFieldID)) {
            return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" . $aErrors[$sFieldID] . "</span>";
        }
        return '';
    }
    private function _hasFieldErrorsOfSection($aErrors, $sSectionID, $sFieldID) {
        return (isset($aErrors[$sSectionID], $aErrors[$sSectionID][$sFieldID]) && is_array($aErrors[$sSectionID]) && !is_array($aErrors[$sSectionID][$sFieldID]));
    }
    private function _hasFieldError($aErrors, $sFieldID) {
        return (isset($aErrors[$sFieldID]) && !is_array($aErrors[$sFieldID]));
    }
}