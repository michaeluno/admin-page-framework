<?php
abstract class AdminPageFramework_PostType extends AdminPageFramework_PostType_Controller {
    public function __construct($sPostType, $aArguments = array(), $sCallerPath = null, $sTextDomain = 'admin-page-framework') {
        if (empty($sPostType)) {
            return;
        }
        $this->oProp = new AdminPageFramework_Property_PostType($this, $sCallerPath ? trim($sCallerPath) : ((is_admin() && isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], array('edit.php', 'post.php', 'post-new.php', 'plugins.php', 'tags.php', 'edit-tags.php',))) ? AdminPageFramework_Utility::getCallerScriptPath(__FILE__) : null), get_class($this), 'publish_posts', $sTextDomain, 'post_type');
        $this->oProp->sPostType = AdminPageFramework_WPUtility::sanitizeSlug($sPostType);
        $this->oProp->aPostTypeArgs = $aArguments;
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}", $this);
    }
}