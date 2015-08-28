<?php
class AdminPageFramework_Property_Widget extends AdminPageFramework_Property_Base {
    public $_sPropertyType = 'widget';
    public $sFieldsType = 'widget';
    public $sClassName = '';
    public $sCallerPath = '';
    public $sWidgetTitle = '';
    public $aWidgetArguments = array();
    public $bShowWidgetTitle = true;
    public $oWidget;
}