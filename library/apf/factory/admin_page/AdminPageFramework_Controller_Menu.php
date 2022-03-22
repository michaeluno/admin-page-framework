<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Controller_Menu extends AdminPageFramework_View_Menu {
    protected $_aBuiltInRootMenuSlugs = array( 'dashboard' => 'index.php', 'posts' => 'edit.php', 'media' => 'upload.php', 'links' => 'link-manager.php', 'pages' => 'edit.php?post_type=page', 'comments' => 'edit-comments.php', 'appearance' => 'themes.php', 'plugins' => 'plugins.php', 'users' => 'users.php', 'tools' => 'tools.php', 'settings' => 'options-general.php', 'network admin' => 'network_admin_menu', );
    public function setRootMenuPage($sRootMenuLabel, $sIcon16x16=null, $iMenuPosition=null)
    {
        $sRootMenuLabel = trim($sRootMenuLabel);
        $_sSlug = $this->___getBuiltInMenuSlugByLabel($sRootMenuLabel);
        $this->oProp->aRootMenu = array( 'sTitle' => $sRootMenuLabel, 'sPageSlug' => strlen($_sSlug) ? $_sSlug : $this->oProp->sClassName, 'sIcon16x16' => $this->oUtil->getResolvedSRC($sIcon16x16), 'iPosition' => $iMenuPosition, 'fCreateRoot' => empty($_sSlug), );
    }
    private function ___getBuiltInMenuSlugByLabel($sMenuLabel)
    {
        $_sMenuLabelLower = strtolower($sMenuLabel);
        return array_key_exists($_sMenuLabelLower, $this->_aBuiltInRootMenuSlugs) ? $this->_aBuiltInRootMenuSlugs[ $_sMenuLabelLower ] : '';
    }
    public function setRootMenuPageBySlug($sRootMenuSlug)
    {
        $this->oProp->aRootMenu[ 'sPageSlug' ] = $sRootMenuSlug;
        $this->oProp->aRootMenu[ 'fCreateRoot' ] = false;
    }
    public function addSubMenuItems()
    {
        foreach (func_get_args() as $_aSubMenuItem) {
            $this->addSubMenuItem($_aSubMenuItem);
        }
    }
    public function addSubMenuItem(array $aSubMenuItem)
    {
        if (isset($aSubMenuItem[ 'href' ])) {
            $this->addSubMenuLink($aSubMenuItem);
        } else {
            $this->addSubMenuPage($aSubMenuItem);
        }
    }
    public function addSubMenuLink(array $aSubMenuLink)
    {
        if (! isset($aSubMenuLink[ 'href' ], $aSubMenuLink[ 'title' ])) {
            return;
        }
        if (! filter_var($aSubMenuLink[ 'href' ], FILTER_VALIDATE_URL)) {
            return;
        }
        $_oFormatter = new AdminPageFramework_Format_SubMenuLink($aSubMenuLink, $this, count($this->oProp->aPages) + 1);
        $_aSubMenuLink = $_oFormatter->get();
        $this->oProp->aPages[ $_aSubMenuLink[ 'href' ] ] = $_aSubMenuLink;
    }
    public function addSubMenuPages()
    {
        foreach (func_get_args() as $_aSubMenuPage) {
            $this->addSubMenuPage($_aSubMenuPage);
        }
    }
    public function addSubMenuPage(array $aSubMenuPage)
    {
        if (! isset($aSubMenuPage[ 'page_slug' ])) {
            return;
        }
        $_oFormatter = new AdminPageFramework_Format_SubMenuPage($aSubMenuPage, $this, count($this->oProp->aPages) + 1);
        $_aSubMenuPage = $_oFormatter->get();
        $this->oProp->aPages[ $_aSubMenuPage[ 'page_slug' ] ] = $_aSubMenuPage;
    }
}
