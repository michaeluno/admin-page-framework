<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_MetaBox_Router extends AdminPageFramework_Factory {
    protected $oResource;
    public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework')
    {
        parent::__construct($this->oProp);
        $this->oProp->sMetaBoxID = $sMetaBoxID ? $this->oUtil->sanitizeSlug($sMetaBoxID) : strtolower($this->oProp->sClassName);
        $this->oProp->sTitle = $sTitle;
        $this->oProp->sContext = $sContext;
        $this->oProp->sPriority = $sPriority;
        if (! $this->oProp->bIsAdmin) {
            return;
        }
        add_action('set_up_' . $this->oProp->sClassName, array( $this, '_replyToCallLoadMethods' ), 100);
        $this->oUtil->registerAction($this->oProp->bIsAdminAjax ? 'wp_loaded' : 'current_screen', array( $this, '_replyToDetermineToLoad' ));
    }
    public function _replyToCallLoadMethods()
    {
        $this->_load();
    }
    protected function _isInThePage()
    {
        if ($this->_isValidAjaxReferrer()) {
            return true;
        }
        if (! in_array($this->oProp->sPageNow, array( 'post.php', 'post-new.php' ))) {
            return false;
        }
        if (! in_array($this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes)) {
            return false;
        }
        return true;
    }
    protected function _isValidAjaxReferrer()
    {
        if (! $this->oProp->bIsAdminAjax) {
            return false;
        }
        $_aReferrer = parse_url($this->oProp->sAjaxReferrer) + array( 'query' => '', 'path' => '' );
        parse_str($_aReferrer[ 'query' ], $_aQuery);
        $_sBaseName = basename($_aReferrer[ 'path' ]);
        if (! in_array($_sBaseName, array( 'post.php', 'post-new.php' ))) {
            return false;
        }
        $_iPost = $this->oUtil->getElement($_aQuery, array( 'post' ), 0);
        $_sPostType = $this->oUtil->getElement($_aQuery, array( 'post_type' ), '');
        $_sPostType = $_sPostType ? $_sPostType : get_post_type($_iPost);
        return in_array($_sPostType, $this->oProp->aPostTypes);
    }
    protected function _isInstantiatable()
    {
        return true;
    }
}
