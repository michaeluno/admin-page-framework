<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Widget extends AdminPageFramework_Widget_Controller {
    protected $_sStructureType = 'widget';
    public function __construct()
    {
        $_sThisClassName = get_class($this);
        $_bAssumedAsWPWidget = 0 === func_num_args();
        $_aDefaults = array( '', array(), 'edit_theme_options', 'admin-page-framework' );
        $_aParameters = $_bAssumedAsWPWidget ? $this->___getConstructorParametersUsedForThisClassName($_sThisClassName) + $_aDefaults : func_get_args() + $_aDefaults;
        $this->___setProperties($_aParameters, $_sThisClassName, $_bAssumedAsWPWidget);
        parent::__construct($this->oProp);
    }
    private function ___setProperties($aParameters, $sThisClassName, $_bAssumedAsWPWidget)
    {
        $sWidgetTitle = $aParameters[ 0 ];
        $aWidgetArguments = $aParameters[ 1 ];
        $sCapability = $aParameters[ 2 ];
        $sTextDomain = $aParameters[ 3 ];
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, null, $sThisClassName, $sCapability, $sTextDomain, $this->_sStructureType);
        $this->oProp->sWidgetTitle = $sWidgetTitle;
        $this->oProp->aWidgetArguments = $aWidgetArguments;
        $this->oProp->bAssumedAsWPWidget = $_bAssumedAsWPWidget;
        if ($_bAssumedAsWPWidget) {
            $this->oProp->aWPWidgetMethods = get_class_methods('WP_Widget');
            $this->oProp->aWPWidgetProperties = get_class_vars('WP_Widget');
        }
    }
    private function ___getConstructorParametersUsedForThisClassName($sClassName)
    {
        if (! isset($GLOBALS[ 'wp_widget_factory' ])) {
            return array();
        }
        if (! is_object($GLOBALS[ 'wp_widget_factory' ])) {
            return array();
        }
        if (! isset($GLOBALS[ 'wp_widget_factory' ]->widgets[ $sClassName ])) {
            return array();
        }
        $_oWPWidget = $GLOBALS[ 'wp_widget_factory' ]->widgets[ $sClassName ];
        return array( $_oWPWidget->oCaller->oProp->sWidgetTitle, $_oWPWidget->oCaller->oProp->aWidgetArguments, $_oWPWidget->oCaller->oProp->sCapability, $_oWPWidget->oCaller->oProp->sTextDomain, );
    }
}
