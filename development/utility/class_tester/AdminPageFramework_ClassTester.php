<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to test class methods including protected and private ones.
 * 
 * This is meant to be used in test cases.
 * 
 * Usage:
 * `
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
 * `
 * 
 * @remark      Requires PHP 5.3.0 or above.
 * @since       3.7.10
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal    
 */
class AdminPageFramework_ClassTester {
        
    /**
     * Creates an object instance with dynamic parameters.
     */
    static public function getInstance( $sClassName, array $aParameters=array() ) {
        
        $_oReflection = new ReflectionClass( $sClassName );
        return $_oReflection->newInstanceArgs( $aParameters );               
        
    }
    
    /**
     * 
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
         * @return      object
         */
        static private function _getMethod( $sClassName, $sMethodName ) {
            
            $_oClass  = new ReflectionClass( $sClassName );
            $_oMethod = $_oClass->getMethod( $sMethodName );
            $_oMethod->setAccessible( true );
            return $_oMethod;
            
        }
       
}
