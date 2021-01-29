<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the contextual help tab .
 *
 * @package     AdminPageFramework/Factory/MetaBox/HelpPane
 * @since       2.1.0
 * @since       3.0.0 Become not abstract.
 * @extends     AdminPageFramework_HelpPane_Base
 * @internal
 */
class AdminPageFramework_HelpPane_post_meta_box extends AdminPageFramework_HelpPane_Base {
    
    /**
     * Registers the contextual help tab contents.
     * 
     * @since       2.1.0
     * @since       3.7.10      Changed the name from `_replyToRegisterHelpTabTextForMetaBox()`.
     * @callback    action      admin_head
     * @internal
     */ 
    public function _replyToRegisterHelpTabText() {

        // Check if the currently loaded page is of meta box page.
        if ( ! $this->oProp->oCaller->isInThePage() ) {
            return false; 
        }

        $this->_setHelpTab(     // defined in the base class.
            $this->oProp->sMetaBoxID, 
            $this->oProp->sTitle, 
            $this->oProp->aHelpTabText, 
            $this->oProp->aHelpTabTextSide 
        );
        
    }
    
  
}
