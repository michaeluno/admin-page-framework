<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Router extends AdminPageFramework_Factory {
    public $oProp;
    public $oForm;
    public function __construct($sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework')
    {
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, $sCallerPath, get_class($this), $sOptionKey, $sCapability, $sTextDomain);
        parent::__construct($this->oProp);
        if (! $this->oProp->bIsAdmin) {
            return;
        }
        add_action('wp_loaded', array( $this, '_replyToDetermineToLoad' ));
        add_action('set_up_' . $this->oProp->sClassName, array( $this, '_replyToLoadComponentsForAjax' ), 100);
    }
    public function _replyToLoadComponentsForAjax()
    {
        if (! $this->oProp->bIsAdminAjax) {
            return;
        }
        new AdminPageFramework_Model_Menu__RegisterMenu($this, 'pseudo_admin_menu');
        do_action('pseudo_admin_menu', '');
        do_action('pseudo_current_screen');
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        if ($this->oProp->isPageAdded($_sPageSlug)) {
            do_action("pseudo_current_screen_{$_sPageSlug}");
        }
    }
    protected function _getLinkObject()
    {
        $_sClassName = $this->aSubClassNames[ 'oLink' ];
        return new $_sClassName($this->oProp, $this->oMsg);
    }
    protected function _getPageLoadObject()
    {
        $_sClassName = $this->aSubClassNames[ 'oPageLoadInfo' ];
        return new $_sClassName($this->oProp, $this->oMsg);
    }
    public function __call($sMethodName, $aArgs=null)
    {
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $_sTabSlug = $this->oProp->getCurrentTabSlug($_sPageSlug);
        $_mFirstArg = $this->oUtil->getElement($aArgs, 0);
        $_aKnownMethodPrefixes = array( 'section_pre_', 'field_pre_', 'load_pre_', );
        switch ($this->_getCallbackName($sMethodName, $_sPageSlug, $_aKnownMethodPrefixes)) { case 'section_pre_': return $this->_renderSectionDescription($sMethodName); case 'field_pre_': return $this->_renderSettingField($_mFirstArg, $_sPageSlug); case 'load_pre_': return $this->_doPageLoadCall($sMethodName, $_sPageSlug, $_sTabSlug, $_mFirstArg); default: return parent::__call($sMethodName, $aArgs); }
    }
    private function _getCallbackName($sMethodName, $sPageSlug, array $aKnownMethodPrefixes=array())
    {
        foreach ($aKnownMethodPrefixes as $_sMethodPrefix) {
            if ($this->oUtil->hasPrefix($_sMethodPrefix, $sMethodName)) {
                return $_sMethodPrefix;
            }
        }
        return '';
    }
    protected function _doPageLoadCall($sMethodName, $sPageSlug, $sTabSlug, $oScreen)
    {
        if (! $this->_isPageLoadCall($sMethodName, $sPageSlug, $oScreen)) {
            return;
        }
        $this->___setPageAndTabSlugsForForm($sPageSlug, $sTabSlug);
        $this->_setShowDebugInfoProperty($sPageSlug);
        $this->_load(array( "load_{$this->oProp->sClassName}", "load_{$sPageSlug}", ));
        $sTabSlug = $this->oProp->getCurrentTabSlug($sPageSlug);
        if (strlen($sTabSlug)) {
            $this->_setShowDebugInfoProperty($sPageSlug, $sTabSlug);
            $this->oUtil->addAndDoActions($this, array( "load_{$sPageSlug}_" . $sTabSlug ), $this);
            add_filter('admin_title', array( $this, '_replyToSetAdminPageTitleForTab' ), 1, 2);
        }
        $this->oUtil->addAndDoActions($this, array( "load_after_{$this->oProp->sClassName}", "load_after_{$sPageSlug}", ), $this);
    }
    private function _setShowDebugInfoProperty($sPageSlug, $sTabSlug='')
    {
        if (! strlen($sTabSlug)) {
            $this->oProp->bShowDebugInfo = $this->oUtil->getElement($this->oProp->aPages, array( $sPageSlug, 'show_debug_info' ), $this->oProp->bShowDebugInfo);
            return;
        }
        $this->oProp->bShowDebugInfo = $this->oUtil->getElement($this->oProp->aInPageTabs, array( $sPageSlug, $sTabSlug, 'show_debug_info' ), $this->oProp->bShowDebugInfo);
    }
    private function ___setPageAndTabSlugsForForm($sPageSlug, $sTabSlug)
    {
        $this->oForm->aSectionsets[ '_default' ][ 'page_slug' ] = $sPageSlug ? $sPageSlug : null;
        $this->oForm->aSectionsets[ '_default' ][ 'tab_slug' ] = $sTabSlug ? $sTabSlug : null;
    }
    private function _isPageLoadCall($sMethodName, $sPageSlug, $osScreenORPageHook)
    {
        if (substr($sMethodName, strlen('load_pre_')) !== $sPageSlug) {
            return false;
        }
        if (! isset($this->oProp->aPageHooks[ $sPageSlug ])) {
            return false;
        }
        $_sPageHook = is_object($osScreenORPageHook) ? $osScreenORPageHook->id : $sPageSlug;
        return $_sPageHook === $this->oProp->aPageHooks[ $sPageSlug ];
    }
    protected function _isInstantiatable()
    {
        if ($this->_isWordPressCoreAjaxRequest()) {
            return false;
        }
        return ! is_network_admin();
    }
    protected function _isInThePage()
    {
        if (! $this->oProp->bIsAdmin) {
            return false;
        }
        if (! did_action('set_up_' . $this->oProp->sClassName)) {
            return true;
        }
        return $this->oProp->isPageAdded();
    }
    public function _replyToLoadComponents()
    {
        if ('plugins.php' === $this->oProp->sPageNow) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        parent::_replyToLoadComponents();
    }
}
