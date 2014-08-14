<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_number' ) ) :
/**
 * Defines the number, and range field type.
 * 
 * @package AdminPageFramework
 * @subpackage FieldType
 * @since 2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_number extends AdminPageFramework_FieldType_text {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'number', 'range' );

    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'size' => 30,
            'maxlength' => 400,
            'class' => '',    
            'min' => '',
            'max' => '',
            'step'  => '',
            'readonly' => '',
            'required' => '',
            'placeholder' => '',
            'list' => '',
            'autofocus' => '',
            'autocomplete' => '',
        ),
    );

    /**
     * Loads the field type necessary components.
     */ 
    public function _replyToFieldLoader() {
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    public function _replyToGetScripts() {
        return "";     
    }    

    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
        return "";     
    }
    
    /**
     * Returns the output of the text input field.
     * 
     * @since 2.1.5
     * @since 3.0.0 Removed unnecessary parameters.
     */
    public function _replyToGetField( $aField ) {
        return parent::_replyToGetField( $aField );
    }
        
}
endif;