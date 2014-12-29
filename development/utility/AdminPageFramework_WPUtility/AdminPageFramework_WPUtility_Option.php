<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with the options table which use WordPress functions.
 *
 * @since       3.0.1
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Option extends AdminPageFramework_WPUtility_File {
    
    /**
     * Stores whether the page is loaded in the network admin or not.
     * @since 3.1.3
     */
    static private $_bIsNetworkAdmin;
    
    /**
     * Deletes the given transient.
     *
     * @since 3.1.3
     */
    static public function deleteTransient( $sTransientKey ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? delete_site_transient( $sTransientKey ) : delete_transient( $sTransientKey );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_vTransient;
    }
    /**
     * Retrieves the given transient.
     * 
     * @since   3.1.3
     * @since   3.1.5   Added the $vDefault parameter.
     */    
    static public function getTransient( $sTransientKey, $vDefault=null ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? get_site_transient( $sTransientKey ) : get_transient( $sTransientKey );    

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return null === $vDefault 
            ? $_vTransient
            : ( false === $_vTransient 
                ? $vDefault
                : $_vTransient
            );        
            
    }
    /**
     * Sets the given transient.
     * 
     * @since       3.1.3
     * @return      boolean     True if set; otherwise, false.
     */
    static public function setTransient( $sTransientKey, $vValue, $iExpiration=0 ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();
        
        $_bIsSet = ( self::$_bIsNetworkAdmin ) ? set_site_transient( $sTransientKey, $vValue, $iExpiration ) : set_transient( $sTransientKey, $vValue, $iExpiration );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_bIsSet;     
    }
    
    /**
     * Retrieves the saved option value from the given option key, field ID, and section ID.
     * 
     * @since   3.0.1
     * @since   3.3.0           Added the <var>$aOptions</var> parameter.
     * @param   string          $sOptionKey   the option key of the options table.
     * @param   string|array    $asKey        the field id or the array that represents the key structure consisting of the section ID and the field ID.
     * @param   mixed           $vDefault     the default value that will be returned if nothing is stored.
     * @param   array           $aOptions     an additional options array to merge with the options array. 
     */
    static public function getOption( $sOptionKey, $asKey=null, $vDefault=null, $aOptions=array() ) {
        
        // If only the option key is given, the default value is treated as the entire option data.
        if ( ! $asKey ) {
            return get_option( $sOptionKey, isset( $vDefault ) ? $vDefault : array() );
        }
        
        // Now either the section ID or field ID is given. 
        $_aOptions  = get_option( $sOptionKey, array() );
        $_aKeys     = self::shiftTillTrue( self::getAsArray( $asKey ) );

        return self::getArrayValueByArrayKeys( self::uniteArrays( $_aOptions, $aOptions ), $_aKeys, $vDefault );
        
    }
    
    /**
     * Retrieves the saved option value from the given option key, field ID, and section ID.
     * 
     * @since 3.1.0
     * @param   string          $sOptionKey     the option key of the options table.
     * @param   array|string    $asKey          the field id or the array that represents the key structure consisting of the section ID and the field ID.
     * @param   mixed           $vDefault       the default value that will be returned if nothing is stored.
     * @remark  Used in the network admin area.
     */
    static public function getSiteOption( $sOptionKey, $asKey=null, $vDefault=null ) {

        // If only the option key is given, the default value is treated as the entire option data.
        if ( ! $asKey ) {
            return get_site_option( $sOptionKey, isset( $vDefault ) ? $vDefault : array() );
        }
        
        // Now either the section ID or field ID is given. 
        $_aOptions  = get_site_option( $sOptionKey, array() );
        $_aKeys     = self::shiftTillTrue( self::getAsArray( $asKey ) );

        return self::getArrayValueByArrayKeys( $_aOptions, $_aKeys, $vDefault );
        
    }    
    
}