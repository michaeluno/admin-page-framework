<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_PostType_Model extends AdminPageFramework_PostType_Router {
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        add_action("set_up_{$this->oProp->sClassName}", array( $this, '_replyToRegisterPostType' ), 999);
        if ($this->oProp->bIsAdmin) {
            add_action('load_' . $this->oProp->sPostType, array( $this, '_replyToSetUpHooksForModel' ));
            if ($this->oProp->sCallerPath) {
                new AdminPageFramework_PostType_Model__FlushRewriteRules($this);
            }
        }
    }
    public function _replyToSetUpHooksForModel()
    {
        add_filter("manage_{$this->oProp->sPostType}_posts_columns", array( $this, '_replyToSetColumnHeader' ));
        add_filter("manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, '_replyToSetSortableColumns' ));
        add_action("manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, '_replyToPrintColumnCell' ), 10, 2);
        add_action('admin_enqueue_scripts', array( $this, '_replyToDisableAutoSave' ));
        $this->oProp->aColumnHeaders = array( 'cb' => '<input type="checkbox" />', 'title' => $this->oMsg->get('title'), 'author' => $this->oMsg->get('author'), 'comments' => '<div class="comment-grey-bubble"></div>', 'date' => $this->oMsg->get('date'), );
    }
    public function _replyToSetSortableColumns($aColumns)
    {
        return $this->oUtil->getAsArray($this->oUtil->addAndApplyFilter($this, "sortable_columns_{$this->oProp->sPostType}", $aColumns));
    }
    public function _replyToSetColumnHeader($aHeaderColumns)
    {
        return $this->oUtil->getAsArray($this->oUtil->addAndApplyFilter($this, "columns_{$this->oProp->sPostType}", $aHeaderColumns));
    }
    public function _replyToPrintColumnCell($sColumnKey, $iPostID)
    {
        echo $this->oUtil->addAndApplyFilter($this, "cell_{$this->oProp->sPostType}_{$sColumnKey}", '', $iPostID);
    }
    public function _replyToDisableAutoSave()
    {
        if ($this->oProp->bEnableAutoSave) {
            return;
        }
        if ($this->oProp->sPostType != get_post_type()) {
            return;
        }
        wp_dequeue_script('autosave');
    }
    public function _replyToRegisterPostType()
    {
        register_post_type($this->oProp->sPostType, $this->oProp->aPostTypeArgs);
        new AdminPageFramework_PostType_Model__SubMenuOrder($this);
    }
    public function _replyToRegisterTaxonomies()
    {
        foreach ($this->oProp->aTaxonomies as $_sTaxonomySlug => $_aArguments) {
            $this->_registerTaxonomy($_sTaxonomySlug, $this->oUtil->getAsArray($this->oProp->aTaxonomyObjectTypes[ $_sTaxonomySlug ]), $_aArguments);
        }
    }
    public function _registerTaxonomy($sTaxonomySlug, array $aObjectTypes, array $aArguments)
    {
        if (! in_array($this->oProp->sPostType, $aObjectTypes)) {
            $aObjectTypes[] = $this->oProp->sPostType;
        }
        register_taxonomy($sTaxonomySlug, array_unique($aObjectTypes), $aArguments);
        $this->_setCustomMenuOrderForTaxonomy($this->oUtil->getElement($aArguments, 'submenu_order', 15), $sTaxonomySlug);
    }
    private function _setCustomMenuOrderForTaxonomy($nSubMenuOrder, $sTaxonomySlug)
    {
        if (15 == $nSubMenuOrder) {
            return;
        }
        $this->oProp->aTaxonomySubMenuOrder[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = $nSubMenuOrder;
    }
    public function _replyToRemoveTexonomySubmenuPages()
    {
        foreach ($this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug) {
            remove_submenu_page($sTopLevelPageSlug, $sSubmenuPageSlug);
            unset($this->oProp->aTaxonomyRemoveSubmenuPages[ $sSubmenuPageSlug ]);
        }
    }
}
