<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_Option extends AdminPageFramework_WPUtility_File {
    public static function isNetworkAdmin()
    {
        if (isset(self::$_bIsNetworkAdmin)) {
            return self::$_bIsNetworkAdmin;
        }
        self::$_bIsNetworkAdmin = is_network_admin();
        return self::$_bIsNetworkAdmin;
    }
    public static function cleanTransients($asPrefixes=array( 'apf' ))
    {
        $_aPrefixes = self::getAsArray($asPrefixes);
        if (self::isNetworkAdmin()) {
            self::cleanTransientsForNetwork($asPrefixes);
            return;
        }
        $_oWPDB = $GLOBALS[ 'wpdb' ];
        foreach ($_aPrefixes as $_sPrefix) {
            $_sSQLQuery = "DELETE FROM `{$_oWPDB->options}` " . "WHERE `option_name` " . "LIKE ( '_transient_%{$_sPrefix}%' )";
            $_oWPDB->query($_sSQLQuery);
        }
    }
    public static function cleanTransientsForNetwork($asPrefixes)
    {
        $_aPrefixes = self::getAsArray($asPrefixes);
        $_oWPDB = $GLOBALS[ 'wpdb' ];
        foreach ($_aPrefixes as $_sPrefix) {
            $_sSQLQuery = "DELETE FROM `{$_oWPDB->sitemeta}` " . "WHERE " . "`meta_key` LIKE ( '_site_transient_%{$_sPrefix}%' )";
            $_oWPDB->query($_sSQLQuery);
        }
    }
    public static function getTransientAsArray($sTransientKey, $mDefault=null)
    {
        return self::getAsArray(self::getTransient($sTransientKey, $mDefault));
    }
    public static function getTransientWithoutCacheAsArray($sTransientKey, $mDefault=null)
    {
        return self::getAsArray(self::getTransientWithoutCache($sTransientKey, $mDefault));
    }
    public static function getTransientWithoutCache($sTransientKey, $mDefault=null)
    {
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey);
        if (self::isNetworkAdmin()) {
            return self::getTransientWithoutCacheForNetwork($sTransientKey, $mDefault);
        }
        $_oWPDB = $GLOBALS[ 'wpdb' ];
        $_sTableName = $_oWPDB->options;
        $_sSQLQuery = "SELECT o1.option_value FROM `{$_sTableName}` o1" . " INNER JOIN `{$_sTableName}` o2" . " WHERE o1.option_name = %s " . " AND o2.option_name = %s " . " AND o2.option_value >= UNIX_TIMESTAMP() " . " LIMIT 1";
        $_mData = $_oWPDB->get_var($_oWPDB->prepare($_sSQLQuery, '_transient_' . $sTransientKey, '_transient_timeout_' . $sTransientKey));
        return is_null($_mData) ? $mDefault : maybe_unserialize($_mData);
    }
    public static function getTransientWithoutCacheForNetwork($sTransientKey, $mDefault)
    {
        $_oWPDB = $GLOBALS[ 'wpdb' ];
        $_sSQLQuery = "SELECT o1.meta_value FROM `{$_oWPDB->sitemeta}` o1" . " INNER JOIN `{$_oWPDB->sitemeta}` o2" . " WHERE o1.meta_key = %s " . " AND o2.meta_key = %s " . " AND o2.site_id = %d " . " AND o2.meta_value >= UNIX_TIMESTAMP() " . " LIMIT 1";
        $_mData = $_oWPDB->get_var($_oWPDB->prepare($_sSQLQuery, '_site_transient_' . $sTransientKey, '_site_transient_timeout_' . $sTransientKey, get_current_network_id()));
        return is_null($_mData) ? $mDefault : maybe_unserialize($_mData);
    }
    private static $_bIsNetworkAdmin;
    public static function deleteTransient($sTransientKey)
    {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey);
        $_aFunctionNames = array( 0 => 'delete_transient', 1 => 'delete_site_transient', );
        $_vTransient = $_aFunctionNames[ ( integer ) self::isNetworkAdmin() ]($sTransientKey);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return $_vTransient;
    }
    public static function getTransient($sTransientKey, $vDefault=null)
    {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey);
        $_aFunctionNames = array( 0 => 'get_transient', 1 => 'get_site_transient', );
        $_vTransient = $_aFunctionNames[ ( integer ) self::isNetworkAdmin() ]($sTransientKey);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return null === $vDefault ? $_vTransient : (false === $_vTransient ? $vDefault : $_vTransient);
    }
    public static function setTransient($sTransientKey, $vValue, $iExpiration=0)
    {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey);
        $_aFunctionNames = array( 0 => 'set_transient', 1 => 'set_site_transient', );
        $_bIsSet = $_aFunctionNames[ ( integer ) self::isNetworkAdmin() ]($sTransientKey, $vValue, $iExpiration);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return $_bIsSet;
    }
    public static function _getCompatibleTransientKey($sSubject, $iDeprecated=null)
    {
        $_iAllowedCharacterLength = isset($iDeprecated) ? $iDeprecated : (self::isNetworkAdmin() ? 40 : 45);
        if (strlen($sSubject) <= $_iAllowedCharacterLength) {
            return $sSubject;
        }
        $_iPrefixLengthToKeep = $_iAllowedCharacterLength - 33;
        $_sPrefixToKeep = substr($sSubject, 0, $_iPrefixLengthToKeep - 1);
        return $_sPrefixToKeep . '_' . md5($sSubject);
    }
    public static function getOption($sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array())
    {
        return self::_getOptionByFunctionName($sOptionKey, $asKey, $vDefault, $aAdditionalOptions);
    }
    public static function getSiteOption($sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array())
    {
        return self::_getOptionByFunctionName($sOptionKey, $asKey, $vDefault, $aAdditionalOptions, 'get_site_option');
    }
    private static function _getOptionByFunctionName($sOptionKey, $asKey=null, $vDefault=null, array $aAdditionalOptions=array(), $sFunctionName='get_option')
    {
        if (! isset($asKey)) {
            $_aOptions = $sFunctionName($sOptionKey, isset($vDefault) ? $vDefault : array());
            ;
            return empty($aAdditionalOptions) ? $_aOptions : self::uniteArrays($_aOptions, $aAdditionalOptions);
        }
        return self::getArrayValueByArrayKeys(self::uniteArrays(self::getAsArray($sFunctionName($sOptionKey, array()), true), $aAdditionalOptions), self::getAsArray($asKey, true), $vDefault);
    }
}
