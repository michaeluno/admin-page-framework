<?php
class AdminPageFramework_MetaBox_Model___PostMeta extends AdminPageFramework_WPUtility {
    public $iPostID = array();
    public $aFieldsets = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->iPostID, $this->aFieldsets,);
        $this->iPostID = $_aParameters[0];
        $this->aFieldsets = $_aParameters[1];
    }
    public function get() {
        if (!$this->iPostID) {
            return array();
        }
        return $this->_getSavedDataFromFieldsets($this->iPostID, $this->aFieldsets);
    }
    private function _getSavedDataFromFieldsets($iPostID, $aFieldsets) {
        $_aMetaKeys = $this->getAsArray(get_post_custom_keys($iPostID));
        $_aMetaData = array();
        foreach ($aFieldsets as $_sSectionID => $_aFieldsets) {
            if ('_default' == $_sSectionID) {
                foreach ($_aFieldsets as $_aFieldset) {
                    if (!in_array($_aFieldset['field_id'], $_aMetaKeys)) {
                        continue;
                    }
                    $_aMetaData[$_aFieldset['field_id']] = get_post_meta($iPostID, $_aFieldset['field_id'], true);
                }
            }
            if (!in_array($_sSectionID, $_aMetaKeys)) {
                continue;
            }
            $_aMetaData[$_sSectionID] = get_post_meta($iPostID, $_sSectionID, true);
        }
        return $_aMetaData;
    }
}