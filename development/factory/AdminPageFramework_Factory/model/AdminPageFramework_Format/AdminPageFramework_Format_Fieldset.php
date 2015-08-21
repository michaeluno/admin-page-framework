<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form field definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_Fieldset extends AdminPageFramework_Format_FormField_Base {
    
    /**
     * Represents the structure of the form field array.
     * 
     * @since       2.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormElement`.
     * @var         array       Represents the array structure of form field.
     * @static
     * @internal
     */ 
    static public $aStructure = array(
    
        // Required Keys
        'field_id'                  => null,
        'type'                      => null,
        'section_id'                => null,

        // Optional Keys        
        'section_title'             => null,    // This will be assigned automatically in the formatting method.
        'page_slug'                 => null,    // This will be assigned automatically in the formatting method.
        'tab_slug'                  => null,    // This will be assigned automatically in the formatting method.
        'option_key'                => null,    // This will be assigned automatically in the formatting method.
        'class_name'                => null,    // Stores the instantiated class name. Used by the export field type. Also a third party custom field type uses it.
        'capability'                => null,        
        'title'                     => null,    
        'tip'                       => null,    
        'description'               => null,    
        'error_message'             => null,    // error message for the field
        'before_label'              => null,    
        'after_label'               => null,    
        'if'                        => true,    
        'order'                     => null,    // do not set the default number here for this key.     
        'default'                   => null,
        'value'                     => null,
        'help'                      => null,    // 2.1.0+
        'help_aside'                => null,    // 2.1.0+
        'repeatable'                => null,    // 2.1.3+
        'sortable'                  => null,    // 2.1.3+
        'show_title_column'         => true,    // 3.0.0+
        'hidden'                    => null,    // 3.0.0+

        // @todo    Examine why an array is not set but null here for the attributes argument.
        'attributes'                => null,    // 3.0.0+ - the array represents the attributes of input tag
        'class'                     => array(   // 3.3.1+
            'fieldrow'  =>  array(),
            'fieldset'  =>  array(),
            'fields'    =>  array(),
            'field'     =>  array(),
        ), 

        'save'                      => true,    // 3.6.0+
        
        // Internal Keys
        '_fields_type'              => null,    // 3.0.0+ - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
        '_caller_object'            => null,    // 3.4.0+ - (object) stores the object of the caller class. The object is referenced when creating nested fields.
        '_nested_depth'             => 0,       // 3.4.0+ - (integer) stores the level of the nesting depth. This is mostly used for debugging by checking if the field is a nested field or not.
                
// @todo deprecate this and use the '_parent_field_object' to generate field input names and ids.
'_parent_field_name_flat'   => '',      // 3.6.0+ - for nested fields. 
    );        
    
    /**
     * Stores the passed unformatted field definition array.
     */
    public $aField = array();
    
    /**
     * Stores the fields type.
     * @remark      This is not the field type but 'fields' type.
     */
    public $sFieldsType = '';
    
    /**
     * The capability.
     */
    public $sCapability = 'manage_options';
    
    /**
     * Stores the count of fields.
     */
    public $iCountOfElements = 0;
    
    /**
     * Stores the section index.
     */
    public $iSectionIndex = null;
    
    /**
     * Stores a flag that indicates whether the section is repeatable or not.
     */
    public $bIsSectionRepeatable = false;
    
    /**
     * Stores the caller object.
     */
    public $oCallerObject;
    
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aField, 
            $this->sFieldsType, 
            $this->sCapability, 
            $this->iCountOfElements, 
            $this->iSectionIndex, 
            $this->bIsSectionRepeatable, 
            $this->oCallerObject
        );
        $this->aField               = $_aParameters[ 0 ];
        $this->sFieldsType          = $_aParameters[ 1 ];
        $this->sCapability          = $_aParameters[ 2 ];
        $this->iCountOfElements     = $_aParameters[ 3 ];
        // @todo    The section index value is still not accurate in the timing that only sanitize and condition sections and fieldset definition arrays.
        $this->iSectionIndex        = $_aParameters[ 4 ];   
        $this->bIsSectionRepeatable = $_aParameters[ 5 ];
        $this->oCallerObject        = $_aParameters[ 6 ];
        
    }
    
    /**
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        
        $_aField = $this->uniteArrays(
            array( 
                '_fields_type'          => $this->sFieldsType,
                '_caller_object'        => $this->oCallerObject,  // 3.4.1+ Stores the caller framework factory object. 
            )
            + $this->aField,
            array( 
                'capability'            => $this->sCapability,
                'section_id'            => '_default',             
                '_section_repeatable'   => $this->bIsSectionRepeatable,
            )
            + self::$aStructure
        );
        $_aField[ 'field_id' ]    = $this->sanitizeSlug( $_aField[ 'field_id' ] );
        $_aField[ 'section_id' ]  = $this->sanitizeSlug( $_aField[ 'section_id' ] );     
        $_aField[ 'tip' ]         = esc_attr( strip_tags(
            $this->getElement(
                $_aField,  // subject array
                'tip', // key
                is_array( $_aField[ 'description' ] )     // default
                    ? implode( '&#10;', $_aField[ 'description' ] ) 
                    : $_aField[ 'description' ] 
            )
        ) );
        $_aField['order']       = $this->getAOrB(
            is_numeric( $_aField[ 'order' ] ),
            $_aField[ 'order' ],
            $this->iCountOfElements + 10
        );            

        return $_aField;        
        
    }
           
           
            
               
}