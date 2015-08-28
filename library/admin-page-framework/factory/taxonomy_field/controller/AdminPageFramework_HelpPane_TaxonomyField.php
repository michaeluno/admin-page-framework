<?php
class AdminPageFramework_HelpPane_TaxonomyField extends AdminPageFramework_HelpPane_MetaBox {
    public function _replyToRegisterHelpTabTextForMetaBox() {
        $this->_setHelpTab($this->oProp->sMetaBoxID, $this->oProp->sTitle, $this->oProp->aHelpTabText, $this->oProp->aHelpTabTextSide);
    }
}