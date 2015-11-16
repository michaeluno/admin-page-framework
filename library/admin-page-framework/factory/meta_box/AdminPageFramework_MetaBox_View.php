<?php
abstract class AdminPageFramework_MetaBox_View extends AdminPageFramework_MetaBox_Model {
    public function _replyToPrintMetaBoxContents($oPost, $vArgs) {
        $_aOutput = array();
        $_aOutput[] = wp_nonce_field($this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false);
        $_aOutput[] = $this->oForm->get();
        $this->oUtil->addAndDoActions($this, 'do_' . $this->oProp->sClassName, $this);
        echo $this->oUtil->addAndApplyFilters($this, "content_{$this->oProp->sClassName}", $this->content(implode(PHP_EOL, $_aOutput)));
    }
    public function content($sContent) {
        return $sContent;
    }
}