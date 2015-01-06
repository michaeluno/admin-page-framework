<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the contextual help tab .
 *
 * @package AdminPageFramework
 * @subpackage HelpPane
 * @since 3.0.4    
 * @extends AdminPageFramework_HelpPane_MetaBox
 * @internal
 */
class AdminPageFramework_HelpPane_MetaBox_Page extends AdminPageFramework_HelpPane_MetaBox {

    /**
     * Determines whether the currently loaded page belongs to the meta box page.
     * 
     * @sicne 3.0.4
     * @internal
     */
    protected function _isInThePage() {

        if ( ! $this->oProp->bIsAdmin ) return false;

        if ( ! isset( $_GET['page'] ) ) return false;
        
        if ( ! $this->oProp->isPageAdded( $_GET['page'] ) ) return false;
        
        if ( ! isset( $_GET['tab'] ) ) return true;
        
        return $this->oProp->isCurrentTab( $_GET['tab'] );    
        
    }
    
}