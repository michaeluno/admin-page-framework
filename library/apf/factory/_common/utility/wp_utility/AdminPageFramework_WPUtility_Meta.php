<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_Meta extends AdminPageFramework_WPUtility_Option {
    public static function getSavedPostMetaArray($iPostID, array $aKeys)
    {
        return self::getMetaDataByKeys($iPostID, $aKeys);
    }
    public static function getSavedUserMetaArray($iUserID, array $aKeys)
    {
        return self::getMetaDataByKeys($iUserID, $aKeys, 'user');
    }
    public static function getSavedTermMetaArray($iTermID, array $aKeys)
    {
        return self::getMetaDataByKeys($iTermID, $aKeys, 'term');
    }
    public static function getMetaDataByKeys($iObjectID, $aKeys, $sMetaType='post')
    {
        $_aSavedMeta = array();
        if (! $iObjectID) {
            return $_aSavedMeta;
        }
        $_aFunctionNames = array( 'post' => 'get_post_meta', 'user' => 'get_user_meta', 'term' => 'get_term_meta', );
        $_sFunctionName = self::getElement($_aFunctionNames, $sMetaType, 'get_post_meta');
        foreach ($aKeys as $_sKey) {
            $_aSavedMeta[ $_sKey ] = call_user_func_array($_sFunctionName, array( $iObjectID, $_sKey, true ));
        }
        return $_aSavedMeta;
    }
}
