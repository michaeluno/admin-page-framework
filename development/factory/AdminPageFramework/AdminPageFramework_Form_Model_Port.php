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
 * @extends         AdminPageFramework_Router
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model_Port extends AdminPageFramework_Router {     
       
     /**
     * Processes importing data.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added additional filters with field id and input id.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Port`.
     */
    protected function _importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) {
        
        $oImport            = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );    
        $sSectionID         = $oImport->getSiblingValue( 'section_id' );
        $sPressedFieldID    = $oImport->getSiblingValue( 'field_id' );
        $sPressedInputID    = $oImport->getSiblingValue( 'input_id' );
        $bMerge             = $oImport->getSiblingValue( 'is_merge' );
    
        // Check if there is an upload error.
        if ( $oImport->getError() > 0 ) {
            $this->setSettingNotice( $this->oMsg->get( 'import_error' ) );    
            return $aStoredOptions; // do not change the framework's options.
        }

        // Apply filters to the uploaded file's MIME type.
        $aMIMEType = $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                "import_mime_types_{$this->oProp->sClassName}_{$sPressedInputID}", 
                $sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_mime_types_{$this->oProp->sClassName}_{$sPressedFieldID}", 
                $sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}" : null, 
                $sTabSlug ? "import_mime_types_{$sPageSlug}_{$sTabSlug}" : null, 
                "import_mime_types_{$sPageSlug}", 
                "import_mime_types_{$this->oProp->sClassName}" ),
            array( 'text/plain', 'application/octet-stream' ),        // .json file is dealt as a binary file.
            $sPressedFieldID,
            $sPressedInputID,
            $this           // 3.4.6+
        );                

        // Check the uploaded file MIME type.
        $_sType = $oImport->getType();
        if ( ! in_array( $oImport->getType(), $aMIMEType ) ) {        
            $this->setSettingNotice( sprintf( $this->oMsg->get( 'uploaded_file_type_not_supported' ), $_sType ) );
            return $aStoredOptions;        // do not change the framework's options.
        }

        // Retrieve the importing data.
        $vData = $oImport->getImportData();
        if ( $vData === false ) {
            $this->setSettingNotice( $this->oMsg->get( 'could_not_load_importing_data' ) );     
            return $aStoredOptions; // do not change the framework's options.
        }
        
        // Apply filters to the data format type.
        $sFormatType = $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                "import_format_{$this->oProp->sClassName}_{$sPressedInputID}",
                $sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}" : null,
                $sTabSlug ? "import_format_{$sPageSlug}_{$sTabSlug}" : null,
                "import_format_{$sPageSlug}",
                "import_format_{$this->oProp->sClassName}"
            ),
            $oImport->getFormatType(), // the set format type, array, json, or text.
            $sPressedFieldID,
            $sPressedInputID,
            $this           // 3.4.6+
        );    

        // Format it.
        $oImport->formatImportData( $vData, $sFormatType ); // it is passed as reference.    
        
        // Apply filters to the importing option key.
        $sImportOptionKey = $this->oUtil->addAndApplyFilters(
            $this,
            array(
                "import_option_key_{$this->oProp->sClassName}_{$sPressedInputID}",
                $sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_option_key_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}" : null,
                $sTabSlug ? "import_option_key_{$sPageSlug}_{$sTabSlug}" : null,
                "import_option_key_{$sPageSlug}",
                "import_option_key_{$this->oProp->sClassName}"
            ),
            $oImport->getSiblingValue( 'option_key' ),    
            $sPressedFieldID,
            $sPressedInputID,
            $this           // 3.4.6+
        );
        
        // Apply filters to the importing data.
        $vData = $this->oUtil->addAndApplyFilters(
            $this,
            array(
                "import_{$this->oProp->sClassName}_{$sPressedInputID}",
                $sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}" : null,
                $sTabSlug ? "import_{$sPageSlug}_{$sTabSlug}" : null,
                "import_{$sPageSlug}",
                "import_{$this->oProp->sClassName}"
            ),
            $vData,
            $aStoredOptions,
            $sPressedFieldID,
            $sPressedInputID,
            $sFormatType,
            $sImportOptionKey,
            $bMerge.
            $this           // 3.4.6+
        );

        // Set the update notice
        $bEmpty = empty( $vData );
        $this->setSettingNotice(  
            $bEmpty ? $this->oMsg->get( 'not_imported_data' ) : $this->oMsg->get( 'imported_data' ), 
            $bEmpty ? 'error' : 'updated',
            $this->oProp->sOptionKey, // message id
            false // do not override 
        );
                
        if ( $sImportOptionKey != $this->oProp->sOptionKey ) {
            update_option( $sImportOptionKey, $vData );
            return $aStoredOptions; // do not change the framework's options.
        }
    
        // The option data to be saved will be returned.
        return $bMerge ?
            $this->oUtil->unitArrays( $vData, $aStoredOptions )
            : $vData;
                        
    }
    
    /**
     * Processes exporting data.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added additional filters with field id and input id.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Port`.
     */
    protected function _exportOptions( $vData, $sPageSlug, $sTabSlug ) {

        $oExport            = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProp->sClassName );
        $sSectionID         = $oExport->getSiblingValue( 'section_id' );
        $sPressedFieldID    = $oExport->getSiblingValue( 'field_id' );
        $sPressedInputID    = $oExport->getSiblingValue( 'input_id' );
        
        // If the data is set in transient,
        $vData = $oExport->getTransientIfSet( $vData );

        // Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
        // and the magic method should be triggered.     
        $vData = $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                "export_{$this->oProp->sClassName}_{$sPressedInputID}", 
                $sSectionID ? "export_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_{$this->oProp->sClassName}_{$sPressedFieldID}",     
                $sTabSlug ? "export_{$sPageSlug}_{$sTabSlug}" : null,     // null will be skipped in the method
                "export_{$sPageSlug}", 
                "export_{$this->oProp->sClassName}" 
            ),
            $vData,
            $sPressedFieldID,
            $sPressedInputID,
            $this               // 3.4.6+
        );    
        
        $sFileName = $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                "export_name_{$this->oProp->sClassName}_{$sPressedInputID}",
                "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sSectionID ? "export_name_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sTabSlug ? "export_name_{$sPageSlug}_{$sTabSlug}" : null,
                "export_name_{$sPageSlug}",
                "export_name_{$this->oProp->sClassName}" 
            ),
            $oExport->getFileName(),
            $sPressedFieldID,
            $sPressedInputID,
            $vData,     // 3.4.6+
            $this       // 3.4.6+
        );    
        
        $sFormatType = $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                "export_format_{$this->oProp->sClassName}_{$sPressedInputID}",
                "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sSectionID ? "export_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
                $sTabSlug ? "export_format_{$sPageSlug}_{$sTabSlug}" : null,
                "export_format_{$sPageSlug}",
                "export_format_{$this->oProp->sClassName}" 
            ),
            $oExport->getFormat(),
            $sPressedFieldID,
            $sPressedInputID,
            $this       // 3.4.6+
        );    
        $oExport->doExport( $vData, $sFileName, $sFormatType );
        exit;
        
    }         
            
}