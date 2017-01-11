<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * A file uploader that lets the user upload files.
 * 
 * This class defines the file field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array( 
 *      'field_id'              => 'file_single',
 *      'title'                 => __( 'File', 'admin-page-framework-loader' ),
 *      'type'                  => 'file',
 *      'label'                 => __( 'Select the file', 'admin-page-framework-loader' ) . ": ",
 *  ),     
 * </code>
 * 
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/file.png
 * @package     AdminPageFramework
 * @subpackage  Common/Form/FieldType
 * @since       2.1.5
 */
class AdminPageFramework_FieldType_file extends AdminPageFramework_FieldType_text {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'file', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'accept'    => 'audio/*|video/*|image/*|MIME_type',
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
    protected function getScripts() { return ""; }    

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
     * @since       2.0.0
     * @since       3.0.0     Reconstructed entirely.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     * @internal
     */
    protected function getField( $aField ) {
            
        return parent::getField( $aField )
        
            // hidden inputs that triggers a validation callback as the framework will not trigger a section validation callback 
            // when the submit input array does not contains the element. So this will insert a dummy element to the input array.
            // The unset flag will help remove the dummy element after the validation callbacks are processed.
            . $this->getHTMLTag( 
                'input',
                array(
                    'type'  => 'hidden',
                    'value' => '',
                    'name'  => $aField[ 'attributes' ][ 'name' ] . '[_dummy_value]',
                )
            )            
            . $this->getHTMLTag( 
                'input',
                array(
                    'type'  => 'hidden',
                    'name'  => '__unset_' . $aField[ '_structure_type' ] . '[' . $aField[ '_input_name_flat' ] . '|_dummy_value' . ']',
                    'value' => $aField[ '_input_name_flat' ] . '|_dummy_value',
                    'class' => 'unset-element-names element-address',
                )
            );
            
    }    

}
