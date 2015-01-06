<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to import option data.
 *
 * @since           2.0.0
 * @extends         AdminPageFramework_CustomSubmitFields
 * @package         AdminPageFramework
 * @subpackage      Setting
 * @internal
 */
class AdminPageFramework_ImportOptions extends AdminPageFramework_CustomSubmitFields {
    
    /* Example of $_FILES for a single import field. 
        Array (
            [__import] => Array (
                [name] => Array (
                   [my_section_my_field_the_index] => APF_GettingStarted_20130709 (1).json
                )
                [type] => Array (
                    [my_section_my_field_the_index] => application/octet-stream
                )
                [tmp_name] => Array (
                    [my_section_my_field_the_index] => Y:\wamp\tmp\php7994.tmp
                )
                [error] => Array (
                    [my_section_my_field_the_index] => 0
                )
                [size] => Array (
                    [my_section_my_field_the_index] => 715
                )
            )
        )
    */
    
    public function __construct( $aFilesImport, $aPostImport ) {

        // Call the parent constructor. This must be done before the getFieldID() method that uses the $aPostElement property.
        parent::__construct( $aPostImport );
    
        $this->aFilesImport = $aFilesImport;
        
    }
    
    private function getElementInFilesArray( $aFilesImport, $sInputID, $sElementKey='error' ) {

        $sElementKey = strtolower( $sElementKey );
        
        return isset( $aFilesImport[ $sElementKey ][ $sInputID ] )
            ? $aFilesImport[ $sElementKey ][ $sInputID ]
            : null;
        
    }    
        
    public function getError() {
        
        return $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'error' );
        
    }
    public function getType() {

        return $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'type' );
        
    }
    public function getImportData() {
        
        // Retrieve the uploaded file path.
        $sFilePath = $this->getElementInFilesArray( $this->aFilesImport, $this->sInputID, 'tmp_name' );
        
        // Read the file contents.
        $vData = file_exists( $sFilePath ) ? file_get_contents( $sFilePath, true ) : false;
        
        return $vData;
        
    }
    public function formatImportData( &$vData, $sFormatType=null ) {
        
        $sFormatType = isset( $sFormatType ) ? $sFormatType : $this->getFormatType();
        switch ( strtolower( $sFormatType ) ) {
            case 'text': // for plain text.
                return; // do nothing
            case 'json': // for json.
                $vData = json_decode( ( string ) $vData, true ); // the second parameter indicates to decode it as array.
                return;
            case 'array': // for serialized PHP array.
            default: // for anything else, 
                $vData = maybe_unserialize( trim( $vData ) );
                return;
        }     
    
    }
    public function getFormatType() {
                    
        $this->sFormatType = isset( $this->sFormatType ) && $this->sFormatType 
            ? $this->sFormatType
            : $this->getSubmitValueByType( $this->aPost, $this->sInputID, 'format' );

        return $this->sFormatType;
        
    }
    
}