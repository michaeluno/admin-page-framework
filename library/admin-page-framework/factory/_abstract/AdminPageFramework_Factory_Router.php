<?php
abstract class AdminPageFramework_Factory_Router {
    public $oProp;
    public $oDebug;
    public $oUtil;
    public $oMsg;
    public $oForm;
    protected $oPageLoadInfo;
    protected $oResource;
    protected $oHeadTag;
    protected $oHelpPane;
    protected $oLink;
    protected $_aSubClassNames = array('oDebug', 'oUtil', 'oMsg', 'oForm', 'oPageLoadInfo', 'oResource', 'oHelpPane', 'oLink',);
    public function __construct($oProp) {
        unset($this->oDebug, $this->oUtil, $this->oMsg, $this->oForm, $this->oPageLoadInfo, $this->oResource, $this->oHelpPane, $this->oLink);
        $this->oProp = $oProp;
        if ($this->oProp->bIsAdmin && !$this->oProp->bIsAdminAjax) {
            if (did_action('current_screen')) {
                $this->_replyToLoadComponents();
            } else {
                add_action('current_screen', array($this, '_replyToLoadComponents'));
            }
        }
        $this->start();
    }
    public function _replyToLoadComponents() {
        if ('plugins.php' === $this->oProp->sPageNow) {
            $this->oLink = $this->oLink;
        }
        if (!$this->_isInThePage()) {
            return;
        }
        if (in_array($this->oProp->_sPropertyType, array('widget')) && 'customize.php' !== $this->oProp->sPageNow) {
            return;
        }
        $this->_setSubClasses();
    }
    private function _setSubClasses() {
        $this->oResource = $this->oResource;
        $this->oHeadTag = $this->oResource;
        $this->oLink = $this->oLink;
        $this->oPageLoadInfo = $this->oPageLoadInfo;
    }
    protected function _isInstantiatable() {
        return true;
    }
    public function _isInThePage() {
        return true;
    }
    protected $_aFormElementClassNameMap = array('admin_page' => 'AdminPageFramework_FormDefinition_Page', 'network_admin_page' => 'AdminPageFramework_FormDefinition_Page', 'post_meta_box' => 'AdminPageFramework_FormDefinition_Meta', 'page_meta_box' => 'AdminPageFramework_FormDefinition', 'post_type' => 'AdminPageFramework_FormDefinition', 'taxonomy_field' => 'AdminPageFramework_FormDefinition', 'widget' => 'AdminPageFramework_FormDefinition', 'user_meta' => 'AdminPageFramework_FormDefinition_Meta',);
    protected function _getFormInstance($oProp) {
        $_sFormClass = "AdminPageFramework_Form_{$oProp->_sPropertyType}";
        return new $_sFormClass($oProp->aFormArguments, $oProp->aFormCallbacks, $this->oMsg);
    }
    protected $_aResourceClassNameMap = array('admin_page' => 'AdminPageFramework_Resource_Page', 'network_admin_page' => 'AdminPageFramework_Resource_Page', 'post_meta_box' => 'AdminPageFramework_Resource_MetaBox', 'page_meta_box' => 'AdminPageFramework_Resource_MetaBox_Page', 'post_type' => 'AdminPageFramework_Resource_PostType', 'taxonomy_field' => 'AdminPageFramework_Resource_TaxonomyField', 'widget' => 'AdminPageFramework_Resource_Widget', 'user_meta' => 'AdminPageFramework_Resource_UserMeta',);
    protected function _getResourceInstance($oProp) {
        return $this->_getInstanceByMap($this->_aResourceClassNameMap, $oProp->sStructureType, $oProp);
    }
    protected $_aHelpPaneClassNameMap = array('admin_page' => 'AdminPageFramework_HelpPane_Page', 'network_admin_page' => 'AdminPageFramework_HelpPane_Page', 'post_meta_box' => 'AdminPageFramework_HelpPane_MetaBox', 'page_meta_box' => 'AdminPageFramework_HelpPane_MetaBox_Page', 'post_type' => null, 'taxonomy_field' => 'AdminPageFramework_HelpPane_TaxonomyField', 'widget' => 'AdminPageFramework_HelpPane_Widget', 'user_meta' => 'AdminPageFramework_HelpPane_UserMeta',);
    protected function _getHelpPaneInstance($oProp) {
        return $this->_getInstanceByMap($this->_aHelpPaneClassNameMap, $oProp->sStructureType, $oProp);
    }
    protected $_aLinkClassNameMap = array('admin_page' => 'AdminPageFramework_Link_Page', 'network_admin_page' => 'AdminPageFramework_Link_NetworkAdmin', 'post_meta_box' => null, 'page_meta_box' => null, 'post_type' => 'AdminPageFramework_Link_PostType', 'taxonomy_field' => null, 'widget' => null, 'user_meta' => null,);
    protected function _getLinkInstancce($oProp, $oMsg) {
        return $this->_getInstanceByMap($this->_aLinkClassNameMap, $oProp->sStructureType, $oProp, $oMsg);
    }
    protected $_aPageLoadClassNameMap = array('admin_page' => 'AdminPageFramework_PageLoadInfo_Page', 'network_admin_page' => 'AdminPageFramework_PageLoadInfo_NetworkAdminPage', 'post_meta_box' => null, 'page_meta_box' => null, 'post_type' => 'AdminPageFramework_PageLoadInfo_PostType', 'taxonomy_field' => null, 'widget' => null, 'user_meta' => null,);
    protected function _getPageLoadInfoInstance($oProp, $oMsg) {
        if (!isset($this->_aPageLoadClassNameMap[$oProp->sStructureType])) {
            return null;
        }
        $_sClassName = $this->_aPageLoadClassNameMap[$oProp->sStructureType];
        return call_user_func_array(array($_sClassName, 'instantiate'), array($oProp, $oMsg));
    }
    private function _getInstanceByMap() {
        $_aParams = func_get_args();
        $_aClassNameMap = array_shift($_aParams);
        $_sKey = array_shift($_aParams);
        if (!isset($_aClassNameMap[$_sKey])) {
            return null;
        }
        $_iParamCount = count($_aParams);
        if ($_iParamCount > 3) {
            return null;
        }
        array_unshift($_aParams, $_aClassNameMap[$_sKey]);
        return call_user_func_array(array($this, "_replyToGetClassInstanceByArgumentOf{$_iParamCount}"), $_aParams);
    }
    private function _replyToGetClassInstanceByArgumentOf0($sClassName) {
        return new $sClassName;
    }
    private function _replyToGetClassInstanceByArgumentOf1($sClassName, $mArg) {
        return new $sClassName($mArg);
    }
    private function _replyToGetClassInstanceByArgumentOf2($sClassName, $mArg1, $mArg2) {
        return new $sClassName($mArg1, $mArg2);
    }
    private function _replyToGetClassInstanceByArgumentOf3($sClassName, $mArg1, $mArg2, $mArg3) {
        return new $sClassName($mArg1, $mArg2, $mArg3);
    }
    public function __get($sPropertyName) {
        switch ($sPropertyName) {
            case 'oHeadTag':
                $sPropertyName = 'oResource';
            break;
        }
        if (in_array($sPropertyName, $this->_aSubClassNames)) {
            return call_user_func(array($this, "_replyTpSetAndGetInstance_{$sPropertyName}"));
        }
    }
    public function _replyTpSetAndGetInstance_oUtil() {
        $this->oUtil = new AdminPageFramework_WPUtility;
        return $this->oUtil;
    }
    public function _replyTpSetAndGetInstance_oDebug() {
        $this->oDebug = new AdminPageFramework_Debug;
        return $this->oDebug;
    }
    public function _replyTpSetAndGetInstance_oMsg() {
        $this->oMsg = AdminPageFramework_Message::getInstance($this->oProp->sTextDomain);
        return $this->oMsg;
    }
    public function _replyTpSetAndGetInstance_oForm() {
        $this->oForm = $this->_getFormInstance($this->oProp);
        return $this->oForm;
    }
    public function _replyTpSetAndGetInstance_oResource() {
        $this->oResource = $this->_getResourceInstance($this->oProp);
        return $this->oResource;
    }
    public function _replyTpSetAndGetInstance_oHelpPane() {
        $this->oHelpPane = $this->_getHelpPaneInstance($this->oProp);
        return $this->oHelpPane;
    }
    public function _replyTpSetAndGetInstance_oLink() {
        $this->oLink = $this->_getLinkInstancce($this->oProp, $this->oMsg);
        return $this->oLink;
    }
    public function _replyTpSetAndGetInstance_oPageLoadInfo() {
        $this->oPageLoadInfo = $this->_getPageLoadInfoInstance($this->oProp, $this->oMsg);
        return $this->oPageLoadInfo;
    }
    public function __call($sMethodName, $aArgs = null) {
        $_mFirstArg = $this->oUtil->getElement($aArgs, 0);
        switch ($sMethodName) {
            case 'validate':
            case 'content':
                return $_mFirstArg;
            case 'setup_pre':
                $this->_setUp();
                $this->oUtil->addAndDoAction($this, "set_up_{$this->oProp->sClassName}", $this);
                return;
        }
        if (has_filter($sMethodName)) {
            return $_mFirstArg;
        }
        trigger_error('Admin Page Framework: ' . ' : ' . sprintf(__('The method is not defined: %1$s', $this->oProp->sTextDomain), $sMethodName), E_USER_WARNING);
    }
    public function __toString() {
        return $this->oUtil->getObjectInfo($this);
    }
    public function setFooterInfoRight() {
    }
    public function setFooterInfoLeft() {
    }
}