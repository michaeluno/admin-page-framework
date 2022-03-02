<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Model__FormSubmission__Validator__Import extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 10;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory)
    {
        if (! $this->_shouldProceed()) {
            return;
        }
        $this->_doImportOptions($this->getElement($aSubmitInformation, 'page_slug'), $this->getElement($aSubmitInformation, 'tab_slug'));
    }
    private function _shouldProceed()
    {
        if ($this->oFactory->hasFieldError()) {
            return false;
        }
        return isset($_POST[ '__import' ][ 'submit' ], $_FILES[ '__import' ]);
    }
    private function _doImportOptions($sPageSlug, $sTabSlug)
    {
        $_oException = new Exception('aReturn');
        $_oException->aReturn = $this->_importOptions($this->oFactory->oProp->aOptions, $sPageSlug, $sTabSlug);
        throw $_oException;
    }
    private function _importOptions($aStoredOptions, $sPageSlug, $sTabSlug)
    {
        $_oImport = new AdminPageFramework_ImportOptions($this->getHTTPRequestSanitized($_FILES[ '__import' ], false), $this->getHTTPRequestSanitized($_POST[ '__import' ]));
        $_aArguments = array( 'class_name' => $this->oFactory->oProp->sClassName, 'page_slug' => $sPageSlug, 'tab_slug' => $sTabSlug, 'section_id' => $_oImport->getSiblingValue('section_id'), 'pressed_field_id' => $_oImport->getSiblingValue('field_id'), 'pressed_input_id' => $_oImport->getSiblingValue('input_id'), 'should_merge' => $_oImport->getSiblingValue('is_merge'), );
        if ($_oImport->getError() > 0) {
            $this->oFactory->setSettingNotice($this->oFactory->oMsg->get('import_error'));
            return $aStoredOptions;
        }
        $_aMIMEType = $this->_getImportMIMEType($_aArguments);
        $_sType = $_oImport->getType();
        if (! in_array($_sType, $_aMIMEType)) {
            $this->oFactory->setSettingNotice(sprintf($this->oFactory->oMsg->get('uploaded_file_type_not_supported'), $_sType));
            return $aStoredOptions;
        }
        $_mData = $_oImport->getImportData();
        if (false === $_mData) {
            $this->oFactory->setSettingNotice($this->oFactory->oMsg->get('could_not_load_importing_data'));
            return $aStoredOptions;
        }
        $_sFormatType = $this->_getImportFormatType($_aArguments, $_oImport->getFormatType());
        $_oImport->formatImportData($_mData, $_sFormatType);
        $_sImportOptionKey = $this->_getImportOptionKey($_aArguments, $_oImport->getSiblingValue('option_key'));
        $_mData = $this->_getFilteredImportData($_aArguments, $_mData, $aStoredOptions, $_sFormatType, $_sImportOptionKey);
        $this->_setImportAdminNotice(empty($_mData));
        if ($_sImportOptionKey != $this->oFactory->oProp->sOptionKey) {
            update_option($_sImportOptionKey, $_mData);
            return $aStoredOptions;
        }
        return $_aArguments[ 'should_merge' ] ? $this->uniteArrays($_mData, $aStoredOptions) : $_mData;
    }
    private function _setImportAdminNotice($bEmpty)
    {
        $this->oFactory->setSettingNotice($bEmpty ? $this->oFactory->oMsg->get('not_imported_data') : $this->oFactory->oMsg->get('imported_data'), $bEmpty ? 'error' : 'updated', $this->oFactory->oProp->sOptionKey, false);
    }
    private function _getImportMIMEType(array $aArguments)
    {
        return $this->_getFilteredItemForPortByPrefix('import_mime_types_', array( 'text/plain', 'application/octet-stream', 'application/json', 'text/html', 'application/txt', ), $aArguments);
    }
    private function _getImportFormatType(array $aArguments, $sFormatType)
    {
        return $this->_getFilteredItemForPortByPrefix('import_format_', $sFormatType, $aArguments);
    }
    private function _getImportOptionKey(array $aArguments, $sImportOptionKey)
    {
        return $this->_getFilteredItemForPortByPrefix('import_option_key_', $sImportOptionKey, $aArguments);
    }
    private function _getFilteredImportData(array $aArguments, $mData, $aStoredOptions, $sFormatType, $sImportOptionKey)
    {
        return $this->addAndApplyFilters($this->oFactory, $this->_getPortFilterHookNames('import_', $aArguments), $mData, $aStoredOptions, $aArguments[ 'pressed_field_id' ], $aArguments[ 'pressed_input_id' ], $sFormatType, $sImportOptionKey, $aArguments[ 'should_merge' ]. $this->oFactory);
    }
    protected function _getFilteredItemForPortByPrefix($sPrefix, $mFilteringValue, array $aArguments)
    {
        return $this->addAndApplyFilters($this->oFactory, $this->_getPortFilterHookNames($sPrefix, $aArguments), $mFilteringValue, $aArguments[ 'pressed_field_id' ], $aArguments[ 'pressed_input_id' ], $this->oFactory);
    }
    protected function _getPortFilterHookNames($sPrefix, array $aArguments)
    {
        return array( $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'pressed_input_id' ], $aArguments[ 'section_id' ] ? $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'section_id' ] .'_' . $aArguments[ 'pressed_field_id' ] : $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'pressed_field_id' ], $aArguments[ 'section_id' ] ? $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'section_id' ] : null, $aArguments[ 'tab_slug' ] ? $sPrefix . $aArguments[ 'page_slug' ] . '_' . $aArguments[ 'tab_slug' ] : null, $sPrefix . $aArguments[ 'page_slug' ], $sPrefix . $aArguments[ 'class_name' ] );
    }
}
