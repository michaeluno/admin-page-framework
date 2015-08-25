<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
class AdminPageFramework_Sort_Input extends AdminPageFramework_Sort_Base {
    
    public $aInput = array();
    public $aSortDimensionalKeys = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aInput, $aSortDImensionalKeys */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aInput, 
            $this->aSortDimensionalKeys, 
        );
        $this->aInput               = $_aParameters[ 0 ];
        $this->aSortDimensionalKeys = $_aParameters[ 1 ];
        
    }
    
    /**
     * Sorts dynamic form input elements such as sortable and repeatable sections and fields.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {

        foreach( $this->aSortDimensionalKeys as $_sFlatFieldAddress ) {
            
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
           
}