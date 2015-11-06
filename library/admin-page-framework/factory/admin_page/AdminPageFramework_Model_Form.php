<?php
abstract class AdminPageFramework_Model_Form extends AdminPageFramework_Router {
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
        new AdminPageFramework_Model_FormRegistration($this);
        new AdminPageFramework_Model_FormSubmission($this);
        if (isset($_REQUEST['apf_remote_request_test']) && '_testing' === $_REQUEST['apf_remote_request_test']) {
            exit('OK');
        }
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