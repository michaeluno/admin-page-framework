<?php
class AdminPageFramework_WPUtility_Post extends AdminPageFramework_WPUtility_Option {
    static public function getSavedMetaArray($iPostID, array $aKeys) {
        $_aSavedMeta = array();
        foreach ($aKeys as $_sKey) {
            $_aSavedMeta[$_sKey] = get_post_meta($iPostID, $_sKey, true);
        }
        return $_aSavedMeta;
    }
}