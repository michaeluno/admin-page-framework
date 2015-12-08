<?php
abstract class AdminPageFramework_Property_Base extends AdminPageFramework_FrameworkUtility {
    private static $_aStructure_CallerInfo = array('sPath' => null, 'sType' => null, 'sName' => null, 'sURI' => null, 'sVersion' => null, 'sThemeURI' => null, 'sScriptURI' => null, 'sAuthorURI' => null, 'sAuthor' => null, 'sDescription' => null,);
    static public $_aLibraryData;
    public $_sPropertyType = '';
    protected $oCaller;
    public $sCallerPath;
    public $sClassName;
    public $sClassHash;
    public $sScript = '';
    public $sStyle = '';
    public $sStyleIE = '';
    public $aFieldTypeDefinitions = array();
    public static $_sDefaultScript = "";
    public static $_sDefaultStyle = "";
    public static $_sDefaultStyleIE = '';
    public $aEnqueuingScripts = array();
    public $aEnqueuingStyles = array();
    public $aResourceAttributes = array();
    public $iEnqueuedScriptIndex = 0;
    public $iEnqueuedStyleIndex = 0;
    public $bIsAdmin;
    public $bIsMinifiedVersion;
    public $sCapability;
    public $sStructureType;
    public $sTextDomain;
    public $sPageNow;
    public $_bSetupLoaded;
    public $bIsAdminAjax;
    public $sLabelPluginSettingsLink = null;
    public $aFooterInfo = array('sLeft' => '__SCRIPT_CREDIT__', 'sRight' => '__FRAMEWORK_CREDIT__',);
    public $oUtil;
    public $_sFormRegistrationHook = 'current_screen';
    public $aFormArguments = array('caller_id' => '', 'structure_type' => '', 'action_hook_form_registration' => '',);
    public $aFormCallbacks = array('hfID' => null, 'hfTagID' => null, 'hfName' => null, 'hfNameFlat' => null, 'hfInputName' => null, 'hfInputNameFlat' => null, 'hfClass' => null,);
    public function __construct($oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sStructureType) {
        $this->oCaller = $oCaller;
        $this->sCallerPath = $this->getAOrB($sCallerPath, $sCallerPath, null);
        $this->sClassName = $sClassName;
        $this->sClassHash = md5($sClassName);
        $this->sCapability = $this->getAOrB(empty($sCapability), 'manage_options', $sCapability);
        $this->sTextDomain = $this->getAOrB(empty($sTextDomain), 'admin-page-framework', $sTextDomain);
        $this->sStructureType = $sStructureType;
        $GLOBALS['aAdminPageFramework'] = $this->getElementAsArray($GLOBALS, 'aAdminPageFramework', array('aFieldFlags' => array()));
        $this->sPageNow = $this->getPageNow();
        $this->bIsAdmin = is_admin();
        $this->bIsAdminAjax = in_array($this->sPageNow, array('admin-ajax.php'));
        $this->aFormArguments = array('caller_id' => $this->sClassName, 'structure_type' => $this->_sPropertyType, 'action_hook_form_registration' => $this->_sFormRegistrationHook,) + $this->aFormArguments;
        $this->aFormCallbacks = array('is_in_the_page' => array($oCaller, '_replyToDetermineWhetherToProcessFormRegistration'), 'load_fieldset_resource' => array($oCaller, '_replyToFieldsetResourceRegistration'), 'is_fieldset_registration_allowed' => null, 'capability' => array($oCaller, '_replyToGetCapabilityForForm'), 'saved_data' => array($oCaller, '_replyToGetSavedFormData'), 'section_head_output' => array($oCaller, '_replyToGetSectionHeaderOutput'), 'fieldset_output' => array($oCaller, '_replyToGetFieldOutput'), 'sectionset_before_output' => array($oCaller, '_replyToFormatSectionsetDefinition'), 'fieldset_before_output' => array($oCaller, '_replyToFormatFieldsetDefinition'), 'fieldset_after_formatting' => array($oCaller, '_replyToModifyFieldsetDefinition'), 'fieldsets_after_formatting' => array($oCaller, '_replyToModifyFieldsetsDefinitions'), 'is_sectionset_visible' => array($oCaller, '_replyToDetermineSectionsetVisibility'), 'is_fieldset_visible' => array($oCaller, '_replyToDetermineFieldsetVisibility'), 'secitonsets_before_registration' => array($oCaller, '_replyToModifySectionsets'), 'fieldsets_before_registration' => array($oCaller, '_replyToModifyFieldsets'), 'handle_form_data' => array($oCaller, '_replyToHandleSubmittedFormData'), 'hfID' => array($oCaller, '_replyToGetInputID'), 'hfTagID' => array($oCaller, '_replyToGetInputTagIDAttribute'), 'hfName' => array($oCaller, '_replyToGetFieldNameAttribute'), 'hfNameFlat' => array($oCaller, '_replyToGetFlatFieldName'), 'hfInputName' => array($oCaller, '_replyToGetInputNameAttribute'), 'hfInputNameFlat' => array($oCaller, '_replyToGetFlatInputName'), 'hfClass' => array($oCaller, '_replyToGetInputClassAttribute'), 'hfSectionName' => array($oCaller, '_replyToGetSectionName'),) + $this->aFormCallbacks;
        $this->_setDeprecated();
    }
    private function _setDeprecated() {
        $this->oUtil = new AdminPageFramework_WPUtility;
    }
    public function _getCallerObject() {
        return $this->oCaller;
    }
    static public function _setLibraryData() {
        self::$_aLibraryData = array('sName' => AdminPageFramework_Registry::NAME, 'sURI' => AdminPageFramework_Registry::URI, 'sScriptName' => AdminPageFramework_Registry::NAME, 'sLibraryName' => AdminPageFramework_Registry::NAME, 'sLibraryURI' => AdminPageFramework_Registry::URI, 'sPluginName' => '', 'sPluginURI' => '', 'sThemeName' => '', 'sThemeURI' => '', 'sVersion' => AdminPageFramework_Registry::getVersion(), 'sDescription' => AdminPageFramework_Registry::DESCRIPTION, 'sAuthor' => AdminPageFramework_Registry::AUTHOR, 'sAuthorURI' => AdminPageFramework_Registry::AUTHOR_URI, 'sTextDomain' => AdminPageFramework_Registry::TEXT_DOMAIN, 'sDomainPath' => AdminPageFramework_Registry::TEXT_DOMAIN_PATH, 'sNetwork' => '', '_sitewide' => '',);
        return self::$_aLibraryData;
    }
    static public function _getLibraryData() {
        return isset(self::$_aLibraryData) ? self::$_aLibraryData : self::_setLibraryData();
    }
    protected function getCallerInfo($sCallerPath = null) {
        $_aCallerInfo = self::$_aStructure_CallerInfo;
        $_aCallerInfo['sPath'] = $sCallerPath;
        $_aCallerInfo['sType'] = $this->_getCallerType($_aCallerInfo['sPath']);
        if ('unknown' == $_aCallerInfo['sType']) {
            return $_aCallerInfo;
        }
        if ('plugin' == $_aCallerInfo['sType']) {
            return $this->getScriptData($_aCallerInfo['sPath'], $_aCallerInfo['sType']) + $_aCallerInfo;
        }
        if ('theme' == $_aCallerInfo['sType']) {
            $_oTheme = wp_get_theme();
            return array('sName' => $_oTheme->Name, 'sVersion' => $_oTheme->Version, 'sThemeURI' => $_oTheme->get('ThemeURI'), 'sURI' => $_oTheme->get('ThemeURI'), 'sAuthorURI' => $_oTheme->get('AuthorURI'), 'sAuthor' => $_oTheme->get('Author'),) + $_aCallerInfo;
        }
        return array();
    }
    protected function _getCallerType($sScriptPath) {
        if (preg_match('/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m)) {
            return 'theme';
        }
        if (preg_match('/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m)) {
            return 'plugin';
        }
        return 'unknown';
    }
    protected function _getOptions() {
        return array();
    }
    public function __get($sName) {
        if ('aScriptInfo' === $sName) {
            $this->sCallerPath = $this->sCallerPath ? $this->sCallerPath : $this->getCallerScriptPath(__FILE__);
            $this->aScriptInfo = $this->getCallerInfo($this->sCallerPath);
            return $this->aScriptInfo;
        }
        if ('aOptions' === $sName) {
            $this->aOptions = $this->_getOptions();
            return $this->aOptions;
        }
    }
}