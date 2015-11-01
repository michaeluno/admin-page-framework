<?php
abstract class AdminPageFramework_Menu_Controller extends AdminPageFramework_Menu_View {
    public function setRootMenuPage($sRootMenuLabel, $sIcon16x16 = null, $iMenuPosition = null) {
        $sRootMenuLabel = trim($sRootMenuLabel);
        $_sSlug = $this->_isBuiltInMenuItem($sRootMenuLabel);
        $this->oProp->aRootMenu = array('sTitle' => $sRootMenuLabel, 'sPageSlug' => $_sSlug ? $_sSlug : $this->oProp->sClassName, 'sIcon16x16' => $this->oUtil->getResolvedSRC($sIcon16x16), 'iPosition' => $iMenuPosition, 'fCreateRoot' => empty($_sSlug),);
    }
    private function _isBuiltInMenuItem($sMenuLabel) {
        $_sMenuLabelLower = strtolower($sMenuLabel);
        if (array_key_exists($_sMenuLabelLower, $this->_aBuiltInRootMenuSlugs)) return $this->_aBuiltInRootMenuSlugs[$_sMenuLabelLower];
    }
    public function setRootMenuPageBySlug($sRootMenuSlug) {
        $this->oProp->aRootMenu['sPageSlug'] = $sRootMenuSlug;
        $this->oProp->aRootMenu['fCreateRoot'] = false;
    }
    public function addSubMenuItems($aSubMenuItem1, $aSubMenuItem2 = null, $_and_more = null) {
        foreach (func_get_args() as $_aSubMenuItem) {
            $this->addSubMenuItem($_aSubMenuItem);
        }
    }
    public function addSubMenuItem(array $aSubMenuItem) {
        if (isset($aSubMenuItem['href'])) {
            $this->addSubMenuLink($aSubMenuItem);
        } else {
            $this->addSubMenuPage($aSubMenuItem);
        }
    }
    public function addSubMenuLink(array $aSubMenuLink) {
        if (!isset($aSubMenuLink['href'], $aSubMenuLink['title'])) {
            return;
        }
        if (!filter_var($aSubMenuLink['href'], FILTER_VALIDATE_URL)) {
            return;
        }
        $_oFormatter = new AdminPageFramework_Format_SubMenuLink($aSubMenuLink, $this);
        $_aSubMenuLink = $_oFormatter->get();
        $this->oProp->aPages[$_aSubMenuLink['href']] = $_aSubMenuLink;
    }
    public function addSubMenuPages() {
        foreach (func_get_args() as $_aSubMenuPage) {
            $this->addSubMenuPage($_aSubMenuPage);
        }
    }
    public function addSubMenuPage(array $aSubMenuPage) {
        if (!isset($aSubMenuPage['page_slug'])) {
            return;
        }
        $_oFormatter = new AdminPageFramework_Format_SubMenuPage($aSubMenuPage, $this);
        $_aSubMenuPage = $_oFormatter->get();
        $this->oProp->aPages[$_aSubMenuPage['page_slug']] = $_aSubMenuPage;
    }
}