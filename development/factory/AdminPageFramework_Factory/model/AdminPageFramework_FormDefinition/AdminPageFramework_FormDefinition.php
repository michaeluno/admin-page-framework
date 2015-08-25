<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with field and section definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.0.0
 * @since       3.6.0       Changed the name from `AdminPageFramework_FormElement`.
 * @internal
 */
class AdminPageFramework_FormDefinition extends AdminPageFramework_FormDefinition_Base {
    
    /**
     * Stores field definition arrays.
     * @since 3.0.0
     */
    public $aFields = array();
    
    /**
     * Stores section definition arrays.
     * 
     * @since 3.0.0
     */
    public $aSections = array(
        '_default' => array(),
    );
    
    /**
     * Stores the conditioned fields definition array.
     * 
     * @since 3.0.0
     */
    public $aConditionedFields = array();
    
    /**
     * Stores the conditioned sections definition array.
     * 
     * @since 3.0.0
     */
    public $aConditionedSections = array();
    
    /**
     * Stores the fields type. 
     * 
     * @since       3.0.0
     * @since       3.5.3       Changed the scope to `public` from `protected` as the meta box and user meta classes access this value from outside.
     * @access      public
     */
    public $sFieldsType = '';
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified.
     * 
     * @since       3.0.0
     */
    protected $_sTargetSectionID = '_default';    
    
    /**
     * Stores the default access level of the fields.
     * 
     * @remark      The scope is public to let the value being changed externally.
     * @since       3.0.0
     */
    public $sCapability = 'manage_option';
    
    /**
     * Stores the default capability.
     * 
     * @since       3.0.0
     * @since       3.4.0       Added the $oCaller parameter.
     * 
     * @param       string      $sFieldsType
     * @param       string      $sCapability    
     * @param       object      $oCaller            The caller object. Each formatted field will have the caller object. 
     * This give power to each field to create nested fields.
     */
    public function __construct( $sFieldsType, $sCapability, $oCaller=null ) {
        
        $this->sFieldsType  = $sFieldsType;
        $this->sCapability  = $sCapability;
        $this->oCaller      = $oCaller;
        
    }
    
    /**
     * Adds the given section definition array to the form property.
     * 
     * @since       3.0.0
     * @return      void
     */
    public function addSection( array $aSection ) {
        
        $aSection                 = $aSection + AdminPageFramework_Format_Sectionset::$aStructure;
        $aSection[ 'section_id' ] = $this->sanitizeSlug( $aSection[ 'section_id' ] );
        
        $this->aSections[ $aSection[ 'section_id' ] ] = $aSection;    
        $this->aFields[ $aSection[ 'section_id' ] ]   = $this->getElement(
            $this->aFields,  // subject array
            $aSection[ 'section_id' ], // key
            array()      // default
        );                                
        
    }
    
    /**
     * Removes a section definition array from the property by the given section ID.
     * 
     * @since       3.0.0
     */
    public function removeSection( $sSectionID ) {
        
        if ( '_default' === $sSectionID ){ 
            return; 
        }
        unset( 
            $this->aSections[ $sSectionID ],
            $this->aFields[ $sSectionID ]
        );
        
    }
    
    /*
     * Adds the given field definition array to the form property.
     * 
     * @since       3.0.0
     * @param       array|string            $asField        A field definition array.
     * @return      array|string|null       If the passed field is set, it returns the set field array. If the target section id is set, the set section id is returned. Otherwise null.
     */    
    public function addField( $asField ) {
        
        if ( ! is_array( $asField ) ) {
            $this->_sTargetSectionID = $this->getAOrB(
                is_string( $asField ),
                $asField,
                $this->_sTargetSectionID
            );
            return $this->_sTargetSectionID;
        }
        $_aField = $asField;
        $this->_sTargetSectionID = $this->getElement(
            $_aField,  // subject array
            'section_id', // key
            $this->_sTargetSectionID // default
        );                               
            
        $_aField = $this->uniteArrays( 
            array( '_fields_type' => $this->sFieldsType )
            + $_aField, 
            array( 'section_id' => $this->_sTargetSectionID )
            // + self::$_aStructure_Field // @deprecated 3.6.0 as the field will be formatted later anyway.
        );
        
        // Required Keys
        if ( ! isset( $_aField['field_id'], $_aField['type'] ) ) { 
            return null; 
        } 
            
        // Sanitize the IDs since they are used as a callback method name.
        $_aField['field_id']     = $this->sanitizeSlug( $_aField['field_id'] );
        $_aField['section_id']   = $this->sanitizeSlug( $_aField['section_id'] );     
        
        $this->aFields[ $_aField['section_id'] ][ $_aField['field_id'] ] = $_aField;
        return $_aField;
        
    }    
        
    /**
     * Removes a field definition array from the property array by the given field ID.
     * 
     *  The structure of the aFields property array looks like this:
     *  <code>    array( 
     *          'my_sec_a' => array(
     *              'my_field_a' => array( ... ),
     *              'my_field_b' => array( ... ),
     *              'my_field_c' => array( ... ),
     *          ),
     *          'my_sec_b' => array(
     *              'my_field_a' => array( ... ),
     *              'my_field_b' => array( ... ),
     *              1 => array(
     *                  'my_field_a' => array( ... ),
     *                  'my_field_b' => array( ... ),
     *              )
     *              2 => array(
     *                  'my_field_a' => array( ... ),
     *                  'my_field_b' => array( ... ),
     *              )     
     *          )
     *      )</code>
     * 
     * @since 3.0.0
     */     
    public function removeField( $sFieldID ) {
               
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {

            if ( array_key_exists( $sFieldID, $_aSubSectionsOrFields ) ) {
                unset( $this->aFields[ $_sSectionID ][ $sFieldID ] );
            }
            
            // Check sub-sections.
            foreach ( $_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields ) {
                
                // if it's a sub-section
                if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
                    if ( array_key_exists( $sFieldID, $_aSubSectionOrFields ) ) {
                        unset( $this->aFields[ $_sSectionID ][ $_sIndexOrFieldID ] );
                    }
                    continue;
                }
                
            }
        }
        
    }
    
    /**
     * Formats the section and field definition arrays.
     * 
     * @since 3.0.0
     */
    public function format() {
        
        $this->aSections    = $this->formatSections( $this->aSections, $this->sFieldsType, $this->sCapability );
        $this->aFields      = $this->formatFields( $this->aFields, $this->sFieldsType, $this->sCapability );
        
    }
    
    /**
     * Formats the stored sections definition array.
     * 
     * @since       3.0.0
     * @since       3.1.1    Added a parameter. Changed to return the formatted sections array.
     * @return      array    the formatted sections array.
     */
    public function formatSections( array $aSections, $sFieldsType, $sCapability ) {

        $_aNewSectionArray = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {

            if ( ! is_array( $_aSection ) ) { 
                continue; 
            }

            $_aSection = $this->formatSection( 
                $_aSection, 
                $sFieldsType, 
                $sCapability, 
                count( $_aNewSectionArray ), // this new array gets updated in this loops so the count will be updated.
                $this->oCaller
            );
            if ( empty( $_aSection ) ) { 
                continue; 
            }
            
            $_aNewSectionArray[ $_sSectionID ] = $_aSection;
            
        }
        uasort( $_aNewSectionArray, array( $this, '_sortByOrder' ) ); 
        return $_aNewSectionArray;
        
    }
    
        /**
         * Returns the formatted section array.
         * 
         * @since       3.0.0
         * @remark      The scope is protected because the extended page class overrides this method.
         * @return      array       The formatted section definition array.
         */
        protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements, $oCaller ) {

            $_aSectionFormatter = new AdminPageFramework_Format_Sectionset(
                $aSection, 
                $sFieldsType, 
                $sCapability, 
                $iCountOfElements,
                $oCaller
            );
            return $_aSectionFormatter->get();
            
        }
        
        
    /**
     * Formats the stored fields definition array.
     * 
     * @since       3.0.0
     * @since       3.1.1       Added a parameter. Changed to return the formatted sections array.
     */
    public function formatFields( array $aFields, $sFieldsType, $sCapability ) {

        $_aNewFields = array();
        foreach( $aFields as $_sSectionID => $_aSubSectionsOrFields ) {
            
            if ( ! isset( $this->aSections[ $_sSectionID ] ) ) { 
                continue; 
            }

            $_aNewFields[ $_sSectionID ] = $this->getElementAsArray( $_aNewFields, $_sSectionID, array() );
            
            // If there are sub-section items,
            $_abSectionRepeatable = $this->aSections[ $_sSectionID ]['repeatable']; // a setting array or boolean or true/false
            
            // If sub-section exists or repeatable,
            if ( count( $this->getIntegerKeyElements( $_aSubSectionsOrFields ) ) || $_abSectionRepeatable ) { 
                                 
                foreach( $this->numerizeElements( $_aSubSectionsOrFields ) as $_iSectionIndex => $_aFields ) {
                                  
                    foreach( $_aFields as $_aField ) {
                        $_iCountElement = count( $this->getElementAsArray( $_aNewFields, array( $_sSectionID, $_iSectionIndex ), array() ) );
                        $_aField        = $this->formatField( 
                            $_aField, 
                            $sFieldsType, 
                            $sCapability, 
                            $_iCountElement, 
                            $_iSectionIndex, 
                            $_abSectionRepeatable, 
                            $this->oCaller 
                        );
                        if ( ! empty( $_aField ) ) {
                            $_aNewFields[ $_sSectionID ][ $_iSectionIndex ][ $_aField['field_id'] ] = $_aField;
                        }
                    }
                    uasort( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ], array( $this, '_sortByOrder' ) );                 
                    
                }
                continue;
                
            }
         
            // Otherwise, these are normal sectioned fields.
            $_aSectionedFields = $_aSubSectionsOrFields;
            foreach( $_aSectionedFields as $_sFieldID => $_aField ) {
                
                // Insert the formatted field definition array. The fields count is needed to set each order value.
                $_iCountElement = count( $this->getElementAsArray( $_aNewFields, $_sSectionID, array() ) ); 
                $_aField        = $this->formatField(
                    $_aField, 
                    $sFieldsType, 
                    $sCapability, 
                    $_iCountElement, 
                    null, 
                    $_abSectionRepeatable,
                    $this->oCaller
                );
                if ( ! empty( $_aField ) ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }
            uasort( $_aNewFields[ $_sSectionID ], array( $this, '_sortByOrder' ) ); 

        }
        
        // Sort by the order of the sections.
        $this->_sortFieldsBySectionsOrder( $_aNewFields, $this->aSections );

        return $_aNewFields;
        
    }
          
        /**
         * Sorts fields by section order.
         * 
         * Assumes the sections are formatted already.
         * 
         * @since       3.5.3
         * @return      void
         * @internal
         */
        private function _sortFieldsBySectionsOrder( array &$aFields, array $aSections ) {

            // Check if they are not empty as taxonomy fields don't have sections
            if ( empty( $aSections ) || empty( $aFields ) ) {
                return;
            }
            
            $_aSortedFields = array();
            foreach( $aSections as $_sSectionID => $_aSeciton ) { 
                if ( isset( $aFields[ $_sSectionID ] ) ) {
                    $_aSortedFields[ $_sSectionID ] = $aFields[ $_sSectionID ];
                }
            }
            $aFields = $_aSortedFields;
        
        }
        
        /**
         * Returns the formatted field array.
         * 
         * @since       3.0.0
         * @return      array|void       An array of formatted field definition array. If required keys are not set, nothing will be returned. 
         */
        protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
            
            if ( ! isset( $aField['field_id'], $aField['type'] ) ) { 
                return; 
            }
            
            $_oFieldsetFormatter = new AdminPageFramework_Format_Fieldset(
                $aField, 
                $sFieldsType, 
                $sCapability, 
                $iCountOfElements, 
                $iSectionIndex, 
                $bIsSectionRepeatable, 
                $oCallerObject
            );
            return $_oFieldsetFormatter->get();
                        
        }
        
    /**
     * Returns the fields-definition array that the conditions have been applied.
     * 
     * @since       3.0.0
     * @since       3.5.3       Removed the parameters.
     */
    public function applyConditions() {
        return $this->getConditionedFields( 
            $this->getAsArray( $this->aFields ), 
            $this->getConditionedSections( $this->getAsArray( $this->aSections ) )
        );
    }
    
    /**
     * Returns a sections-array by applying the conditions.
     * 
     * @remark      Updates the `$aConditionedSections` array property.
     * @since       3.0.0
     * @since       3.5.3       Added a type hint and changed the default value to array from null.
     * @return      array       The conditioned sections array.
     */
    public function getConditionedSections( array $aSections=array() ) {
        
        $_aNewSections  = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {
            $_aSection = $this->getConditionedSection( $_aSection );
            if ( ! empty( $_aSection ) ) {
                $_aNewSections[ $_sSectionID ] = $_aSection;
            }
        }        
        $this->aConditionedSections = $_aNewSections;
        return $_aNewSections;
        
    }
        /**
         * Returns the conditioned section definition array.
         * 
         * This method is meant to be overridden in the extended class to have more customized conditions.
         * 
         * @since       3.0.0
         * @since       3.5.3       Changed the return type to only array from array|null.
         * @return      array       The filtered section array.
         */
        protected function getConditionedSection( array $aSection ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aSection['capability'] ) ) { 
                return array();
            }
            if ( ! $aSection['if'] ) { 
                return array(); 
            }
            
            return $aSection;
            
        }
    
    /**
     * Returns a fields-array by applying the conditions.
     * 
     * This will internally stores the aConditionedFields array into the property.
     * 
     * @remark      Assumes sections are conditioned already.
     * @since       3.0.0
     * @since       3.5.3       Added type hints to the parameters and removed default values.
     */
    public function getConditionedFields( array $aFields, array $aSections ) {

        // Drop keys of fields-array which do not exist in the sections-array. 
        // For this reasons, the sections-array should be conditioned first before applying this method.
        $aFields    = $this->castArrayContents( $aSections, $aFields );

        $_aNewFields = array();
        foreach( $aFields as $_sSectionID => $_aSubSectionOrFields ) {
            
            // This type check is important as the parsing field array is content-cast, which can set null value to elements.
            if ( ! is_array( $_aSubSectionOrFields ) ) { 
                continue; 
            }
                        
            $this->_setConditionedFields( 
                $_aNewFields,   // by reference - gets updated in the method.
                $_aSubSectionOrFields, 
                $_sSectionID
            );
      
        }
                
        $this->aConditionedFields = $_aNewFields;
        return $_aNewFields;
        
    }     
        /**
         * Updates the given array of conditioned fields.
         * 
         * @since       3.5.3
         * @internal
         * @return      void
         */
        private function _setConditionedFields( array &$_aNewFields, $_aSubSectionOrFields, $_sSectionID ) {
            
            foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
                
                // If it is a sub-section array.
                if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
                    $_sSubSectionIndex  = $_sIndexOrFieldID;
                    $_aFields           = $_aSubSectionOrField;
                    foreach( $_aFields as $_aField ) {
                        $_aField = $this->getConditionedField( $_aField );
                        if ( ! empty( $_aField ) ) {
                            $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $_aField;
                        }
                    }
                    continue;
                    
                }
                
                // Otherwise, insert the formatted field definition array.
                $_aField = $_aSubSectionOrField;
                $_aField = $this->getConditionedField( $_aField );
                if ( ! empty( $_aField ) ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }            
            
        }
        /**
         * Returns the field definition array by applying conditions. 
         * 
         * This method is intended to be extended to let the extended class customize the conditions.
         * 
         * @since       3.0.0
         * @since       3.5.3       Added a type hint to the parameter. Changed the return type to only array from null|array.
         * @return      array       The filtered field definition array.
         */
        protected function getConditionedField( array $aField ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aField['capability'] ) ) { 
                return array();
            }
            if ( ! $aField['if'] ) { 
                return array(); 
            }
            return $aField;
            
        }
    
    
    /**
     * Updates the `aConditionedFields` property by adding dynamic elements from the given options array.
     * 
     * Dynamic elements are repeatable sections and sortable/repeatable fields. 
     * This method checks the structure of the given array 
     * and adds section elements to the `$aConditionedFields` property arrays.
     * 
     * @remark      Assumes sections and fields have already conditioned.
     * @since       3.0.0
     * @return      void
     * @todo        Display a warning when sections and fields are not conditioned.
     */
    public function setDynamicElements( $aOptions ) {
        
        $aOptions = $this->castArrayContents( $this->aConditionedSections, $aOptions );
        foreach( $aOptions as $_sSectionID => $_aSubSectionOrFields ) {
            
            $_aSubSection = $this->_getSubSectionFromOptions(   
                $_sSectionID,
                // Content-cast array elements (done with castArrayContents()) can be null so make sure to have it an array
                $this->getAsArray( 
                    $_aSubSectionOrFields   // a sub-section or fields extracted from the saved options array
                )  
            );

            if ( empty( $_aSubSection ) ) {
                continue;
            }
            
            // At this point, the associative keys will be gone but the element only consists of numeric keys.
            $this->aConditionedFields[ $_sSectionID ] = $_aSubSection;
            
        }

    }
        /**
         * Extracts sub-section from the given options array element.
         * 
         * The options array is the one stored in and retrieved from the database.
         * 
         * @internal
         * @since       3.5.3
         * @param       string      $_sSectionID                    The expected section ID.
         * @param       array       $_aSubSectionOrFields           sub-sections or fields extracted from the saved options array
         * @return      array       sub-sections array.
         */
        private function _getSubSectionFromOptions( $_sSectionID, array $_aSubSectionOrFields ) {
            
            $_aSubSection = array();
            $_iPrevIndex  = null;
            foreach( $_aSubSectionOrFields as $_isIndexOrFieldID => $_aSubSectionOrFieldOptions ) {
            
                // If it is not a sub-section array, skip.
                if ( ! $this->isNumericInteger( $_isIndexOrFieldID ) ) { 
                    continue; 
                }
                
                $_iIndex = $_isIndexOrFieldID;
                
                $_aSubSection[ $_iIndex ] = $this->_getSubSectionItemsFromOptions(
                    $_aSubSection, 
                    $_sSectionID, 
                    $_iIndex, 
                    $_iPrevIndex 
                );
   
                // Update the internal section index key
                foreach( $_aSubSection[ $_iIndex ] as &$_aField ) {
                    $_aField['_section_index'] = $_iIndex;
                }
                unset( $_aField ); // to be safe in PHP
                
                $_iPrevIndex = $_iIndex;
                
            }
            return $_aSubSection;
            
        }
            /**
             * Returns items belonging to the given sub-section from the options array.
             * 
             * @internal
             * @since       3.5.3
             * @param       array           $_aSubSection       the subsection array
             * @param       string          $_sSectionID        the section id
             * @param       integer         $_iIndex            the sub-section index
             * @param       integer|null    $_iPrevIndex
             * @return      array
             */
            private function _getSubSectionItemsFromOptions( array $_aSubSection, $_sSectionID, $_iIndex, $_iPrevIndex ) {
                
                $_aFields = isset( $this->aConditionedFields[ $_sSectionID ][ $_iIndex ] )
                    ? $this->aConditionedFields[ $_sSectionID ][ $_iIndex ]
                    : $this->getNonIntegerKeyElements( $this->aConditionedFields[ $_sSectionID ] );
                    
                // if empty, merge with the previous element.
                return ! empty( $_aFields )
                    ? $_aFields
                    : $this->getElementAsArray(
                        $_aSubSection,
                        $_iPrevIndex,
                        array()
                    );                     
                
            }
        
}