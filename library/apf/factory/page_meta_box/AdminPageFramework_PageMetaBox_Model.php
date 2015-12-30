<?php
/**
 Admin Page Framework v3.7.8b02 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AdminPageFramework_PageMetaBox_Model extends AdminPageFramework_PageMetaBox_Router {
    static protected $_sStructureType = 'page_meta_box';
    public function __construct($sMetaBoxID, $sTitle, $asPageSlugs = array(), $sContext = 'normal', $sPriority = 'default', $sCapability = 'manage_options', $sTextDomain = 'admin-page-framework') {
        $this->oProp = new AdminPageFramework_Property_MetaBox_Page($this, get_class($this), $sCapability, $sTextDomain, self::$_sStructureType);
        $this->oProp->aPageSlugs = is_string($asPageSlugs) ? array($asPageSlugs) : $asPageSlugs;
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
    protected function _setUpValidationHooks($oScreen) {
        foreach ($this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug) {
            if (is_string($_asTabArrayOrPageSlug)) {
                $_sPageSlug = $_asTabArrayOrPageSlug;
                add_filter("validation_saved_options_without_dynamic_elements_{$_sPageSlug}", array($this, '_replyToFilterPageOptionsWODynamicElements'), 10, 2);
                add_filter("validation_{$_sPageSlug}", array($this, '_replyToValidateOptions'), 10, 4);
                add_filter("options_update_status_{$_sPageSlug}", array($this, '_replyToModifyOptionsUpdateStatus'));
                continue;
            }
            $_sPageSlug = $_sIndexOrPageSlug;
            $_aTabs = $_asTabArrayOrPageSlug;
            foreach ($_aTabs as $_sTabSlug) {
                add_filter("validation_{$_sPageSlug}_{$_sTabSlug}", array($this, '_replyToValidateOptions'), 10, 4);
                add_filter("validation_saved_options_without_dynamic_elements_{$_sPageSlug}_{$_sTabSlug}", array($this, '_replyToFilterPageOptionsWODynamicElements'), 10, 2);
                add_filter("options_update_status_{$_sPageSlug}_{$_sTabSlug}", array($this, '_replyToModifyOptionsUpdateStatus'));
            }
        }
    }
    public function _replyToAddMetaBox($sPageHook = '') {
        foreach ($this->oProp->aPageSlugs as $sKey => $_asPage) {
            if (is_string($_asPage)) {
                $this->_addMetaBox($_asPage);
                continue;
            }
            $this->_addMetaBoxes($sKey, $_asPage);
        }
    }
    private function _addMetaBoxes($sPageSlug, $asPage) {
        foreach ($this->oUtil->getAsArray($asPage) as $_sTabSlug) {
            if (!$this->oProp->isCurrentTab($_sTabSlug)) {
                continue;
            }
            $this->_addMetaBox($sPageSlug);
        }
    }
    private function _addMetaBox($sPageSlug) {
        add_meta_box($this->oProp->sMetaBoxID, $this->oProp->sTitle, array($this, '_replyToPrintMetaBoxContents'), $this->oProp->_getScreenIDOfPage($sPageSlug), $this->oProp->sContext, $this->oProp->sPriority, null);
    }
    public function _replyToFilterPageOptions($aPageOptions) {
        return $aPageOptions;
    }
    public function _replyToFilterPageOptionsWODynamicElements($aOptionsWODynamicElements, $oFactory) {
        return $this->oForm->dropRepeatableElements($aOptionsWODynamicElements);
    }
    public function _replyToValidateOptions($aNewPageOptions, $aOldPageOptions, $oAdminPage, $aSubmitInfo) {
        $_aNewMetaBoxInputs = $this->oForm->getSubmittedData($_POST);
        $_aOldMetaBoxInputs = $this->oUtil->castArrayContents($this->oForm->getDataStructureFromAddedFieldsets(), $aOldPageOptions);
        $_aNewMetaBoxInputsRaw = $_aNewMetaBoxInputs;
        $_aNewMetaBoxInputs = call_user_func_array(array($this, 'validate'), array($_aNewMetaBoxInputs, $_aOldMetaBoxInputs, $this, $aSubmitInfo));
        $_aNewMetaBoxInputs = $this->oUtil->addAndApplyFilters($this, "validation_{$this->oProp->sClassName}", $_aNewMetaBoxInputs, $_aOldMetaBoxInputs, $this, $aSubmitInfo);
        if ($this->hasFieldError()) {
            $this->setLastInputs($_aNewMetaBoxInputsRaw);
        }
        return $this->oUtil->uniteArrays($_aNewMetaBoxInputs, $aNewPageOptions);
    }
    public function _replyToModifyOptionsUpdateStatus($aStatus) {
        if (!$this->hasFieldError()) {
            return $aStatus;
        }
        return array('field_errors' => true) + $this->oUtil->getAsArray($aStatus);
    }
    public function _replyToGetSavedFormData() {
        $_aPageOptions = $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->oProp->oAdminPage->oProp->aOptions);
        return $this->oUtil->castArrayContents($this->oForm->getDataStructureFromAddedFieldsets(), $_aPageOptions);
    }
    private function _getPageMetaBoxOptionsFromPageOptions(array $aPageOptions, array $aFieldsets) {
        $_aOptions = array();
        foreach ($aFieldsets as $_sSectionID => $_aFieldsets) {
            if ('_default' === $_sSectionID) {
                foreach ($_aFieldsets as $_aField) {
                    if (array_key_exists($_aField['field_id'], $aPageOptions)) {
                        $_aOptions[$_aField['field_id']] = $aPageOptions[$_aField['field_id']];
                    }
                }
            }
            if (array_key_exists($_sSectionID, $aPageOptions)) {
                $_aOptions[$_sSectionID] = $aPageOptions[$_sSectionID];
            }
        }
        return $_aOptions;
    }
}