<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to help handle array data.
 * 
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @since       3.6.0
 */
class AdminPageFramework_ArrayHandler extends AdminPageFramework_WPUtility {
        
    /**
     * Stores field definitions.
     * 
     * @since       3.6.0
     */
    public $aData            = array();
    
    /**
     * Stores the defaulte values.
     */
    public $aDefault         = array();
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* array $aData, array $aDefault */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aData,
            $this->aDefault,
        );
        $this->aData    = $_aParameters[ 0 ];
        $this->aDefault = $_aParameters[ 1 ];
        
    }
    
    /**
     * Returns the specified option value.
     * 
     * @since       3.6.0
     */
    public function get( /* $sKey1, $sKey2, $sKey3, ... OR $aKeys, $vDefault */ ) {
        
        $_mDefault  = null;
        $_aKeys     = func_get_args() + array( null );
        
        // If no key is specified, return the whole array.
        if ( ! isset( $_aKeys[ 0 ] ) ) {
            return $this->uniteArrays(
                $this->aData,
                $this->aDefault
            );
        }
        
        // If the first parameter is a dimensional array, the second parameter is the default value.
        if ( is_array( $_aKeys[ 0 ] ) ) {
            $_aKeys     = $_aKeys[ 0 ];
            $_mDefault  = $this->getElement( $_aKeys, 1 );
        }
        
        // Now either the section ID or field ID is given. 
        return $this->getArrayValueByArrayKeys( 
            $this->aData,   // subject array
            $_aKeys,        // dimensional keys
            $this->getDefaultValue( // default value
                $_mDefault, 
                $_aKeys 
            )
        );
        
    }
        /**
         * @since       3.6.0
         */
        private function _getDefaultValue( $_mDefault, $_aKeys ) {
            return isset( $_mDefault )
                ? $_mDefault
                : $this->getArrayValueByArrayKeys( 
                    $this->aDefault,
                    $_aKeys
                );
        }
    
    /**
     * Sets an value by specified dimensional keys.
     * 
     * @since       3.6.0
     * @return      void
     */
    public function set( /* $asKeys, $mValue */ ) {
        
        $_aParameters   = func_get_args();
        if ( ! isset( $_aParameters[ 0 ], $_aParameters[ 1 ] ) ) {
            return;
        }
        $_asKeys        = $_aParameters[ 0 ];
        $_mValue        = $_aParameters[ 1 ];
        
        // string, integer, float, boolean
        if ( is_scalar( $_asKeys ) ) {
            $this->aData[ $_asKeys ] = $_mValue;
            return;
        }
        
        // the keys are passed as an array
        $this->setMultiDimensionalArray( $this->aData, $_asKeys, $_mValue );

    }
    
    /**
     * Removes an element by dimensional keys.
     * @since       3.6.0
     * @return      void
     */
    public function delete( /* $sKey1, $sKey2, $sKey3 ... OR $aKeys */ ) {
        
        $_aParameters   = func_get_args();
        if ( ! isset( $_aParameters[ 0 ], $_aParameters[ 1 ] ) ) {
            return;
        }
        $_asKeys        = $_aParameters[ 0 ];
        $_mValue        = $_aParameters[ 1 ];
        
        // string, integer, float, boolean
        if ( is_scalar( $_asKeys ) ) {
            $this->aData[ $_asKeys ] = $_mValue;
            return;
        }        
        
        $this->unsetDimensionalArrayElement( $this->aData, $aKeys );
        
    }
           
    /**
     * Prevents the output from getting too long when the object is dumped.
     * 
     * @remark      Called when the object is called as a string.
     * @since       3.6.0
     */   
    public function __toString() {
        return $this->getObjectInfo( $this );           
    }
    
}