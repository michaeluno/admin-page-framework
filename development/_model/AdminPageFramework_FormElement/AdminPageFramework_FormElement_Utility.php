<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides public methods used outside the class definition.
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.0.0
 * @internal
 * @todo        Some internal methods still do not have an underscore prefixed and do not have the @internal tag.
 */
class AdminPageFramework_FormElement_Utility extends AdminPageFramework_WPUtility {
            
    /**
     * Drops repeatable section and field elements from the given array.
     * 
     * This is used in the filtering method that merges user input data with the saved options. If the user input data includes repeatable sections
     * and the user removed some elements, then the corresponding elements also need to be removed from the options array. Otherwise, the user's removing element
     * remains in the saved option array as the framework performs recursive array merge.
     * 
     * @remark      The options array structure is slightly different from the fields array. An options array does not have '_default' section keys.
     * @remark      If the user capability is insufficient to display the element, it should not be removed because the element(field/section) itself is not submitted and
     * if the merging saved options array misses the element(which this method is going to deal with), the element will be gone forever. 
     * @remark      This method MUST be called after formatting the form elements because this checks the set user capability.
     * @since       3.0.0
     * @since       3.1.1       Made it not remove the repeatable elements if the user capability is insufficient.
     */
    public function dropRepeatableElements( array $aOptions ) {

        foreach( $aOptions as $_sFieldOrSectionID => $_aSectionOrFieldValue ) {
            
            // If it's a section
            if ( $this->isSection( $_sFieldOrSectionID ) ) {
                
                $_aFields       = $_aSectionOrFieldValue;
                $_sSectionID    = $_sFieldOrSectionID;     
                
                // Check the capability - do not drop if the level is insufficient.
                if ( ! $this->isCurrentUserCapable( $_sSectionID ) ) {
                    continue;
                }
                
                if ( $this->isRepeatableSection( $_sSectionID ) ) {
                    unset( $aOptions[ $_sSectionID ] );     
                    continue;
                }
                
                // An error may occur with an empty _default element.
                if ( ! is_array( $_aFields ) ) { continue; }    
                
                // At this point, it is not a repeatable section. 
                foreach( $_aFields as $_sFieldID => $_aField ) {
                    
                    if ( ! $this->isCurrentUserCapable( $_sSectionID, $_sFieldID ) ) {
                        continue;
                    }     
                    
                    if ( $this->isRepeatableField( $_sFieldID, $_sSectionID ) ) {
                        unset( $aOptions[ $_sSectionID ][ $_sFieldID ] );
                        continue;
                    }
                }
                continue;
                
            }

            // It's a field saved in the root dimension, which corresponds to the '_default' section of the stored registered fields array.
            $_sFieldID = $_sFieldOrSectionID;     
            if ( ! $this->isCurrentUserCapable( '_default', $_sFieldID ) ) {
                continue;
            }
            if ( $this->isRepeatableField( $_sFieldID, '_default' ) ) {
                unset( $aOptions[ $_sFieldID ] );
            }
        
        }    
        return $aOptions;
        
    }    
    
        /**
         * Checks if the current user can have the sufficient capability level.
         * 
         * @since       3.1.1
         * @internal
         * @todo        Examine whether the method name should have an underscore prefixed.
         */
        private function isCurrentUserCapable( $sSectionID, $sFieldID='' ) {
            
            // Section
            if ( ! $sFieldID ) {
                return isset( $this->aSections[ $sSectionID ]['capability'] )
                    ? current_user_can( $this->aSections[ $sSectionID ]['capability'] )
                    : true;
            }
            
            // Field
            return isset( $this->aFields[ $sSectionID ][ $sFieldID ]['capability'] )
                ? current_user_can( $this->aFields[ $sSectionID ][ $sFieldID ]['capability'] )
                : true;
            
        }
    
        /**
         * Checks whether a section is repeatable from the given section ID.
         * 
         * @since       3.0.0
         * @internal
         * @todo        Examine whether the method name should have an underscore prefixed.
         */
        private function isRepeatableSection( $sSectionID ) {
            
            return isset( $this->aSections[ $sSectionID ]['repeatable'] ) && $this->aSections[ $sSectionID ]['repeatable'];
            
        }
        
        /**
         * Checks whether a field is repeatable from the given field ID.
         * 
         * @since       3.0.0
         * @internal
         * @todo        Examine whether the method name should have an underscore prefixed.
         * @todo        Examine whether it should check with conditioned field array. 
         */     
        private function isRepeatableField( $sFieldID, $sSectionID ) {
            
            return ( isset( $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] ) && $this->aFields[ $sSectionID ][ $sFieldID ]['repeatable'] );
            
        }
        
    /**
     * Determines whether the given ID is of a registered form section.
     * 
     * Consider the possibility that the given ID may be used both for a section and a field.
     * 1. Check if the given ID is not a section.
     * 2. Parse stored fields and check their ID. If one matches, return false.
     * 
     * @since 3.0.0
     */
    public function isSection( $sID ) {
        
        // Integer IDs are not accepted.
        if ( is_numeric( $sID ) && is_int( $sID + 0 ) ) { return false; }
        
        // If the section ID is not registered, return false.
        if ( ! array_key_exists( $sID, $this->aSections ) ) { return false; }
        
        // the fields array's first dimension is also filled with the keys of section ids.
        if ( ! array_key_exists( $sID, $this->aFields ) ) { return false; }    
        
        // Since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
        $_bIsSeciton = false;
        foreach( $this->aFields as $_sSectionID => $_aFields ) {    
        
            if ( $_sSectionID == $sID ) { $_bIsSeciton = true; }
            
            // a field using the ID is found, and it precedes a section match.     
            if ( array_key_exists( $sID, $_aFields ) ) { return false; }    
            
        }
        
        return $_bIsSeciton;
        
    }    
        
    /**
     * Returns a fields model array that represents the structure of the array of saving data from the given fields definition array.
     * 
     * The passed fields array should be structured like the following. This is used for page meta boxes.
     * <code>
     *     array(  
     *         '_default' => array( // _default is reserved for the system.
     *             'my_field_id' => array( .... ),
     *             'my_field_id2' => array( .... ),
     *         ),
     *         'my_secion_id' => array(
     *             'my_field_id' => array( ... ),
     *             'my_field_id2' => array( ... ),
     *             'my_field_id3' => array( ... ),
     *     
     *         ),
     *         'my_section_id2' => array(
     *             'my_field_id' => array( ... ),
     *         ),
     *         ...
     * )
     * </code>
     * It will be converted to 
     * <code>
     *     array(  
     *         'my_field_id' => array( .... ),
     *         'my_field_id2' => array( .... ),
     *         'my_secion_id' => array(
     *             'my_field_id' => array( ... ),
     *             'my_field_id2' => array( ... ),
     *             'my_field_id3' => array( ... ),
     *     
     *         ),
     *         'my_section_id2' => array(
     *             'my_field_id' => array( ... ),
     *         ),
     *         ...
     * )
     * </code>
     * @remark Just the _default section elements get extracted to the upper dimension.
     * @since 3.0.0
     */
    public function getFieldsModel( array $aFields=array() )  {
        
        $_aFieldsModel  = array();
        $aFields        = empty( $aFields ) 
            // @todo examine whether it should be the $this->aConditionedFields property rather than $this->aFields
            ? $this->aFields 
            : $aFields;
            
        foreach ( $aFields as $_sSectionID => $_aFields ) {

            if ( $_sSectionID != '_default' ) {
                
                $_aFieldsModel[ $_sSectionID ] = $_aFields;    
                // $_aFieldsModel[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;    
                continue;
            }
            
            // For default field items.
            foreach( $_aFields as $_sFieldID => $_aField ) {
                $_aFieldsModel[ $_aField['field_id'] ] = $_aField;
            }

        }
        return $_aFieldsModel;
        
    }
    
        /**
         * Calculates the subtraction of two values with the array key of `order`.
         * 
         * This is used to sort arrays.
         * 
         * @since       3.0.0     
         * @remark      a callback method for `uasort()`.
         * @return      integer
         * @internal
         */ 
        public function _sortByOrder( $a, $b ) {
            return isset( $a['order'], $b['order'] )
                ? $a['order'] - $b['order']
                : 1;
        }     
    
    /**
     * Applies filters to each conditioned field definition array.
     * 
     * @since       3.0.2
     * @since       3.1.1       Made it reformat the fields after applying filters.
     */
    public function applyFiltersToFields( $oCaller, $sClassName ) {
            
        // Apply filters to each definition field.
        foreach( $this->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) {
                        
            foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
                
                // If it is a sub-section array.
                if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) {
                    $_sSubSectionIndex  = $_sIndexOrFieldID;
                    $_aFields           = $_aSubSectionOrField;
                    $_sSectionSubString = '_default' == $_sSectionID ? '' : "_{$_sSectionID}";
                    foreach( $_aFields as $_aField ) {
                        $this->aConditionedFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $this->addAndApplyFilter(
                            $oCaller,
                            "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}",
                            $_aField,
                            $_sSubSectionIndex
                        );    
                    }
                    continue;
                    
                }
                
                // Otherwise, insert the formatted field definition array.
                $_aField            = $_aSubSectionOrField;
                $_sSectionSubString = '_default' == $_sSectionID ? '' : "_{$_sSectionID}";
                $this->aConditionedFields[ $_sSectionID ][ $_aField['field_id'] ] = $this->addAndApplyFilter(
                    $oCaller,
                    "field_definition_{$sClassName}{$_sSectionSubString}_{$_aField['field_id']}",
                    $_aField     
                );
                
            }
            
        }

        // Apply filters to all the conditioned fields.
        $this->aConditionedFields = $this->addAndApplyFilter(
            $oCaller,
            "field_definition_{$sClassName}",
            $this->aConditionedFields
        );     
        $this->aConditionedFields = $this->formatFields( $this->aConditionedFields, $this->sFieldsType, $this->sCapability );
        
    }
    
}