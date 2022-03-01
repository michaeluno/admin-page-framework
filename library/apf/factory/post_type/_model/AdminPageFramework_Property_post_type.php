<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Property_post_type extends AdminPageFramework_Property_Base {
    public $_sPropertyType = 'post_type';
    public $sPostType = '';
    public $aPostTypeArgs = array();
    public $sClassName = '';
    public $aColumnHeaders = array( 'cb' => '<input type="checkbox" />', 'title' => 'Title', 'author' => 'Author', 'comments' => '<div class="comment-grey-bubble"></div>', 'date' => 'Date', );
    public $aColumnSortable = array( 'title' => true, 'date' => true, );
    public $sCallerPath = '';
    public $aTaxonomies;
    public $aTaxonomyObjectTypes = array();
    public $aTaxonomyTableFilters = array();
    public $aTaxonomyRemoveSubmenuPages = array();
    public $bEnableAutoSave = true;
    public $bEnableAuthorTableFileter = false;
    public $aTaxonomySubMenuOrder = array();
}
