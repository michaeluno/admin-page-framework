<?php
class AdminPageFramework_Property_UserMeta extends AdminPageFramework_Property_MetaBox {
    public $_sPropertyType = 'user_meta';
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    protected function _getOptions() {
        return array();
    }
}