<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format an array holding section-sets definitions.
 * 
 * It is assumed that this class gets instantiated before section-sets definition array is formatted.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form___FormatFieldsets extends AdminPageFramework_Form_Base {
    
    public $aSectionsets    = array();
    public $aFieldsets      = array();
    public $sStructureType  = '';
    public $sCapability     = '';
    public $aCallbacks      = array(
        'fieldset_before_output' => null
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
     * @since       DEVVER
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
     * @since       DEVVER
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {

        $this->aFieldsets = $this->_getFieldsetsFormatted( 
            $this->aFieldsets,
            $this->aSectionsets,
            $this->sStructureType,
            $this->sCapability
        );   
        
        // Add the repeatable section elements to the fieldsets definition array.
        return $this->_getDynamicElementsAdded();
        
    }
        /**
         * Adds repeatable section items to the given form data array.
         * @since       DEVVER
         * @return      array
         */
        private function _getDynamicElementsAdded() {
            $_oDynamicElements = new AdminPageFramework_Form___FormatDynamicElements(
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
         * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatFields()`.
         * Added the `$aSectionsets` parameter.
         * @retuen      array
         */
        private function _getFieldsetsFormatted( array $aFieldsets, array $aSectionsets, $sStructureType, $sCapability ) {

            $_aNewFieldsets = array();
            foreach( $aFieldsets as $_sSectionID => $_aSubSectionsOrFields ) {

                if ( ! isset( $aSectionsets[ $_sSectionID ] ) ) { 
                    continue; 
                }

                // 3.6.0+ Get the section's capability
                $sCapability = $this->getElement( 
                    $aSectionsets[ $_sSectionID ], 
                    'capability',
                    $sCapability
                );

// @todo Investigate what this is for.                
                $_aNewFieldsets[ $_sSectionID ] = $this->getElementAsArray( 
                    $_aNewFieldsets, 
                    $_sSectionID, 
                    array() 
                );

                // If there are sub-section items,
                // a setting array or boolean or true/false
                $_abSectionRepeatable = $aSectionsets[ $_sSectionID ][ 'repeatable' ]; 
                
                // If sub-section exists or repeatable,
                if ( count( $this->getIntegerKeyElements( $_aSubSectionsOrFields ) ) || $_abSectionRepeatable ) { 
                                     
                    foreach( $this->numerizeElements( $_aSubSectionsOrFields ) as $_iSectionIndex => $_aFieldsets ) {
                                      
                        foreach( $_aFieldsets as $_aFieldset ) {
                            $_iCountElement = count( $this->getElementAsArray( $_aNewFieldsets, array( $_sSectionID, $_iSectionIndex ), array() ) );
                            $_aFieldset        = $this->_getFieldsetFormatted( 
                                $_aFieldset, 
                                $aSectionsets,
                                $sStructureType, 
                                $sCapability, 
                                $_iCountElement, 
                                $_iSectionIndex, 
                                $_abSectionRepeatable, 
                                $this->oCallerForm
                            );
                            if ( ! empty( $_aFieldset ) ) {
                                $_aNewFieldsets[ $_sSectionID ][ $_iSectionIndex ][ $_aFieldset['field_id'] ] = $_aFieldset;
                            }
                        }
                        uasort( $_aNewFieldsets[ $_sSectionID ][ $_iSectionIndex ], array( $this, 'sortArrayByKey' ) );
                        
                    }
                    continue;
                    
                }
             
                // Otherwise, these are normal sectioned fields.
                $_aSectionedFields = $_aSubSectionsOrFields;
                foreach( $_aSectionedFields as $_sFieldID => $_aFieldset ) {
                    
                    // Insert the formatted field definition array. The fields count is needed to set each order value.
                    $_iCountElement = count( $this->getElementAsArray( $_aNewFieldsets, $_sSectionID, array() ) ); 
                    $_aFieldset        = $this->_getFieldsetFormatted(
                        $_aFieldset, 
                        $aSectionsets,
                        $sStructureType, 
                        $sCapability, 
                        $_iCountElement, 
                        null, 
                        $_abSectionRepeatable,
                        $this->oCallerForm
                    );
                    if ( ! empty( $_aFieldset ) ) {
                        $_aNewFieldsets[ $_sSectionID ][ $_aFieldset['field_id'] ] = $_aFieldset;
                    }
                    
                }
                uasort( $_aNewFieldsets[ $_sSectionID ], array( $this, 'sortArrayByKey' ) ); 

            }

            // Sort by the order of the sections.
            $this->_sortFieldsBySectionsOrder( $_aNewFieldsets, $aSectionsets );

            return $this->callBack(
                $this->aCallbacks[ 'fieldsets_after_formatting' ], 
                array( 
                    $_aNewFieldsets,
                    $aSectionsets
                )
            );            
                        
        }
              
            /**
             * Sorts fields by section order.
             * 
             * Assumes the sections are formatted already.
             * 
             * @since       3.5.3
             * @since       DEVVER          Moved from `AdminPageFramework_FormDefinition`.
             * @return      void
             * @internal
             */
            private function _sortFieldsBySectionsOrder( array &$aFieldsets, array $aSections ) {

                // Check if they are not empty as taxonomy fields don't have sections
                if ( empty( $aSections ) || empty( $aFieldsets ) ) {
                    return;
                }
                
                $_aSortedFields = array();
                foreach( $aSections as $_sSectionID => $_aSeciton ) { 
                    if ( isset( $aFieldsets[ $_sSectionID ] ) ) {
                        $_aSortedFields[ $_sSectionID ] = $aFieldsets[ $_sSectionID ];
                    }
                }
                $aFieldsets = $_aSortedFields;
            
            }
            
            /**
             * Returns the formatted fieldset array.
             * 
             * @since       3.0.0
             * @since       DEVVER          Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatField()`.
             * @return      array|void      An array of formatted field definition array. If required keys are not set, nothing will be returned. 
             */
            private function _getFieldsetFormatted( $aFieldset, $aSectionsets, $sStructureType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
// @todo Use a callback so that the caller factory object does not have to be passed. <-- not sure what it meant
                if ( ! isset( $aFieldset[ 'field_id' ], $aFieldset[ 'type' ] ) ) { 
                    return; 
                }

                $_oFieldsetFormatter = new AdminPageFramework_Format_Fieldset(
                    $aFieldset, 
                    $sStructureType, 
                    $sCapability, 
                    $iCountOfElements, 
                    $iSectionIndex, 
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
                return $this->callBack(
                    $this->aCallbacks[ 'fieldset_after_formatting' ], 
                    array( 
                        $_aFieldset,
                        $aSectionsets
                    )
                );
                            
            }    
 
}