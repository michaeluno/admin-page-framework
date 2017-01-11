<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to help handle array contents.
 * 
 * This is useful when you edit multi-dimensional arrays.
 * 
 * <h2>Usage</h2>
 * Instantiate the class by passing an array to manipulate. 
 * Then use the `set()`, `get()`, and `delete()` methods to retrieve/modify the contents.
 * 
 * <h2>Example</h2>
 * <code>
 * $_aArray = array(
 *      'a' => array(
 *          'a_i' => array(
 *              'a_i_x' => array(
 *                  'foo',
 *                  'bar',
 *              ),
 *          ),
 *      )
 * );
 * $_oData  = new AdminPageFramework_ArrayHandler( $_aArray );
 * $_oData->set( array( 'a', 'a_i', 'a_i_y' ), 'A New Value' );
 * var_dump( $_oData->get( 'a', 'a_i' ) );
 * $_oData->delete( array( 'a', 'a_i' ) );
 * var_dump( $_oData->get( 'a' ) );
 * 
 * </code>
 * 
 * @package     AdminPageFramework/Common/Utility
 * @since       3.6.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_ArrayHandler extends AdminPageFramework_FrameworkUtility {
       
    /**#@+
     * @internal
     */
    /**
     * Stores field definitions.
     * 
     * @since       3.6.0
     * @var         array
     */
    public $aData            = array();
    
    /**
     * Stores the default values.
     * @since       3.6.0
     * @var         array
     */
    public $aDefault         = array();
    /**#@-*/
    
    /**
     * Sets up properties.
     * 
     * @since       3.6.0
     * @param       array       A subject array.
     * @param       array       An array holding default values.
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
     * @param       string|array        $sKey1|$aKeys       The dimensional key or keys of an array of the element to extract its value.
     * If a string is set to the first parameter, the second parameter is also expected to be a string serving as a second dimensional key. 
     * For example, there is a multi-dimensional array, `array( 'a' => array( 'b' => 'bbb' ) )` to extract the value of the 'b' element in the second depth,
     * set the parameter as follows. `get( 'a', 'b' )` or `get( array( 'a', 'b' ) );`
     * @param       string|variant      $sKey2|$vDefault    The second dimensional key or the default value in case the value is not set.
     * @param       string              $sKey3              The third dimensional key and so on.
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
            $this->_getDefaultValue( // default value
                $_mDefault, 
                $_aKeys 
            )
        );
        
    }
        /**
         * @since       3.6.0
         * @internal
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
     * @param       string|array        $asKeys       The key or keys of an array of the element to set its value.
     * If a string is passed, it specifies the element with the set key. If an array is passed, it specifies the element with the dimensional keys.
     * For example, `set( 'a', 'aaa' )` will set a value, `array( 'a' => 'aaa' )` and `set( array( 'a', 'b' ), 'bbb' )` will set `array( 'a' => array( 'b' => 'bbb' ) )`.
     * @param       variant             $mValue       The value to be set.
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
     * @param       string|array        $asKeys       The key or keys of an array of the element to set its value.
     * If a string is passed, it specifies the element with the set key. If an array is passed, it specifies the element with the dimensional keys.
     * For example, `delete( 'a' )` will unset an element of `a` in `array( 'a' => 'some value' )` so it becomes `array()`.
     * `delete( array( 'a', 'b' ) )` will unset the element of `b` in `array( 'a' => array( 'b' => 'bbb' ) )`.
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
     * @internal
     */   
    public function __toString() {
        return $this->getObjectInfo( $this );           
    }
    
}
