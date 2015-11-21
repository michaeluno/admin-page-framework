<?php
abstract class AdminPageFramework_Controller extends AdminPageFramework_View {
    public function setUp() {
    }
    public function addHelpTab($aHelpTab) {
        if (method_exists($this->oHelpPane, '_addHelpTab')) {
            $this->oHelpPane->_addHelpTab($aHelpTab);
        }
    }
    public function enqueueStyles($aSRCs, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyles')) {
            return $this->oResource->_enqueueStyles($aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueStyle($sSRC, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyle')) {
            return $this->oResource->_enqueueStyle($sSRC, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueScripts($aSRCs, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScripts')) {
            return $this->oResource->_enqueueScripts($aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueScript($sSRC, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScript')) {
            return $this->oResource->_enqueueScript($sSRC, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function addLinkToPluginDescription($sTaggedLinkHTML1, $sTaggedLinkHTML2 = null, $_and_more = null) {
        if (method_exists($this->oLink, '_addLinkToPluginDescription')) {
            $this->oLink->_addLinkToPluginDescription(func_get_args());
        }
    }
    public function addLinkToPluginTitle($sTaggedLinkHTML1, $sTaggedLinkHTML2 = null, $_and_more = null) {
        if (method_exists($this->oLink, '_addLinkToPluginTitle')) {
            $this->oLink->_addLinkToPluginTitle(func_get_args());
        }
    }
    public function setPluginSettingsLinkLabel($sLabel) {
        $this->oProp->sLabelPluginSettingsLink = $sLabel;
    }
    public function setCapability($sCapability) {
        $this->oProp->sCapability = $sCapability;
        if (isset($this->oForm)) {
            $this->oForm->sCapability = $sCapability;
        }
    }
    public function setAdminNotice($sMessage, $sClassSelector = 'error', $sID = '') {
        $sID = $sID ? $sID : md5($sMessage);
        $this->oProp->aAdminNotices[$sID] = array('sMessage' => $sMessage, 'aAttributes' => array('id' => $sID, 'class' => $sClassSelector));
        new AdminPageFramework_AdminNotice($this->oProp->aAdminNotices[$sID]['sMessage'], $this->oProp->aAdminNotices[$sID]['aAttributes'], array('should_show' => array($this, '_isInThePage'),));
    }
    public function setDisallowedQueryKeys($asQueryKeys, $bAppend = true) {
        if (!$bAppend) {
            $this->oProp->aDisallowedQueryKeys = ( array )$asQueryKeys;
            return;
        }
        $aNewQueryKeys = array_merge(( array )$asQueryKeys, $this->oProp->aDisallowedQueryKeys);
        $aNewQueryKeys = array_filter($aNewQueryKeys);
        $aNewQueryKeys = array_unique($aNewQueryKeys);
        $this->oProp->aDisallowedQueryKeys = $aNewQueryKeys;
    }
    static public function getOption($sOptionKey, $asKey = null, $vDefault = null) {
        return AdminPageFramework_WPUtility::getOption($sOptionKey, $asKey, $vDefault);
    }
}