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
 * @since       2.1.5
 * @since       2.1.7   Added the replyToAddStyle() method.
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_HeadTag_MetaBox
 * @subpackage  HeadTag
 * @internal
 */
class AdminPageFramework_HeadTag_PostType extends AdminPageFramework_HeadTag_MetaBox {}
endif;