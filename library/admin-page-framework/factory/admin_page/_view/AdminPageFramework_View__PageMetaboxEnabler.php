<?php
class AdminPageFramework_View__PageMetaboxEnabler extends AdminPageFramework_WPUtility {
    public $oFactory;
    public function __construct($oFactory) {
        $this->oFactory = $oFactory;
        add_action('admin_head', array($this, '_replyToEnableMetaBox'));
    }
    public function _replyToEnableMetaBox() {
        if (!$this->oFactory->oProp->isPageAdded()) {
            return;
        }
        if (!$this->_isMetaBoxAdded()) {
            return;
        }
        $_sCurrentScreenID = $this->getCurrentScreenID();
        do_action("add_meta_boxes_{$_sCurrentScreenID}", null);
        do_action('add_meta_boxes', $_sCurrentScreenID, null);
        wp_enqueue_script('postbox');
        add_screen_option('layout_columns', array('max' => 2, 'default' => 2,));
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        if (isset($GLOBALS['page_hook'])) {
            add_action("admin_footer-{$GLOBALS['page_hook']}", array($this, '_replyToAddMetaboxScript'));
        }
    }
    private function _isMetaBoxAdded($sPageSlug = '') {
        $_aPageMetaBoxClasses = $this->getElementAsArray($GLOBALS, array('aAdminPageFramework', 'aMetaBoxForPagesClasses'));
        if (empty($_aPageMetaBoxClasses)) {
            return false;
        }
        $sPageSlug = $sPageSlug ? $sPageSlug : $this->getElement($_GET, 'page', '');
        if (!$sPageSlug) {
            return false;
        }
        foreach ($_aPageMetaBoxClasses as $_sClassName => $_oMetaBox) {
            if ($this->_isPageOfMetaBox($sPageSlug, $_oMetaBox)) {
                return true;
            }
        }
        return false;
    }
    private function _isPageOfMetaBox($sPageSlug, $oMetaBox) {
        if (in_array($sPageSlug, $oMetaBox->oProp->aPageSlugs)) {
            return true;
        }
        if (!array_key_exists($sPageSlug, $oMetaBox->oProp->aPageSlugs)) {
            return false;
        }
        $_aTabs = $oMetaBox->oProp->aPageSlugs[$sPageSlug];
        $_sCurrentTabSlug = $this->oFactory->oProp->getCurrentTabSlug();
        return ($_sCurrentTabSlug && in_array($_sCurrentTabSlug, $_aTabs));
    }
    public function _replyToAddMetaboxScript() {
        $_bLoaded = $this->getElement($GLOBALS, array('aAdminPageFramework', 'bAddedMetaBoxScript'), false);
        if ($_bLoaded) {
            return;
        }
        $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] = true;
        $_sScript = <<<JAVASCRIPTS
jQuery( document).ready( function(){ 
    postboxes.add_postbox_toggles( pagenow ); 
});
JAVASCRIPTS;
        echo '<script class="admin-page-framework-insert-metabox-script">' . $_sScript . '</script>';
    }
}