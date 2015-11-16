<?php
class AdminPageFramework_WPUtility_Meta extends AdminPageFramework_WPUtility_Option {
    static public function getSavedPostMetaArray($iPostID, array $aKeys) {
        return self::getMetaDataByKeys($iPostID, $aKeys);
    }
    static public function getSavedUserMetaArray($iUserID, array $aKeys) {
        return self::getMetaDataByKeys($iUserID, $aKeys, 'user');
    }
    static public function getMetaDataByKeys($iObjectID, $aKeys, $sMetaType = 'post') {
        $_aSavedMeta = array();
        if (!$iObjectID) {
            return $_aSavedMeta;
        }
        $_aFunctionNames = array('post' => 'get_post_meta', 'user' => 'get_user_meta',);
        $_sFunctionName = self::getElement($_aFunctionNames, $sMetaType, 'get_post_meta');
        foreach ($aKeys as $_sKey) {
            $_aSavedMeta[$_sKey] = call_user_func_array($_sFunctionName, array($iObjectID, $_sKey, true));
        }
        return $_aSavedMeta;
    }
}