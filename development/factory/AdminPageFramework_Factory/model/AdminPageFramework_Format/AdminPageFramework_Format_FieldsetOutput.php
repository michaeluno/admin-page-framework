<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form field definition arrays to generate actual outputs.
 *
 * There are some internal keys that cannot be set until the container sections table gets rendered.
 * This class provides methods to set those internal keys.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_FieldsetOutput extends AdminPageFramework_Format_Fieldset {
    
    /**
     * Represents the structure of the form field definition array.
     * 
     * These key values become available when the section container gets rendered.
     * 
     * @static
     * @internal
     */ 
    static public $aStructure = array(       
        '_section_index'            => null,    // 3.0.0+ - internally set to indicate the section index for repeatable sections.        
        
        'tag_id'                    => null,
        '_tag_id_model'             => '',      // 3.6.0+   
        
        '_field_name'               => '',      // 3.6.0+   
        '_field_name_model'         => '',      // 3.6.0+           
        
        '_field_name_flat'          => '',      // 3.6.0+
        '_field_name_flat_model'    => '',      // 3.6.0+   
                
        '_field_address'            => '',      // 3.6.0+
        '_field_address_model'      => '',      // 3.6.0+
                
        '_parent_field_object'      => null,    // 3.6.0+   Assigned when a field creates a nested field.
        
    );        
    
    /**
     * Stores the passed unformatted field definition array.
     */
    public $aFieldset = array();
    
    /**
     * Stores the section index.
     * 
     * @remark      the value is checked with `isset()` so `null` needs to be set.
     */
    public $iSectionIndex = null;
    
    public $aFieldTypeDefinitions = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aFieldset, $iSectionIndex, $aFieldTypeDefinitions */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->iSectionIndex,
            $this->aFieldTypeDefinitions,
        );
        $this->aFieldset             = $_aParameters[ 0 ];
        $this->iSectionIndex         = $_aParameters[ 1 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 2 ];
        
    }
    
    /**
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        
        $_aFieldset = $this->aFieldset + self::$aStructure;
        
        // The section index must be set before generating a field tag id as it uses a section index.
        $_aFieldset[ '_section_index' ]   = $this->iSectionIndex;

        $_oFieldTagIDGenerator = new AdminPageFramework_Generate_FieldTagID( 
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->oProp->aFieldCallbacks[ 'hfTagID' ]
        );
        $_aFieldset[ 'tag_id' ]        = $_oFieldTagIDGenerator->get();
        $_aFieldset[ '_tag_id_model' ] = $_oFieldTagIDGenerator->getModel();
        
        $_oFieldNameGenerator = new AdminPageFramework_Generate_FieldName( 
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->oProp->aFieldCallbacks[ 'hfName' ]        
        );
        $_aFieldset[ '_field_name' ]        = $_oFieldNameGenerator->get();
        $_aFieldset[ '_field_name_model' ]  = $_oFieldNameGenerator->getModel();

        // Flat section and field names, used for sorting dynamic elements.
        $_oFieldFlatNameGenerator = new AdminPageFramework_Generate_FlatFieldName(
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->oProp->aFieldCallbacks[ 'hfNameFlat' ]
        );
        $_aFieldset[ '_field_name_flat' ]       = $_oFieldFlatNameGenerator->get();
        $_aFieldset[ '_field_name_flat_model' ] = $_oFieldFlatNameGenerator->getModel();
        
        $_oFieldAddressGenerator = new AdminPageFramework_Generate_FieldAddress( $_aFieldset );
        $_aFieldset[ '_field_address' ]         = $_oFieldAddressGenerator->get();
        $_aFieldset[ '_field_address_model' ]   = $_oFieldAddressGenerator->getModel();
        
        return $this->_getMergedFieldTypeDefault(
            $_aFieldset,
            $this->aFieldTypeDefinitions
        );
        
    }
           
        /**
         * Generates a dimensional field keys delimited by the pipe character, used by marking dynamic fields such as repeatable and sortable fields.
         *          
         * @since       3.6.0
         * @return      string
         * @deprecated
         */
/*         public function _getFlatFieldName( array $aFieldset ) {    
            
            // If the parent field exists, append the field id.
            if ( is_object( $aFieldset[ '_parent_field_object' ] ) ) {
                $_oParentField = $aFieldset[ '_parent_field_object' ];
                return $_oParentField->get( '_field_name_flat' ) . '|' . $aFieldset[ 'field_id' ];
            }
            
            $_aDimensionalKeys = array();
            
            // Section dimension
            if ( $this->_isSectionSet( $aFieldset ) ) {
                $_aDimensionalKeys[] = $aFieldset[ 'section_id' ];
            }
            
            // Sub-section index
            if ( isset( $aFieldset[ 'section_id' ], $aFieldset[ '_section_index' ] ) ) {
                $_aDimensionalKeys[] = $aFieldset[ '_section_index' ];
            }
            
            // Field dimension
            $_aDimensionalKeys[] = $aFieldset[ 'field_id' ];

            // Output
            $_sNameAttribute = implode( '|', $_aDimensionalKeys );
            
            // Get the callable
            $_hfCallback = $aFieldset[ '_caller_object' ]->oProp->aFieldCallbacks[ 'hfName' ];
            return is_callable( $_hfCallback )
                ? call_user_func_array( 
                    $_hfCallback, 
                    array( 
                        $_sNameAttribute, 
                        $aFieldset
                    ) 
                )
                : $_sNameAttribute;
                            
        }           */
               
        /**
         * Merge the given field definition array with the field type default key array that holds default values.
         * 
         * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
         * which affects the way of rendering the row that contains the field output (by the field output callback).
         * 
         * @internal
         * @since       3.0.0
         * @since       3.4.0       Changed the name from `_mergeDefault()`.
         * @since       3.6.0       Moved from `AdminPageFramework_FormPart_Table_Row`. Changed the name from `mergeFIeldTYpeDefault`.
         * @remark      The returning merged field definition array does not respect sub-fields so when passing the field definition to the callback,
         * do not use the array returned from this method but the raw (non-merged) array.
         */
        private function _getMergedFieldTypeDefault( array $aFieldset, array $aFieldTypeDefinitions ) {
            return $this->uniteArrays( 
                $aFieldset, 
                $this->getElementAsArray(
                    $aFieldTypeDefinitions,
                    array( $aFieldset[ 'type' ], 'aDefaultKeys' ),
                    array()
                )
            );
        }                    
               
}