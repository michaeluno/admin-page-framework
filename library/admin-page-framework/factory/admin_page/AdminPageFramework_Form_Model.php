<?php
abstract class AdminPageFramework_Form_Model extends AdminPageFramework_Form_Model_Validation {
    public $aFieldErrors;
    static protected $_sFieldsType = 'page';
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
        add_action("load_after_{$this->oProp->sClassName}", array($this, '_replyToRegisterSettings'), 20);
        add_action("load_after_{$this->oProp->sClassName}", array($this, '_replyToCheckRedirects'), 21);
        if (isset($_GET['apf_action'], $_GET['transient']) && 'email' === $_GET['apf_action']) {
            ignore_user_abort(true);
            $this->oUtil->registerAction('plugins_loaded', array($this, '_replyToSendFormEmail'));
        }
        if (isset($_REQUEST['apf_remote_request_test']) && '_testing' === $_REQUEST['apf_remote_request_test']) {
            exit('OK');
        }
    }
    static public $_bDoneEmail = false;
    public function _replyToSendFormEmail() {
        if (self::$_bDoneEmail) {
            return;
        }
        self::$_bDoneEmail = true;
        $_sTransient = $this->oUtil->getElement($_GET, 'transient', '');
        if (!$_sTransient) {
            return;
        }
        $_aFormEmail = $this->oUtil->getTransient($_sTransient);
        $this->oUtil->deleteTransient($_sTransient);
        if (!is_array($_aFormEmail)) {
            return;
        }
        $_oEmail = new AdminPageFramework_FormEmail($_aFormEmail['email_options'], $_aFormEmail['input'], $_aFormEmail['section_id']);
        $_bSent = $_oEmail->send();
        exit;
    }
    public function _replyToCheckRedirects() {
        if (!$this->_isInThePage()) {
            return;
        }
        if (!(isset($_GET['settings-updated']) && !empty($_GET['settings-updated']))) {
            return;
        }
        if (!isset($_GET['confirmation']) || 'redirect' !== $_GET['confirmation']) {
            return;
        }
        $_sTransient = 'apf_rurl' . md5(trim("redirect_{$this->oProp->sClassName}_{$_GET['page']}"));
        $_aError = $this->_getFieldErrors($_GET['page'], false);
        if (!empty($_aError)) {
            $this->oUtil->deleteTransient($_sTransient);
            return;
        }
        $_sURL = $this->oUtil->getTransient($_sTransient);
        if (false === $_sURL) {
            return;
        }
        $this->oUtil->deleteTransient($_sTransient);
        exit(wp_redirect($_sURL));
    }
    public function _replyToRegisterSettings() {
        if (!$this->_isInThePage()) {
            return;
        }
        $this->oForm->aSections = $this->oUtil->addAndApplyFilter($this, "sections_{$this->oProp->sClassName}", $this->oForm->aSections);
        foreach ($this->oForm->aFields as $_sSectionID => & $_aFields) {
            $_aFields = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}_{$_sSectionID}", $_aFields);
            unset($_aFields);
        }
        $this->oForm->aFields = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}", $this->oForm->aFields);
        $this->oForm->setDefaultPageSlug($this->oProp->sDefaultPageSlug);
        $this->oForm->setOptionKey($this->oProp->sOptionKey);
        $this->oForm->setCallerClassName($this->oProp->sClassName);
        $this->oForm->format();
        $_sCurrentPageSlug = $this->oProp->getCurrentPageSlug();
        $this->oForm->setCurrentPageSlug($_sCurrentPageSlug);
        $this->oForm->setCurrentTabSlug($this->oProp->getCurrentTabSlug($_sCurrentPageSlug));
        $this->oForm->applyConditions();
        $this->oForm->applyFiltersToFields($this, $this->oProp->sClassName);
        $this->oForm->setDynamicElements($this->oProp->aOptions);
        $this->_loadFieldTypeDefinitions();
        foreach ($this->oForm->aConditionedSections as $_aSection) {
            if (empty($_aSection['help'])) {
                continue;
            }
            $this->addHelpTab(array('page_slug' => $_aSection['page_slug'], 'page_tab_slug' => $_aSection['tab_slug'], 'help_tab_title' => $_aSection['title'], 'help_tab_id' => $_aSection['section_id'], 'help_tab_content' => $_aSection['help'], 'help_tab_sidebar_content' => $_aSection['help_aside'] ? $_aSection['help_aside'] : "",));
        }
        $this->_registerFields($this->oForm->aConditionedFields);
        $this->oProp->bEnableForm = true;
        $this->_handleSubmittedData();
    }
    protected function _registerField(array $aField) {
        AdminPageFramework_FieldTypeRegistration::_setFieldResources($aField, $this->oProp, $this->oResource);
        if ($aField['help']) {
            $this->addHelpTab(array('page_slug' => $aField['page_slug'], 'page_tab_slug' => $aField['tab_slug'], 'help_tab_title' => $aField['section_title'], 'help_tab_id' => $aField['section_id'], 'help_tab_content' => "<span class='contextual-help-tab-title'>" . $aField['title'] . "</span> - " . PHP_EOL . $aField['help'], 'help_tab_sidebar_content' => $aField['help_aside'] ? $aField['help_aside'] : "",));
        }
        if (isset($this->oProp->aFieldTypeDefinitions[$aField['type']]['hfDoOnRegistration']) && is_callable($this->oProp->aFieldTypeDefinitions[$aField['type']]['hfDoOnRegistration'])) {
            call_user_func_array($this->oProp->aFieldTypeDefinitions[$aField['type']]['hfDoOnRegistration'], array($aField));
        }
    }
    public function getSavedOptions() {
        $_bHasConfirmation = isset($_GET['confirmation']);
        $_bHasFieldErrors = isset($_GET['field_errors']) && $_GET['field_errors'];
        $_aLastInput = $_bHasConfirmation || $_bHasFieldErrors ? $this->oProp->aLastInput : array();
        return $_aLastInput + $this->oProp->aOptions;
    }
}