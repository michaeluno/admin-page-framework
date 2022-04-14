<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View__Resource extends AdminPageFramework_FrameworkUtility {
    public $oForm;
    public function __construct($oForm)
    {
        $this->oForm = $oForm;
        if ($this->isDoingAjax()) {
            return;
        }
        if ($this->hasBeenCalled('resource_' . $oForm->aArguments[ 'caller_id' ])) {
            return;
        }
        $this->___setHooks();
    }
    private function ___setHooks()
    {
        if (is_admin()) {
            $this->___setAdminHooks();
            return;
        }
        add_action('wp_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ));
        add_action('wp_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ));
        add_action(did_action('wp_print_styles') ? 'wp_print_footer_scripts' : 'wp_print_styles', array( $this, '_replyToAddStyle' ), 999);
        add_action('wp_footer', array( $this, '_replyToEnqueueScripts' ));
        add_action('wp_footer', array( $this, '_replyToEnqueueStyles' ));
        add_action('wp_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999);
        add_action('wp_print_footer_scripts', array( $this, '_replyToAddScript' ), 999);
        new AdminPageFramework_Form_View__Resource__Head($this->oForm, 'wp_head');
    }
    private function ___setAdminHooks()
    {
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ));
        add_action('admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ));
        add_action(did_action('admin_print_styles') ? 'admin_print_footer_scripts' : 'admin_print_styles', array( $this, '_replyToAddStyle' ), 999);
        add_action('customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueScripts' ));
        add_action('customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueStyles' ));
        add_action('admin_footer', array( $this, '_replyToEnqueueScripts' ));
        add_action('admin_footer', array( $this, '_replyToEnqueueStyles' ));
        add_action('admin_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999);
        add_action('admin_print_footer_scripts', array( $this, '_replyToAddScript' ), 999);
        new AdminPageFramework_Form_View__Resource__Head($this->oForm, 'admin_head');
    }
    public function _replyToEnqueueScripts()
    {
        if (! $this->oForm->isInThePage()) {
            return;
        }
        $_aRegister = $this->oForm->getResources('register');
        foreach ($this->getElementAsArray($_aRegister, array( 'scripts' )) as $_iIndex => $_aRegister) {
            $this->___registerScript($_aRegister);
        }
        foreach ($this->oForm->getResources('src_scripts') as $_isIndex => $_asEnqueue) {
            $this->___enqueueScript($_asEnqueue);
            $this->oForm->unsetResources(array( 'src_scripts', $_isIndex ));
        }
    }
    private function ___registerScript(array $aRegister)
    {
        $aRegister = $aRegister + array( 'handle_id' => '', 'src' => '', 'dependencies' => array(), 'version' => false, 'in_footer' => false, 'translation' => array(), 'translation_var' => '', );
        $_bRegistered = wp_register_script($aRegister[ 'handle_id' ], $this->___getSRCFormatted($aRegister), $aRegister[ 'dependencies' ], $aRegister[ 'version' ], $aRegister[ 'in_footer' ]);
        if ($_bRegistered && ! empty($aRegister[ 'translation' ])) {
            wp_localize_script($aRegister[ 'handle_id' ], $aRegister[ 'translation_var' ] ? $aRegister[ 'translation_var' ] : $aRegister[ 'translation_var' ], $this->getAsArray($aRegister[ 'translation' ]));
        }
    }
    private static $_aEnqueued = array();
    private function ___enqueueScript($asEnqueue)
    {
        $_sSetHandleID = $this->getElement($this->getAsArray($asEnqueue), 'handle_id', '');
        $_aEnqueueItem = $this->___getFormattedEnqueueScript($asEnqueue);
        $_sCacheID = $_sSetHandleID . $this->getElement($_aEnqueueItem, 'src', '');
        if (! empty($_sCacheID) && isset(self::$_aEnqueued[ $_sCacheID ])) {
            return;
        }
        self::$_aEnqueued[ $_sCacheID ] = $_aEnqueueItem;
        wp_enqueue_script($_aEnqueueItem[ 'handle_id' ], $_aEnqueueItem[ 'src' ], $_aEnqueueItem[ 'dependencies' ], $_aEnqueueItem[ 'version' ], did_action('admin_body_class') || ( boolean ) $_aEnqueueItem[ 'in_footer' ]);
        if ($_aEnqueueItem[ 'translation' ]) {
            wp_localize_script($_aEnqueueItem[ 'handle_id' ], empty($_aEnqueueItem[ 'translation_var' ]) ? $_aEnqueueItem[ 'handle_id' ] : $_aEnqueueItem[ 'translation_var' ], $_aEnqueueItem[ 'translation' ]);
        }
        if ($_aEnqueueItem[ 'conditional' ]) {
            wp_script_add_data($_aEnqueueItem[ 'handle_id' ], 'conditional', $_aEnqueueItem[ 'conditional' ]);
        }
    }
    private function ___getFormattedEnqueueScript($asEnqueue)
    {
        static $_iCallCount = 1;
        $_aEnqueueItem = $this->getAsArray($asEnqueue) + array( 'handle_id' => 'admin-page-framework-script-form-' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount, 'src' => null, 'dependencies' => null, 'version' => null, 'in_footer' => false, 'translation' => null, 'conditional' => null, 'translation_var' => null, );
        if (is_string($asEnqueue)) {
            $_aEnqueueItem[ 'src' ] = $asEnqueue;
        }
        $_aEnqueueItem[ 'src' ] = $this->___getSRCFormatted($_aEnqueueItem);
        $_iCallCount++;
        return $_aEnqueueItem;
    }
    public function _replyToEnqueueStyles()
    {
        if (! $this->oForm->isInThePage()) {
            return;
        }
        $_aRegister = $this->oForm->getResources('register');
        foreach ($this->getElementAsArray($_aRegister, array( 'styles' )) as $_iIndex => $_aRegister) {
            $this->___registerStyle($_aRegister);
        }
        foreach ($this->oForm->getResources('src_styles') as $_isIndex => $_asEnqueueItem) {
            $this->___enqueueStyle($_asEnqueueItem);
            $this->oForm->unsetResources(array( 'src_styles', $_isIndex ));
        }
    }
    private function ___registerStyle(array $aRegister)
    {
        $_aRegister = $aRegister + array( 'handle_id' => null, 'src' => null, 'dependencies' => array(), 'version' => false, 'media' => 'all', );
        wp_register_style($_aRegister[ 'handle_id' ], $this->___getSRCFormatted($_aRegister), $_aRegister[ 'dependencies' ], $_aRegister[ 'version' ], $_aRegister[ 'media' ]);
    }
    private function ___enqueueStyle($asEnqueue)
    {
        $_aEnqueueItem = $this->___getFormattedEnqueueStyle($asEnqueue);
        wp_enqueue_style($_aEnqueueItem[ 'handle_id' ], $_aEnqueueItem[ 'src' ], $_aEnqueueItem[ 'dependencies' ], $_aEnqueueItem[ 'version' ], $_aEnqueueItem[ 'media' ]);
        $_aAddData = array( 'conditional', 'rtl', 'suffix', 'alt', 'title' );
        foreach ($_aAddData as $_sDataKey) {
            if (! isset($aEnqueueItem[ $_sDataKey ])) {
                continue;
            }
            wp_style_add_data($aEnqueueItem[ 'handle_id' ], $_sDataKey, $aEnqueueItem[ $_sDataKey ]);
        }
    }
    private function ___getFormattedEnqueueStyle($asEnqueue)
    {
        static $_iCallCount = 1;
        $_aEnqueueItem = $this->getAsArray($asEnqueue) + array( 'handle_id' => 'admin-page-framework-style-form-' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount, 'src' => null, 'dependencies' => null, 'version' => null, 'media' => null, 'conditional' => null, 'rtl' => null, 'suffix' => null, 'alt' => null, 'title' => null, );
        if (is_string($asEnqueue)) {
            $_aEnqueueItem[ 'src' ] = $asEnqueue;
        }
        $_aEnqueueItem[ 'src' ] = $this->___getSRCFormatted($_aEnqueueItem);
        $_iCallCount++;
        return $_aEnqueueItem;
    }
    private function ___getSRCFormatted(array $aEnqueueItem)
    {
        $_sSRCRaw = wp_normalize_path($aEnqueueItem[ 'src' ]);
        $_sSRC = $this->getResolvedSRC($_sSRCRaw);
        if (! $this->oForm->aArguments[ 'autoload_min_resource' ]) {
            return $_sSRC;
        }
        if ($this->isDebugMode()) {
            return $_sSRC;
        }
        if ($this->isURL($_sSRCRaw)) {
            return $_sSRC;
        }
        $_sMinPrefix = '.min';
        if (false !== stripos($_sSRC, $_sMinPrefix)) {
            return $_sSRC;
        }
        $_aPathParts = pathinfo($_sSRCRaw) + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' );
        if (! $_aPathParts[ 'extension' ]) {
            return $_sSRC;
        }
        $_aPathPartsURL = pathinfo($_sSRC) + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' );
        $_sPathMinifiedVersion = $_aPathParts[ 'dirname' ] . '/' . $_aPathParts[ 'filename' ] . $_sMinPrefix . '.' . $_aPathParts[ 'extension' ];
        return file_exists($_sPathMinifiedVersion) ? $_aPathPartsURL[ 'dirname' ] . '/' . $_aPathPartsURL[ 'filename' ] . $_sMinPrefix . '.' . $_aPathPartsURL[ 'extension' ] : $_sSRC;
    }
    public function _replyToAddStyle()
    {
        if (! $this->oForm->isInThePage()) {
            return;
        }
        $_sCSSRules = $this->___getFormattedInternalStyles($this->oForm->getResources('internal_styles'));
        $_sID = $this->sanitizeSlug(strtolower($this->oForm->aArguments[ 'caller_id' ]));
        if ($_sCSSRules) {
            echo "<style type='text/css' id='internal-style-{$_sID}' class='admin-page-framework-form-style'>" . $_sCSSRules . "</style>";
        }
        $_sIECSSRules = $this->___getFormattedInternalStyles($this->oForm->getResources('internal_styles_ie'));
        if ($_sIECSSRules) {
            echo "<!--[if IE]><style type='text/css' id='internal-style-ie-{$_sID}' class='admin-page-framework-form-ie-style'>" . $_sIECSSRules . "</style><![endif]-->";
        }
        $this->oForm->setResources('internal_styles', array());
        $this->oForm->setResources('internal_styles_ie', array());
    }
    private function ___getFormattedInternalStyles(array $aInternalStyles)
    {
        return trim(implode(PHP_EOL, array_unique($aInternalStyles)));
    }
    public function _replyToAddScript()
    {
        if (! $this->oForm->isInThePage()) {
            return;
        }
        $_sScript = implode(PHP_EOL, array_unique($this->oForm->getResources('internal_scripts')));
        $_sScript = trim($_sScript);
        if ($_sScript) {
            $_sID = $this->sanitizeSlug(strtolower($this->oForm->aArguments[ 'caller_id' ]));
            echo "<script type='text/javascript' id='internal-script-{$_sID}' class='admin-page-framework-form-script'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
        }
        $this->oForm->setResources('internal_scripts', array());
    }
}
