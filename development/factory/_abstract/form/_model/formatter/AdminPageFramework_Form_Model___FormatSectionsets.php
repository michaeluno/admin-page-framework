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
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
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
     * @since       DEVVER
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
     * @since       DEVVER
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {
        
        if ( empty( $this->aSectionsets ) ) {
            return array();
        }
        return $this->_getSectionsetsFormatted( 
            $this->aSectionsets,
            $this->sStructureType,
            $this->sCapability
        );
        
    }
        /**
         * Formats the stored sections definition array.
         * 
         * @since       3.0.0
         * @since       3.1.1    Added a parameter. Changed to return the formatted sections array.
         * @since       DEVVER   Moved from `AdminPageFramework_FormDefinition`. Changed the name from `formatSections()`.
         * @return      array    the formatted sections array.
         */
        private function _getSectionsetsFormatted( array $aSectionsets, $sStructureType, $sCapability ) {

            $_aNewSectionsets = array();
            foreach( $aSectionsets as $_sSectionID => $_aSection ) {

                if ( ! is_array( $_aSection ) ) { 
                    continue; 
                }
                $_aSectionFormatter = new AdminPageFramework_Form_Model___FormatSectionset(
                    $_aSection, 
                    $sStructureType, 
                    $sCapability, 
                    count( $_aNewSectionsets ), // this new array gets updated in this loops so the count will be updated.
                    $this->oCallerForm
                );
                $_aSection = $this->callBack(
                    $this->aCallbacks[ 'sectionset_before_output' ], 
                    array( $_aSectionFormatter->get() )
                );
                if ( empty( $_aSection ) ) { 
                    continue; 
                }
                $_aNewSectionsets[ $_sSectionID ] = $_aSection;
                
            }
            uasort( $_aNewSectionsets, array( $this, 'sortArrayByKey' ) ); 
            return $_aNewSectionsets;
            
        }    
 
}