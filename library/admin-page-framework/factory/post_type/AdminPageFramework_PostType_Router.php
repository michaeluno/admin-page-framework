<?php
abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory {
    public function _isInThePage() {
        if (!$this->oProp->bIsAdmin) {
            return false;
        }
        if (!in_array($this->oProp->sPageNow, array('edit.php', 'edit-tags.php', 'post.php', 'post-new.php'))) {
            return false;
        }
        return ($this->oUtil->getCurrentPostType() == $this->oProp->sPostType);
    }
}