<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_Array extends AdminPageFramework_Utility_String {
    public static function getArrayMappedRecursive($cCallback, $aArray, array $aArguments=array())
    {
        $_aOutput = array();
        foreach ($aArray as $_isKey => $_vValue) {
            if (is_array($_vValue)) {
                $_aOutput[ $_isKey ] = self::getArrayMappedRecursive($cCallback, $_vValue, $aArguments);
                continue;
            }
            $_aOutput[ $_isKey ] = call_user_func_array($cCallback, array_merge(array( $_vValue ), $aArguments));
        }
        return $_aOutput;
    }
    public static function getUnusedNumericIndex($aArray, $nIndex, $iOffset=1)
    {
        if (! isset($aArray[ $nIndex ])) {
            return $nIndex;
        }
        return self::getUnusedNumericIndex($aArray, $nIndex + $iOffset, $iOffset);
    }
    public static function isAssociative(array $aArray)
    {
        return array_keys($aArray) !== range(0, count($aArray) - 1);
    }
    public static function isLastElement(array $aArray, $sKey)
    {
        end($aArray);
        return $sKey === key($aArray);
    }
    public static function isFirstElement(array $aArray, $sKey)
    {
        reset($aArray);
        return $sKey === key($aArray);
    }
    public static function isMultiDimensional(array $aArray)
    {
        return count($aArray) !== count($aArray, COUNT_RECURSIVE);
    }
    public static function isAssociativeArray(array $aArray)
    {
        return ( boolean ) count(array_filter(array_keys($aArray), 'is_string'));
    }
}
