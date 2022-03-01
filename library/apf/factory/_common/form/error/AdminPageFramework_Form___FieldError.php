<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form___FieldError extends AdminPageFramework_FrameworkUtility {
    private static $_aErrors = array();
    public $sCallerID;
    public $sTransientKey;
    public function __construct($sCallerID)
    {
        $this->sCallerID = $sCallerID;
        $this->sTransientKey = $this->_getTransientKey();
    }
    private function _getTransientKey()
    {
        $_sPageSlug = $this->getHTTPQueryGET('page', '');
        $_sTabSlug = $this->getHTTPQueryGET('tab', '');
        $_sUserID = get_current_user_id();
        return "apf_fe_" . md5($this->getPageNow() . $_sPageSlug . $_sTabSlug . $_sUserID);
    }
    public function hasError()
    {
        return isset(self::$_aErrors[ $this->sCallerID ]);
    }
    public function set($aErrors)
    {
        if (empty(self::$_aErrors)) {
            add_action('shutdown', array( $this, '_replyToSave' ));
        }
        self::$_aErrors[ $this->sCallerID ] = isset(self::$_aErrors[ $this->sCallerID ]) ? $this->uniteArrays(self::$_aErrors[ $this->sCallerID ], $aErrors) : $aErrors;
    }
    public function _replyToSave()
    {
        if (empty(self::$_aErrors)) {
            return;
        }
        $this->setTransient($this->sTransientKey, self::$_aErrors, 300);
    }
    public function get()
    {
        self::$_aFieldErrorCaches[ $this->sTransientKey ] = isset(self::$_aFieldErrorCaches[ $this->sTransientKey ]) ? self::$_aFieldErrorCaches[ $this->sTransientKey ] : $this->getTransient($this->sTransientKey);
        return $this->getElementAsArray(self::$_aFieldErrorCaches[ $this->sTransientKey ], $this->sCallerID, array());
    }
    private static $_aFieldErrorCaches = array();
    public function delete()
    {
        if ($this->hasBeenCalled('delete_' . $this->sTransientKey)) {
            return;
        }
        add_action('shutdown', array( $this, '_replyToDelete' ));
    }
    public function _replyToDelete()
    {
        $this->deleteTransient($this->sTransientKey);
    }
}
