<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with sorting form input array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_Model___Modifier_SortInput extends AdminPageFramework_Form_Model___Modifier_Base {
    
    public $aInput = array();
    public $aFieldAddresses = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aInput, $aFieldAddresses */ ) {
        
        $_aParameters = func_get_args() + array(
            $this->aInput,
            $this->aFieldAddresses,
        );
        $this->aInput               = $_aParameters[ 0 ];
        $this->aFieldAddresses = $_aParameters[ 1 ];
        
        
    }
            
    /**
     * Sorts dynamic form input elements such as sortable and repeatable sections and fields.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {

        foreach( $this->_getFormattedDimensionalKeys( $this->aFieldAddresses ) as $_sFlatFieldAddress ) {
            
            $_aDimensionalKeys = explode( '|', $_sFlatFieldAddress );
                        
            $_aDynamicElements = $this->getElement(
                $this->aInput,
                $_aDimensionalKeys
            );
            
            // If the retrieved value does not exist, null will be given.
            // This occurs with page meta boxes.
            if ( ! is_array( $_aDynamicElements ) ) {
                continue;
            }
            
            $this->setMultiDimensionalArray(
                $this->aInput,
                $_aDimensionalKeys,
                array_values( $_aDynamicElements ) // re-indexed array
            );
                      
        }

        return $this->aInput;
        
    }
    
        /**
         * Formats the array containing section and field addresses.
         * 
         * The array must be sorted so that deeper elements get parsed before shallower dimensions get parsed.
         * This is because if the shallower dimensions get parsed before deeper dimensions, when deeper ones get parsed,
         * the addresses are already modified and it causes data loss.
         * 
         * @since       3.6.2
         * @return      array
         */
        private function _getFormattedDimensionalKeys( $aFieldAddresses ) {
        
            $aFieldAddresses = $this->getAsArray( $aFieldAddresses );
            $aFieldAddresses = array_unique( $aFieldAddresses );
            arsort( $aFieldAddresses );

            return $aFieldAddresses;
            
        }
           
}
