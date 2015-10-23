<?php
class AdminPageFramework_AdminNotice {
    public function __construct($sNotice, array $aAttributes = array('class' => 'error')) {
        $this->sNotice = $sNotice;
        $this->aAttributes = $aAttributes + array('class' => 'error',);
        $this->aAttributes['class'] = trim($this->aAttributes['class']) . ' admin-page-framework-settings-notice-message notice is-dismissible';
        if (did_action('admin_notices')) {
            $this->_replyToDisplayAdminNotice();
        } else {
            add_action('admin_notices', array($this, '_replyToDisplayAdminNotice'));
        }
    }
    public function _replyToDisplayAdminNotice() {
        echo "<div " . $this->_getAttributes($this->aAttributes) . ">" . "<p>" . $this->sNotice . "</p>" . "</div>";
    }
    private function _getAttributes(array $aAttributes) {
        $_sQuoteCharactor = "'";
        $_aOutput = array();
        foreach ($aAttributes as $_sAttribute => $_asProperty) {
            if ('style' === $_sAttribute && is_array($_asProperty)) {
                $_asProperty = $this->_getInlineCSS($_asProperty);
            }
            if (!is_scalar($_asProperty)) {
                continue;
            }
            $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}" . esc_attr($_asProperty) . "{$_sQuoteCharactor}";
        }
        return trim(implode(' ', $_aOutput));
    }
    private function _getInlineCSS(array $aCSSRules) {
        $_aOutput = array();
        foreach ($aCSSRules as $_sProperty => $_sValue) {
            $_aOutput[] = $_sProperty . ': ' . $_sValue;
        }
        return implode('; ', $_aOutput);
    }
}