<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Property_user_meta extends AdminPageFramework_Property_post_meta_box
{
    public $_sPropertyType = 'user_meta';
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    protected function _getOptions()
    {
        return array();
    }
}
