<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the file field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @internal
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
     */ 
    protected function setUp() {}
    
    /**
     * Returns the field type specific JavaScript script.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     */ 
    protected function getScripts() { return ""; }    

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() { return ""; }
    
    /**
     * Returns the output of the field type.
     * 
     * @since       2.0.0
     * @since       3.0.0     Reconstructed entirely.
     * @since       3.3.1     Changed from `_replyToGetField()`.
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
                    'name'  => '__unset_' . $aField[ '_fields_type' ] . '[' . $aField[ '_input_name_flat' ] . '|_dummy_value' . ']',
                    'value' => $aField[ '_input_name_flat' ] . '|_dummy_value',
                    'class' => 'unset-element-names element-address',
                )
            );
            
    }    

}