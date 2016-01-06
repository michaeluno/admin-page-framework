<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to get default form values from given fieldsets.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @deprecated
 */
class AdminPageFramework_Form_Model___DefaultValues extends AdminPageFramework_Form_Base {
    
    public $aFieldsets  = array();

    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( /* $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldsets, 
        );
        $this->aFieldsets  = $_aParameters[ 0 ];                    
        
    }

    /**
     * Retrieves the default values from the set fieldsetss.
     * The structure of the fieldsets looks like the following.
     * 
     * array(
     *      // section id => fieldsets
     *      'my_section_a' => array(
     *          'my_field_a' => array(
     *              'type' => 'text',
     *              'section_id' => 'my_section_a',
     *              ...
     *          ),
     *          'my_field_b' => array(
     *              'type' => 'textarea',
     *              'section_id' => 'my_section_b',
     *              ...
     *          )
     *      ),
     *      'my_section_b' => array(
     *          // nested section id => fieldsets
     *          'nested_section_b_a' => array(
     *              'my_field_d'    => array(
     *                  'type'  => 'color',
     *                  'section_id' => array( 'my_section_b', 'nested_section_b_a' ),
     *              ),
     *          ),
     *          'nested_section_b_b' => array(
     *              'my_field_e'    => array(
     *                  'type'  => 'radio',
     *                  'section_id' => array( 'my_section_b', 'nested_section_b_b' ),
     *              ),
     *          ),
     *      ),
     * 
     * )
     * 
     * @since       3.7.0
     * @return      array       
     */
    public function get() {

// @todo Test the result completely, especially for repeated sections.     
        $_aResult = $this->_getDefaultValues(
            $this->aFieldsets,
            array()
        ); 
        return $_aResult;
        
    }    
        /**
         * @return      array
         */
        private function _getDefaultValues( $aFieldsets, $aDefaultOptions ) {
            
            foreach( $aFieldsets as $_sSectionPath => $_aItems ) {
                
                $_aSectionPath   = explode( '|', $_sSectionPath );
                foreach( $_aItems as $_sFieldPath => $_aFieldset ) {
                    $_aFieldPath = explode( '|', $_sFieldPath );
                    $this->setMultiDimensionalArray( 
                        $aDefaultOptions,  // by reference
                        '_default' === $_sSectionPath
                            ? array( $_sFieldPath )
                            : array_merge( $_aSectionPath, $_aFieldPath ), // key address
                            // : array( $_sSectionID, $_sFieldPath ), // key address
                        $this->_getDefautValue( $_aFieldset )   // the value to set
                    );                    
                        
                }                
                
            }
            return $aDefaultOptions;
        }

    /**
     * @deprecated
     */
  /*   public function _get() {
        
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
        
    } */
 
        /**
         * Returns the default value from the given field definition array.
         * 
         * This is a helper function for the above getDefaultOptions() method.
         * 
         * @since       3.0.0
         * @since       3.7.0      Moved from `AdminPageFramework_Property_Page`.
         */
        private function _getDefautValue( $aFieldset ) {
            
            // Check if sub-fields exist whose keys are numeric
            $_aSubFields = $this->getIntegerKeyElements( $aFieldset );

            // If there are no sub-fields     
            if ( count( $_aSubFields ) == 0 ) {
                return $this->getElement(
                    $aFieldset,     // subject
                    'value',        // key
                    $this->getElement(   // default value
                        $aFieldset,      // subject  
                        'default',       // key
                        null             // default value
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