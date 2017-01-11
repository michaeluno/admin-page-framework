<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form field definition arrays to generate actual outputs.
 *
 * This class should be called during the form rendering routine.
 * 
 * This constructs attributes array and some other internal keys crucial to form field outputs 
 * such as input name and id. Without them, the form data will not be sent 
 * and repeatable and sortable JavaScirpt scripts will not be able to bind events.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_Model___Format_Fieldset
 * @internal
 */
class AdminPageFramework_Form_Model___Format_FieldsetOutput extends AdminPageFramework_Form_Model___Format_Fieldset {
    
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
        
        '_parent_tag_id'            => null,    // 3.8.0+   Set outside the class.
        
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
        $_oFieldTagIDGenerator = new AdminPageFramework_Form_View___Generate_FieldTagID( 
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->aCallbacks[ 'hfTagID' ]
        );
        $_aFieldset[ 'tag_id' ]        = $_oFieldTagIDGenerator->get();
        $_aFieldset[ '_tag_id_model' ] = $_oFieldTagIDGenerator->getModel();

        $_oFieldNameGenerator = new AdminPageFramework_Form_View___Generate_FieldName( 
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->aCallbacks[ 'hfName' ]        
        );
        $_aFieldset[ '_field_name' ]        = $_oFieldNameGenerator->get();
        $_aFieldset[ '_field_name_model' ]  = $_oFieldNameGenerator->getModel();

        // Flat section and field names, used for sorting dynamic elements.
        $_oFieldFlatNameGenerator = new AdminPageFramework_Form_View___Generate_FlatFieldName(
            $_aFieldset,
            $_aFieldset[ '_caller_object' ]->aCallbacks[ 'hfNameFlat' ]
        );
        $_aFieldset[ '_field_name_flat' ]       = $_oFieldFlatNameGenerator->get();
        $_aFieldset[ '_field_name_flat_model' ] = $_oFieldFlatNameGenerator->getModel();
        
        $_oFieldAddressGenerator = new AdminPageFramework_Form_View___Generate_FieldAddress( $_aFieldset );
        $_aFieldset[ '_field_address' ]         = $_oFieldAddressGenerator->get();
        $_aFieldset[ '_field_address_model' ]   = $_oFieldAddressGenerator->getModel();

        $_aFieldset = $this->_getMergedFieldTypeDefault(
            $_aFieldset,
            $this->aFieldTypeDefinitions
        );
        
        // 3.8.0+ Format nested fieldsets.
        if ( $this->hasFieldDefinitionsInContent( $_aFieldset ) ) {
            foreach( $_aFieldset[ 'content' ] as &$_aNestedFieldset ) {
                // The inline-mixed type has a string element.
                if ( is_scalar( $_aNestedFieldset ) ) {
                    continue;
                }                
                $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput( 
                    $_aNestedFieldset, 
                    $this->iSectionIndex,
                    $this->aFieldTypeDefinitions
                );                                    
                $_aNestedFieldset = $_oFieldsetOutputFormatter->get();
            }
        }
        
        return $_aFieldset;
        
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
