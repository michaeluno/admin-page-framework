<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with post data.
 *
 * @since           3.0.1
 * @since           DEVVER      Renamed from `AdminPageFramework_WPUtility_Post`.
 * @extends         AdminPageFramework_Utility
 * @package         AdminPageFramework
 * @subpackage      Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Meta extends AdminPageFramework_WPUtility_Option {
    
    /**
     * Retrieves the saved option value from the given option key, field ID, and section ID.
     * 
     * @since       3.0.4
     * @since       DEVVER      Rnamed from `getSavedMetaArray`.
     * @return      array       The saved meta data composed with the given keys.
     */
    static public function getSavedPostMetaArray( $iPostID, array $aKeys ) {
        return self::getMetaDataByKeys( $iPostID, $aKeys );
    }
    
    static public function getSavedUserMetaArray( $iUserID, array $aKeys ) {
        return self::getMetaDataByKeys( $iUserID, $aKeys, 'user' );
    }
    
    /**
     * Retrieves meta data by given keys and type (user or post).
     * @return      array
     */
    static public function getMetaDataByKeys( $iObjectID, $aKeys, $sMetaType='post' ) {
               
        $_aSavedMeta = array();
                    
        if ( ! $iObjectID ) {
            return $_aSavedMeta;
        }
        
        $_aFunctionNames = array(
            'post'  => 'get_post_meta',
            'user'  => 'get_user_meta',
        );
        $_sFunctionName = self::getElement( $_aFunctionNames, $sMetaType, 'get_post_meta' );
        
        foreach ( $aKeys as $_sKey ) {
            $_aSavedMeta[ $_sKey ] = call_user_func_array(
                $_sFunctionName,
                array(
                    $iObjectID, 
                    $_sKey, 
                    true                 
                )
            );
        }
        return $_aSavedMeta;        
        
    }

}