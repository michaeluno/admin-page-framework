<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to get default form values from given fieldsets.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 * @deprecated
 */
class AdminPageFramework_Form_Model___DefaultValues extends AdminPageFramework_WPUtility {
    
    public $aFieldsets  = array();

    /**
     * Sets up hooks.
     * @since       DEVVER
     */
    public function __construct( /* $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldsets, 
        );
        $this->aFieldsets  = $_aParameters[ 0 ];                    
        
    }

    /**
     * @since       DEVVER
     * @return      array       
     */
    public function get() {
        
        $_aDefaultOptions = array();
        foreach( $this->aFieldsets as $_sSectionID => $_aFieldsetsPerSection ) {
            
// @todo Think of a new way when there are nested fieldsets and sectionsets.                
            foreach( $_aFieldsetsPerSection as $_sFieldID => $_aFieldset ) {
                
                $_vDefault = $this->_getDefautValue( $_aFieldset );
                
                if ( isset( $_aFieldset[ 'section_id' ] ) && $_aFieldset[ 'section_id' ] != '_default' ) {
                    $_aDefaultOptions[ $_aFieldset[ 'section_id' ] ][ $_sFieldID ] = $_vDefault;
                } else {
                    $_aDefaultOptions[ $_sFieldID ] = $_vDefault;
                }
                    
            }
                
        }     
        return $_aDefaultOptions;   
        
    }
 
        /**
         * Returns the default value from the given field definition array.
         * 
         * This is a helper function for the above getDefaultOptions() method.
         * 
         * @since       3.0.0
         * @since       DEVVER      Moved from `AdminPageFramework_Property_Page`.
         */
        private function _getDefautValue( $aFieldset ) {
            
            // Check if sub-fields exist whose keys are numeric
            $_aSubFields = $this->getIntegerKeyElements( $aFieldset );

            // If there are no sub-fields     
            if ( count( $_aSubFields ) == 0 ) {
                return $this->getElement(
                    $aFieldset,   // subject
                    'value',    // key
                    $this->getElement(   // default value
                        $aFieldset,   // subject  
                        'default',  // key
                        null        // default value
                    )
                );
            }
            
            // Otherwise, there are sub-fields
            $_aDefault = array();
            array_unshift( $_aSubFields, $aFieldset ); // insert the main field into the very first index.
            foreach( $_aSubFields as $_iIndex => $_aField ) {
                $_aDefault[ $_iIndex ] = $this->getElement( 
                    $_aField,   // subject
                    'value',    // key
                    $this->getElement(   // default value
                        $_aField,   // subject  
                        'default',  // key
                        null        // default value
                    )
                ); 
            }
            return $_aDefault;
            
        }            

}