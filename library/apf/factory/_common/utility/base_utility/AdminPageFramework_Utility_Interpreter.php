<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_Interpreter extends AdminPageFramework_Utility_InterpreterHTMLTable {
    public static function getReadableListOfArray(array $aArray)
    {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = self::getReadableArrayContents($_sKey, $_vValue, 32) . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    public static function getReadableArrayContents($sKey, $vValue, $sLabelCharLengths=16, $iOffset=0)
    {
        $_aOutput = array();
        $_aOutput[] = ($iOffset ? str_pad(' ', $iOffset) : '') . ($sKey ? '[' . $sKey . ']' : '');
        if (! in_array(gettype($vValue), array( 'array', 'object' ))) {
            $_aOutput[] = $vValue;
            return implode(PHP_EOL, $_aOutput);
        }
        foreach ($vValue as $_sTitle => $_asDescription) {
            if (! in_array(gettype($_asDescription), array( 'array', 'object' ))) {
                $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . str_pad(':', $sLabelCharLengths - self::getStringLength($_sTitle)) . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . ": {" . self::getReadableArrayContents('', $_asDescription, 16, $iOffset + 4) . PHP_EOL . str_pad(' ', $iOffset) . "}";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    public static function getReadableListOfArrayAsHTML(array $aArray)
    {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>" . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    public static function getReadableArrayContentsHTML($sKey, $vValue)
    {
        $_aOutput = array();
        $_aOutput[] = $sKey ? "<h3 class='array-key'>" . $sKey . "</h3>" : "";
        if (! in_array(gettype($vValue), array( 'array', 'object' ), true)) {
            $_aOutput[] = "<div class='array-value'>" . html_entity_decode(nl2br($vValue), ENT_QUOTES) . "</div>";
            return "<li>" . implode(PHP_EOL, $_aOutput) . "</li>";
        }
        foreach ($vValue as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>";
        }
        return implode(PHP_EOL, $_aOutput) ;
    }
}
