<?php
class AdminPageFramework_Property_Widget extends AdminPageFramework_Property_Base {
    public $_sPropertyType = 'widget';
    public $sStructureType = 'widget';
    public $sClassName = '';
    public $sCallerPath = '';
    public $sWidgetTitle = '';
    public $aWidgetArguments = array();
    public $bShowWidgetTitle = true;
    public $oWidget;
    public function __construct($oCaller, $sCallerPath, $sClassName, $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework', $sStructureType) {
        $this->_sFormRegistrationHook = 'load_' . $sClassName;
        parent::__construct($oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sStructureType);
    }
}