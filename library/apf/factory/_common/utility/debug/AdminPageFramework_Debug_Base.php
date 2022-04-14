<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Debug_Base extends AdminPageFramework_Debug_Utility {
    public static $iLegibleArrayDepthLimit = 50;
    public static $iLegibleStringCharacterLimit = 99999;
    protected static function _getLegibleDetails($mValue, $iStringLengthLimit=0, $iArrayDepthLimit=0)
    {
        if (is_array($mValue)) {
            return '(array, length: ' . count($mValue).') ' . self::getAsString(print_r(self::___getLegibleDetailedArray($mValue, $iStringLengthLimit, $iArrayDepthLimit), true));
        }
        return self::getAsString(print_r(self::getLegibleDetailedValue($mValue, $iStringLengthLimit), true));
    }
    protected static function _getLegible($mValue, $iStringLengthLimit=0, $iArrayDepthLimit=0)
    {
        $iArrayDepthLimit = $iArrayDepthLimit ? $iArrayDepthLimit : self::$iLegibleArrayDepthLimit;
        $mValue = is_object($mValue) ? (method_exists($mValue, '__toString') ? ( string ) $mValue : ( array ) $mValue) : $mValue;
        $mValue = is_array($mValue) ? self::getArrayMappedRecursive(array( __CLASS__, 'getObjectName' ), self::getSlicedByDepth($mValue, $iArrayDepthLimit), array()) : $mValue;
        $mValue = is_string($mValue) ? self::___getLegibleString($mValue, $iStringLengthLimit, false) : $mValue;
        return self::getArrayRepresentationSanitized(self::getAsString(print_r($mValue, true)));
    }
    private static function ___getLegibleDetailedCallable($asoCallable)
    {
        return '(callable) ' . self::___getCallableName($asoCallable);
    }
    public static function ___getCallableName($asoCallable)
    {
        if (is_string($asoCallable)) {
            return $asoCallable;
        }
        if (is_object($asoCallable)) {
            return get_class($asoCallable);
        }
        $_sSubject = is_object($asoCallable[ 0 ]) ? get_class($asoCallable[ 0 ]) : ( string ) $asoCallable[ 0 ];
        return $_sSubject . '::' . ( string ) $asoCallable[ 1 ];
    }
    private static function ___getLegibleDetailedObject($oObject)
    {
        if (method_exists($oObject, '__toString')) {
            return ( string ) $oObject;
        }
        return '(object) ' . get_class($oObject) . ' ' . count(get_object_vars($oObject)) . ' properties.';
    }
    private static function ___getLegibleDetailedArray(array $aArray, $iStringLengthLimit=0, $iDepthLimit=0)
    {
        $_iDepthLimit = $iDepthLimit ? $iDepthLimit : self::$iLegibleArrayDepthLimit;
        return self::getArrayMappedRecursive(array( __CLASS__, 'getLegibleDetailedValue' ), self::getSlicedByDepth($aArray, $_iDepthLimit), array( $iStringLengthLimit ));
    }
    public static function getLegibleDetailedValue($mItem, $iStringLengthLimit)
    {
        if (is_callable($mItem)) {
            return self::___getLegibleDetailedCallable($mItem);
        }
        return is_scalar($mItem) ? self::___getLegibleDetailedScalar($mItem, $iStringLengthLimit) : self::___getLegibleDetailedNonScalar($mItem);
    }
    private static function ___getLegibleDetailedNonScalar($mNonScalar)
    {
        $_sType = gettype($mNonScalar);
        if (is_null($mNonScalar)) {
            return '(null)';
        }
        if (is_object($mNonScalar)) {
            return self::___getLegibleDetailedObject($mNonScalar);
        }
        if (is_array($mNonScalar)) {
            return '(' . $_sType . ') ' . count($mNonScalar) . ' elements';
        }
        return '(' . $_sType . ') ' . ( string ) $mNonScalar;
    }
    private static function ___getLegibleDetailedScalar($sScalar, $iStringLengthLimit)
    {
        if (is_bool($sScalar)) {
            return '(boolean) ' . ($sScalar ? 'true' : 'false');
        }
        return is_string($sScalar) ? self::___getLegibleString($sScalar, $iStringLengthLimit, true) : '(' . gettype($sScalar) . ', length: ' . self::___getValueLength($sScalar) . ') ' . $sScalar;
    }
    private static function ___getValueLength($mValue)
    {
        $_sVariableType = gettype($mValue);
        if (in_array($_sVariableType, array( 'string', 'integer' ))) {
            return strlen($mValue);
        }
        if ('array' === $_sVariableType) {
            return count($mValue);
        }
        return null;
    }
    private static function ___getLegibleString($sString, $iLengthLimit, $bShowDetails=true)
    {
        static $_iMBSupport;
        $_iMBSupport = isset($_iMBSupport) ? $_iMBSupport : ( integer ) function_exists('mb_strlen');
        $_aStrLenMethod = array( 'strlen', 'mb_strlen' );
        $_aSubstrMethod = array( 'substr', 'mb_substr' );
        $iCharLimit = $iLengthLimit ? $iLengthLimit : self::$iLegibleStringCharacterLimit;
        $_iCharLength = call_user_func_array($_aStrLenMethod[ $_iMBSupport ], array( $sString ));
        if ($bShowDetails) {
            return $_iCharLength <= $iCharLimit ? '(string, length: ' . $_iCharLength . ') ' . $sString : '(string, length: ' . $_iCharLength . ') ' . call_user_func_array($_aSubstrMethod[ $_iMBSupport ], array( $sString, 0, $iCharLimit )) . '...';
        }
        return $_iCharLength <= $iCharLimit ? $sString : call_user_func_array($_aSubstrMethod[ $_iMBSupport ], array( $sString, 0, $iCharLimit ));
    }
    public static function getStackTrace($iSkip=0, $_deprecated=null)
    {
        $_iSkip = 1;
        $_oException = new Exception();
        if (is_object($iSkip) && $iSkip instanceof Exception) {
            $_oException = $iSkip;
            $iSkip = ( integer ) $_deprecated;
        }
        $_iSkip = $_iSkip + $iSkip;
        $_aTraces = array();
        $_aFrames = $_oException->getTrace();
        $_aFrames = array_slice($_aFrames, $_iSkip);
        foreach (array_reverse($_aFrames) as $_iIndex => $_aFrame) {
            $_aFrame = $_aFrame + array( 'file' => null, 'line' => null, 'function' => null, 'class' => null, 'args' => array(), );
            $_sArguments = self::___getArgumentsOfEachStackTrace($_aFrame[ 'args' ]);
            $_aTraces[] = sprintf("#%s %s(%s): %s(%s)", $_iIndex + 1, $_aFrame[ 'file' ], $_aFrame[ 'line' ], isset($_aFrame[ 'class' ]) ? $_aFrame[ 'class' ] . '->' . $_aFrame[ 'function' ] : $_aFrame[ 'function' ], $_sArguments);
        }
        return implode(PHP_EOL, $_aTraces) . PHP_EOL;
    }
    private static function ___getArgumentsOfEachStackTrace(array $aTraceArguments)
    {
        $_aArguments = array();
        foreach ($aTraceArguments as $_mArgument) {
            $_sType = gettype($_mArgument);
            $_sType = str_replace(array( 'resource (closed)', 'unknown type', 'integer', 'double', ), array( 'resource', 'unknown', 'scalar', 'scalar', ), $_sType);
            $_sMethodName = "___getStackTraceArgument_{$_sType}";
            $_aArguments[] = method_exists(__CLASS__, $_sMethodName) ? self::{$_sMethodName}($_mArgument) : $_sType;
        }
        return join(", ", $_aArguments);
    }
    private static function ___getStackTraceArgument_string($mArgument)
    {
        $_sString = self::___getLegibleString($mArgument, 200, true);
        return "'" . $_sString . "'";
    }
    private static function ___getStackTraceArgument_scalar($mArgument)
    {
        return $mArgument;
    }
    private static function ___getStackTraceArgument_boolean($mArgument)
    {
        return ($mArgument) ? "true" : "false";
    }
    private static function ___getStackTraceArgument_NULL($mArgument)
    {
        return 'NULL';
    }
    private static function ___getStackTraceArgument_object($mArgument)
    {
        return 'Object(' . get_class($mArgument) . ')';
    }
    private static function ___getStackTraceArgument_resource($mArgument)
    {
        return get_resource_type($mArgument);
    }
    private static function ___getStackTraceArgument_unknown($mArgument)
    {
        return gettype($mArgument);
    }
    private static function ___getStackTraceArgument_array($mArgument)
    {
        $_sOutput = '';
        $_iMax = 10;
        $_iTotal = count($mArgument);
        $_iIndex = 0;
        foreach ($mArgument as $_sKey => $_mValue) {
            $_iIndex++;
            $_mValue = is_scalar($_mValue) ? self::___getLegibleDetailedScalar($_mValue, 100) : ucfirst(gettype($_mValue)) . (is_object($_mValue) ? ' (' . get_class($_mValue) . ')' : '');
            $_sOutput .= $_sKey . ': ' . $_mValue . ', ';
            if ($_iIndex > $_iMax && $_iTotal > $_iMax) {
                $_sOutput = rtrim($_sOutput, ',') . '...';
                break;
            }
        }
        $_sOutput = rtrim($_sOutput, ',');
        return "Array({$_sOutput})";
    }
}
