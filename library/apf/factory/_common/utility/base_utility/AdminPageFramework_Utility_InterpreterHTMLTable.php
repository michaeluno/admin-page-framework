<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_InterpreterHTMLTable extends AdminPageFramework_Utility_HTMLAttribute {
    public static function getTableOfKeyValues(array $aArray, array $aAllAttributes=array(), array $aHeader=array(), array $aFooter=array(), $bEscape=true, $sCaption='')
    {
        $_aAllAttributes = $aAllAttributes + array( 'table' => array(), 'caption' => array(), 'tbody' => array(), 'td' => array( array(), array(), ), 'tr' => array(), 't' => array(), 'ul' => array(), 'li' => array(), 'p' => array(), );
        return "<table " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'table')) . ">" . self::___getTableCaption($sCaption, $_aAllAttributes, $bEscape) . self::___getTableHeaderOfKeyValuePair($aHeader, $aAllAttributes, $bEscape) . "<tbody " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'tbody')) . ">" . self::___getRowsOfKeyValuePair($aArray, $aAllAttributes, $bEscape) . "</tbody>" . self::___getTableFooterOfKeyValuePair($aFooter, $aAllAttributes, $bEscape) . "</table>";
    }
    public static function getTableOfArray(array $aArray, array $aAllAttributes=array(), array $aHeader=array(), array $aFooter=array(), $bEscape=true, $sCaption='')
    {
        $_aAllAttributes = $aAllAttributes + array( 'table' => array(), 'caption' => array(), 'tbody' => array(), 'td' => array( array(), array(), ), 'tr' => array(), 't' => array(), 'ul' => array(), 'li' => array(), 'p' => array(), );
        return "<table " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'table')) . ">" . self::___getTableCaption($sCaption, $_aAllAttributes, $bEscape) . self::___getTableHeader($aHeader, $_aAllAttributes, $bEscape) . "<tbody " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'tbody')) . ">" . self::___getTableRows($aArray, $_aAllAttributes, $bEscape) . "</tbody>" . self::___getTableFooter($aFooter, $_aAllAttributes, $bEscape) . "</table>";
    }
    private static function ___getTableCaption($sCaption, $aAllAttributes, $bEscape)
    {
        $sCaption = ( string ) $sCaption;
        if (! strlen($sCaption)) {
            return '';
        }
        $_aCapAttr = self::getElementAsArray($aAllAttributes, 'caption');
        $_sCaption = $bEscape ? htmlspecialchars($sCaption) : $sCaption;
        return "<caption " . self::getAttributes($_aCapAttr) . ">" . $_sCaption . "</caption>";
    }
    private static function ___getHTMLEscaped($sOutput, $bEscape)
    {
        return $bEscape ? htmlspecialchars($sOutput) : $sOutput;
    }
    private static function ___getTableHeader(array $aHeader, array $aAllAttributes, $bEscape)
    {
        if (empty($aHeader)) {
            return '';
        }
        return self::isAssociative($aHeader) ? self::___getTableHeaderOfKeyValuePair($aHeader, $aAllAttributes, $bEscape) : self::___getTableHeaderOfMultiColumns($aHeader, $aAllAttributes, $bEscape);
    }
    private static function ___getTableHeaderOfKeyValuePair(array $aHeader, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTRAttr[ 'class' ] = self::getClassAttribute('key-value', self::getElement($_aTRAttr, array( 'class' ), ''));
        $_aTHAttr = self::getElementAsArray($aAllAttributes, 'th');
        $_aTHAttr1 = self::getElementAsArray($aAllAttributes, array( 'th', 0 )) + $_aTHAttr;
        $_aTHAttr2 = self::getElementAsArray($aAllAttributes, array( 'th', 1 )) + $_aTHAttr;
        $_sOutput = '';
        foreach ($aHeader as $_sKey => $_sValue) {
            $_sOutput .= "<tr " . self::getAttributes($_aTRAttr) . ">" . "<th " . self::getAttributes($_aTHAttr1) . ">" . self::___getHTMLEscaped($_sKey, $bEscape) . "</th>" . "<th " . self::getAttributes($_aTHAttr2) . ">" . self::___getHTMLEscaped($_sValue, $bEscape) . "</th>" . "</tr>";
        }
        return "<thead>" . $_sOutput . "</thead>";
    }
    private static function ___getTableHeaderOfMultiColumns(array $aHeader, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTHAttr = self::getElementAsArray($aAllAttributes, 'th');
        $_sOutput = "<tr " . self::getAttributes($_aTRAttr) . ">";
        foreach (array_values($aHeader) as $_iIndex => $_sColumnName) {
            $_aTHAttrNth = self::getElementAsArray($aAllAttributes, array( 'th', $_iIndex )) + $_aTHAttr;
            $_sOutput .= "<th " . self::getAttributes($_aTHAttrNth) . ">" . self::___getHTMLEscaped(( string ) $_sColumnName, $bEscape) . "</th>";
        }
        $_sOutput .= "</tr>";
        return "<thead>" . $_sOutput . "</thead>";
    }
    private static function ___getTableFooter(array $aFooter, array $aAllAttributes, $bEscape)
    {
        if (empty($aFooter)) {
            return '';
        }
        return self::isAssociative($aFooter) ? self::___getTableFooterOfKeyValuePair($aFooter, $aAllAttributes, $bEscape) : self::___getTableFooterOfMultiColumns($aFooter, $aAllAttributes, $bEscape);
    }
    private static function ___getTableFooterOfKeyValuePair(array $aFooter, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTRAttr[ 'class' ] = self::getClassAttribute('key-value', self::getElement($_aTRAttr, array( 'class' ), ''));
        $_aTDAttr1 = self::getElementAsArray($aAllAttributes, array( 'td', 0 )) + $_aTDAttr;
        $_aTDAttr2 = self::getElementAsArray($aAllAttributes, array( 'td', 1 )) + $_aTDAttr;
        $_sOutput = '';
        foreach ($aFooter as $_sKey => $_sValue) {
            $_sOutput = "<tr " . self::getAttributes($_aTRAttr) . ">" . "<td " . self::getAttributes($_aTDAttr1) . ">" . self::___getHTMLEscaped($_sKey, $bEscape) . "</td>" . "<td " . self::getAttributes($_aTDAttr2) . ">" . self::___getHTMLEscaped($_sValue, $bEscape) . "</td>" . "</tr>";
        }
        return "<tfoot>" . $_sOutput . "</tfoot>";
    }
    private static function ___getTableFooterOfMultiColumns(array $aFooter, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_sOutput = "<tr " . self::getAttributes($_aTRAttr) . ">";
        foreach (array_values($aFooter) as $_iIndex => $_sColumnName) {
            $_aTDAttrNth = self::getElementAsArray($aAllAttributes, array( 'td', $_iIndex )) + $_aTDAttr;
            $_sOutput .= "<td " . self::getAttributes($_aTDAttrNth) . ">" . self::___getHTMLEscaped(( string ) $_sColumnName, $bEscape) . "</td>";
        }
        $_sOutput .= "</tr>";
        return "<tfoot>" . $_sOutput . "</tfoot>";
    }
    private static function ___getTableRows(array $aArray, array $aAllAttributes, $bEscape)
    {
        if (empty($aArray)) {
            return '';
        }
        return self::___shouldKeyValuePair($aArray) ? self::___getRowsOfKeyValuePair($aArray, $aAllAttributes, $bEscape) : self::___getRowsOfMultiColumns($aArray, $aAllAttributes, $bEscape);
    }
    private static function ___shouldKeyValuePair(array $aArray)
    {
        if (self::isAssociative($aArray)) {
            return true;
        }
        $_aFirstItem = self::getAsArray(self::getFirstElement($aArray));
        if (self::isAssociative($_aFirstItem) || self::isMultiDimensional($_aFirstItem)) {
            return true;
        }
        return false;
    }
    private static function ___getRowsOfKeyValuePair(array $aItem, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTRAttr[ 'class' ] = self::getClassAttribute('key-value', self::getElement($_aTRAttr, array( 'class' ), ''));
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTDAttr = array_filter($_aTDAttr, 'is_scalar');
        $_aPAttr = self::getElementAsArray($aAllAttributes, array( 'p' ));
        $_aTDAttrFirst = self::getElementAsArray($aAllAttributes, array( 'td', 0 )) + $_aTDAttr;
        $_aTDAttrFirst[ 'class' ] = self::getClassAttribute('column-key', self::getElement($_aTDAttrFirst, array( 'class' ), ''));
        $_sOutput = '';
        foreach ($aItem as $_sColumnName => $_asValue) {
            $_sOutput .= "<tr " . self::getAttributes($_aTRAttr) . ">";
            $_sOutput .= "<td " . self::getAttributes($_aTDAttrFirst) . ">" . "<p " . self::getAttributes($_aPAttr) . ">" . self::___getHTMLEscaped($_sColumnName, $bEscape) . "</p>" . "</td>";
            $_sOutput .= self::___getColumnValue($_asValue, $aAllAttributes, $bEscape, 1);
            $_sOutput .= "</tr>";
        }
        return $_sOutput;
    }
    private static function ___getRowsOfMultiColumns(array $aArray, array $aAllAttributes, $bEscape)
    {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_sOutput = '';
        foreach ($aArray as $_iRowIndex => $_asValue) {
            if (is_scalar($_asValue)) {
                $_sOutput .= "<tr " . self::getAttributes($_aTRAttr) . ">" . self::___getColumnValue($_asValue, $aAllAttributes, $bEscape, 0) . "</tr>";
                continue;
            }
            $_aColumns = self::getAsArray($_asValue);
            $_sOutput .= "<tr " . self::getAttributes($_aTRAttr) . ">" . self::___getColumns($_aColumns, $aAllAttributes, $bEscape) . "</tr>";
        }
        return $_sOutput;
    }
    private static function ___getColumns(array $aColumns, $aAllAttributes, $bEscape)
    {
        $_sOutput = '';
        foreach (array_values($aColumns) as $_iIndex => $_asValue) {
            $_sOutput .= self::___getColumnValue($_asValue, $aAllAttributes, $bEscape, $_iIndex);
        }
        return $_sOutput;
    }
    private static function ___getColumnValue($mValue, array $aAllAttributes, $bEscape, $iColumnIndex)
    {
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTDAttr = array_filter($_aTDAttr, 'is_scalar');
        $_aTDAttrNth = self::getElementAsArray($aAllAttributes, array( 'td', $iColumnIndex )) + $_aTDAttr;
        $_aTDAttrNth[ 'class' ] = self::getClassAttribute('column-value', self::getElement($_aTDAttrNth, array( 'class' ), ''));
        if (is_null($mValue)) {
            $mValue = '(null)';
        }
        $_aPAttr = self::getElementAsArray($aAllAttributes, 'p');
        if (is_scalar($mValue)) {
            return "<td " . self::getAttributes($_aTDAttrNth) . ">" . "<p " . self::getAttributes($_aPAttr) . ">" . self::___getHTMLEscaped($mValue, $bEscape) . "</p>" . "</td>";
        }
        if (is_array($mValue)) {
            return self::isAssociativeArray($mValue) || self::isMultiDimensional($mValue) ? "<td " . self::getAttributes($_aTDAttrNth) . ">" . self::getTableOfKeyValues($mValue, $aAllAttributes) . "</td>" : "<td " . self::getAttributes($_aTDAttrNth) . ">" . self::___getList($mValue, $aAllAttributes, $bEscape) . "</td>";
        }
        return "<td " . self::getAttributes($_aTDAttrNth) . ">" . '(' . gettype($mValue) . ')' . (is_object($mValue) ? get_class($mValue) : '') . "</td>";
    }
    private static function ___getList(array $aArray, $aAllAttributes, $bEscape)
    {
        $_aULAttr = self::getElementAsArray($aAllAttributes, 'ul');
        $_aLIAttr = self::getElementAsArray($aAllAttributes, 'li');
        $_aULAttr[ 'class' ] = self::getClassAttribute('numeric', self::getElement($_aULAttr, array( 'class' ), ''));
        if (empty($aArray)) {
            return '';
        }
        $_sList = "<ul " . self::getAttributes($_aULAttr) . ">";
        foreach ($aArray as $_asValue) {
            $_sItem = is_array($_asValue) ? self::___getList($_asValue, $aAllAttributes, $bEscape) : self::___getHTMLEscaped($_asValue, $bEscape);
            $_sList .= "<li " . self::getAttributes($_aLIAttr) . ">" . $_sItem . "</li>";
        }
        $_sList .= "</ul>";
        return $_sList;
    }
}
