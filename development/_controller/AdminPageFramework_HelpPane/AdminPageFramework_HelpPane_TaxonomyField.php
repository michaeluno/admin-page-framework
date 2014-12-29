<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the contextual help tab for the taxonomy field class.
 *
 * @package     AdminPageFramework
 * @subpackage  HelpPane
 * @since       3.0.0     
 * @extends     AdminPageFramework_HelpPane_MetaBox
 * @internal
 */
class AdminPageFramework_HelpPane_TaxonomyField extends AdminPageFramework_HelpPane_MetaBox {
    
    /**
     * Registers the contextual help tab contents.
     * 
     * @internal
     * @since       3.0.0
     * @remark      A call back for the `load-{page hook}` action hook.
     * @remark      The method name implies that this is for meta boxes. This does not mean this method is only for meta box form fields. Extra help text can be added with the `addHelpText()` method.
     * @internal
     */ 
    public function _replyToRegisterHelpTabTextForMetaBox() {
            
        $this->_setHelpTab(     // defined in the base class.
            $this->oProp->sMetaBoxID, 
            $this->oProp->sTitle, 
            $this->oProp->aHelpTabText, 
            $this->oProp->aHelpTabTextSide 
        );
        
    }
    
}