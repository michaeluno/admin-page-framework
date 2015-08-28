<?php
class AdminPageFramework_Property_NetworkAdmin extends AdminPageFramework_Property_Page {
    public $_sPropertyType = 'network_admin_page';
    public $sFieldsType = 'network_admin_page';
    protected function _getOptions() {
        return AdminPageFramework_WPUtility::addAndApplyFilter($GLOBALS['aAdminPageFramework']['aPageClasses'][$this->sClassName], 'options_' . $this->sClassName, $this->sOptionKey ? get_site_option($this->sOptionKey, array()) : array());
    }
    public function updateOption($aOptions = null) {
        if ($this->_bDisableSavingOptions) {
            return;
        }
        return update_site_option($this->sOptionKey, $aOptions !== null ? $aOptions : $this->aOptions);
    }
}