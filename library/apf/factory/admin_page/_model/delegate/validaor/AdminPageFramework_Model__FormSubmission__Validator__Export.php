<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Model__FormSubmission__Validator__Export extends AdminPageFramework_Model__FormSubmission__Validator__Import {
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 20;
    public $iCallbackParameters = 5;
    public function _replyToCallback($aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory)
    {
        if (! $this->_shouldProceed()) {
            return;
        }
        $this->_exportOptions($this->oFactory->oProp->aOptions, $this->getElement($aSubmitInformation, 'page_slug'), $this->getElement($aSubmitInformation, 'tab_slug'));
    }
    private function _shouldProceed()
    {
        if ($this->oFactory->hasFieldError()) {
            return false;
        }
        return isset($_POST[ '__export' ][ 'submit' ]);
    }
    protected function _exportOptions($mData, $sPageSlug, $sTabSlug)
    {
        $_oExport = new AdminPageFramework_ExportOptions($this->getHTTPRequestSanitized($this->getElementAsArray($_POST, array( '__export' ))), $this->oFactory->oProp->sClassName);
        $_aArguments = array( 'class_name' => $this->oFactory->oProp->sClassName, 'page_slug' => $sPageSlug, 'tab_slug' => $sTabSlug, 'section_id' => $_oExport->getSiblingValue('section_id'), 'pressed_field_id' => $_oExport->getSiblingValue('field_id'), 'pressed_input_id' => $_oExport->getSiblingValue('input_id'), );
        $_mData = $this->_getFilteredExportingData($_aArguments, $_oExport->getTransientIfSet($mData));
        $_sFileName = $this->_getExportFileName($_aArguments, $_oExport->getFileName(), $_mData);
        $_oExport->doExport($_mData, $this->_getExportFormatType($_aArguments, $_oExport->getFormat()), $this->_getExportHeaderArray($_aArguments, $_sFileName, $mData));
        exit;
    }
    private function _getExportHeaderArray(array $aArguments, $sFileName, $mData)
    {
        $_aHeader = array( 'Content-Description' => 'File Transfer', 'Content-Disposition' => "attachment; filename=\"{$sFileName}\";", );
        return $this->addAndApplyFilters($this->oFactory, $this->_getPortFilterHookNames('export_header_', $aArguments), $_aHeader, $aArguments[ 'pressed_field_id' ], $aArguments[ 'pressed_input_id' ], $mData, $sFileName, $this->oFactory);
    }
    private function _getFilteredExportingData(array $aArguments, $mData)
    {
        return $this->_getFilteredItemForPortByPrefix('export_', $mData, $aArguments);
    }
    private function _getExportFileName(array $aArguments, $sExportFileName, $mData)
    {
        return $this->addAndApplyFilters($this->oFactory, $this->_getPortFilterHookNames('export_name_', $aArguments), $sExportFileName, $aArguments[ 'pressed_field_id' ], $aArguments[ 'pressed_input_id' ], $mData, $this->oFactory);
    }
    private function _getExportFormatType(array $aArguments, $sExportFileFormat)
    {
        return $this->_getFilteredItemForPortByPrefix('export_format_', $sExportFileFormat, $aArguments);
    }
}
