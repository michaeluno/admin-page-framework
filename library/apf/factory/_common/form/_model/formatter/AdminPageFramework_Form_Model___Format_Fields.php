<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_Model___Format_Fields extends AdminPageFramework_Form_Model___Format_FormField_Base {
    public static $aStructure = array();
    public $aField = array();
    public $aOptions = array();
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aField, $this->aOptions, );
        $this->aField = $_aParameters[ 0 ];
        $this->aOptions = $_aParameters[ 1 ];
    }
    public function get()
    {
        $_mSavedValue = $this->___getStoredInputFieldValue($this->aField, $this->aOptions);
        $_aFields = $this->___getFieldsWithSubs($this->aField, $_mSavedValue);
        $this->___setSavedFieldsValue($_aFields, $_mSavedValue, $this->aField);
        $this->___setFieldsValue($_aFields);
        return $_aFields;
    }
    private function ___getFieldsWithSubs($aField, $mSavedValue)
    {
        $aFirstField = array();
        $aSubFields = array();
        $this->___divideMainAndSubFields($aField, $aFirstField, $aSubFields);
        $this->___fillRepeatableElements($aField, $aSubFields, $mSavedValue);
        $this->___fillSubFields($aSubFields, $aFirstField);
        return array_merge(array( $aFirstField ), $aSubFields);
    }
    private function ___divideMainAndSubFields($aField, array &$aFirstField, array &$aSubFields)
    {
        foreach ($aField as $_nsIndex => $_mFieldElement) {
            if (is_numeric($_nsIndex)) {
                $aSubFields[] = $_mFieldElement;
            } else {
                $aFirstField[ $_nsIndex ] = $_mFieldElement;
            }
        }
    }
    private function ___fillRepeatableElements($aField, array &$aSubFields, $mSavedValue)
    {
        if (empty($aField[ 'repeatable' ])) {
            return;
        }
        $_aSavedValues = ( array ) $mSavedValue;
        unset($_aSavedValues[ 0 ]);
        foreach ($_aSavedValues as $_iIndex => $vValue) {
            $aSubFields[ $_iIndex - 1 ] = isset($aSubFields[ $_iIndex - 1 ]) && is_array($aSubFields[ $_iIndex - 1 ]) ? $aSubFields[ $_iIndex - 1 ] : array();
        }
    }
    private function ___fillSubFields(array &$aSubFields, array $aFirstField)
    {
        foreach ($aSubFields as &$_aSubField) {
            $_aLabel = $this->getElement($_aSubField, 'label', $this->getElement($aFirstField, 'label', null));
            $_aSubField = $this->uniteArrays($_aSubField, $aFirstField);
            $_aSubField[ 'label' ] = $_aLabel;
        }
    }
    private function ___setSavedFieldsValue(array &$aFields, $mSavedValue, $aField)
    {
        if (! $this->hasSubFields($aFields, $aField)) {
            $aFields[ 0 ][ '_saved_value' ] = $mSavedValue;
            $aFields[ 0 ][ '_is_multiple_fields' ] = false;
            return;
        }
        foreach ($aFields as $_iIndex => &$_aThisField) {
            $_aThisField[ '_saved_value' ] = $this->getElement($mSavedValue, $_iIndex, null);
            $_aThisField[ '_subfield_index' ] = $_iIndex;
            $_aThisField[ '_is_multiple_fields' ] = true;
        }
    }
    private function ___setFieldsValue(&$aFields)
    {
        foreach ($aFields as &$_aField) {
            $_aField[ '_is_value_set_by_user' ] = isset($_aField[ 'value' ]);
            $_aField[ 'value' ] = $this->___getSetFieldValue($_aField);
        }
    }
    private function ___getSetFieldValue($aField)
    {
        if (isset($aField[ 'value' ])) {
            return $aField[ 'value' ];
        }
        if (isset($aField[ '_saved_value' ])) {
            return $aField[ '_saved_value' ];
        }
        if (isset($aField[ 'default' ])) {
            return $aField[ 'default' ];
        }
        return null;
    }
    private function ___getStoredInputFieldValue($aField, $aOptions)
    {
        $_aFieldPath = $aField[ '_field_path_array' ];
        if (! isset($aField[ 'section_id' ]) || '_default' === $aField[ 'section_id' ]) {
            return $this->getElement($aOptions, $_aFieldPath, null);
        }
        $_aSectionPath = $aField[ '_section_path_array' ];
        if (isset($aField[ '_section_index' ])) {
            return $this->getElement($aOptions, array_merge($_aSectionPath, array( $aField[ '_section_index' ] ), $_aFieldPath), null);
        }
        return $this->getElement($aOptions, array_merge($_aSectionPath, $_aFieldPath), null);
    }
}
