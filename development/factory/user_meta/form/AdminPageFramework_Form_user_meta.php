<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `user_meta` structure type.
 * 
 * The suffix represents the structure type of the form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0      
 * @extends     AdminPageFramework_Form_Meta       There are some methods defined in the post_meta_box class and are used in this class.
 * @internal
 */
class AdminPageFramework_Form_user_meta extends AdminPageFramework_Form_Meta {
    public $sStructureType = 'user_meta';
}
