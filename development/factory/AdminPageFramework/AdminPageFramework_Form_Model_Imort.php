<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with exporting and importing options.
 * 
 * 
 * @abstract
 * @since           3.0.0
 * @since           3.3.1       Changed the name from `AdminPageFramework_Setting_Port`.
 * @since           3.5.3       Changed the name from `AdminPageFramework_Form_Model_Port`.
 * @extends         AdminPageFramework_Router
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model_Import extends AdminPageFramework_Router {     
       
    /**
     * Processes importing data.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added additional filters with field id and input id.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Port`.
     */
    protected function _importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) {
        
        $_oImport           = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );      
        $_aArguments        = array(
            'class_name'        => $this->oProp->sClassName,
            'page_slug'         => $sPageSlug,
            'tab_slug'          => $sTabSlug,
            'section_id'        => $_oImport->getSiblingValue( 'section_id' ),
            'pressed_field_id'  => $_oImport->getSiblingValue( 'field_id' ),
            'pressed_input_id'  => $_oImport->getSiblingValue( 'input_id' ),
            'should_merge'      => $_oImport->getSiblingValue( 'is_merge' ),
        );
        
        // Check if there is an upload error.
        if ( $_oImport->getError() > 0 ) {
            $this->setSettingNotice( $this->oMsg->get( 'import_error' ) );    
            return $aStoredOptions; // do not change the framework's options.
        }
    
        // Check the uploaded file MIME type.
        $_aMIMEType = $this->_getImportMIMEType( $_aArguments );
        $_sType     = $_oImport->getType();
        if ( ! in_array( $_sType, $_aMIMEType ) ) {        
            $this->setSettingNotice( sprintf( $this->oMsg->get( 'uploaded_file_type_not_supported' ), $_sType ) );
            return $aStoredOptions;        // do not change the framework's options.
        }

        // Retrieve the importing data.
        $_mData = $_oImport->getImportData();
        if ( false === $_mData ) {
            $this->setSettingNotice( $this->oMsg->get( 'could_not_load_importing_data' ) );     
            return $aStoredOptions; // do not change the framework's options.
        }
        
        // Apply filters to the data format type.
        $_sFormatType = $this->_getImportFormatType( $_aArguments, $_oImport->getFormatType() );
    
        // Format it - passed by reference.    
        $_oImport->formatImportData( $_mData, $_sFormatType );
        
        // Apply filters to the importing option key.
        $_sImportOptionKey = $this->_getImportOptionKey( $_aArguments, $_oImport->getSiblingValue( 'option_key' ) );
            
        // Apply filters to the importing data.
        $_mData = $this->_getFilteredImportData( $_aArguments, $_mData, $aStoredOptions, $_sFormatType, $_sImportOptionKey );
    
        // Set the update notice
        $_bEmpty = empty( $_mData );
        $this->setSettingNotice(  
            $_bEmpty 
                ? $this->oMsg->get( 'not_imported_data' ) 
                : $this->oMsg->get( 'imported_data' ), 
            $_bEmpty 
                ? 'error' 
                : 'updated',
            $this->oProp->sOptionKey, // message id
            false // do not override 
        );
                
        if ( $_sImportOptionKey != $this->oProp->sOptionKey ) {
            update_option( $_sImportOptionKey, $_mData );
            return $aStoredOptions; // do not change the framework's options.
        }
    
        // The option data to be saved will be returned.
        return $_aArguments['should_merge']
            ? $this->oUtil->uniteArrays( $_mData, $aStoredOptions )
            : $_mData;
                        
    }
        /**
         * Returns the filtered MIME types for a importing file.
         * @since       3.5.3
         * @return      array       An array holding processable MIME types.       
         */
        private function _getImportMIMEType( array $aArguments ) {            
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 
                    'import_mime_types_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'], 
                    $aArguments['section_id']
                        ? 'import_mime_types_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id']
                        : 'import_mime_types_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'], 
                    $aArguments['section_id']
                        ? 'import_mime_types_' . $aArguments['class_name'] . '_' . $aArguments['section_id']
                        : null, 
                    $aArguments['tab_slug']
                        ? 'import_mime_types_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug']
                        : null, 
                    'import_mime_types_' . $aArguments['page_slug'], 
                    'import_mime_types_' . $aArguments['class_name'],
                ),
                array( 'text/plain', 'application/octet-stream' ), // .json file is dealt as a binary file.
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $this           // 3.4.6+
            );  
        }    
            
        /**
         * Returns the import format type.
         * @since       3.5.3
         * @internal   
         * @return      string      The import format type. Should be either 'array', 'json', or 'text'.
         */
        private function _getImportFormatType( array $aArguments, $sFormatType ) {
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 
                    'import_format_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'],
                    $aArguments['section_id'] 
                        ? 'import_format_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id']
                        : 'import_format_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['section_id'] 
                        ? 'import_format_' . $aArguments['class_name'] . '_' . $aArguments['section_id']
                        : null,
                    $aArguments['tab_slug'] 
                        ? 'import_format_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug']
                        : null,
                    'import_format_' . $aArguments['page_slug'],
                    'import_format_' . $aArguments['class_name']
                ),
                $sFormatType, // the set format type, array, json, or text.
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $this           // 3.4.6+
            );    
        }            
            
        /**
         * Returns the import option key.
         * @since       3.5.3
         * @internal   
         * @return      string      The import option key.
         */    
        private function _getImportOptionKey( array $aArguments, $sImportOptionKey ) {
            return $this->oUtil->addAndApplyFilters(
                $this,
                array(
                    'import_option_key_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'],
                    $aArguments['section_id'] 
                        ? 'import_option_key_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] . '_' . $aArguments['pressed_field_id']
                        : 'import_option_key_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['section_id'] 
                        ? 'import_option_key_' . $aArguments['class_name'] . '_' . $aArguments['section_id']
                        : null,
                    $aArguments['tab_slug'] 
                        ? 'import_option_key_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug'] 
                        : null,
                    'import_option_key_' . $aArguments['page_slug'],
                    'import_option_key_' . $aArguments['class_name']
                ),
                $sImportOptionKey,    
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $this           // 3.4.6+
            );
        }
 
        /**
         * Returns the filtered import data.
         * @since       3.5.3
         * @internal   
         * @return      string      The filtered import data.
         */    
        private function _getFilteredImportData( array $aArguments, $mData, $aStoredOptions, $sFormatType, $sImportOptionKey ) {
            return $this->oUtil->addAndApplyFilters(
                $this,
                array(
                    'import_' . $aArguments['class_name'] . '_' . $aArguments['pressed_input_id'],
                    $aArguments['section_id'] 
                        ? 'import_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] .'_' . $aArguments['pressed_field_id']
                        : 'import_' . $aArguments['class_name'] . '_' . $aArguments['pressed_field_id'],
                    $aArguments['section_id'] 
                        ? 'import_' . $aArguments['class_name'] . '_' . $aArguments['section_id'] 
                        : null,
                    $aArguments['tab_slug'] 
                        ? 'import_' . $aArguments['page_slug'] . '_' . $aArguments['tab_slug']
                        : null,
                    'import_' . $aArguments['page_slug'],
                    'import_' . $aArguments['class_name']
                ),
                $mData,
                $aStoredOptions,
                $aArguments['pressed_field_id'],
                $aArguments['pressed_input_id'],
                $sFormatType,
                $sImportOptionKey,
                $aArguments['should_merge'].
                $this           // 3.4.6+
            );
        }
 
}