<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form fieldset definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_Model___Format_Fieldset extends AdminPageFramework_Form_Model___Format_FormField_Base {
    
    /**
     * Represents the structure of the form field array.
     * 
     * @since       2.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
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
        'content'                   => null,     // 3.6.1+ - (string) An overriding field-set output.
        
        // Internal Keys
        '_fields_type'              => null,    // @deprecated  DEVVER++, 3.0.0+ - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
        '_structure_type'           => null,    // DEVVEr+
        '_caller_object'            => null,    // 3.4.0+ - (object) stores the object of the caller class. The object is referenced when creating nested fields.
        '_nested_depth'             => 0,       // 3.4.0+ - (integer) stores the level of the nesting depth. This is mostly used for debugging by checking if the field is a nested field or not.
                
// @todo deprecate this and use the '_parent_field_object' to generate field input names and ids.
'_parent_field_name_flat'   => '',      // 3.6.0+ - for nested fields. 
    );        
    
    /**
     * Stores the passed unformatted fieldset definition array.
     */
    public $aFieldset = array();
    
    /**
     * Stores the fields type.
     * @remark      This is not the field type but 'fields' type.
     */
    public $sStructureType = '';
    
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
    public function __construct( /* $aFieldset, $sStructureType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->sStructureType, 
            $this->sCapability, 
            $this->iCountOfElements, 
            $this->iSectionIndex, 
            $this->bIsSectionRepeatable, 
            $this->oCallerObject
        );
        $this->aFieldset            = $_aParameters[ 0 ];
        $this->sStructureType          = $_aParameters[ 1 ];
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
        
        // Fill missing argument keys - this method overrides 'null' values.
        $_aFieldset = $this->uniteArrays(
            array( 
                '_fields_type'          => $this->sStructureType, // @deprecated DEVVER backward-compatibility
                '_structure_type'       => $this->sStructureType,  
                '_caller_object'        => $this->oCallerObject,  // 3.4.1+ Stores the caller framework factory object. 
            )
            + $this->aFieldset,
            array( 
                'capability'            => $this->sCapability,
                'section_id'            => '_default',             
                '_section_repeatable'   => $this->bIsSectionRepeatable,
            )
            + self::$aStructure
        );
        
        $_aFieldset[ 'field_id' ]    = $this->sanitizeSlug( $_aFieldset[ 'field_id' ] );
        $_aFieldset[ 'section_id' ]  = $this->sanitizeSlug( $_aFieldset[ 'section_id' ] );     
        $_aFieldset[ 'tip' ]         = esc_attr( strip_tags(
            $this->getElement(
                $_aFieldset,  // subject array
                'tip', // key
                is_array( $_aFieldset[ 'description' ] )     // default
                    ? implode( '&#10;', $_aFieldset[ 'description' ] ) 
                    : $_aFieldset[ 'description' ] 
            )
        ) );
        $_aFieldset[ 'order' ]       = $this->getAOrB(
            is_numeric( $_aFieldset[ 'order' ] ),
            $_aFieldset[ 'order' ],
            $this->iCountOfElements + 10
        );            
        
        $_aFieldset[ 'class' ] = $this->getAsArray( $_aFieldset[ 'class' ] );
        
        return $_aFieldset;        
        
    }     
               
}