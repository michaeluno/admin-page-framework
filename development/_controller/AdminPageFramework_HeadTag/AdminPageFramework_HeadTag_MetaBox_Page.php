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
 
}
endif;