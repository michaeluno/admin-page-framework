<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to manipulate the contextual help tab .
 *
 * @package AdminPageFramework
 * @subpackage HelpPane
 * @since 3.0.4    
 * @extends AdminPageFramework_HelpPane_Base
 * @internal
 */
class AdminPageFramework_HelpPane_page_meta_box extends AdminPageFramework_HelpPane_Base {

    /**
     * Determines whether the currently loaded page belongs to the meta box page.
     * 
     * @sicne       3.0.4
     * @internal
     * @deprecated  3.7.10
     */
/*     protected function _isInThePage() {

        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        if ( ! isset( $_GET[ 'page' ] ) ) {
            return false;
        }
        
        if ( ! $this->oProp->isPageAdded( $_GET[ 'page' ] ) ) {
            return false;
        }
        
        if ( ! isset( $_GET[ 'tab' ] ) ) {
            return true;
        }
        
        return $this->oProp->isCurrentTab( $_GET[ 'tab' ] );
        
    }
     */
}