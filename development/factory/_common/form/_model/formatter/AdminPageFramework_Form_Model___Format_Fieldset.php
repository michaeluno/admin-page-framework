<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form field-set definition arrays.
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
        'field_id'                  => null,    // (string)
        'type'                      => null,    // (string)
        'section_id'                => null,    // (string)

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
        'help'                      => null,    // 2.1.0
        'help_aside'                => null,    // 2.1.0
        'repeatable'                => null,    // 2.1.3
        'sortable'                  => null,    // 2.1.3
        'show_title_column'         => true,    // 3.0.0
        'hidden'                    => null,    // 3.0.0

        // @todo    Examine why an array is not set but null here for the attributes argument.
        'attributes'                => null,    // 3.0.0 - the array represents the attributes of input tag
        'class'                     => array(   // 3.3.1
            'fieldrow'  =>  array(),
            'fieldset'  =>  array(),
            'fields'    =>  array(),
            'field'     =>  array(),
        ), 

        'save'                      => true,    // 3.6.0
        'content'                   => null,    // 3.6.1 - (string) An overriding field-set output.
        
        // Internal Keys
        '_fields_type'              => null,    // @deprecated  3.7.0, 3.0.0 - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
        '_structure_type'           => null,    // 3.7.0
        '_caller_object'            => null,    // 3.4.0 (object) stores the object of the caller class. The object is referenced when creating nested fields.
                                                         
        '_section_path'             => '',      // 3.7.0 (string) Stores the section path that indicates the structural address of the nested section. e.g. my_section|nested_one
        '_section_path_array'       => '',      // 3.7.0 (array) An array version of the above section path.
        '_nested_depth'             => 0,       // 3.4.0 (integer) stores the level of the nesting depth. This is mostly used for debugging by checking if the field is a nested field or not.        
        '_subsection_index'         => null,    // 3.7.0 Passed to the `field_definition_{...}` filter hook callbacks.
        '_section_repeatable'       => false,   // @deprecated
        '_is_section_repeatable'    => false,   // 3.8.0 (boolean) Whether the belonging section is repeatable or not.
               
        '_field_path'               => '',      // 3.7.0 (string) Stores the field path that indicates the structural location of the field. This is relative to the belonging section.
        '_field_path_array'         => array(), // 3.7.0 (array) An array version of the above field path.
        '_parent_field_path'        => '',      // 3.8.0 (string)
        '_parent_field_path_array'  => array(), // 3.8.0 (array)
        
// @deprecated temporarily        
        // '_is_parent_dynamic'        => false,   // 3.8.0 (boolean) indicates whether the parent field is repeatable or sortable (if the field is a nested field).
        // '_field_index'              => 0,       // 3.8.0 (integer) holds the index of the field in sub-fields if it is repeatable or sortable. 
        // '_parent_field_index'       => 0,       // 3.8.0 (integer) holds the index of the parent field. Used to construct a field path.
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
    public $iSubSectionIndex = null;
    
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
    public function __construct( /* $aFieldset, $sStructureType, $sCapability, $iCountOfElements, $iSubSectionIndex, $bIsSectionRepeatable, $oCallerObject */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->sStructureType, 
            $this->sCapability, 
            $this->iCountOfElements, 
            $this->iSubSectionIndex, 
            $this->bIsSectionRepeatable, 
            $this->oCallerObject
        );
        $this->aFieldset            = $_aParameters[ 0 ];
        $this->sStructureType       = $_aParameters[ 1 ];
        $this->sCapability          = $_aParameters[ 2 ];
        $this->iCountOfElements     = $_aParameters[ 3 ];
        // @todo    The section index value is still not accurate in the timing that only sanitize and condition sections and fieldset definition arrays.
        $this->iSubSectionIndex     = $_aParameters[ 4 ];   
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
                '_fields_type'           => $this->sStructureType, // @deprecated 3.7.0 backward-compatibility
                '_structure_type'        => $this->sStructureType,  
                '_caller_object'         => $this->oCallerObject,  // 3.4.1+ Stores the caller framework factory object. 
                '_subsection_index'      => $this->iSubSectionIndex,  // 3.7.0+
            )
            + $this->aFieldset,
            array( 
                'capability'             => $this->sCapability,
                'section_id'             => '_default',             
                '_section_repeatable'    => $this->bIsSectionRepeatable,   // @deprecated  3.8.0   This was not used.
                '_is_section_repeatable' => $this->bIsSectionRepeatable,
            )
            + self::$aStructure
        );
        
        $_aFieldset[ 'field_id' ]            = $this->getIDSanitized( $_aFieldset[ 'field_id' ] );
        $_aFieldset[ 'section_id' ]          = $this->getIDSanitized( $_aFieldset[ 'section_id' ] );
        $_aFieldset[ '_section_path' ]       = $this->getFormElementPath( $_aFieldset[ 'section_id' ] );
        $_aFieldset[ '_section_path_array' ] = explode( '|', $_aFieldset[ '_section_path' ] );
        $_aFieldset[ '_field_path' ]         = $this->_getFieldPath( $_aFieldset );
        $_aFieldset[ '_field_path_array' ]   = explode( '|', $_aFieldset[ '_field_path' ] );
        $_aFieldset[ 'order' ]               = $this->getAOrB(
            is_numeric( $_aFieldset[ 'order' ] ),
            $_aFieldset[ 'order' ],
            $this->iCountOfElements + 10
        );            
        
        $_aFieldset[ 'class' ] = $this->getAsArray( $_aFieldset[ 'class' ] );

        // 3.8.0+ Support nested fields.
        if ( $this->hasNestedFields( $_aFieldset ) ) {            
            $_aFieldset[ 'content' ] = $this->_getNestedFieldsetsFormatted( $_aFieldset[ 'content' ], $_aFieldset );
        }        
        
        return $_aFieldset;
        
    }     
    
        /**
         * Calculates a field path.
         * @since       3.8.0
         * @return      string
         */
        private function _getFieldPath( array $aFieldset ) {

            // $_bHasNestedFields = $this->hasNestedFields( $aFieldset );
            $_sFieldPath       = $this->getTrailingPipeCharacterAppended( $aFieldset[ '_parent_field_path' ] )
                // @todo for dynamic nested fields, the field-set is re-formatted so omit the sub-field dimension here.
                // . ( $aFieldset[ '_is_parent_dynamic' ] 
                    // ? max( $this->iCountOfElements - 1, 0 ) . '|'  
                    // : '' 
                // )
                . $this->getFormElementPath( $aFieldset[ 'field_id' ] );

// return $_bHasNestedFields && ( $aFieldset[ 'repeatable' ] || $aFieldset[ 'sortable' ] )
    // ? $this->getTrailingPipeCharacterAppended( $_sFieldPath ) . max( $this->iCountOfElements - 1, 0 )
    // : $_sFieldPath;
            return $_sFieldPath;        

        }
        
        /**
         * Formats the nested fieldsets definition arrays.
         * 
         * @since       3.8.0
         * @return      array
         */
        private function _getNestedFieldsetsFormatted( array $aNestedFieldsets, array $aParentFieldset ) {
                    
            $_aInheritingFieldsetValues = array(
                'section_id'                => $aParentFieldset[ 'section_id' ], 
                'section_title'             => $aParentFieldset[ 'section_title' ], 
                'page_slug'                 => $aParentFieldset[ 'page_slug' ],
                'tab_slug'                  => $aParentFieldset[ 'tab_slug' ],
                'option_key'                => $aParentFieldset[ 'option_key' ],
                'class_name'                => $aParentFieldset[ 'class_name' ],
                'capability'                => $aParentFieldset[ 'capability' ],
                '_structure_type'           => $aParentFieldset[ '_structure_type' ],
                '_caller_object'            => $aParentFieldset[ '_caller_object' ],                       
                '_section_path'             => $aParentFieldset[ '_section_path' ],
                '_section_path_array'       => $aParentFieldset[ '_section_path_array' ],
                '_subsection_index'         => $aParentFieldset[ '_subsection_index' ],
          
// @todo        temporarily deprecated          
                // '_is_parent_dynamic'        => $aParentFieldset[ 'repeatable' ] || $aParentFieldset[ 'sortable' ],
            );

            foreach( $aNestedFieldsets as $_isIndex => &$_aNestedFieldset ) {
                $_aNestedFieldset[ '_parent_field_path' ]       = $aParentFieldset[ '_field_path' ];
                $_aNestedFieldset[ '_parent_field_path_array' ] = explode( '|', $aParentFieldset[ '_parent_field_path' ] );
                $_aNestedFieldset[ '_nested_depth' ]            = $aParentFieldset[ '_nested_depth' ] + 1;
                $_oFieldsetFormatter = new AdminPageFramework_Form_Model___Format_Fieldset(
                    $_aNestedFieldset + $_aInheritingFieldsetValues, // merge with the parent definition to inherit its values
                    $this->sStructureType,
                    $this->sCapability, 
                    $this->iCountOfElements,
                    $this->iSubSectionIndex, 
                    $this->bIsSectionRepeatable, 
                    $this->oCallerObject
                );                
                $_aNestedFieldset = $_oFieldsetFormatter->get();
                
            }            
            
            return $aNestedFieldsets;
            
        }
    
}
