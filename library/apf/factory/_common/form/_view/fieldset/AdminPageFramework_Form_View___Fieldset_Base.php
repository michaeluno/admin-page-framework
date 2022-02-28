<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Form_View___Fieldset_Base extends AdminPageFramework_Form_Utility {
    public $aFieldset = array();
    public $aFieldTypeDefinitions = array();
    public $aOptions = array();
    public $aErrors = array();
    public $oMsg;
    public $aCallbacks = array();
    public function __construct($aFieldset, $aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg, array $aCallbacks=array())
    {
        $this->aFieldset = $this->_getFormatted($aFieldset, $aFieldTypeDefinitions);
        $this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
        $this->aOptions = $aOptions;
        $this->aErrors = $this->getAsArray($aErrors);
        $this->oMsg = $oMsg;
        $this->aCallbacks = $aCallbacks + array( 'hfID' => null, 'hfTagID' => null, 'hfName' => null, 'hfNameFlat' => null, 'hfInputName' => null, 'hfInputNameFlat' => null, 'hfClass' => null, );
    }
    private function _getFormatted($aFieldset, $aFieldTypeDefinitions)
    {
        return $this->uniteArrays($aFieldset, $this->_getFieldTypeDefaultArguments($aFieldset[ 'type' ], $aFieldTypeDefinitions) + AdminPageFramework_Form_Model___Format_Fieldset::$aStructure);
    }
    private function _getFieldTypeDefaultArguments($sFieldType, $aFieldTypeDefinitions)
    {
        $_aFieldTypeDefinition = $this->getElement($aFieldTypeDefinitions, $sFieldType, $aFieldTypeDefinitions[ 'default' ]);
        $_aDefaultKeys = $this->getAsArray($_aFieldTypeDefinition[ 'aDefaultKeys' ]);
        $_aDefaultKeys[ 'attributes' ] = array( 'fieldrow' => $_aDefaultKeys[ 'attributes' ][ 'fieldrow' ], 'fieldset' => $_aDefaultKeys[ 'attributes' ][ 'fieldset' ], 'fields' => $_aDefaultKeys[ 'attributes' ][ 'fields' ], 'field' => $_aDefaultKeys[ 'attributes' ][ 'field' ], );
        return $_aDefaultKeys;
    }
    protected function _getRepeatableFieldButtons($sFieldsContainerID, $iFieldCount, $aSettings)
    {
        if (empty($aSettings)) {
            return '';
        }
        $_aSettings = $this->getAsArray($aSettings);
        $_oFormatter = new AdminPageFramework_Form_Model___Format_RepeatableField($_aSettings, $this->oMsg);
        $_aSettings = $_oFormatter->get();
        return "<div class='hidden repeatable-field-buttons-model' " . $this->getDataAttributes($_aSettings) . ">" . $this->___getRepeatableButtonHTML($sFieldsContainerID, $_aSettings, $iFieldCount, false) . "</div>";
    }
    private function ___getRepeatableButtonHTML($sFieldsContainerID, array $aArguments, $iFieldCount, $bSmall=true)
    {
        $_aArguments = $aArguments;
        $_sSmallButtonSelector = $bSmall ? ' button-small' : '';
        $_sDisabledContent = $this->getModalForDisabledRepeatableElement('repeatable_field_disabled_' . $sFieldsContainerID, $_aArguments[ 'disabled' ]);
        if (version_compare($GLOBALS[ 'wp_version' ], '5.3', '>=')) {
            return "<div " . $this->___getContainerAttributes($_aArguments) . " >" . "<a " . $this->___getRemoveButtonAttributes($sFieldsContainerID, $_sSmallButtonSelector, $iFieldCount) . ">" . "<span class='dashicons dashicons-minus'></span>" . "</a>" . "<a " . $this->___getAddButtonAttributes($_aArguments, $sFieldsContainerID, $_sSmallButtonSelector) . ">" . "<span class='dashicons dashicons-plus-alt2'></span>" ."</a>" . "</div>" . $_sDisabledContent;
        }
        return "<div " . $this->___getContainerAttributes($_aArguments) . " >" . "<a " . $this->___getRemoveButtonAttributes($sFieldsContainerID, $_sSmallButtonSelector, $iFieldCount) . ">" . "-" . "</a>" . "<a " . $this->___getAddButtonAttributes($_aArguments, $sFieldsContainerID, $_sSmallButtonSelector) . ">" . "+" ."</a>" . "</div>" . $_sDisabledContent;
    }
    private function ___getAddButtonAttributes($aArguments, $sFieldsContainerID, $sSmallButtonSelector)
    {
        $_sPlusButtonAttributes = array( 'class' => 'repeatable-field-add-button button-secondary repeatable-field-button button' . $sSmallButtonSelector, 'title' => $this->oMsg->get('add'), 'data-id' => $sFieldsContainerID, 'href' => empty($aArguments[ 'disabled' ]) ? null : '#TB_inline?width=' . $aArguments[ 'disabled' ][ 'box_width' ] . '&height=' . $aArguments[ 'disabled' ][ 'box_height' ] . '&inlineId=' . 'repeatable_field_disabled_' . $sFieldsContainerID, );
        return $this->getAttributes($_sPlusButtonAttributes);
    }
    private function ___getRemoveButtonAttributes($sFieldsContainerID, $sSmallButtonSelector, $iFieldCount)
    {
        $_aMinusButtonAttributes = array( 'class' => 'repeatable-field-remove-button button-secondary repeatable-field-button button' . $sSmallButtonSelector, 'title' => $this->oMsg->get('remove'), 'style' => $iFieldCount <= 1 ? 'visibility: hidden' : null, 'data-id' => $sFieldsContainerID, );
        return $this->getAttributes($_aMinusButtonAttributes);
    }
    private function ___getContainerAttributes($aArguments)
    {
        $_aContainerAttributes = array( 'class' => $this->getClassAttribute('admin-page-framework-repeatable-field-buttons', ! empty($aArguments[ 'disabled' ]) ? 'disabled' : ''), );
        unset($aArguments[ 'disabled' ][ 'message' ]);
        if (empty($aArguments[ 'disabled' ])) {
            unset($aArguments[ 'disabled' ]);
        }
        return $this->getAttributes($_aContainerAttributes) . ' ' . $this->getDataAttributes($aArguments);
    }
}
