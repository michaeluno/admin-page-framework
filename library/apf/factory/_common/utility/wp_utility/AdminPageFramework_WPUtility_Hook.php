<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_Hook extends AdminPageFramework_WPUtility_Page {
    public static function registerAction($sActionHook, $oCallable, $iPriority=10)
    {
        if (did_action($sActionHook)) {
            return call_user_func_array($oCallable, array());
        }
        add_action($sActionHook, $oCallable, $iPriority);
    }
    public static function doActions($aActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null)
    {
        $aArgs = func_get_args();
        $aActionHooks = $aArgs[ 0 ];
        foreach (( array ) $aActionHooks as $sActionHook) {
            $aArgs[ 0 ] = $sActionHook;
            call_user_func_array('do_action', $aArgs);
        }
    }
    public static function addAndDoActions()
    {
        $aArgs = func_get_args();
        $oCallerObject = $aArgs[ 0 ];
        $aActionHooks = $aArgs[ 1 ];
        foreach (( array ) $aActionHooks as $sActionHook) {
            if (! $sActionHook) {
                continue;
            }
            $aArgs[ 1 ] = $sActionHook;
            call_user_func_array(array( get_class(), 'addAndDoAction' ), $aArgs);
        }
    }
    public static function addAndDoAction()
    {
        $_iArgs = func_num_args();
        $_aArgs = func_get_args();
        $_oCallerObject = $_aArgs[ 0 ];
        $_sActionHook = $_aArgs[ 1 ];
        if (! $_sActionHook) {
            return;
        }
        $_sAutoCallbackMethodName = str_replace('\\', '_', $_sActionHook);
        if (method_exists($_oCallerObject, $_sAutoCallbackMethodName)) {
            add_action($_sActionHook, array( $_oCallerObject, $_sAutoCallbackMethodName ), 10, $_iArgs - 2);
        }
        array_shift($_aArgs);
        call_user_func_array('do_action', $_aArgs);
    }
    public static function addAndApplyFilters()
    {
        $_aArgs = func_get_args();
        $_aFilters = $_aArgs[ 1 ];
        $_vInput = $_aArgs[ 2 ];
        foreach (( array ) $_aFilters as $_sFilter) {
            if (! $_sFilter) {
                continue;
            }
            $_aArgs[ 1 ] = $_sFilter;
            $_aArgs[ 2 ] = $_vInput;
            $_vInput = call_user_func_array(array( get_class(), 'addAndApplyFilter' ), $_aArgs);
        }
        return $_vInput;
    }
    public static function addAndApplyFilter()
    {
        $_iArgs = func_num_args();
        $_aArgs = func_get_args();
        $_oCallerObject = $_aArgs[ 0 ];
        $_sFilter = $_aArgs[ 1 ];
        if (! $_sFilter) {
            return $_aArgs[ 2 ];
        }
        $_sAutoCallbackMethodName = str_replace('\\', '_', $_sFilter);
        if (method_exists($_oCallerObject, $_sAutoCallbackMethodName)) {
            add_filter($_sFilter, array( $_oCallerObject, $_sAutoCallbackMethodName ), 10, $_iArgs - 2);
        }
        array_shift($_aArgs);
        return call_user_func_array('apply_filters', $_aArgs);
    }
    public static function getFilterArrayByPrefix($sPrefix, $sClassName, $sPageSlug, $sTabSlug, $bReverse=false)
    {
        $_aFilters = array();
        if ($sTabSlug && $sPageSlug) {
            $_aFilters[] = "{$sPrefix}{$sPageSlug}_{$sTabSlug}";
        }
        if ($sPageSlug) {
            $_aFilters[] = "{$sPrefix}{$sPageSlug}";
        }
        if ($sClassName) {
            $_aFilters[] = "{$sPrefix}{$sClassName}";
        }
        return $bReverse ? array_reverse($_aFilters) : $_aFilters;
    }
}
