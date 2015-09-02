<?php
class AdminPageFramework_WPUtility_HTML extends AdminPageFramework_WPUtility_URL {
    static public function getAttributes(array $aAttributes) {
        $_sQuoteCharactor = "'";
        $_aOutput = array();
        foreach ($aAttributes as $_sAttribute => $_mProperty) {
            if (is_scalar($_mProperty)) {
                $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}" . esc_attr($_mProperty) . "{$_sQuoteCharactor}";
            }
        }
        return implode(' ', $_aOutput);
    }
    static public function generateAttributes(array $aAttributes) {
        return self::getAttributes($aAttributes);
    }
    static public function getDataAttributes(array $aArray) {
        return self::getAttributes(self::getDataAttributeArray($aArray));
    }
    static public function generateDataAttributes(array $aArray) {
        return self::getDataAttributes($aArray);
    }
    static public function getHTMLTag($sTagName, array $aAttributes, $sValue = null) {
        $_sTag = tag_escape($sTagName);
        return null === $sValue ? "<" . $_sTag . " " . self::getAttributes($aAttributes) . " />" : "<" . $_sTag . " " . self::getAttributes($aAttributes) . ">" . $sValue . "</{$_sTag}>";
    }
    static public function generateHTMLTag($sTagName, array $aAttributes, $sValue = null) {
        return self::getHTMLTag($sTagName, $aAttributes, $sValue);
    }
}