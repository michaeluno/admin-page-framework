<?php
abstract class AdminPageFramework_Model_Form extends AdminPageFramework_Router {
    public $aFieldErrors;
    static protected $_sStructureType = 'admin_page';
    protected $_sTargetPageSlug = null;
    protected $_sTargetTabSlug = null;
    protected $_sTargetSectionTabSlug = null;
    public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework') {
        parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
        if ($this->oProp->bIsAdminAjax) {
            return;
        }
        if (!$this->oProp->bIsAdmin) {
            return;
        }
        new AdminPageFramework_Model__FormEmailHandler($this);
        if (isset($_REQUEST['apf_remote_request_test']) && '_testing' === $_REQUEST['apf_remote_request_test']) {
            exit('OK');
        }
    }
    public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets) {
        new AdminPageFramework_Model__FormSubmission($this, $aSavedData, $aArguments, $aSectionsets, $aFieldsets);
    }
    public function _replyToFieldsetReourceRegistration($aFieldset) {
        $aFieldset = $aFieldset + array('help' => null, 'title' => null, 'help_aside' => null, 'page_slug' => null, 'tab_slug' => null, 'section_title' => null, 'section_id' => null,);
        if (!$aFieldset['help']) {
            return;
        }
        $this->addHelpTab(array('page_slug' => $aFieldset['page_slug'], 'page_tab_slug' => $aFieldset['tab_slug'], 'help_tab_title' => $aFieldset['section_title'], 'help_tab_id' => $aFieldset['section_id'], 'help_tab_content' => "<span class='contextual-help-tab-title'>" . $aFieldset['title'] . "</span> - " . PHP_EOL . $aFieldset['help'], 'help_tab_sidebar_content' => $aFieldset['help_aside'] ? $aFieldset['help_aside'] : "",));
    }
    public function _replyToModifySectionsets($aSectionsets) {
        $this->_registerHelpPaneItemsOfFormSections($aSectionsets);
        return parent::_replyToModifySectionsets($aSectionsets);
    }
    public function _registerHelpPaneItemsOfFormSections($aSectionsets) {
        foreach ($aSectionsets as $_aSectionset) {
            $_aSectionset = $_aSectionset + array('help' => null, 'page_slug' => null, 'tab_slug' => null, 'title' => null, 'section_id' => null, 'help' => null, 'help_aside' => null,);
            if (empty($_aSectionset['help'])) {
                continue;
            }
            $this->addHelpTab(array('page_slug' => $_aSectionset['page_slug'], 'page_tab_slug' => $_aSectionset['tab_slug'], 'help_tab_title' => $_aSectionset['title'], 'help_tab_id' => $_aSectionset['section_id'], 'help_tab_content' => $_aSectionset['help'], 'help_tab_sidebar_content' => $this->getElement($_aSectionset, 'help_aside', ''),));
        }
    }
    public function _replyToDetermineSectionsetVisibility($bVisible, $aSectionset) {
        if (!current_user_can($aSectionset['capability'])) {
            return false;
        }
        if (!$aSectionset['if']) {
            return false;
        }
        if (!$this->_isSectionOfCurrentPage($aSectionset)) {
            return false;
        }
        return $bVisible;
    }
    private function _isSectionOfCurrentPage(array $aSectionset) {
        $_sCurrentPageSlug = ( string )$this->oProp->getCurrentPageSlug();
        if ($aSectionset['page_slug'] !== $_sCurrentPageSlug) {
            return false;
        }
        return ($aSectionset['tab_slug'] === $this->oProp->getCurrentTabSlug($_sCurrentPageSlug));
    }
    public function _replyToDetermineFieldsetVisibility($bVisible, $aFieldset) {
        $_sCurrentPageSlug = $this->oProp->getCurrentPageSlug();
        if ($aFieldset['page_slug'] !== $_sCurrentPageSlug) {
            return false;
        }
        return parent::_replyToDetermineFieldsetVisibility($bVisible, $aFieldset);
    }
    public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets) {
        if (empty($aFieldset)) {
            return $aFieldset;
        }
        $_sSectionID = $aFieldset['section_id'];
        $aFieldset['option_key'] = $this->oProp->sOptionKey;
        $aFieldset['class_name'] = $this->oProp->sClassName;
        $aFieldset['page_slug'] = $this->oUtil->getElement($aSectionsets, array($_sSectionID, 'page_slug'), null);
        $aFieldset['tab_slug'] = $this->oUtil->getElement($aSectionsets, array($_sSectionID, 'tab_slug'), null);
        $_aSectionset = $this->oUtil->getElementAsArray($aSectionsets, $_sSectionID);
        $aFieldset['section_title'] = $this->oUtil->getElement($_aSectionset, 'title');
        $aFieldset['capability'] = $aFieldset['capability'] ? $aFieldset['capability'] : $this->_replyToGetCapabilityForForm($this->oUtil->getElement($_aSectionset, 'capability'), $aSectionset['page_slug'], $aSectionset['tab_slug']);
        return parent::_replyToFormatFieldsetDefinition($aFieldset, $aSectionsets);
    }
    public function _replyToFormatSectionsetDefinition($aSectionset) {
        if (empty($aSectionset)) {
            return $aSectionset;
        }
        $aSectionset = $aSectionset + array('page_slug' => null, 'tab_slug' => null, 'capability' => null,);
        $aSectionset['page_slug'] = $aSectionset['page_slug'] ? $aSectionset['page_slug'] : $this->oProp->sDefaultPageSlug;
        $aSectionset['capability'] = $this->_replyToGetCapabilityForForm($aSectionset['capability'], $aSectionset['page_slug'], $aSectionset['tab_slug']);
        return parent::_replyToFormatSectionsetDefinition($aSectionset);
    }
    public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed) {
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        return $this->oProp->isPageAdded($_sPageSlug);
    }
    public function _replyToGetCapabilityForForm($sCapability) {
        $_aParameters = func_get_args() + array('', '', '');
        $_sPageSlug = $this->oUtil->getAOrB($_aParameters[1], $_aParameters[1], $this->oProp->getCurrentPageSlug());
        $_sTabSlug = $this->oUtil->getAOrB($_aParameters[2], $_aParameters[2], $this->oProp->getCurrentTabSlug($_sPageSlug));
        $_sTabCapability = $this->_getInPageTabCapability($_sTabSlug, $_sPageSlug);
        $_sPageCapability = $this->_getPageCapability($_sPageSlug);
        $_aCapabilities = array_filter(array($_sTabCapability, $_sPageCapability)) + array($this->oProp->sCapability);
        return $_aCapabilities[0];
    }
}