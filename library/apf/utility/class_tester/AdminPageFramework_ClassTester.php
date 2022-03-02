<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_ClassTester {
    public static function getInstance($sClassName, array $aParameters=array())
    {
        $_oReflection = new ReflectionClass($sClassName);
        return $_oReflection->newInstanceArgs($aParameters);
    }
    public static function call($oClass, $sMethodName, $aParameters)
    {
        if (version_compare(phpversion(), '<', '5.3.0')) {
            trigger_error('Program Name' . ': ' . sprintf('The method cannot run with your PHP version: %1$s', phpversion()), E_USER_WARNING);
            return;
        }
        $_sClassName = get_class($oClass);
        $_oMethod = self::_getMethod($_sClassName, $sMethodName);
        return $_oMethod->invokeArgs($oClass, $aParameters);
    }
    private static function _getMethod($sClassName, $sMethodName)
    {
        $_oClass = new ReflectionClass($sClassName);
        $_oMethod = $_oClass->getMethod($sMethodName);
        $_oMethod->setAccessible(true);
        return $_oMethod;
    }
}
