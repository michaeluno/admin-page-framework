<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with exporting options.
 * 
 * This is meant to be used to export options stored by the framework. 
 * So the data size to export is not expected so large. The exporting date 
 * will be filtered with callbacks and the HTTP header and the data output are sent.
 * 
 * The data are filtered to let the user customize contents. On the other hand, it makes it difficult to deal with large data 
 * as the allocated memory capacity can reach the limit. Maybe at later some point, introduce a `download` field type 
 * that can handle large data, which does not filter the exporting date.
 * 
 * @abstract
 * @since           3.5.3       
 * @extends         AdminPageFramework_Form_Model_Import
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model_Export extends AdminPageFramework_Form_Model_Import {     
      
    /**
     * Processes exporting data.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added additional filters with field id and input id.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Port`.
     * @since       3.5.3       Moved from `AdminPageFramework_Form_Model_Port`.
     */
    protected function _exportOptions( $mData, $sPageSlug, $sTabSlug ) {

        $_oExport           = new AdminPageFramework_ExportOptions( 
            $_POST['__export'], 
            $this->oProp->sClassName 
        );
        $_aArguments        = array(
            'class_name'        => $this->oProp->sClassName,
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
         * Retrieves the header array pass to `header()` funciton.
         * 
         * @since       3.5.4
         * @return      array
         */
        private function _getExportHeaderArray( array $aArguments, $sFileName, $mData ) {

            $_aHeader = array(  
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => "attachment; filename=\"{$sFileName}\";",
            ); 
            
            return $this->oUtil->addAndApplyFilters(
                $this,
                $this->_getPortFilterHookNames( 'export_header_', $aArguments ),
                $_aHeader, 
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $mData,
                $sFileName,
                $this
            );                
            
        }
        /**
         * Returns the filtered export data.
         * @since       3.5.3
         * @iunternal   
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
         * @iunternal   
         * @return      string      The export file name.
         */        
        private function _getExportFileName( array $aArguments, $sExportFileName, $mData ) {        
            return $this->oUtil->addAndApplyFilters(
                $this,
                $this->_getPortFilterHookNames( 'export_name_', $aArguments ),
                $sExportFileName, 
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $mData,     // 3.4.6+
                $this       // 3.4.6+
            );    
        }
        /**
         * Returns the export format type.
         * @since       3.5.3
         * @iunternal   
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