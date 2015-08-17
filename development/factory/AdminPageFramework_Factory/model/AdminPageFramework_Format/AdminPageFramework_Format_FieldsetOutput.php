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
        '_section_index'    => null,    // 3.0.0+ - internally set to indicate the section index for repeatable sections.        
        '_field_name_flat'  => '',      // 3.6.0+
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
        
        $_aFieldset[ '_section_index' ]   = $this->iSectionIndex;

        // Flat section and field names, used for sorting dynamic elements.
        $_aFieldset[ '_field_name_flat' ] = $this->_getFlatFieldName( $_aFieldset );                     

        
        return $this->_getMergedFieldTypeDefault(
            $_aFieldset,
            $this->aFieldTypeDefinitions
        );
        
        // return $_aFieldset;
        
    }
           
           
        /**
         * Generates a dimensional field keys delimited by the pipe character, used by marking dynamic fields such as repeatable and sortable fields.
         *          
         * @since       3.6.0
         * @return      string
         */
        public function _getFlatFieldName( array $aFieldset ) {    
            
            $_aDimensionalKeys = array();
            
            // Section dimension
            if ( $this->_isSectionSet( $aFieldset ) ) {
                $_aDimensionalKeys[] = $aFieldset[ 'section_id' ];
            }
            
            // Sub-section index
            if ( isset( $aFieldset[ 'section_id' ], $aFieldset[ '_section_index' ] ) ) {
                $_aDimensionalKeys[] = $aFieldset['_section_index'];
            }
            
            // Field dimension
            $_aDimensionalKeys[] = $aFieldset[ 'field_id' ];
            
            // Output
            return implode( '|', $_aDimensionalKeys );
                            
        }          
               
        /**
         * Merge the given field definition array with the field type default key array that holds default values.
         * 
         * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
         * which affects the way of rendering the row that contains the field output (by the field output callback).
         * 
         * @internal
         * @since       3.0.0
         * @since       3.4.0       Changed the name from `_mergeDefault()`.
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`. Changed the name from `mergeFIeldTYpeDefault`.
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