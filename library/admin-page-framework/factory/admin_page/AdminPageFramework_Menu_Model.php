<?php
abstract class AdminPageFramework_Menu_Model extends AdminPageFramework_Page_Controller {
    protected $_aBuiltInRootMenuSlugs = array('dashboard' => 'index.php', 'posts' => 'edit.php', 'media' => 'upload.php', 'links' => 'link-manager.php', 'pages' => 'edit.php?post_type=page', 'comments' => 'edit-comments.php', 'appearance' => 'themes.php', 'plugins' => 'plugins.php', 'users' => 'users.php', 'tools' => 'tools.php', 'settings' => 'options-general.php', 'network admin' => "network_admin_menu",);
    public function _replyToBuildMenu() {
        if ($this->oProp->aRootMenu['fCreateRoot']) {
            $this->_registerRootMenuPage();
        }
        $this->oProp->aPages = $this->oUtil->addAndApplyFilter($this, "pages_{$this->oProp->sClassName}", $this->oProp->aPages);
        uasort($this->oProp->aPages, array($this->oUtil, 'sortArrayByKey'));
        $this->_setDefaultPage();
        foreach ($this->oProp->aPages as & $aSubMenuItem) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuItem($aSubMenuItem, $this);
            $aSubMenuItem = $_oFormatter->get();
            $aSubMenuItem['_page_hook'] = $this->_registerSubMenuItem($aSubMenuItem);
        }
        if ($this->oProp->aRootMenu['fCreateRoot']) {
            remove_submenu_page($this->oProp->aRootMenu['sPageSlug'], $this->oProp->aRootMenu['sPageSlug']);
        }
        $this->oProp->_bBuiltMenu = true;
    }
    private function _setDefaultPage() {
        foreach ($this->oProp->aPages as $_aPage) {
            if (!isset($_aPage['page_slug'])) {
                continue;
            }
            $this->oProp->sDefaultPageSlug = $_aPage['page_slug'];
            return;
        }
    }
    private function _registerRootMenuPage() {
        $this->oProp->aRootMenu['_page_hook'] = add_menu_page($this->oProp->sClassName, $this->oProp->aRootMenu['sTitle'], $this->oProp->sCapability, $this->oProp->aRootMenu['sPageSlug'], '', $this->oProp->aRootMenu['sIcon16x16'], $this->oUtil->getElement($this->oProp->aRootMenu, 'iPosition', null));
    }
    private function _registerSubMenuItem(array $aArgs) {
        if (!current_user_can($aArgs['capability'])) {
            return '';
        }
        $_sRootPageSlug = $this->oProp->aRootMenu['sPageSlug'];
        $_sMenuSlug = plugin_basename($_sRootPageSlug);
        switch ($aArgs['type']) {
            case 'page':
                return $this->_addPageSubmenuItem($_sRootPageSlug, $_sMenuSlug, $aArgs['page_slug'], $this->oUtil->getElement($aArgs, 'page_title', $aArgs['title']), $this->oUtil->getElement($aArgs, 'menu_title', $aArgs['title']), $aArgs['capability'], $aArgs['show_in_menu']);
            case 'link':
                return $this->_addLinkSubmenuItem($_sMenuSlug, $aArgs['title'], $aArgs['capability'], $aArgs['href'], $aArgs['show_in_menu']);
            default:
                return '';
        }
    }
    private function _addPageSubmenuItem($sRootPageSlug, $sMenuSlug, $sPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $bShowInMenu) {
        if (!$sPageSlug) {
            return '';
        }
        $_sPageHook = add_submenu_page($sRootPageSlug, $sPageTitle, $sMenuTitle, $sCapability, $sPageSlug, array($this, $this->oProp->sClassHash . '_page_' . $sPageSlug));
        if (!isset($this->oProp->aPageHooks[$_sPageHook])) {
            add_action('current_screen', array($this, "load_pre_" . $sPageSlug), 20);
        }
        $this->oProp->aPageHooks[$sPageSlug] = $this->oUtil->getAOrB(is_network_admin(), $_sPageHook . '-network', $_sPageHook);
        if ($bShowInMenu) {
            return $_sPageHook;
        }
        $this->_removePageSubmenuItem($sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug);
        return $_sPageHook;
    }
    private function _removePageSubmenuItem($sMenuSlug, $sMenuTitle, $sPageTitle, $sPageSlug) {
        foreach (( array )$GLOBALS['submenu'][$sMenuSlug] as $_iIndex => $_aSubMenu) {
            if (!isset($_aSubMenu[3])) {
                continue;
            }
            $_aA = array($_aSubMenu[0], $_aSubMenu[3], $_aSubMenu[2],);
            $_aB = array($sMenuTitle, $sPageTitle, $sPageSlug,);
            if ($_aA !== $_aB) {
                continue;
            }
            $this->_removePageSubMenuItemByIndex($sPageSlug, $sMenuSlug, $_iIndex);
            $this->oProp->aHiddenPages[$sPageSlug] = $sMenuTitle;
            add_filter('admin_title', array($this, '_replyToFixPageTitleForHiddenPages'), 10, 2);
            break;
        }
    }
    private function _removePageSubMenuItemByIndex($sPageSlug, $sMenuSlug, $_iIndex) {
        if (is_network_admin()) {
            unset($GLOBALS['submenu'][$sMenuSlug][$_iIndex]);
            return;
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && $sPageSlug != $_GET['page']) {
            unset($GLOBALS['submenu'][$sMenuSlug][$_iIndex]);
        }
    }
    private function _addLinkSubmenuItem($sMenuSlug, $sTitle, $sCapability, $sHref, $bShowInMenu) {
        if (!$bShowInMenu) {
            return;
        }
        if (!isset($GLOBALS['submenu'][$sMenuSlug])) {
            $GLOBALS['submenu'][$sMenuSlug] = array();
        }
        $GLOBALS['submenu'][$sMenuSlug][] = array($sTitle, $sCapability, $sHref,);
    }
    public function _replyToFixPageTitleForHiddenPages($sAdminTitle, $sPageTitle) {
        if (isset($_GET['page'], $this->oProp->aHiddenPages[$_GET['page']])) {
            return $this->oProp->aHiddenPages[$_GET['page']] . $sAdminTitle;
        }
        return $sAdminTitle;
    }
}