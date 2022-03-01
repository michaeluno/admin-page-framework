<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_PostType_Controller extends AdminPageFramework_PostType_View {
    public function setUp()
    {}
    public function load()
    {}
    public function enqueueStyles()
    {
        $_aParams = func_get_args() + array( array(), array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'aPostTypes' => array( $this->oProp->sPostType ), ) + $_aParams[ 1 ], 'style');
    }
    public function enqueueStyle()
    {
        $_aParams = func_get_args() + array( '', array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'aPostTypes' => array( $this->oProp->sPostType ), ) + $_aParams[ 1 ], 'style');
    }
    public function enqueueScripts()
    {
        $_aParams = func_get_args() + array( array(), array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'aPostTypes' => array( $this->oProp->sPostType ), ) + $_aParams[ 1 ], 'script');
    }
    public function enqueueScript()
    {
        $_aParams = func_get_args() + array( '', array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'aPostTypes' => array( $this->oProp->sPostType ), ) + $_aParams[ 1 ], 'script');
    }
    protected function setAutoSave($bEnableAutoSave=true)
    {
        $this->oProp->bEnableAutoSave = $bEnableAutoSave;
    }
    protected function addTaxonomy($sTaxonomySlug, array $aArguments, array $aAdditionalObjectTypes=array())
    {
        $sTaxonomySlug = $this->oUtil->sanitizeSlug($sTaxonomySlug);
        $aArguments = $aArguments + array( 'show_table_filter' => null, 'show_in_sidebar_menus' => null, 'submenu_order' => 15, ) ;
        $this->oProp->aTaxonomies[ $sTaxonomySlug ] = $aArguments;
        if ($aArguments[ 'show_table_filter' ]) {
            $this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug;
        }
        if (! $aArguments[ 'show_in_sidebar_menus' ]) {
            $this->oProp->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = "edit.php?post_type={$this->oProp->sPostType}";
        }
        $_aExistingObjectTypes = $this->oUtil->getElementAsArray($this->oProp->aTaxonomyObjectTypes, $sTaxonomySlug, array());
        $aAdditionalObjectTypes = array_merge($_aExistingObjectTypes, $aAdditionalObjectTypes);
        $this->oProp->aTaxonomyObjectTypes[ $sTaxonomySlug ] = array_unique($aAdditionalObjectTypes);
        $this->_addTaxonomy_setUpHooks($sTaxonomySlug, $aArguments, $aAdditionalObjectTypes);
    }
    private function _addTaxonomy_setUpHooks($sTaxonomySlug, array $aArguments, array $aAdditionalObjectTypes)
    {
        if (did_action('init')) {
            $this->_registerTaxonomy($sTaxonomySlug, $aAdditionalObjectTypes, $aArguments);
        } else {
            add_action('init', array( $this, '_replyToRegisterTaxonomies' ));
        }
        $this->oUtil->registerAction('admin_menu', array( $this, '_replyToRemoveTexonomySubmenuPages' ), 999);
    }
    protected function setAuthorTableFilter($bEnableAuthorTableFileter=false)
    {
        $this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
    }
    protected function setPostTypeArgs($aArgs)
    {
        $this->setArguments(( array ) $aArgs);
    }
    protected function setArguments(array $aArguments=array())
    {
        $this->oProp->aPostTypeArgs = $aArguments;
    }
}
