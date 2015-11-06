<?php
abstract class AdminPageFramework_Controller_Page extends AdminPageFramework_View_Page {
    public function addInPageTabs() {
        foreach (func_get_args() as $asTab) {
            $this->addInPageTab($asTab);
        }
    }
    public function addInPageTab($asInPageTab) {
        static $__sTargetPageSlug;
        if (!is_array($asInPageTab)) {
            $__sTargetPageSlug = is_string($asInPageTab) ? $asInPageTab : $__sTargetPageSlug;
            return;
        }
        $aInPageTab = $asInPageTab + array('page_slug' => $__sTargetPageSlug, 'tab_slug' => null, 'order' => null,);
        $__sTargetPageSlug = $aInPageTab['page_slug'];
        if (!isset($aInPageTab['page_slug'], $aInPageTab['tab_slug'])) {
            return;
        }
        $_aElements = $this->oUtil->getElement($this->oProp->aInPageTabs, $aInPageTab['page_slug'], array());
        $_iCountElement = count($_aElements);
        $aInPageTab = array('page_slug' => $this->oUtil->sanitizeSlug($aInPageTab['page_slug']), 'tab_slug' => $this->oUtil->sanitizeSlug($aInPageTab['tab_slug']), 'order' => $this->oUtil->getAOrB(is_numeric($aInPageTab['order']), $aInPageTab['order'], $_iCountElement + 10),) + $aInPageTab;
        $this->oProp->aInPageTabs[$aInPageTab['page_slug']][$aInPageTab['tab_slug']] = $aInPageTab;
    }
    public function setPageTitleVisibility($bShow = true, $sPageSlug = '') {
        $this->_setPageProperty('bShowPageTitle', 'show_page_title', $bShow, $sPageSlug);
    }
    public function setPageHeadingTabsVisibility($bShow = true, $sPageSlug = '') {
        $this->_setPageProperty('bShowPageHeadingTabs', 'show_page_heading_tabs', $bShow, $sPageSlug);
    }
    public function setInPageTabsVisibility($bShow = true, $sPageSlug = '') {
        $this->_setPageProperty('bShowInPageTabs', 'show_in_page_tabs', $bShow, $sPageSlug);
    }
    public function setInPageTabTag($sTag = 'h3', $sPageSlug = '') {
        $this->_setPageProperty('sInPageTabTag', 'in_page_tab_tag', $sTag, $sPageSlug);
    }
    public function setPageHeadingTabTag($sTag = 'h2', $sPageSlug = '') {
        $this->_setPageProperty('sPageHeadingTabTag', 'page_heading_tab_tag', $sTag, $sPageSlug);
    }
    private function _setPageProperty($sPropertyName, $sPropertyKey, $mValue, $sPageSlug) {
        $sPageSlug = $this->oUtil->sanitizeSlug($sPageSlug);
        if ($sPageSlug) {
            $this->oProp->aPages[$sPageSlug][$sPropertyKey] = $mValue;
            return;
        }
        $this->oProp->{$sPropertyName} = $mValue;
        foreach ($this->oProp->aPages as & $_aPage) {
            $_aPage[$sPropertyKey] = $mValue;
        }
    }
}