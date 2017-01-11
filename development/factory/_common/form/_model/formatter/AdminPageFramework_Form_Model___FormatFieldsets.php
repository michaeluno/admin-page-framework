<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format an array holding field-sets definitions.
 * 
 * It is assumed that this class gets instantiated before section-sets definition array is formatted.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.7.0
 * @extends     AdminPageFramework_Form_Base
 * @internal
 */
class AdminPageFramework_Form_Model___FormatFieldsets extends AdminPageFramework_Form_Base {
    
    public $aSectionsets    = array();
    public $aFieldsets      = array();
    public $sStructureType  = '';
    public $sCapability     = '';
    public $aCallbacks      = array(
        'fieldset_before_output'     => null,
        'fieldset_after_formatting'  => null,
    );
    
    public $aSavedData = array();
    
    /**
     * Stores the caller form object. 
     * 
     * This will be set in the definition array. Mostly used to construct nested items.
     */
    public $oCallerForm;
    
    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( /* array $aFieldsets, array $aSectionsets $sStructureType, $aSavedData, $sCapability, $aCallbacks, $oCallerForm */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldsets, 
            $this->aSectionsets,
            $this->sStructureType, 
            $this->aSavedData,
            $this->sCapability,
            $this->aCallbacks,
            $this->oCallerForm,
        );
        $this->aFieldsets       = $_aParameters[ 0 ];                    
        $this->aSectionsets     = $_aParameters[ 1 ];
        $this->sStructureType   = $_aParameters[ 2 ];
        $this->aSavedData       = $_aParameters[ 3 ];
        $this->sCapability      = $_aParameters[ 4 ];
        $this->aCallbacks       = $_aParameters[ 5 ];
        $this->oCallerForm      = $_aParameters[ 6 ];
            
    }

    /**
     * @since       3.7.0
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {

        $this->aFieldsets = $this->_getFieldsetsFormatted( 
            $this->aFieldsets,
            $this->aSectionsets,
            $this->sCapability
        );   
        
        // Add the repeatable section elements to the fieldsets definition array.
        return $this->_getDynamicElementsAddedToFieldsets();
        
    }
        /**
         * Adds repeatable section items to the given form data array.
         * @since       3.7.0
         * @return      array
         */
        private function _getDynamicElementsAddedToFieldsets() {
            $_oDynamicElements = new AdminPageFramework_Form_Model___FormatDynamicElements(
                $this->aSectionsets,
                $this->aFieldsets,
                $this->aSavedData
            );
            return $_oDynamicElements->get();
        }           
        
        /**
         * Formats the stored fields definition array.
         * 
         * @since       3.0.0
         * @since       3.1.1       Added a parameter. Changed to return the formatted sections array.
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatFields()`.
         * Added the `$aSectionsets` parameter.
         * @return      array
         */
        private function _getFieldsetsFormatted( array $aFieldsets, array $aSectionsets, $sCapability ) {

            // 3.8.4+ Changed the timing of this callback from AFTER formatting field-sets to BEFORE.
            $aFieldsets = $this->callBack(
                $this->aCallbacks[ 'fieldsets_before_formatting' ], 
                array( 
                    $aFieldsets,
                    $aSectionsets
                )
            );
        
            $_aNewFieldsets = array();
            foreach( $aFieldsets as $_sSectionPath => $_aItems ) {

                // If the section is not set, skip.
                if ( ! isset( $aSectionsets[ $_sSectionPath ] ) ) {               
                    continue;
                }

                $_aNewFieldsets[ $_sSectionPath ] = $this->_getItemsFormatteed( 
                    $_sSectionPath, 
                    $_aItems,
                    $this->getElement(  // 3.6.0+ Get the section's capability
                        $aSectionsets, 
                        array( $_sSectionPath, 'capability', ),
                        $sCapability
                    ),
                    $aSectionsets
                );
                
            }
  
            // Sort by the order of the sections.
            $this->_sortFieldsBySectionsOrder( 
                $_aNewFieldsets, // by reference 
                $aSectionsets   
            );
            
            return $_aNewFieldsets;
                        
        }
            /**
             * @since       3.7.0  
             * @return      array   
             */
            private function _getItemsFormatteed( $sSectionPath, $aItems, $sCapability, $aSectionsets ) {
                                
                // a setting array or boolean or true/false
                $_abSectionRepeatable = $this->getElement(   
                    $aSectionsets,
                    array( $sSectionPath, 'repeatable' ),
                    false                    
                );
                
                // If there are sub-section items,
                if ( $this->_isSubSections( $aItems, $_abSectionRepeatable ) ) {

                    return $this->_getSubSectionsFormatted( 
                        $aItems, 
                        $sCapability,
                        $aSectionsets,
                        $_abSectionRepeatable                        
                    );
                }  
                
                // Normal fields,
                return $this->_getNormalFieldsetsFormatted( 
                    $aItems, 
                    $sCapability,
                    $aSectionsets, 
                    $_abSectionRepeatable                    
                ); 
                
            }
              
                /**
                 * Formates sectioned fieldsets.
                 * @return      array
                 */
                private function _getNormalFieldsetsFormatted( $aItems, $sCapability, $aSectionsets, $_abSectionRepeatable ) {
                    
                    $_aNewItems     = array();
                    foreach( $aItems as $_sFieldID => $_aFieldset ) {
                        
                        // Insert the formatted field definition array. The fields count is needed to set each order value.
                        $_aFieldset        = $this->_getFieldsetFormatted(
                            $_aFieldset, 
                            $aSectionsets,
                            $sCapability, 
                            count( $_aNewItems ), // index of elements - zero based
                            null,   // sub-section index
                            $_abSectionRepeatable,
                            $this->oCallerForm
                        );
                        if ( empty( $_aFieldset ) ) {
                            continue;
                        }
                        $_aNewItems[ $_aFieldset[ 'field_id' ] ] = $_aFieldset;
                        
                    }
                    uasort( $_aNewItems, array( $this, 'sortArrayByKey' ) ); 
                    return $_aNewItems;
                    
                }
                
                /**
                 * @return      boolean
                 */
                private function _isSubSections( $aItems, $_abSectionRepeatable ) {
                    if ( ! empty( $_abSectionRepeatable ) ) {
                        return true;
                    }
                    return ( boolean ) count( $this->getIntegerKeyElements( $aItems ) );
                }
                /**
                 * @return      array
                 */
                private function _getSubSectionsFormatted( $aItems, $sCapability, $aSectionsets, $_abSectionRepeatable ) {
                             
                    $_aNewFieldset = array();
                    foreach( $this->numerizeElements( $aItems ) as $_iSubSectionIndex => $_aFieldsets ) {
                                      
                        foreach( $_aFieldsets as $_aFieldset ) {
                            $_iCountElement = count( $this->getElementAsArray( $_aNewFieldset, $_iSubSectionIndex ) );
                            $_aFieldset     = $this->_getFieldsetFormatted( 
                                $_aFieldset, 
                                $aSectionsets,
                                $sCapability, 
                                $_iCountElement, 
                                $_iSubSectionIndex, // sub-section index
                                $_abSectionRepeatable, 
                                $this->oCallerForm
                            );
                            if ( empty( $_aFieldset ) ) {
                                continue;
                            }
                            $_aNewFieldset[ $_iSubSectionIndex ][ $_aFieldset['field_id'] ] = $_aFieldset;
                        }
                        uasort( $_aNewFieldset[ $_iSubSectionIndex ], array( $this, 'sortArrayByKey' ) );
                        
                    }
                    return $_aNewFieldset;
                    
                }                       
                
            /**
             * Sorts fields by section order.
             * 
             * Assumes the sections are formatted already.
             * 
             * @since       3.5.3
             * @since       3.7.0          Moved from `AdminPageFramework_FormDefinition`.
             * @return      void
             * @internal
             */
            private function _sortFieldsBySectionsOrder( array &$aFieldsets, array $aSectionsets ) {

                // Check if they are not empty as taxonomy factory fields don't have sections
                if ( empty( $aSectionsets ) || empty( $aFieldsets ) ) {
                    return;
                }
                
                $_aSortedFields = array();
                foreach( $aSectionsets as $_sSectionPath => $_aSecitonset ) { 
                    if ( isset( $aFieldsets[ $_sSectionPath ] ) ) {
                        $_aSortedFields[ $_sSectionPath ] = $aFieldsets[ $_sSectionPath ];
                    }
                }
                $aFieldsets = $_aSortedFields;
            
            }
            
            /**
             * Returns the formatted fieldset array.
             * 
             * @since       3.0.0
             * @since       3.7.0          Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatField()`.
             * @return      array|void      An array of formatted field definition array. If required keys are not set, nothing will be returned. 
             */
            private function _getFieldsetFormatted( $aFieldset, $aSectionsets, $sCapability, $iCountOfElements, $iSubSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {

                // 3.8.0+ Dropped the check for $aFieldset[ 'type' ] to allow it to be omitted.
                if ( ! isset( $aFieldset[ 'field_id' ] ) ) { 
                    return; 
                }
                   
                $_oFieldsetFormatter = new AdminPageFramework_Form_Model___Format_Fieldset(
                    $aFieldset,
                    $this->sStructureType,
                    $sCapability, 
                    $iCountOfElements, 
                    $iSubSectionIndex, 
                    $bIsSectionRepeatable, 
                    $oCallerObject
                );
                $_aFieldset = $this->callBack(
                    $this->aCallbacks[ 'fieldset_before_output' ], 
                    array( 
                        $_oFieldsetFormatter->get(), // 1st parameter
                        $aSectionsets
                    )
                );
                $_aFieldset = $this->callBack(
                    $this->aCallbacks[ 'fieldset_after_formatting' ], 
                    array( 
                        $_aFieldset,
                        $aSectionsets
                    )
                );
                return $_aFieldset; 
                
            }     
 
}
