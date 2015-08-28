<?php
abstract class AdminPageFramework_UserMeta extends AdminPageFramework_UserMeta_Controller {
    static protected $_sFieldsType = 'user_meta';
    public function __construct($sCapability = 'edit_user', $sTextDomain = 'admin-page-framework') {
        $this->oProp = new AdminPageFramework_Property_UserMeta($this, get_class($this), $sCapability, $sTextDomain, self::$_sFieldsType);
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}");
    }
}