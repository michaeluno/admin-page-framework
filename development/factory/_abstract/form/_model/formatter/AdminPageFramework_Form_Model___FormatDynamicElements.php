<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to set dynamic form elements such as repeatable secitons.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_Model___FormatDynamicElements extends AdminPageFramework_WPUtility {

    public $aSectionsets   = array();
    public $aFieldsets     = array();
    public $aSavedFormData = array();
    
    /**
     * Sets up properties.
     * @since       DEVVER
     */
    public function __construct( /* $aSectionsets, $aFieldsets, $aSavedFormData */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSectionsets, 
            $this->aFieldsets,
            $this->aSavedFormData,
        );
        $this->aSectionsets   = $_aParameters[ 0 ];                    
        $this->aFieldsets     = $_aParameters[ 1 ];
        $this->aSavedFormData = $_aParameters[ 2 ];
        
    }
    
    /**
     * @sinde       DEVVER
     * @return      array       
     */
    public function get() {
        $this->_setDynamicElements( $this->aSavedFormData );
        return $this->aFieldsets;
    }
    
        /**
         * Updates the `aFieldsets` property by adding dynamic elements from the given options array.
         * 
         * Dynamic elements are repeatable sections and sortable/repeatable fields. 
         * This method checks the structure of the given array 
         * and adds section elements to the `$aFieldsets` property arrays.
         * 
         * @remark      Assumes sections and fields have already conditioned.
         * @since       3.0.0
         * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition`. Changed the name from `setDynamicElements()`. 
         * Changed the visibility scope from public.
         * @return      void
         */
        private function _setDynamicElements( $aOptions ) {
            
            $aOptions = $this->castArrayContents( 
                $this->aSectionsets, // model
                $aOptions // data source
            );
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
                
                // At this point, the associative keys will be gone 
                // but the element only consists of numeric keys.
                $this->aFieldsets[ $_sSectionID ] = $_aSubSection;
                
            }

        }
            /**
             * Extracts sub-section from the given options array element.
             * 
             * The options array is the one stored in and retrieved from the database.
             * 
             * @internal
             * @since       3.5.3
             * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition`.
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
                        $_aField[ '_section_index' ] = $_iIndex;
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
                 * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition`.
                 * @param       array           $_aSubSection       the subsection array
                 * @param       string          $_sSectionID        the section id
                 * @param       integer         $_iIndex            the sub-section index
                 * @param       integer|null    $_iPrevIndex
                 * @return      array
                 */
                private function _getSubSectionItemsFromOptions( array $_aSubSection, $_sSectionID, $_iIndex, $_iPrevIndex ) {
                    
                    if ( ! isset( $this->aFieldsets[ $_sSectionID ] ) ) {
                        return array();
                    }
                    
                    $_aFields = isset( $this->aFieldsets[ $_sSectionID ][ $_iIndex ] )
                        ? $this->aFieldsets[ $_sSectionID ][ $_iIndex ]
                        : $this->getNonIntegerKeyElements( $this->aFieldsets[ $_sSectionID ] );
                        
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