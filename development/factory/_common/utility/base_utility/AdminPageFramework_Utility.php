<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       2.0.0
 * @since       3.7.3       Became not abstract for the xdebug max nesting level fatal error workaround.
 * @extends     AdminPageFramework_Utility_SystemInformation
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
class AdminPageFramework_Utility extends AdminPageFramework_Utility_HTMLAttribute {
       
    /**
     * Stores calls.
     */
    static private $_aCallStack = array();
    
    /**
     * Checks if the given id (usually a function name) has been called throughout the page load.
     * 
     * This is used to check if a function which needs to be done only once has been already called or not.
     * 
     * @since       3.7.0
     * @return      boolean
     */
    static public function hasBeenCalled( $sID ) {
        if ( isset( self::$_aCallStack[ $sID ] ) ) {
            return true;
        }
        self::$_aCallStack[ $sID ] = true;
        return false;
    }
       
    /**
     * Captures the output buffer of the given function.
     * @since       3.6.3
     * @return      string      The captured output buffer.
     */
    static public function getOutputBuffer( $oCallable, array $aParameters=array() ) {
        
        ob_start(); 
        echo call_user_func_array( $oCallable, $aParameters );
        $_sContent = ob_get_contents(); 
        ob_end_clean(); 
        return $_sContent;        
        
    }
                  
    /**
     * Generates brief object information.
     * 
     * @remark      Meant to be used for the `__toString()` method.
     * @since       3.6.0
     * @return      string
     */   
    static public function getObjectInfo( $oInstance ) {
        
        $_iCount     = count( get_object_vars( $oInstance ) );
        $_sClassName = get_class( $oInstance );
        return '(object) ' . $_sClassName . ': ' . $_iCount . ' properties.';
        
    }
                   
    
    /**
     * Returns one or the other.
     * 
     * Saves one conditional statement.
     * 
     * @remark      Use this only when the performance is not critical.
     * @since       3.5.3
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mValue     The value to evaluate.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mTrue      The value to return when the first parameter value yields true.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mTrue      The value to return when the first parameter value yields false.
     * @return      mixed
     */
    static public function getAOrB( $mValue, $mTrue=null, $mFalse=null ) {
        return $mValue ? $mTrue : $mFalse;
    }    
    
}