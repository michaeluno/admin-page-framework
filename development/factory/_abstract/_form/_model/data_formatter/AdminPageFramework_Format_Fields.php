<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form sub-fields definition arrays.
 * 
 * The user defines a field with a field definition array. Sub-fields will be created from the field definition array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_Fields extends AdminPageFramework_Format_FormField_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
     */
    static public $aStructure = array(
    );
    
    /**
     * 
     */
    public $aField      = array();
    
    public $aOptions    = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* array $aField, array $aOptions */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aField, 
            $this->aOptions,
        );
        $this->aField           = $_aParameters[ 0 ];
        $this->aOptions         = $_aParameters[ 1 ];
        
    }

    /**
     * 
     * @return      array       A sub-fields definition array.
     */
    public function get() {

        // Get the set value(s)
        $_mSavedValue    = $this->_getStoredInputFieldValue( 
            $this->aField, 
            $this->aOptions 
        );

        // Construct fields array.
        $_aFields = $this->_getFieldsWithSubs( 
            $this->aField, 
            $_mSavedValue 
        );
             
        // Set the saved values
        $this->_setSavedFieldsValue( 
            $_aFields, 
            $_mSavedValue, 
            $this->aField 
        );

        // Determine the value
        $this->_setFieldsValue( $_aFields ); // by reference

        return $_aFields;
        
    }

        /**
         * Returns fields array which includes sub-fields.
         * 
         * @since       3.5.3
         * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
         */
        private function _getFieldsWithSubs( array $aField, $mSavedValue ) {

            // Separate the first field and sub-fields
            $aFirstField    = array();
            $aSubFields     = array();
            
            // $aFirstField and $aSubFields get updated in the method
            $this->_divideMainAndSubFields( $aField, $aFirstField, $aSubFields );
                        
            // $aSubFields gets updated in the method
            $this->_fillRepeatableElements( $aField, $aSubFields, $mSavedValue );
                            
             // $aSubFields gets updated in the method
            $this->_fillSubFields( $aSubFields, $aFirstField );

            // Put them together
            return array_merge( array( $aFirstField ), $aSubFields );
            
        }            
            /**
             * Divide the fields into the main field and sub fields.
             * 
             * @remark      The method will update the arrays passed to the second and the third parameter.
             * @since       3.5.3
             * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
             * @internal
             * @return      void
             */
            private function _divideMainAndSubFields( array $aField, array &$aFirstField, array &$aSubFields ) {
                foreach( $aField as $_nsIndex => $_mFieldElement ) {
                    if ( is_numeric( $_nsIndex ) ) {
                        $aSubFields[] = $_mFieldElement;
                    } else {
                        $aFirstField[ $_nsIndex ] = $_mFieldElement;
                    }
                }     
            }   
            /**
             * Fills sub-fields with repeatable fields.
             * 
             * This method creates the sub-fields of repeatable fields based on the saved values.
             * 
             * @remark      This method updates the passed array to the second parameter.
             * @since       3.5.3
             * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
             * @internal
             * @return      void
             */
            private function _fillRepeatableElements( array $aField, array &$aSubFields, $mSavedValue ) {
                if ( ! $aField['repeatable'] ) {
                    return;
                }
                $_aSavedValues = ( array ) $mSavedValue;
                unset( $_aSavedValues[ 0 ] );
                foreach( $_aSavedValues as $_iIndex => $vValue ) {
                    $aSubFields[ $_iIndex - 1 ] = isset( $aSubFields[ $_iIndex - 1 ] ) && is_array( $aSubFields[ $_iIndex - 1 ] ) 
                        ? $aSubFields[ $_iIndex - 1 ] 
                        : array();     
                }       
            }
            /**
             * Fills sub-fields.
             * @since       3.5.3
             * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
             * @internal
             * @return      void
             */
            private function _fillSubFields( array &$aSubFields, array $aFirstField ) {
                        
                foreach( $aSubFields as &$_aSubField ) {
                    
                    // Evacuate the label element which should not be merged.
                    $_aLabel = $this->getElement( 
                        $_aSubField, 
                        'label',
                        $this->getElement( $aFirstField, 'label', null )
                    );
                    
                    // Do recursive array merge - the 'attributes' array of some field types have more than one dimensions.
                    $_aSubField = $this->uniteArrays( $_aSubField, $aFirstField ); 
                    
                    // Restore the label element.
                    $_aSubField['label'] = $_aLabel;
                    
                }
            }
            
        /**
         * Sets saved field values to the given field arrays.
         * 
         * @since       3.5.3
         * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
         */
        private function _setSavedFieldsValue( array &$aFields, $mSavedValue, $aField ) {
         
            // Determine whether the elements are saved in an array.
            $_bHasSubFields = count( $aFields ) > 1 || $aField['repeatable'] || $aField['sortable'];
            if ( ! $_bHasSubFields ) {
                $aFields[ 0 ]['_saved_value'] = $mSavedValue;
                $aFields[ 0 ]['_is_multiple_fields'] = false;
                return;                    
            }
     
            foreach( $aFields as $_iIndex => &$_aThisField ) {
                $_aThisField['_saved_value'] = $this->getElement( $mSavedValue, $_iIndex, null );
                $_aThisField['_is_multiple_fields'] = true;
            }
    
        } 
        
        /**
         * Sets the value to the given fields array.
         * 
         * @since       3.5.3
         * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
         */
        private function _setFieldsValue( array &$aFields ) {
            foreach( $aFields as &$_aField ) {
                $_aField['_is_value_set_by_user'] = isset( $_aField['value'] );
                $_aField['value']                 = $this->_getSetFieldValue( $_aField );
            }
        }
        /**
         * Returns the set field value.
         * 
         * @since       3.5.3
         * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
         */
        private function _getSetFieldValue( array $aField ) {
            
            if ( isset( $aField['value'] ) ) {
                return $aField['value'];
            }
            if ( isset( $aField['_saved_value'] ) ) {
                return $aField['_saved_value'];
            }
            if ( isset( $aField['default'] ) ) {
                return $aField['default'];
            }
            return null;                  
            
        }            
        /**
         * Returns the stored field value.
         * 
         * It checks if a previously saved option value exists or not. Regular setting pages and page meta boxes will be applied here.
         * It's important to return null if not set as the returned value will be checked later on whether it is set or not. If an empty value is returned, they will think it's set.
         * 
         * @since       2.0.0
         * @since       3.0.0       Removed the check of the 'value' and 'default' keys. Made it use the '_fields_type' internal key.
         * @since       3.1.0       Changed the name to _getStoredInputFieldValue from _getInputFieldValue
         * @since       3.4.1       Removed the switch block as it was redundant.
         * @since       3.6.0       Moved from `AdminPageFramework_FieldDefinition`.
         * @since       DEVVER      Changed the `_field_type` element to `_structure_type`.
         */
        private function _getStoredInputFieldValue( $aField, $aOptions ) {    

            // If a section is not set, check the first dimension element.
            if ( ! isset( $aField['section_id'] ) || '_default' === $aField['section_id'] ) {
                return $this->getElement( 
                    $aOptions, 
                    $aField['field_id'],
                    null
                );
            }
                
            // At this point, the section dimension is set.
            
            // If it belongs to a sub section,
            if ( isset( $aField['_section_index'] ) ) {
                return $this->getElement(
                    $aOptions,
                    array( $aField['section_id'], $aField['_section_index'], $aField['field_id'] ),
                    null
                );
            }
            
            // Otherwise, return the second dimension element.
            return $this->getElement(
                $aOptions,
                array( $aField['section_id'], $aField['field_id'] ),
                null
            );
                                            
        }         
    
}
