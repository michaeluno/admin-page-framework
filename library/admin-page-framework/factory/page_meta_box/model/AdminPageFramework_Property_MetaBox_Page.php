<?php
class AdminPageFramework_Property_MetaBox_Page extends AdminPageFramework_Property_MetaBox {
    public $_sPropertyType = 'page_meta_box';
    public $aPageSlugs = array();
    public $oAdminPage;
    public $aHelpTabs = array();
    function __construct($oCaller, $sClassName, $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework', $sFieldsType = 'page_meta_box') {
        add_action('admin_menu', array($this, '_replyToSetUpProperties'), 100);
        if (is_network_admin()) {
            add_action('network_admin_menu', array($this, '_replyToSetUpProperties'), 100);
        }
        parent::__construct($oCaller, $sClassName, $sCapability, $sTextDomain, $sFieldsType);
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] = isset($GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses']) && is_array($GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses']) ? $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] : array();
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'][$sClassName] = $oCaller;
    }
    public function _replyToSetUpProperties() {
        if (!isset($_GET['page'])) {
            return;
        }
        $this->oAdminPage = $this->_getOwnerObjectOfPage($_GET['page']);
        if (!$this->oAdminPage) {
            return;
        }
        $this->aHelpTabs = $this->oAdminPage->oProp->aHelpTabs;
        $this->oAdminPage->oProp->bEnableForm = true;
        $this->aOptions = $this->oAdminPage->oProp->aOptions;
    }
    public function _getScreenIDOfPage($sPageSlug) {
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->aPages[$sPageSlug]['_page_hook'] . (is_network_admin() ? '-network' : '') : '';
    }
    public function isPageAdded($sPageSlug = '') {
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->isPageAdded($sPageSlug) : false;
    }
    public function isCurrentTab($sTabSlug) {
        $_sCurrentPageSlug = isset($_GET['page']) ? $_GET['page'] : '';
        if (!$_sCurrentPageSlug) {
            return false;
        }
        $_sCurrentTabSlug = isset($_GET['tab']) ? $_GET['tab'] : $this->getDefaultInPageTab($_sCurrentPageSlug);
        return ($sTabSlug == $_sCurrentTabSlug);
    }
    public function getCurrentPageSlug() {
        return isset($_GET['page']) ? $_GET['page'] : '';
    }
    public function getCurrentTabSlug($sPageSlug) {
        $_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug);
        return $_oAdminPage->oProp->getCurrentTabSlug($sPageSlug);
    }
    public function getCurretTab($sPageSlug) {
        return $this->getCurrentTabSlug($sPageSlug);
    }
    public function getDefaultInPageTab($sPageSlug) {
        if (!$sPageSlug) {
            return '';
        }
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->getDefaultInPageTab($sPageSlug) : '';
    }
    public function getOptionKey($sPageSlug) {
        if (!$sPageSlug) {
            return '';
        }
        return ($_oAdminPage = $this->_getOwnerObjectOfPage($sPageSlug)) ? $_oAdminPage->oProp->sOptionKey : '';
    }
    private function _getOwnerObjectOfPage($sPageSlug) {
        if (!isset($GLOBALS['aAdminPageFramework']['aPageClasses'])) {
            return null;
        }
        if (!is_array($GLOBALS['aAdminPageFramework']['aPageClasses'])) {
            return null;
        }
        foreach ($GLOBALS['aAdminPageFramework']['aPageClasses'] as $__oClass) {
            if ($__oClass->oProp->isPageAdded($sPageSlug)) {
                return $__oClass;
            }
        }
        return null;
    }
}