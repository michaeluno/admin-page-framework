<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_UserMeta_Router extends AdminPageFramework_Factory {
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        if (! $this->oProp->bIsAdmin) {
            return;
        }
        $this->oUtil->registerAction($this->oProp->bIsAdminAjax ? 'wp_loaded' : 'current_screen', array( $this, '_replyToDetermineToLoad' ));
        add_action('set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpHooks' ));
    }
    protected function _isInThePage()
    {
        if (! $this->oProp->bIsAdmin) {
            return false;
        }
        if ($this->oProp->bIsAdminAjax) {
            return $this->_isValidAjaxReferrer();
        }
        return in_array($this->oProp->sPageNow, array( 'user-new.php', 'user-edit.php', 'profile.php' ));
    }
    protected function _isValidAjaxReferrer()
    {
        $_aReferrer = parse_url($this->oProp->sAjaxReferrer) + array( 'query' => '', 'path' => '' );
        parse_str($_aReferrer[ 'query' ], $_aQuery);
        $_sBaseName = basename($_aReferrer[ 'path' ]);
        return in_array($_sBaseName, array( 'user-new.php', 'user-edit.php', 'profile.php' ));
    }
    public function _replyToSetUpHooks($oFactory)
    {
        add_action('show_user_profile', array( $this, '_replyToPrintFields' ));
        add_action('edit_user_profile', array( $this, '_replyToPrintFields' ));
        add_action('user_new_form', array( $this, '_replyToPrintFields' ));
        add_action('personal_options_update', array( $this, '_replyToSaveFieldValues' ));
        add_action('edit_user_profile_update', array( $this, '_replyToSaveFieldValues' ));
        add_action('user_register', array( $this, '_replyToSaveFieldValues' ));
        $this->_load();
    }
}
