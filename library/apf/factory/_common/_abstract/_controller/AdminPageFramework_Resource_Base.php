<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Resource_Base extends AdminPageFramework_FrameworkUtility {
    protected static $_aStructure_EnqueuingResources = array( 'sSRC' => null, 'sSRCRaw' => null, 'aPostTypes' => array(), 'sPageSlug' => null, 'sTabSlug' => null, 'sType' => null, 'handle_id' => null, 'dependencies' => array(), 'version' => false, 'attributes' => array(), 'conditional' => null, 'translation' => array(), 'translation_var' => '', 'in_footer' => false, 'media' => 'all', 'rtl' => null, 'suffix' => null, 'alt' => null, 'title' => null, );
    protected $_sClassSelector_Style = 'admin-page-framework-style';
    protected $_sClassSelector_Script = 'admin-page-framework-script';
    protected $_aHandleIDs = array();
    public $oProp;
    public $oUtil;
    public function __construct($oProp)
    {
        $this->oProp = $oProp;
        $this->oUtil = new AdminPageFramework_WPUtility;
        if ($this->isDoingAjax()) {
            return;
        }
        $this->registerAction('current_screen', array( $this, '_replyToSetUpHooks' ));
    }
    public function _replyToSetUpHooks()
    {
        if (! $this->oProp->oCaller->isInThePage()) {
            return;
        }
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueCommonScripts' ), 1);
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueCommonStyles' ), 1);
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ));
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ));
        add_action(did_action('admin_print_styles') ? 'admin_print_footer_scripts' : 'admin_print_styles', array( $this, '_replyToAddStyle' ), 999);
        add_action(did_action('admin_print_scripts') ? 'admin_print_footer_scripts' : 'admin_print_scripts', array( $this, '_replyToAddScript' ), 999);
        add_action('customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueScripts' ));
        add_action('customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueStyles' ));
        add_action('admin_footer', array( $this, '_replyToEnqueueScripts' ));
        add_action('admin_footer', array( $this, '_replyToEnqueueStyles' ));
        add_action('admin_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999);
        add_action('admin_print_footer_scripts', array( $this, '_replyToAddScript' ), 999);
        add_filter('script_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2);
        add_filter('style_loader_src', array( $this, '_replyToSetupArgumentCallback' ), 1, 2);
    }
    protected function _enqueueSRCByCondition($aEnqueueItem)
    {
        $this->_enqueueSRC($aEnqueueItem);
    }
    public function _replyToSetupArgumentCallback($sSRC, $sHandleID)
    {
        if (isset($this->oProp->aResourceAttributes[ $sHandleID ])) {
            $this->_aHandleIDs[ $sSRC ] = $sHandleID;
            add_filter('clean_url', array( $this, '_replyToModifyEnqueuedAttributes' ), 1, 3);
            remove_filter(current_filter(), array( $this, '_replyToSetupArgumentCallback' ), 1);
        }
        return $sSRC;
    }
    public function _replyToModifyEnqueuedAttributes($sSanitizedURL, $sOriginalURL, $sContext)
    {
        if ('display' !== $sContext) {
            return $sSanitizedURL;
        }
        if (isset($this->_aHandleIDs[ $sOriginalURL ])) {
            $_sHandleID = $this->_aHandleIDs[ $sOriginalURL ];
            $_aAttributes = $this->oProp->aResourceAttributes[ $_sHandleID ];
            if (empty($_aAttributes)) {
                return $sSanitizedURL;
            }
            $_sAttributes = $this->getAttributes($_aAttributes);
            return $sSanitizedURL . "' " . rtrim($_sAttributes, "'\"");
        }
        return $sSanitizedURL;
    }
    protected function _printCommonStyles($sIDPrefix, $sClassName)
    {
        if ($this->hasBeenCalled('COMMON_STYLES: ' . get_class($this) . '::' . __METHOD__)) {
            return;
        }
        $_oCaller = $this->oProp->oCaller;
        echo $this->___getCommonStyleTag($_oCaller, $sIDPrefix);
        echo $this->___getCommonIEStyleTag($_oCaller, $sIDPrefix);
    }
    private function ___getCommonStyleTag($oCaller, $sIDPrefix)
    {
        $_sStyle = $this->addAndApplyFilters($oCaller, array( "style_common_admin_page_framework", "style_common_{$this->oProp->sClassName}", ), '');
        $_sStyle = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified($_sStyle);
        $_sStyle = trim($_sStyle);
        if ($_sStyle) {
            return "<style type='text/css' id='" . esc_attr(strtolower($sIDPrefix)) . "'>" . $_sStyle . "</style>";
        }
    }
    private function ___getCommonIEStyleTag($oCaller, $sIDPrefix)
    {
        $_sStyleIE = $this->addAndApplyFilters($oCaller, array( "style_ie_common_admin_page_framework", "style_ie_common_{$this->oProp->sClassName}", ), AdminPageFramework_CSS::getDefaultCSSIE());
        $_sStyleIE = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified($_sStyleIE);
        $_sStyleIE = trim($_sStyleIE);
        return $_sStyleIE ? "<!--[if IE]><style type='text/css' id='" . esc_attr(strtolower($sIDPrefix . "-ie")) . "'>" . $_sStyleIE . "</style><![endif]-->" : '';
    }
    protected function _printCommonScripts($sIDPrefix, $sClassName)
    {
        if ($this->hasBeenCalled('COMMON_SCRIPT: ' . get_class($this) . '::' . __METHOD__)) {
            return;
        }
        $_sScript = $this->addAndApplyFilters($this->oProp->oCaller, array( "script_common_admin_page_framework", "script_common_{$this->oProp->sClassName}", ), AdminPageFramework_Property_Base::$_sDefaultScript);
        $_sScript = trim($_sScript);
        if (! $_sScript) {
            return;
        }
        echo "<script type='text/javascript' id='" . esc_attr(strtolower($sIDPrefix)) . "'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
    protected function _printClassSpecificStyles($sIDPrefix)
    {
        $_oCaller = $this->oProp->oCaller;
        echo $this->_getClassSpecificStyleTag($_oCaller, $sIDPrefix);
        echo $this->_getClassSpecificIEStyleTag($_oCaller, $sIDPrefix);
        $this->oProp->sStyle = '';
        $this->oProp->sStyleIE = '';
    }
    private function _getClassSpecificStyleTag($_oCaller, $sIDPrefix)
    {
        static $_iCallCount = 0;
        $_sFilterName = "style_{$this->oProp->sClassName}";
        if ($this->hasBeenCalled('FILTER: ' . $_sFilterName)) {
            return '';
        }
        $_sStyle = $this->addAndApplyFilters($_oCaller, $_sFilterName, $this->oProp->sStyle);
        $_sStyle = $this->isDebugMode() ? $_sStyle : $this->getCSSMinified($_sStyle);
        $_sStyle = trim($_sStyle);
        if (! $_sStyle) {
            return '';
        }
        $_iCallCount++;
        $_sID = strtolower("{$sIDPrefix}-" . $this->oProp->sClassName . "_{$_iCallCount}");
        return "<style type='text/css' id='" . esc_attr($_sID) . "'>" . $_sStyle . "</style>";
    }
    private function _getClassSpecificIEStyleTag($_oCaller, $sIDPrefix)
    {
        static $_iCallCountIE = 1;
        $_sFilterName = "style_ie_{$this->oProp->sClassName}";
        if ($this->hasBeenCalled('FILTER: ' . $_sFilterName)) {
            return '';
        }
        $_sStyleIE = $this->addAndApplyFilters($_oCaller, $_sFilterName, $this->oProp->sStyleIE);
        $_sStyleIE = $this->isDebugMode() ? $_sStyleIE : $this->getCSSMinified($_sStyleIE);
        $_sStyleIE = trim($_sStyleIE);
        if (! $_sStyleIE) {
            return '';
        }
        $_iCallCountIE++;
        $_sID = strtolower("{$sIDPrefix}-ie-{$this->oProp->sClassName}_{$_iCallCountIE}");
        return "<!--[if IE]><style type='text/css' id='" . esc_attr($_sID) . "'>" . $_sStyleIE . "</style><![endif]-->";
    }
    protected function _printClassSpecificScripts($sIDPrefix)
    {
        static $_iCallCount = 1;
        $_sFilterName = "script_{$this->oProp->sClassName}";
        if ($this->hasBeenCalled('FILTER: ' . $_sFilterName)) {
            return '';
        }
        $_sScript = $this->addAndApplyFilters($this->oProp->oCaller, $_sFilterName, $this->oProp->sScript);
        $_sScript = trim($_sScript);
        if (! $_sScript) {
            return '';
        }
        $_iCallCount++;
        $_sID = strtolower("{$sIDPrefix}-{$this->oProp->sClassName}_{$_iCallCount}");
        echo "<script type='text/javascript' id='" . esc_attr($_sID) . "'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
        $this->oProp->sScript = '';
    }
    public function _replyToAddStyle()
    {
        $_oCaller = $this->oProp->oCaller;
        if (! $_oCaller->isInThePage()) {
            return;
        }
        $this->_printCommonStyles('admin-page-framework-style-common', get_class());
        $this->_printClassSpecificStyles($this->_sClassSelector_Style . '-' . $this->oProp->sStructureType);
    }
    public function _replyToAddScript()
    {
        $_oCaller = $this->oProp->oCaller;
        if (! $_oCaller->isInThePage()) {
            return;
        }
        $this->_printCommonScripts('admin-page-framework-script-common', get_class());
        $this->_printClassSpecificScripts($this->_sClassSelector_Script . '-' . $this->oProp->sStructureType);
    }
    protected function _enqueueSRC($aEnqueueItem)
    {
        $_sSRC = $this->___getSRCFormatted($aEnqueueItem);
        if ('style' === $aEnqueueItem[ 'sType' ]) {
            $this->___enqueueStyle($_sSRC, $aEnqueueItem);
            return;
        }
        $this->___enqueueScript($_sSRC, $aEnqueueItem);
    }
    private function ___enqueueScript($sSRC, array $aEnqueueItem)
    {
        wp_enqueue_script($aEnqueueItem[ 'handle_id' ], $sSRC, $aEnqueueItem[ 'dependencies' ], $aEnqueueItem[ 'version' ], did_action('admin_body_class') || ( boolean ) $aEnqueueItem[ 'in_footer' ]);
        if ($aEnqueueItem[ 'translation' ]) {
            wp_localize_script($aEnqueueItem[ 'handle_id' ], empty($aEnqueueItem[ 'translation_var' ]) ? $aEnqueueItem[ 'handle_id' ] : $aEnqueueItem[ 'translation_var' ], $aEnqueueItem[ 'translation' ]);
        }
        if ($aEnqueueItem[ 'conditional' ]) {
            wp_script_add_data($aEnqueueItem[ 'handle_id' ], 'conditional', $aEnqueueItem[ 'conditional' ]);
        }
    }
    private function ___enqueueStyle($sSRC, array $aEnqueueItem)
    {
        wp_enqueue_style($aEnqueueItem[ 'handle_id' ], $sSRC, $aEnqueueItem[ 'dependencies' ], $aEnqueueItem[ 'version' ], $aEnqueueItem[ 'media' ]);
        $_aAddData = array( 'conditional', 'rtl', 'suffix', 'alt', 'title' );
        foreach ($_aAddData as $_sDataKey) {
            if (! isset($aEnqueueItem[ $_sDataKey ])) {
                continue;
            }
            wp_style_add_data($aEnqueueItem[ 'handle_id' ], $_sDataKey, $aEnqueueItem[ $_sDataKey ]);
        }
    }
    private function ___getSRCFormatted(array $aEnqueueItem)
    {
        if (! $this->oProp->bAutoloadMinifiedResource) {
            return $aEnqueueItem[ 'sSRC' ];
        }
        if ($this->isDebugMode()) {
            return $aEnqueueItem[ 'sSRC' ];
        }
        if ($this->isURL($aEnqueueItem[ 'sSRCRaw' ])) {
            return $aEnqueueItem[ 'sSRC' ];
        }
        $_sMinPrefix = '.min';
        if (false !== stripos($aEnqueueItem[ 'sSRC' ], $_sMinPrefix)) {
            return $aEnqueueItem[ 'sSRC' ];
        }
        $_aPathParts = pathinfo($aEnqueueItem[ 'sSRCRaw' ]) + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' );
        if (! $_aPathParts[ 'extension' ]) {
            return $aEnqueueItem[ 'sSRC' ];
        }
        $_aPathPartsURL = pathinfo($aEnqueueItem[ 'sSRC' ]) + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' );
        $_sPathMinifiedVersion = $_aPathParts[ 'dirname' ] . '/' . $_aPathParts[ 'filename' ] . $_sMinPrefix . '.' . $_aPathParts[ 'extension' ];
        return file_exists($_sPathMinifiedVersion) ? $_aPathPartsURL[ 'dirname' ] . '/' . $_aPathPartsURL[ 'filename' ] . $_sMinPrefix . '.' . $_aPathPartsURL[ 'extension' ] : $aEnqueueItem[ 'sSRC' ];
    }
    public function _replyToEnqueueCommonScripts()
    {
        if ($this->hasBeenCalled('COMMON_EXTERNAL_SCRIPTS: ' . __METHOD__)) {
            return;
        }
    }
    public function _replyToEnqueueCommonStyles()
    {
        if ($this->hasBeenCalled('COMMON_EXTERNAL_STYLES: ' . __METHOD__)) {
            return;
        }
        $this->_addEnqueuingResourceByType(AdminPageFramework_Registry::$sDirPath . '/factory/_common/asset/css/common.css', array( 'version' => AdminPageFramework_Registry::VERSION, ), 'style');
    }
    public function _replyToEnqueueStyles()
    {
        foreach ($this->oProp->aEnqueuingStyles as $_sKey => $_aEnqueuingStyle) {
            $this->_enqueueSRCByCondition($_aEnqueuingStyle);
            unset($this->oProp->aEnqueuingStyles[ $_sKey ]);
        }
    }
    public function _replyToEnqueueScripts()
    {
        foreach ($this->oProp->aEnqueuingScripts as $_sKey => $_aEnqueuingScript) {
            $this->_enqueueSRCByCondition($_aEnqueuingScript);
            unset($this->oProp->aEnqueuingScripts[ $_sKey ]);
        }
    }
    public function _enqueueResourcesByType($aSRCs, array $aCustomArgs=array(), $sType='style')
    {
        $_aHandleIDs = array();
        foreach ($aSRCs as $_sSRC) {
            $_aHandleIDs[] = call_user_func_array(array( $this, '_addEnqueuingResourceByType' ), array( $_sSRC, $aCustomArgs, $sType ));
        }
        return $_aHandleIDs;
    }
    public function _addEnqueuingResourceByType($sSRC, array $aCustomArgs=array(), $sType='style')
    {
        $sSRC = trim($sSRC);
        if (empty($sSRC)) {
            return '';
        }
        $_sRawSRC = wp_normalize_path($sSRC);
        $_sSRC = $this->getResolvedSRC($_sRawSRC);
        $_sContainerPropertyName = $this->___getContainerPropertyNameByType($sType);
        $_sEnqueuedIndexPropertyName = $this->___getEnqueuedIndexPropertyNameByType($sType);
        $this->oProp->{$_sContainerPropertyName}[ $_sSRC ] = array_filter($this->getAsArray($aCustomArgs), array( $this, 'isNotNull' )) + array( 'sSRCRaw' => $_sRawSRC, 'sSRC' => $_sSRC, 'sType' => $sType, 'handle_id' => $sType . '_' . strtolower($this->oProp->sClassName) . '_' . (++$this->oProp->{$_sEnqueuedIndexPropertyName}), ) + self::$_aStructure_EnqueuingResources;
        $this->oProp->aResourceAttributes[ $this->oProp->{$_sContainerPropertyName}[ $_sSRC ]['handle_id'] ] = $this->oProp->{$_sContainerPropertyName}[ $_sSRC ]['attributes'];
        return $this->oProp->{$_sContainerPropertyName}[ $_sSRC ][ 'handle_id' ];
    }
    private function ___getContainerPropertyNameByType($sType)
    {
        switch ($sType) { default: case 'style': return 'aEnqueuingStyles'; case 'script': return 'aEnqueuingScripts'; }
    }
    private function ___getEnqueuedIndexPropertyNameByType($sType)
    {
        switch ($sType) { default: case 'style': return 'iEnqueuedStyleIndex'; case 'script': return 'iEnqueuedScriptIndex'; }
    }
}
