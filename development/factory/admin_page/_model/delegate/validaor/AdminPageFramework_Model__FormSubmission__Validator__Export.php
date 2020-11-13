<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to handle importing options.
 *
 * @package     AdminPageFramework/Factory/AdminPage/Model
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Export extends AdminPageFramework_Model__FormSubmission__Validator__Import {

    /**
     * @remark      moved to after the validation callbacks (3.4.6+)
     */
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 20;
    public $iCallbackParameters = 5;

    /**
     * Handles the callback.
     *
     * @since       3.6.3
     * @return      array       The formatted definition array.
     * @callback    action      try_validation_after_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {

        if ( ! $this->_shouldProceed() ) {
            return;
        }
        $this->_exportOptions(
            $this->oFactory->oProp->aOptions,
            $this->getElement( $aSubmitInformation, 'page_slug' ),
            $this->getElement( $aSubmitInformation, 'tab_slug' )
        );

    }
        /**
         * @since       3.6.3
         * @return      boolean
         */
        private function _shouldProceed() {
            if ( $this->oFactory->hasFieldError() ) {
                return false;
            }
            return isset( $_POST[ '__export' ][ 'submit' ] );
        }

        /**
         * Processes exporting data.
         *
         * @param mixed  $mData
         * @param string $sPageSlug
         * @param string $sTabSlug
         * @since 2.0.0
         * @since 2.1.5  Added additional filters with field id and input id.
         * @since 3.3.1  Moved from `AdminPageFramework_Setting_Port`.
         * @since 3.5.3  Moved from `AdminPageFramework_Form_Model_Port`.
         * @since 3.6.3  Moved from `AdminPageFramework_Form_Model_Export`.
         */
        protected function _exportOptions( $mData, $sPageSlug, $sTabSlug ) {

            $_oExport           = new AdminPageFramework_ExportOptions(
                $this->getHTTPRequestSanitized( $this->getElementAsArray( $_POST, array( '__export' ) ), true ),
                $this->oFactory->oProp->sClassName
            );
            $_aArguments        = array(
                'class_name'        => $this->oFactory->oProp->sClassName,
                'page_slug'         => $sPageSlug,
                'tab_slug'          => $sTabSlug,
                'section_id'        => $_oExport->getSiblingValue( 'section_id' ),
                'pressed_field_id'  => $_oExport->getSiblingValue( 'field_id' ),
                'pressed_input_id'  => $_oExport->getSiblingValue( 'input_id' ),
            );
            $_mData     = $this->_getFilteredExportingData( $_aArguments, $_oExport->getTransientIfSet( $mData ) );
            $_sFileName = $this->_getExportFileName( $_aArguments, $_oExport->getFileName(), $_mData );
            $_oExport->doExport(
                $_mData,
                $this->_getExportFormatType( $_aArguments, $_oExport->getFormat() ),
                $this->_getExportHeaderArray( $_aArguments, $_sFileName, $mData )
            );
            exit;

        }
            /**
             * Retrieves the header array pass to `header()` function.
             *
             * @since       3.5.4
             * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Export`.
             * @return      array
             */
            private function _getExportHeaderArray( array $aArguments, $sFileName, $mData ) {

                $_aHeader = array(
                    'Content-Description' => 'File Transfer',
                    'Content-Disposition' => "attachment; filename=\"{$sFileName}\";",
                );

                return $this->addAndApplyFilters(
                    $this->oFactory,
                    $this->_getPortFilterHookNames( 'export_header_', $aArguments ),
                    $_aHeader,
                    $aArguments[ 'pressed_field_id' ],
                    $aArguments[ 'pressed_input_id' ],
                    $mData,
                    $sFileName,
                    $this->oFactory
                );

            }
            /**
             * Returns the filtered export data.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Export`.
             * @internal
             * @return      string      the filtered export data.
             */
            private function _getFilteredExportingData( array $aArguments, $mData ) {
                return $this->_getFilteredItemForPortByPrefix(
                    'export_',
                    $mData,
                    $aArguments
                );
            }
            /**
             * Returns the export file name.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Export`.
             * @internal
             * @return      string      The export file name.
             */
            private function _getExportFileName( array $aArguments, $sExportFileName, $mData ) {
                return $this->addAndApplyFilters(
                    $this->oFactory,
                    $this->_getPortFilterHookNames( 'export_name_', $aArguments ),
                    $sExportFileName,
                    $aArguments[ 'pressed_field_id' ],
                    $aArguments[ 'pressed_input_id' ],
                    $mData,     // 3.4.6+
                    $this->oFactory       // 3.4.6+
                );
            }
            /**
             * Returns the export format type.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Export`.
             * @internal
             * @return      string      The export format type.
             */
            private function _getExportFormatType( array $aArguments, $sExportFileFormat ) {
                return $this->_getFilteredItemForPortByPrefix(
                    'export_format_',
                    $sExportFileFormat,
                    $aArguments
                );
            }

}
