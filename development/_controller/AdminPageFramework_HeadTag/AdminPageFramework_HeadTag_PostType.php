<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_HeadTag_PostType' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class.
 * 
 * @since 2.1.5
 * @since 2.1.7 Added the replyToAddStyle() method.
 * @package AdminPageFramework
 * @subpackage HeadTag
 * @internal
 */
class AdminPageFramework_HeadTag_PostType extends AdminPageFramework_HeadTag_MetaBox {

    /**
     * Stores the class selector used to the class-specific style.
     * @since   3.2.0
     * @remark  This value should be overridden in an extended class.
     * @internal
     */
    protected $_sClassSelector_Style    = 'admin-page-framework-style-post-type';
    
    /**
     * Stores the class selector used to the class-specific script.
     * @since   3.2.0
     * @remark  This value should be overridden in an extended class.
     * @internal
     */    
    protected $_sClassSelector_Script   = 'admin-page-framework-script-post-type';
    
}
endif;