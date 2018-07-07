<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the export field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 *  <ul>
 *      <li>**file_name** - (optional, string) the file name to download.</li>
 *      <li>**format** - (optional, string) the format type. `array`, `json`, or `text` is supported. Default: `array`.</li>
 *      <li>**data** - (optional, string|array|object ) the data to export.</li>
 *  </ul>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'export_single',
 *      'type'          => 'export',
 *      'description'   => __( 'Download the saved option data.', 'admin-page-framework-loader' ),
 *  )
 * </code>
 * <h3>Export Custom Data</h3>
 * <code>
 *  array( 
 *      'field_id'      => 'export_custom_data',
 *      'title'         => __( 'Custom Exporting Data', 'admin-page-framework-loader' ),
 *      'type'          => 'export',
 *      'data'          => __( 'Hello World! This is custom export data.', 'admin-page-framework-loader' ),
 *      'file_name'     => 'hello_world.txt',
 *      'label'         => __( 'Export Custom Data', 'admin-page-framework-loader' ),
 *      'description'   => __( 'It is possible to set custom data to be downloaded. For that, use the <code>data</code> argument in the field definition array.', 'admin-page-framework-loader' ),    
 *  )
 * </code>
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/export.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 */
class AdminPageFramework_FieldType_export extends AdminPageFramework_FieldType_submit {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'export', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'data'          => null,        // ( array|string|object ) This is for the export field type. Do not set a value here.     
        'format'        => 'json',      // ( string ) for the export field type. Do not set a default value here. Currently array, json, and text are supported.
        'file_name'     => null,        // ( string ) for the export field type. Do not set a default value here.    
        'attributes'    => array(
            'class' => 'button button-primary',
        ),    
    );    

    /**
     * Loads the field type necessary components.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToFieldLoader()`.
     * @internal
     */ 
    protected function setUp() {}
    
    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     * @internal
     */ 
    protected function getScripts() {
        return "";     
    }    

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     * @internal
     */ 
    protected function getStyles() { return ""; }
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.1.5       Moved from the AdminPageFramework_FormField class. The name was changed from getHiddenField().
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {
            
        /* Set the transient data to export - If the value is not an array and the export data is set. */
        if ( isset( $aField['data'] ) ) {
            $this->setTransient( md5( "{$aField['class_name']}_{$aField['input_id']}" ), $aField['data'], 60*2 ); // 2 minutes.
        } 
        
        /* Set some required values */
        $aField['attributes']['name']   = "__export[submit][{$aField['input_id']}]";
        $aField['file_name']            = $aField['file_name'] ? $aField['file_name'] : $this->_generateExportFileName( $aField['option_key'] ? $aField['option_key'] : $aField['class_name'], $aField['format'] );
        $aField['label']                = $aField['label'] ? $aField['label'] : $this->oMsg->get( 'export' );
        
        return parent::getField( $aField );
        
    }
    
    /**
     * Returns the output of hidden fields for this field type that enables custom submit buttons.
     * 
     * @since 3.0.0
     * @internal
     */
    protected function _getExtraInputFields( &$aField ) {

        $_aAttributes = array( 'type' => 'hidden' );
        return
            "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][input_id]",
                    'value' => $aField['input_id'],
                ) + $_aAttributes
            ) . "/>"
            . "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][field_id]",
                    'value' => $aField['field_id'],
                ) + $_aAttributes
            ) . "/>"
            . "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][section_id]",
                    'value' => isset( $aField['section_id'] ) && $aField['section_id'] != '_default' ? $aField['section_id'] : '',
                ) + $_aAttributes
            ) . "/>"
            . "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][file_name]",
                    'value' => $aField['file_name'],
                ) + $_aAttributes
            ) . "/>"
            . "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][format]",
                    'value' => $aField['format'],
                ) + $_aAttributes
            ) . "/>"
            . "<input " . $this->getAttributes( 
                array(
                    'name' => "__export[{$aField['input_id']}][transient]",
                    'value' => isset( $aField['data'] ),
                ) + $_aAttributes
            ) . "/>"
            ;
    }
            
        /**
         * Generates a file name for the exporting data.
         * 
         * A helper function for the above method.
         * 
         * @remark Currently only array, text or json is supported.
         * @since 2.0.0
         * @since 2.1.5 Moved from the AdminPageFramework_FormField class.
         * @internal
         */ 
        private function _generateExportFileName( $sOptionKey, $sExportFormat='json' ) {
                
            switch ( trim( strtolower( $sExportFormat ) ) ) {
                case 'text': // for plain text.
                    $sExt = "txt";
                    break;
                case 'json': // for json.
                    $sExt = "json";
                    break;
                case 'array': // for serialized PHP arrays.
                default: // for anything else, 
                    $sExt = "txt";
                    break;
            }     
                
            return $sOptionKey . '_' . date("Ymd") . '.' . $sExt;
            
        }

}
