<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class of form input classes that return outputs of input form elements.
 * 
 * @package     AdminPageFramework
 * @subpackage  FormInput
 * @since       3.4.0
 * @internal
 */
abstract class AdminPageFramework_Input_Base extends AdminPageFramework_WPUtility {
    
    /**
     * Stores the field definition array.
     * 
     * @since       3.4.0
     */
    public $aField = array();

    /**
     * Stores the options of how the input elements should be constructed.
     * 
     * @since       3.4.0
     */
    public $aOptions = array();
    
    /**
     * Represents the structure of the options array.
     * 
     * @since       3.4.0
     */
    public $aStructureOptions = array(
        'input_container_tag'          => 'span',
        'input_container_attributes'    => array(
            'class' => 'admin-page-framework-input-container',
        ),
        'label_container_tag'          => 'span',
        'label_container_attributes'    => array(
            'class' => 'admin-page-framework-input-label-string',
        ),         
    );
    
    /**
     * Sets up properties.
     * 
     * @since       3.4.0
     */
    public function __construct( array $aField, array $aOptions=array() ) {
        
        $this->aField   = $aField;
        $this->aOptions = $aOptions + $this->aStructureOptions;
        
    }
    
    /**
     * Returns the output of the input element.
     * 
     * @remark       This method should be overridden in each extended class.
     * @since        3.4.0     
     */
    public function get() {}
    
}