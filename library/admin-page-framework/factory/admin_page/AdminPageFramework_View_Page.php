<?php
abstract class AdminPageFramework_View_Page extends AdminPageFramework_Model_Page {
    public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework') {
        parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
        if ($this->oProp->bIsAdminAjax) {
            return;
        }
        new AdminPageFramework_View_PageMetaboxEnabler($this);
    }
    public function _replyToEnqueuePageAssets() {
        new AdminPageFramework_View_Resource($this);
    }
    protected function _renderPage($sPageSlug, $sTabSlug = null) {
        $_oPageRenderer = new AdminPageFramework_View_PageRenderer($this, $sPageSlug, $sTabSlug);
        $_oPageRenderer->render();
    }
}