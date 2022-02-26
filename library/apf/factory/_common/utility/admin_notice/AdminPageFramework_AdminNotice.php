<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_AdminNotice extends AdminPageFramework_FrameworkUtility {
    private static $___aNotices = array();
    public $sNotice = '';
    public $aAttributes = array();
    public $aCallbacks = array( 'should_show' => null, );
    public function __construct($sNotice, array $aAttributes=array( 'class' => 'error' ), array $aCallbacks=array())
    {
        $this->aAttributes = $aAttributes + array( 'class' => 'error', );
        $this->aAttributes[ 'class' ] = $this->getClassAttribute($this->aAttributes[ 'class' ], 'admin-page-framework-settings-notice-message', 'admin-page-framework-settings-notice-container', 'notice', 'is-dismissible');
        $this->aCallbacks = $aCallbacks + $this->aCallbacks;
        new AdminPageFramework_AdminNotice___Script;
        if (! $sNotice) {
            return;
        }
        $this->sNotice = $sNotice;
        self::$___aNotices[ $sNotice ] = $sNotice;
        $this->registerAction('admin_notices', array( $this, '_replyToDisplayAdminNotice' ));
        $this->registerAction('network_admin_notices', array( $this, '_replyToDisplayAdminNotice' ));
    }
    public function _replyToDisplayAdminNotice()
    {
        if (! $this->___shouldProceed()) {
            return;
        }
        $_aAttributes = $this->aAttributes + array( 'style' => '' );
        $_aAttributes[ 'style' ] = $this->getStyleAttribute($_aAttributes[ 'style' ], 'display: none');
        echo "<div " . $this->getAttributes($_aAttributes) . ">" . "<p>" . self::$___aNotices[ $this->sNotice ] . "</p>" . "</div>" . "<noscript>" . "<div " . $this->getAttributes($this->aAttributes) . ">" . "<p>" . self::$___aNotices[ $this->sNotice ] . "</p>" . "</div>" . "</noscript>";
        unset(self::$___aNotices[ $this->sNotice ]);
    }
    private function ___shouldProceed()
    {
        if (! is_callable($this->aCallbacks[ 'should_show' ])) {
            return true;
        }
        return call_user_func_array($this->aCallbacks[ 'should_show' ], array( true, ));
    }
}
