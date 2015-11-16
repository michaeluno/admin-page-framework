<?php
abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model {
    public function __construct($oProp) {
        parent::__construct($oProp);
        if (!$this->_isInThePage()) {
            return;
        }
        if ($this->oProp->bIsAdminAjax) {
            return;
        }
        if (is_network_admin()) {
            add_action('network_admin_notices', array($this, '_replyToPrintSettingNotice'));
        } else {
            add_action('admin_notices', array($this, '_replyToPrintSettingNotice'));
        }
    }
    public function _replyToGetSectionName() {
        $_aParams = func_get_args() + array(null, null,);
        return $_aParams[0];
    }
    public function _replyToGetInputID() {
        $_aParams = func_get_args() + array(null, null, null, null);
        return $_aParams[0];
    }
    public function _replyToGetInputTagIDAttribute() {
        $_aParams = func_get_args() + array(null, null, null, null);
        return $_aParams[0];
    }
    public function _replyToGetFieldNameAttribute() {
        $_aParams = func_get_args() + array(null, null,);
        return $_aParams[0];
    }
    public function _replyToGetFlatFieldName() {
        $_aParams = func_get_args() + array(null, null,);
        return $_aParams[0];
    }
    public function _replyToGetInputNameAttribute() {
        $_aParams = func_get_args() + array(null, null, null);
        return $_aParams[0];
    }
    public function _replyToGetFlatInputName() {
        $_aParams = func_get_args() + array(null, null, null);
        return $_aParams[0];
    }
    public function _replyToGetInputClassAttribute() {
        $_aParams = func_get_args() + array(null, null, null, null);
        return $_aParams[0];
    }
    public function _replyToDetermineSectionsetVisibility($bVisible, $aSectionset) {
        return $this->_isElementVisible($aSectionset, $bVisible);
    }
    public function _replyToDetermineFieldsetVisibility($bVisible, $aFieldset) {
        return $this->_isElementVisible($aFieldset, $bVisible);
    }
    private function _isElementVisible($aElementDefinition, $bDefault) {
        $aElementDefinition = $aElementDefinition + array('if' => true, 'capability' => '',);
        if (!$aElementDefinition['if']) {
            return false;
        }
        if (!$aElementDefinition['capability']) {
            return true;
        }
        if (!current_user_can($aElementDefinition['capability'])) {
            return false;
        }
        return $bDefault;
    }
    public function isSectionSet(array $aFieldset) {
        $aFieldset = $aFieldset + array('section_id' => null,);
        return $aFieldset['section_id'] && '_default' !== $aFieldset['section_id'];
    }
    static private $_bSettingNoticeLoaded = false;
    public function _replyToPrintSettingNotice() {
        if (!$this->_isInThePage()) {
            return;
        }
        if (self::$_bSettingNoticeLoaded) {
            return;
        }
        self::$_bSettingNoticeLoaded = true;
        $_iUserID = get_current_user_id();
        $_aNotices = $this->oUtil->getTransient("apf_notices_{$_iUserID}");
        if (false === $_aNotices) {
            return;
        }
        $this->oUtil->deleteTransient("apf_notices_{$_iUserID}");
        if (isset($_GET['settings-notice']) && !$_GET['settings-notice']) {
            return;
        }
        $this->_printSettingNotices($_aNotices);
    }
    private function _printSettingNotices($aNotices) {
        $_aPeventDuplicates = array();
        foreach (array_filter(( array )$aNotices, 'is_array') as $_aNotice) {
            $_sNotificationKey = md5(serialize($_aNotice));
            if (isset($_aPeventDuplicates[$_sNotificationKey])) {
                continue;
            }
            $_aPeventDuplicates[$_sNotificationKey] = true;
            echo $this->_getSettingNotice($_aNotice);
        }
    }
    private function _getSettingNotice(array $aNotice) {
        if (!isset($aNotice['aAttributes'], $aNotice['sMessage'])) {
            return '';
        }
        if (!$aNotice['sMessage']) {
            return '';
        }
        $aNotice['aAttributes']['class'] = $this->oUtil->getClassAttribute($this->oUtil->getElement($aNotice, array('aAttributes', 'class'), ''), 'admin-page-framework-settings-notice-container', 'notice is-dismissible');
        return "<div " . $this->oUtil->getAttributes($aNotice['aAttributes']) . ">" . "<p class='admin-page-framework-settings-notice-message'>" . $aNotice['sMessage'] . "</p>" . "</div>";
    }
    public function _replyToGetSectionHeaderOutput($sSectionDescription, $aSectionset) {
        return $this->oUtil->addAndApplyFilters($this, array('section_head_' . $this->oProp->sClassName . '_' . $aSectionset['section_id']), $sSectionDescription);
    }
    public function _replyToGetFieldOutput($sFieldOutput, $aFieldset) {
        $_sSectionPart = $this->oUtil->getAOrB(isset($aFieldset['section_id']) && '_default' !== $aFieldset['section_id'], '_' . $aFieldset['section_id'], '');
        return $this->oUtil->addAndApplyFilters($this, array('field_' . $this->oProp->sClassName . $_sSectionPart . '_' . $aFieldset['field_id']), $sFieldOutput, $aFieldset);
    }
}