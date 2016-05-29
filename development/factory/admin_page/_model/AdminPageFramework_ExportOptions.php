<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to export option data.
 *
 * @since           2.0.0
 * @extends         AdminPageFramework_CustomSubmitFields
 * @package         AdminPageFramework
 * @subpackage      Factory/AdminPage/Form
 * @internal
 */
class AdminPageFramework_ExportOptions extends AdminPageFramework_CustomSubmitFields {

    /**
     * Stores the caller object class name.
     * 
     * Used for a generated transient name.
     */
    public $sClassName;
    
    /**
     * Stores the import file name.
     */
    public $sFileName;
    
    /**
     * Stores the import format file type.
     */
    public $sFormatType;
    
    /**
     * Indicates whether the custom data is set by the user.
     */
    public $bIsDataSet;

    /**
     * Sets up properties.
     */
    public function __construct( $aPostExport, $sClassName ) {
        
        // Call the parent constructor.
        parent::__construct( $aPostExport );
        
        // Properties
        $this->sClassName   = $sClassName; // will be used in the getTransientIfSet() method.
        
        // Set the file name to download and the format type. Also find whether the exporting data is set in transient.
        $this->sFileName    = $this->getSubmitValueByType( $aPostExport, $this->sInputID, 'file_name' );
        $this->sFormatType  = $this->getSubmitValueByType( $aPostExport, $this->sInputID, 'format' );
        $this->bIsDataSet   = $this->getSubmitValueByType( $aPostExport, $this->sInputID, 'transient' );
    
    }
    
    public function getTransientIfSet( $vData ) {
        
        if ( $this->bIsDataSet ) {
            $_tmp = $this->getTransient( md5( "{$this->sClassName}_{$this->sInputID}" ) );
            if ( $_tmp !== false ) {
                $vData = $_tmp;
                // Do not delete the transient 
                // as the user may press the button multiple times to get the copies of the file.                
            }
        }
        return $vData;
    }
    
    public function getFileName() {
        return $this->sFileName;
    }
    public function getFormat() {
        return $this->sFormatType;
    }
    
    /**
     * Performs exporting data.
     * 
     * Sample HTML elements that triggers the method.
     * e.g.
     * <code>
     * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
     * <input type="hidden" name="__export[export_sinble][format]" value="json">
     * <input id="export_and_import_export_sinble_0" 
     *  type="submit" 
     *  name="__export[submit][export_sinble]" 
     *  value="Export Options">
     * </code>
     * @since       2.0.0
     * @since       3.5.4       Added the `$aHeader` parameter. Deprecated the `$sFileName` parameter as it is included in the $aHeader definition.
     */ 
    public function doExport( $vData, $sFormatType=null, array $aHeader=array() ) {

        $sFormatType    = isset( $sFormatType ) 
            ? $sFormatType 
            : $this->sFormatType;

        $this->_outputHTTPHeader( $aHeader );
        $this->_outputDataByType( $vData, $sFormatType );
        exit;
        
    }
        /**
         * Sends a HTTP header to a client by an array defining header items.
         * 
         * <h3>Example</h3>
         * <code>
         *  $_aHeader = array(
         *      'Content-Description' => 'File Transfer',
         *      'Content-Disposition' => "attachment; filename={$sFileName}",
         *  );        
         * $this->_outputHTTPHeader( $_aHeader );
         * </code>
         * 
         * @internal    
         * @remark      If a parsing item is an array,
         * @since       3.5.4
         * @return      void
         */
        private function _outputHTTPHeader( array $aHeader, $sKey='' ) {
            
            foreach( $aHeader as $_sKey => $_asValue ) {
                
                // Nested items. Set the key to overrider array keys.
                if ( is_array( $_asValue ) ) {
                    $this->_outputHTTPHeader( $_asValue, $_sKey );
                    continue;
                }
                
                // If the key is explicitly set via the parameter, use it.
                $_sKey = $this->getAOrB( 
                    $sKey,
                    $sKey,
                    $_sKey
                );

                // Make sure there is no space before the colon. If there is, it won't work in FireFox.
                header( "{$_sKey}: {$_asValue}" );
                
            }            
            
        }
        
        /**
         * Outputs the given data by type.
         * 
         * @since       3.5.3
         * @return      void
         * @internal
         */
        private function _outputDataByType( $vData, $sFormatType ) {

            switch ( strtolower( $sFormatType ) ) {
                case 'text': // for plain text.
                    if ( in_array( gettype( $vData ), array( 'array', 'object' ) ) ) {
                        echo AdminPageFramework_Debug::get( $vData, null, false );
                    }
                    echo $vData;
                    return;
                case 'json': // for json.
                    echo json_encode( ( array ) $vData );
                    return ;
                case 'array': // for serialized PHP array.
                default: // for anything else, 
                    echo serialize( ( array ) $vData );
                    return;
            }        
            
        }
        
}
