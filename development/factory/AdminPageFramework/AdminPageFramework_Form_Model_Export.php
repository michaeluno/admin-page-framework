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

        $_oExport           = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProp->sClassName );        
        $_aArguments        = array(
            'class_name'        => $this->oProp->sClassName,
            'page_slug'         => $sPageSlug,
            'tab_slug'          => $sTabSlug,
            'section_id'        => $_oExport->getSiblingValue( 'section_id' ),
            'pressed_field_id'  => $_oExport->getSiblingValue( 'field_id' ),
            'pressed_input_id'  => $_oExport->getSiblingValue( 'input_id' ),        
        );    
        $_mData = $this->_getFilteredExportingData( $_aArguments, $_oExport->getTransientIfSet( $mData ) );
        $_oExport->doExport( 
            $_mData,
            $this->_getExportFileName( $_aArguments, $_oExport->getFileName(), $_mData ), 
            $this->_getExportFormatType( $_aArguments, $_oExport->getFormat() )
        );
        exit;
        
    }      
        /**
         * 
         * @since       3.5.3
         * @iunternal   
         * @return      string
         */
        private function _getFilteredExportingData( array $aArguments, $mData ) {
    
            // 'section_id'        
                   
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 
                    'export_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'], 
                    $aArguments['section_id'] 
                        ? 'export_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id'] 
                        : 'export_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],     
                    $aArguments['tab_slug']
                        ? 'export_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug'] 
                        : null,     // null will be skipped in the method
                    'export_' . $aArguments['page_slug'], 
                    'export_' . $aArguments['class_name'] 
                ),
                $mData,
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $this               // 3.4.6+
            );    
        }      
        /**
         * 
         * @since       3.5.3
         * @iunternal   
         * @return      string
         */        
        private function _getExportFileName( array $aArguments, $sExportFileName, $mData ) {        
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 
                    'export_name_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'],
                    'export_name_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['section_id'] 
                        ? 'export_name_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id']
                        : 'export_name_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['tab_slug'] 
                        ? 'export_name_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug'] 
                        : null,
                    'export_name_' . $aArguments['page_slug'],
                    'export_name_' . $aArguments['class_name'] 
                ),
                $sExportFileName, 
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $mData,     // 3.4.6+
                $this       // 3.4.6+
            );    
        }
        /**
         * 
         * @since       3.5.3
         * @iunternal   
         * @return      string
         */        
        private function _getExportFormatType( array $aArguments, $sExportFileFormat ) {
            
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 
                    'export_format_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'],
                    'export_format_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['section_id'] 
                        ? 'export_format_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id'] 
                        : 'export_format_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['tab_slug'] 
                        ? 'export_format_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug']
                        : null,
                    'export_format_' . $aArguments['page_slug'],
                    'export_format_' . $aArguments['class_name'] 
                ),
                $sExportFileFormat,
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $this       // 3.4.6+
            ); 
        }

}