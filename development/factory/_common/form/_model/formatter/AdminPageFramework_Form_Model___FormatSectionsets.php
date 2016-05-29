<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format an array holding section-sets definitions.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.7.0
 * @extends     AdminPageFramework_Form_Base
 * @internal
 */
class AdminPageFramework_Form_Model___FormatSectionsets extends AdminPageFramework_Form_Base {
    
    public $sStructureType = '';
    public $aSectionsets  = array();
    public $sCapability = '';
    public $aCallbacks = array(
        'sectionset_before_output' => null
    );
    
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
    public function __construct( /* array $aSectionsets, $sStructureType, $sCapability, $aCallbacks, $oCallerForm */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSectionsets, 
            $this->sStructureType, 
            $this->sCapability,
            $this->aCallbacks,
            $this->oCallerForm
        );
        $this->aSectionsets     = $_aParameters[ 0 ];                    
        $this->sStructureType   = $_aParameters[ 1 ];
        $this->sCapability      = $_aParameters[ 2 ];
        $this->aCallbacks       = $_aParameters[ 3 ];
        $this->oCallerForm      = $_aParameters[ 4 ];
        
    }

    /**
     * @since       3.7.0
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {
        
        if ( empty( $this->aSectionsets ) ) {
            return array();
        }

        $_aSectionsets = $this->_getSectionsetsFormatted(
            array(),                // sectionsets array to modify - new formatted items will be stored here
            $this->aSectionsets,    // parsing sectionsets
            array(),                // section path - empty for root 
            $this->sCapability      // capability
        );

        return $_aSectionsets;
        
    }
        /**
         * Formats the stored sections definition array.
         * 
         * @since       3.0.0
         * @since       3.1.1    Added a parameter. Changed to return the formatted sections array.
         * @since       3.7.0   Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatSections()`.
         * @return      array    the formatted sections array.
         */
        private function _getSectionsetsFormatted( $_aNewSectionsets, $aSectionsetsToParse, $aSectionPath, $sCapability ) {

            foreach( $aSectionsetsToParse as $_sSectionPath => $_aSectionset ) {

                // The '_default' section can be empty so do not check `if ( empty( $_aSectionset ) )` here.
                if ( ! is_array( $_aSectionset ) ) { 
                    continue; 
                }
                
                $_aSectionPath = array_merge( $aSectionPath, array( $_aSectionset[ 'section_id' ] ) );
                $_sSectionPath = implode( '|', $_aSectionPath );
                
                $_aSectionsetFormatter = new AdminPageFramework_Form_Model___FormatSectionset(
                    $_aSectionset, 
                    $_sSectionPath,
                    $this->sStructureType, 
                    $sCapability, 
                    count( $_aNewSectionsets ), // this new array gets updated in this loops so the count will be updated.
                    $this->oCallerForm
                );
                $_aSectionset = $this->callBack(
                    $this->aCallbacks[ 'sectionset_before_output' ], 
                    array( $_aSectionsetFormatter->get() )
                );
                if ( empty( $_aSectionset ) ) { 
                    continue; 
                }
                
                $_aNewSectionsets[ $_sSectionPath ] = $_aSectionset;
                
                // 3.7.0+ For nested sections         
                $_aNewSectionsets = $this->_getNestedSections( 
                    $_aNewSectionsets,  // sectionset array to modify
                    $_aSectionset, 
                    $_aSectionPath, // section path
                    $_aSectionset[ 'capability' ]
                );
                
            }

            uasort( $_aNewSectionsets, array( $this, 'sortArrayByKey' ) ); 
            return $_aNewSectionsets;
            
        }   
            /**
             * @return      array       The modified sectionsets definitions.
             */
            private function _getNestedSections( $aSectionsetsToEdit, $aSectionset, $aSectionPath, $sCapability ) {

                if ( ! $this->_hasNestedSections( $aSectionset ) ) {
                    return $aSectionsetsToEdit;
                }

                // Recursive call
                return $this->_getSectionsetsFormatted(
                        $aSectionsetsToEdit,          // sectionsets array to modify - new formatted items will be stored here
                        $aSectionset[ 'content' ],    // parsing sectionsets
                        $aSectionPath,                // section path - empty for root 
                        $sCapability                  // capability
                    );                          

            }
                /**
                 * Checks if a given sectionset definition has nested sections.
                 * @return      boolean
                 * @sinec       3.7.0
                 */
                private function _hasNestedSections( $aSectionset ) {
                    
                    $aSectionset = $aSectionset + array( 'content' => null );
                    if ( ! is_array( $aSectionset[ 'content' ] ) ) {
                        return false;
                    }
                    $_aContents  = $aSectionset[ 'content' ];
                    $_aFirstItem = $this->getFirstElement( $_aContents );
                    return is_scalar( $this->getElement( $_aFirstItem, 'section_id', null ) );
                    
                }
 
}
