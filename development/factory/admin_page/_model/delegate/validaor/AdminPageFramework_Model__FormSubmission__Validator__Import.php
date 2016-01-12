<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle importing options.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Import extends AdminPageFramework_Model__FormSubmission__Validator_Base {
    
    /**
     * @remark      moved to after the validation callbacks (3.4.6+)
     */
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 10;
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
        $this->_doImportOptions(
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
            return isset( 
                $_POST[ '__import' ][ 'submit' ], 
                $_FILES[ '__import' ] 
            );                   
        }
        /**
         * Handles importing options.
         * 
         * @internal
         * @since       3.5.3
         * @since       3.6.3       Moved from `AdminPageFramework_Validation`.
         * @return      void
         */
        private function _doImportOptions( $sPageSlug, $sTabSlug ) {
            
            // Import data are set.
            $_oException = new Exception( 'aReturn' );
            $_oException->aReturn = $this->_importOptions( 
                $this->oFactory->oProp->aOptions, 
                $sPageSlug, 
                $sTabSlug 
            );
            throw $_oException;
            
        }      
            /**
             * Processes importing data.
             * 
             * @since       2.0.0
             * @since       2.1.5       Added additional filters with field id and input id.
             * @since       3.3.1       Moved from `AdminPageFramework_Setting_Port`.
             */
            private function _importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) {
                
                $_oImport           = new AdminPageFramework_ImportOptions( $_FILES[ '__import' ], $_POST[ '__import' ] );      
                $_aArguments        = array(
                    'class_name'        => $this->oFactory->oProp->sClassName,
                    'page_slug'         => $sPageSlug,
                    'tab_slug'          => $sTabSlug,
                    'section_id'        => $_oImport->getSiblingValue( 'section_id' ),
                    'pressed_field_id'  => $_oImport->getSiblingValue( 'field_id' ),
                    'pressed_input_id'  => $_oImport->getSiblingValue( 'input_id' ),
                    'should_merge'      => $_oImport->getSiblingValue( 'is_merge' ),
                );
                
                // Check if there is an upload error.
                if ( $_oImport->getError() > 0 ) {
                    $this->oFactory->setSettingNotice( $this->oFactory->oMsg->get( 'import_error' ) );
                    return $aStoredOptions; // do not change the framework's options.
                }
            
                // Check the uploaded file MIME type.
                $_aMIMEType = $this->_getImportMIMEType( $_aArguments );
                $_sType     = $_oImport->getType();
                if ( ! in_array( $_sType, $_aMIMEType ) ) {        
                    $this->oFactory->setSettingNotice( sprintf( $this->oFactory->oMsg->get( 'uploaded_file_type_not_supported' ), $_sType ) );
                    return $aStoredOptions;        // do not change the framework's options.
                }

                // Retrieve the importing data.
                $_mData = $_oImport->getImportData();
                if ( false === $_mData ) {
                    $this->oFactory->setSettingNotice( $this->oFactory->oMsg->get( 'could_not_load_importing_data' ) );
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
            
                // Set an update notice
                $this->_setImportAdminNotice( empty( $_mData ) );
                        
                if ( $_sImportOptionKey != $this->oFactory->oProp->sOptionKey ) {
                    update_option( $_sImportOptionKey, $_mData );
                    return $aStoredOptions; // do not change the framework's options.
                }
            
                // The option data to be saved will be returned.
                return $_aArguments[ 'should_merge' ]
                    ? $this->uniteArrays( $_mData, $aStoredOptions )
                    : $_mData;
                                
            }
                /**
                 * Sets update admin notice.
                 * @since       3.5.3
                 * @return      void
                 */
                private function _setImportAdminNotice( $bEmpty ) {
                    $this->oFactory->setSettingNotice(  
                        $bEmpty 
                            ? $this->oFactory->oMsg->get( 'not_imported_data' ) 
                            : $this->oFactory->oMsg->get( 'imported_data' ), 
                        $bEmpty 
                            ? 'error' 
                            : 'updated',
                        $this->oFactory->oProp->sOptionKey, // message id
                        false // do not override 
                    );
                }
                
                /**
                 * Returns the filtered MIME types for a importing file.
                 * @since       3.5.3
                 * @return      array       An array holding processable MIME types.       
                 */
                private function _getImportMIMEType( array $aArguments ) {  
                    return $this->_getFilteredItemForPortByPrefix(
                        'import_mime_types_',
                        array( 
                            'text/plain', 
                            'application/octet-stream', // .json file is dealt as a binary file.
                            'application/json',         // 3.7.0+ some servers cannot upload json files without this
                            'text/html',                // 3.7.2+
                            'application/txt',          // 3.7.2+
                        ), 
                        $aArguments
                    ); 
                }    
                    
                /**
                 * Returns the import format type.
                 * @since       3.5.3
                 * @internal   
                 * @return      string      The import format type. Should be either 'array', 'json', or 'text'.
                 */
                private function _getImportFormatType( array $aArguments, $sFormatType ) {
                    return $this->_getFilteredItemForPortByPrefix(
                        'import_format_',
                        $sFormatType,
                        $aArguments
                    );
                }            
                    
                /**
                 * Returns the import option key.
                 * @since       3.5.3
                 * @internal   
                 * @return      string      The import option key.
                 */    
                private function _getImportOptionKey( array $aArguments, $sImportOptionKey ) {
                    return $this->_getFilteredItemForPortByPrefix(
                        'import_option_key_',
                        $sImportOptionKey,    
                        $aArguments
                    );
                }
         
                /**
                 * Returns the filtered import data.
                 * @since       3.5.3
                 * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Import`.
                 * @internal   
                 * @return      string      The filtered import data.
                 */    
                private function _getFilteredImportData( array $aArguments, $mData, $aStoredOptions, $sFormatType, $sImportOptionKey ) {
                    return $this->addAndApplyFilters(
                        $this->oFactory,
                        $this->_getPortFilterHookNames( 'import_', $aArguments ),
                        $mData,
                        $aStoredOptions,
                        $aArguments[ 'pressed_field_id' ],
                        $aArguments[ 'pressed_input_id' ],
                        $sFormatType,
                        $sImportOptionKey,
                        $aArguments[ 'should_merge' ].
                        $this->oFactory           // 3.4.6+
                    );
                }
                
    /**
     * Returns the value which filters for export/import options were applied.
     * 
     * @internal    
     * @since       3.5.3
     * @access      protected       A child class accesses this method.
     * @remark      Accessed from `AdminPageFramework_Form_Model_Export`.
     * @return      mixed       The filtered value.
     * @param       string      The filter prefix.
     * @param       mixed       The subject filtering value.
     * @param       array       An argument array holding submit information. Part of this will be passed to the filter callbacks but this itself does not mean is for the filter callbacks.
     */
    protected function _getFilteredItemForPortByPrefix( $sPrefix, $mFilteringValue, array $aArguments ) {
        return $this->addAndApplyFilters(
            $this->oFactory,
            $this->_getPortFilterHookNames( $sPrefix, $aArguments ),
            $mFilteringValue,    
            $aArguments[ 'pressed_field_id' ],
            $aArguments[ 'pressed_input_id' ],
            $this->oFactory           // 3.4.6+
        );                
    }
    /**
     * Returns an array holding the generated filter names by the given prefix.
     * 
     * @access      protected       A child class accesses this method.
     * @remark      Accessed from `AdminPageFramework_Form_Model_Export`.
     * @since       3.5.3
     * @return      array       An array holding the generated filter names by the given prefix.
     */
    protected function _getPortFilterHookNames( $sPrefix, array $aArguments ) {
        
        return array(
            $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'pressed_input_id' ],
            $aArguments[ 'section_id' ] 
                ? $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'section_id' ] .'_' . $aArguments[ 'pressed_field_id' ]
                : $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'pressed_field_id' ],
            $aArguments[ 'section_id' ] 
                ? $sPrefix . $aArguments[ 'class_name' ] . '_' . $aArguments[ 'section_id' ] 
                : null,
            $aArguments[ 'tab_slug' ] 
                ? $sPrefix . $aArguments[ 'page_slug' ] . '_' . $aArguments[ 'tab_slug' ]
                : null,
            $sPrefix . $aArguments[ 'page_slug' ],
            $sPrefix . $aArguments[ 'class_name' ]
        );            
        
    }        

     
}
