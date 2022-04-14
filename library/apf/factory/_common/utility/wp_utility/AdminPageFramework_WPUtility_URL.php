<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_URL extends AdminPageFramework_Utility {
    private static $___aGET;
    public static function getHTTPQueryGET($asKeys=array(), $mDefault=null)
    {
        self::$___aGET = isset(self::$___aGET) ? self::$___aGET : self::getArrayMappedRecursive('sanitize_text_field', $_GET);
        if (empty($asKeys)) {
            return self::$___aGET;
        }
        return self::getElement(self::$___aGET, $asKeys, $mDefault);
    }
    public static function getCurrentAdminURL()
    {
        $sRequestURI = $GLOBALS[ 'is_IIS' ] ? $_SERVER[ 'PATH_INFO' ] : $_SERVER[ "REQUEST_URI" ];
        $sPageURL = 'on' == @$_SERVER[ "HTTPS" ] ? "https://" : "http://";
        if ("80" != $_SERVER[ "SERVER_PORT" ]) {
            $sPageURL .= $_SERVER[ "SERVER_NAME" ] . ":" . $_SERVER[ "SERVER_PORT" ] . $sRequestURI;
        } else {
            $sPageURL .= $_SERVER[ "SERVER_NAME" ] . $sRequestURI;
        }
        return $sPageURL;
    }
    public static function getQueryAdminURL($aAddingQueries=array(), $aRemovingQueryKeys=array(), $sSubjectURL='')
    {
        $_sAdminURL = is_network_admin() ? network_admin_url(AdminPageFramework_WPUtility_Page::getPageNow()) : admin_url(AdminPageFramework_WPUtility_Page::getPageNow());
        $sSubjectURL = $sSubjectURL ? $sSubjectURL : add_query_arg(self::getHTTPQueryGET(), $_sAdminURL);
        return self::getQueryURL($aAddingQueries, $aRemovingQueryKeys, $sSubjectURL);
    }
    public static function getQueryURL($aAddingQueries, $aRemovingQueryKeys, $sSubjectURL)
    {
        $sSubjectURL = empty($aRemovingQueryKeys) ? $sSubjectURL : remove_query_arg(( array ) $aRemovingQueryKeys, $sSubjectURL);
        $sSubjectURL = add_query_arg($aAddingQueries, $sSubjectURL);
        return $sSubjectURL;
    }
    public static function getSRCFromPath($sFilePath)
    {
        $sFilePath = str_replace('\\', '/', $sFilePath);
        $_sContentDirPath = str_replace('\\', '/', WP_CONTENT_DIR);
        if (false !== strpos($sFilePath, $_sContentDirPath)) {
            $_sRelativePath = AdminPageFramework_Utility::getRelativePath(WP_CONTENT_DIR, $sFilePath);
            $_sRelativePath = preg_replace("/^\.[\/\\\]/", '', $_sRelativePath, 1);
            return content_url($_sRelativePath);
        }
        $_sRelativePath = AdminPageFramework_Utility::getRelativePath(ABSPATH, $sFilePath);
        $_sRelativePath = preg_replace("/^\.[\/\\\]/", '', $_sRelativePath, 1);
        return trailingslashit(get_bloginfo('url')) . $_sRelativePath;
    }
    public static function getResolvedSRC($sSRC, $bReturnNullIfNotExist=false)
    {
        if (! self::isResourcePath($sSRC)) {
            return $bReturnNullIfNotExist ? null : $sSRC;
        }
        if (filter_var($sSRC, FILTER_VALIDATE_URL)) {
            return $sSRC;
        }
        if (file_exists(realpath($sSRC))) {
            return self::getSRCFromPath($sSRC);
        }
        if ($bReturnNullIfNotExist) {
            return null;
        }
        return $sSRC;
    }
    public static function resolveSRC($sSRC, $bReturnNullIfNotExist=false)
    {
        return self::getResolvedSRC($sSRC, $bReturnNullIfNotExist);
    }
}
