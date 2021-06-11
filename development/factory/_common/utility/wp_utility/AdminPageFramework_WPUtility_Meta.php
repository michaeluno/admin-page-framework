<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with post data.
 *
 * @since           3.0.1
 * @since           3.7.0      Renamed from `AdminPageFramework_WPUtility_Post`.
 * @extends         AdminPageFramework_Utility
 * @package         AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Meta extends AdminPageFramework_WPUtility_Option {

    /**
     * Retrieves the saved option value from the given option key, field ID, and section ID.
     *
     * @since       3.0.4
     * @since       3.7.0      Rnamed from `getSavedMetaArray`.
     * @return      array       The saved meta data composed with the given keys.
     */
    static public function getSavedPostMetaArray( $iPostID, array $aKeys ) {
        return self::getMetaDataByKeys( $iPostID, $aKeys );
    }

    static public function getSavedUserMetaArray( $iUserID, array $aKeys ) {
        return self::getMetaDataByKeys( $iUserID, $aKeys, 'user' );
    }

    /**
     * Retrieves the saved term meta value.
     * @since       3.8.0
     */
    static public function getSavedTermMetaArray( $iTermID, array $aKeys ) {
        return self::getMetaDataByKeys( $iTermID, $aKeys, 'term' );
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
            'term'  => 'get_term_meta',
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
