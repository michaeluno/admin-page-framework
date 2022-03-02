<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_ArrayGetter extends AdminPageFramework_Utility_Array {
    public static function getFirstElement(array $aArray)
    {
        foreach ($aArray as $_mElement) {
            return $_mElement;
        }
        return null;
    }
    public static function getElement($aSubject, $aisKey, $mDefault=null, $asToDefault=array( null ))
    {
        $_aToDefault = is_null($asToDefault) ? array( null ) : self::getAsArray($asToDefault, true);
        $_mValue = self::getArrayValueByArrayKeys($aSubject, self::getAsArray($aisKey, true), $mDefault);
        return in_array($_mValue, $_aToDefault, true) ? $mDefault : $_mValue;
    }
    public static function getElementAsArray($aSubject, $aisKey, $mDefault=null, $asToDefault=array( null ))
    {
        return self::getAsArray(self::getElement($aSubject, $aisKey, $mDefault, $asToDefault), true);
    }
    public static function getIntegerKeyElements(array $aParse)
    {
        foreach ($aParse as $_isKey => $_v) {
            if (! is_numeric($_isKey)) {
                unset($aParse[ $_isKey ]);
                continue;
            }
            $_isKey = $_isKey + 0;
            if (! is_int($_isKey)) {
                unset($aParse[ $_isKey ]);
            }
        }
        return $aParse;
    }
    public static function getNonIntegerKeyElements(array $aParse)
    {
        foreach ($aParse as $_isKey => $_v) {
            if (is_numeric($_isKey) && is_int($_isKey+ 0)) {
                unset($aParse[ $_isKey ]);
            }
        }
        return $aParse;
    }
    public static function getArrayValueByArrayKeys($aArray, $aKeys, $vDefault=null)
    {
        $_sKey = array_shift($aKeys);
        if (isset($aArray[ $_sKey ])) {
            if (empty($aKeys)) {
                return $aArray[ $_sKey ];
            }
            if (is_array($aArray[ $_sKey ])) {
                return self::getArrayValueByArrayKeys($aArray[ $_sKey ], $aKeys, $vDefault);
            }
            return $vDefault;
        }
        return $vDefault;
    }
    public static function getAsArray($mValue, $bPreserveEmpty=false)
    {
        if (is_array($mValue)) {
            return $mValue;
        }
        if ($bPreserveEmpty) {
            return ( array ) $mValue;
        }
        if (empty($mValue)) {
            return array();
        }
        return ( array ) $mValue;
    }
    public static function getArrayElementsByKeys(array $aSubject, array $aKeys)
    {
        return array_intersect_key($aSubject, array_flip($aKeys));
    }
}
