<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle form elements.
 * 
 * Used by the user meta and post meta box classes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.5.3
 * @internal
 */
class AdminPageFramework_FormElement_Meta extends AdminPageFramework_FormElement {

    /**
     * Extracts the user submitted values from the $_POST array.
     * 
     * @since       3.0.0
     * @since       3.5.3       Changed the name from `_getInputArray`. Changed the scope to public from protected. Moved from `AdminPageFramework_MetaBox_Model`.
     * @remark      Used by the meta box class and user meta class.
     * @internal
     */    
    public function getUserSubmitDataFromPOST( array $aFieldDefinitionArrays, array $aSectionDefinitionArrays ) {

        // Construct an array consisting of the submitted registered field values.
        $_aInput = array();
        foreach( $aFieldDefinitionArrays as $_sSectionID => $_aSubSectionsOrFields ) {
            
            // If a section is not set,
            if ( '_default' == $_sSectionID ) {
                $_aFields = $_aSubSectionsOrFields;
                foreach( $_aFields as $_aField ) {
                    $_aInput[ $_aField['field_id'] ] = $this->getElement(
                        $_POST, // subject
                        $_aField['field_id'],    // dimensional keys
                        null   // default value
                    );                            
                }
                continue;
            }     
                            
            // At this point, the section is set
            $_aInput[ $_sSectionID ] = $this->getElementAsArray(
                $_aInput, // subjct
                $_sSectionID,    // dimensional keys
                array()   // default value
            );      
            
            // If the section does not contain sub sections,
            if ( ! count( $this->getIntegerKeyElements( $_aSubSectionsOrFields ) ) ) {
                
                $_aFields = $_aSubSectionsOrFields;
                foreach( $_aFields as $_aField ) {
                    $_aInput[ $_sSectionID ][ $_aField['field_id'] ] = $this->getElement(
                        $_POST, // subjct
                        array( $_sSectionID, $_aField['field_id'] ),    // dimensional keys
                        null   // default value
                    );
                }
                continue;

            }
                
            // Otherwise, it's sub-sections. 
            // Since the registered fields don't have information how many items the user added, parse the submitted data.
            foreach( $_POST[ $_sSectionID ] as $_iIndex => $_aFields ) { // will include the main section as well.
                $_aInput[ $_sSectionID ][ $_iIndex ] = $this->getElement(
                    $_POST, // subjct
                    array( $_sSectionID, $_iIndex ),    // dimensional keys
                    null   // default value
                );
            }        
                           
        }

        return $_aInput;
        
    }    
    
    /**
     * Saves the meta data with the given object ID with the given type with the user submit data.
     * 
     * Used by the post meta box and user meta classes.
     * 
     * @since       3.5.3
     * @param       integer     $iObjectID      The ID that is associated with the meta data such as user ID and post ID.
     * @param       array       $aInput         The user submit form input data.
     * @param       array       $aSavedMeta     The stored form data (old input data).
     * @param       string      $sFieldsType    The type of object. Currently 'post_meta_box' or 'user_meta' is accepted.
     * @return      void
     */
    public function updateMetaDataByType( $iObjectID, array $aInput, array $aSavedMeta, $sFieldsType='post_meta_box' ) {
        
        if ( ! $iObjectID ) {
            return;
        }
           
        $_aFunctionNameMapByFieldsType = array(
            // 'page'              => null,
            // 'page_meta_box'     => null,
            'post_meta_box'     => 'update_post_meta',
            // 'taxonomy'          => null,
            // 'widget'            => null,
            'user_meta'         => 'update_user_meta',               
        );
        if ( ! in_array( $sFieldsType, array_keys( $_aFunctionNameMapByFieldsType ) ) ) {
            return;
        }
        $_sFunctionName = $this->getElement( $_aFunctionNameMapByFieldsType, $sFieldsType );
        
        // Loop through sections/fields and save the data.
        foreach ( $aInput as $_sSectionOrFieldID => $_vValue ) {

            $this->_updateMetaDatumByFuncitonName( 
                $iObjectID,
                $_vValue, 
                $aSavedMeta, 
                $_sSectionOrFieldID, 
                $_sFunctionName
            );

        }
        
    }        
        /**
         * Saves an individual meta datum with the given section or field ID with the given function name.
         * 
         * @internal
         * @since       3.5.3
         * @return      void
         */
        private function _updateMetaDatumByFuncitonName( $iObjectID, $_vValue, array $aSavedMeta, $_sSectionOrFieldID, $_sFunctionName ) {
            
            if ( is_null( $_vValue ) ) { 
                return;
            }

            $_vSavedValue = $this->getElement(
                $aSavedMeta, // subject
                $_sSectionOrFieldID,    // dimensional keys
                null   // default value
            );
                
            // PHP can compare even array contents with the == operator. See http://www.php.net/manual/en/language.operators.array.php
            // if the input value and the saved meta value are the same, no need to update it.
            if ( $_vValue == $_vSavedValue ) { 
                return; 
            }
            
            // Currently either 'update_post_meta' or 'update_user_meta'
            $_sFunctionName( $iObjectID, $_sSectionOrFieldID, $_vValue );             
            
        }

}