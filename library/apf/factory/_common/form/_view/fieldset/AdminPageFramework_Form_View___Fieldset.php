<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___Fieldset extends AdminPageFramework_Form_View___Fieldset_Base {
    public function get()
    {
        $_aOutputs = array();
        $_oFieldError = new AdminPageFramework_Form_View___Fieldset___FieldError($this->aErrors, $this->aFieldset[ '_section_path_array' ], $this->aFieldset[ '_field_path_array' ], $this->aFieldset[ 'error_message' ]);
        $_aOutputs[] = $_oFieldError->get();
        $_oFieldsFormatter = new AdminPageFramework_Form_Model___Format_Fields($this->aFieldset, $this->aOptions);
        $_aFields = $_oFieldsFormatter->get();
        $_aOutputs[] = $this->_getFieldsOutput($this->aFieldset, $_aFields, $this->aCallbacks);
        return $this->_getFinalOutput($this->aFieldset, $_aOutputs, count($_aFields));
    }
    private function _getFieldsOutput($aFieldset, array $aFields, array $aCallbacks=array())
    {
        $_aOutput = array();
        foreach ($aFields as $_isIndex => $_aField) {
            $_aOutput[] = $this->_getEachFieldOutput($_aField, $_isIndex, $aCallbacks, $this->isLastElement($aFields, $_isIndex));
        }
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getEachFieldOutput($aField, $isIndex, array $aCallbacks, $bIsLastElement=false)
    {
        $_aFieldTypeDefinition = $this->_getFieldTypeDefinition($aField[ 'type' ]);
        if (! is_callable($_aFieldTypeDefinition[ 'hfRenderField' ])) {
            return '';
        }
        $_oSubFieldFormatter = new AdminPageFramework_Form_Model___Format_EachField($aField, $isIndex, $aCallbacks, $_aFieldTypeDefinition);
        $aField = $_oSubFieldFormatter->get();
        return $this->_getFieldOutput(call_user_func_array($_aFieldTypeDefinition[ 'hfRenderField' ], array( $aField )), $aField, $bIsLastElement);
    }
    private function _getFieldOutput($sContent, $aField, $bIsLastElement)
    {
        $_oFieldAttribute = new AdminPageFramework_Form_View___Attribute_Field($aField);
        return $aField[ 'before_field' ] . "<div " . $_oFieldAttribute->get() . ">" . $sContent . $this->_getUnsetFlagFieldInputTag($aField) . $this->_getDelimiter($aField, $bIsLastElement) . "</div>" . $aField[ 'after_field' ];
    }
    private function _getUnsetFlagFieldInputTag($aField)
    {
        if (false !== $aField[ 'save' ]) {
            return '';
        }
        return $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => '__unset_' . $aField[ '_fields_type' ] . '[' . $aField[ '_input_name_flat' ] . ']', 'value' => $aField[ '_input_name_flat' ], 'class' => 'unset-element-names element-address', ));
    }
    private function _getFieldTypeDefinition($sFieldTypeSlug)
    {
        return $this->getElement($this->aFieldTypeDefinitions, $sFieldTypeSlug, $this->aFieldTypeDefinitions[ 'default' ]);
    }
    private function _getDelimiter($aField, $bIsLastElement)
    {
        return $aField[ 'delimiter' ] ? "<div " . $this->getAttributes(array( 'class' => 'delimiter', 'id' => "delimiter-{$aField[ 'input_id' ]}", 'style' => $this->getAOrB($bIsLastElement, "display:none;", ""), )) . ">" . $aField[ 'delimiter' ] . "</div>" : '';
    }
    private function _getFinalOutput($aFieldset, array $aFieldsOutput, $iFieldsCount)
    {
        $_oFieldsetAttributes = new AdminPageFramework_Form_View___Attribute_Fieldset($aFieldset);
        return $aFieldset[ 'before_fieldset' ] . "<fieldset " . $_oFieldsetAttributes->get() . ">" . $this->_getEmbeddedFieldTitle($aFieldset) . $this->_getChildFieldTitle($aFieldset) . $this->_getFieldsetContent($aFieldset, $aFieldsOutput, $iFieldsCount) . $this->_getExtras($aFieldset, $iFieldsCount) . "</fieldset>" . $aFieldset[ 'after_fieldset' ];
    }
    private function _getEmbeddedFieldTitle($aFieldset)
    {
        if (! $aFieldset[ '_is_title_embedded' ]) {
            return '';
        }
        $_oFieldTitle = new AdminPageFramework_Form_View___FieldTitle($aFieldset, '', $this->aOptions, $this->aErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldTitle->get();
    }
    private function _getChildFieldTitle($aFieldset)
    {
        if (! $aFieldset[ '_nested_depth' ]) {
            return '';
        }
        if ($aFieldset[ '_is_title_embedded' ]) {
            return '';
        }
        $_oFieldTitle = new AdminPageFramework_Form_View___FieldTitle($aFieldset, array( 'admin-page-framework-child-field-title' ), $this->aOptions, $this->aErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldTitle->get();
    }
    private function _getFieldsetContent($aFieldset, $aFieldsOutput, $iFieldsCount)
    {
        if (is_scalar($aFieldset[ 'content' ])) {
            return $aFieldset[ 'content' ];
        }
        $_oFieldsAttributes = new AdminPageFramework_Form_View___Attribute_Fields($aFieldset, array(), $iFieldsCount);
        return "<div " . $_oFieldsAttributes->get() . ">" . $aFieldset[ 'before_fields' ] . implode(PHP_EOL, $aFieldsOutput) . $aFieldset[ 'after_fields' ] . "</div>";
    }
    private function _getExtras($aField, $iFieldsCount)
    {
        $_aOutput = array();
        $_oFieldDescription = new AdminPageFramework_Form_View___Description($aField[ 'description' ], 'admin-page-framework-fields-description');
        $_aOutput[] = $_oFieldDescription->get();
        $_aOutput[] = $this->_getDynamicElementFlagFieldInputTag($aField);
        $_aOutput[] = $this->_getRepeatableFieldButtons('fields-' . $aField[ 'tag_id' ], $iFieldsCount, $aField[ 'repeatable' ]);
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getDynamicElementFlagFieldInputTag($aFieldset)
    {
        if (! empty($aFieldset[ 'repeatable' ])) {
            return $this->_getRepeatableFieldFlagTag($aFieldset);
        }
        if (! empty($aFieldset[ 'sortable' ])) {
            return $this->_getSortableFieldFlagTag($aFieldset);
        }
        return '';
    }
    private function _getRepeatableFieldFlagTag($aFieldset)
    {
        return $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => '__repeatable_elements_' . $aFieldset[ '_structure_type' ] . '[' . $aFieldset[ '_field_address' ] . ']', 'class' => 'element-address', 'value' => $aFieldset[ '_field_address' ], 'data-field_address_model' => $aFieldset[ '_field_address_model' ], ));
    }
    private function _getSortableFieldFlagTag($aFieldset)
    {
        return $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => '__sortable_elements_' . $aFieldset[ '_structure_type' ] . '[' . $aFieldset[ '_field_address' ] . ']', 'class' => 'element-address', 'value' => $aFieldset[ '_field_address' ], 'data-field_address_model' => $aFieldset[ '_field_address_model' ], ));
    }
}
