<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to test class methods including protected and private ones.
 * 
 * This is for test cases.
 * 
 * <h2>Usage</h2>
 * Retrieve a class instance with the `getInstance()` method. And with it call methods to test using the `call()` method.
 * 
 * <h2>Example</h2>
 * <code>
 *  $_oClass = AdminPageFramework_ClassTester::getInstance( 'One' );
 *  $_mValue = AdminPageFramework_ClassTester::call( 
 *      $_oClass,           // subject class object
 *      '_getString',       // method name (private/protected supported)
 *      array( 'aaa' )      // method parameters
 *  );
 *  var_dump( $_mValue );
 *  
 *  class One {
 *  
 *      private function _getString( $sString ) {
 *          return $sString;
 *      }    
 *      
 *  }
 * </code>
 * 
 * @remark      Requires PHP 5.3.0 or above.
 * @since       3.7.10
 * @package     AdminPageFramework/Utility
 */
class AdminPageFramework_ClassTester {
        
    /**
     * Creates an object instance with dynamic parameters.
     * 
     * @since       3.7.10
     * @param       string      $sCalssName     The class name for testing.
     * @param       array       $aParameters    The parameters to pass to the constructor of the class set in the first parameter.
     * @return      object      An object instance of the class specified in the first parameter.
     */
    static public function getInstance( $sClassName, array $aParameters=array() ) {
        
        $_oReflection = new ReflectionClass( $sClassName );
        return $_oReflection->newInstanceArgs( $aParameters );               
        
    }
    
    /**
     * Performs the method specified in the second parameter.
     * 
     * @since       3.7.10
     * @param       object      $oClass         The subject class object.
     * @param       string      $sMathodName    The subject method name.
     * @param       array       $aParameters    The parameters to pass to the method set in the second parameter.    
     * @remark      This supports private methods to be executed.
     */
    static public function call( $oClass, $sMethodName, $aParameters ) {
        
        // For PHP 5.2.x or below
        if ( version_compare( phpversion(), '<', '5.3.0' ) ) {
            trigger_error(
                'Program Name' . ': ' 
                    . sprintf( 
                        'The method cannot run with your PHP version: %1$s',
                        phpversion()
                    ), 
                E_USER_WARNING
            );                        
            return;
        }                
        
        $_sClassName = get_class( $oClass );
        $_oMethod    = self::_getMethod( $_sClassName, $sMethodName );
        return $_oMethod->invokeArgs( $oClass, $aParameters );
        
    }
        /**
         * @since       3.7.10
         * @return      object
         * @internal
         */
        static private function _getMethod( $sClassName, $sMethodName ) {
            
            $_oClass  = new ReflectionClass( $sClassName );
            $_oMethod = $_oClass->getMethod( $sMethodName );
            $_oMethod->setAccessible( true );
            return $_oMethod;
            
        }
       
}
