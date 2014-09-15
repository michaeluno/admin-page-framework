<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_HeadTag_MetaBox_Page' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class for pages added by the framework.
 * 
 * @since       3.0.0
 * @use         AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  HeadTag
 * @internal
 */
class AdminPageFramework_HeadTag_MetaBox_Page extends AdminPageFramework_HeadTag_Page {
        
    /**
     * Stores the class selector used to the class-specific style.
     * @since   3.2.0
     * @internal
     */
    protected $_sClassSelector_Style    = 'admin-page-framework-style-page-meta-box';
    
    /**
     * Stores the class selector used to the class-specific script.
     * @since   3.2.0
     * @internal
     */    
    protected $_sClassSelector_Script   = 'admin-page-framework-script-page-meta-box';
 
    /**
     * Checks wither the currently loading page is appropriate for the meta box to be displayed.
     * @since   3.0.0
     * @since   3.2.0    Changed the name to _isInThePage() from _isMetaBoxPage().
     * @internal
     */
    protected function _isInThePage() {
            
        if ( ! isset( $_GET['page'] ) ) { return false; }
        
        if ( in_array( $_GET['page'], $this->oProp->aPageSlugs ) ) {
            return true;
        }
        
        return false;
        
    }

}
endif;