<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Widget_Controller extends AdminPageFramework_Widget_View {
    public function setUp()
    {}
    public function load()
    {}
    protected function setArguments(array $aArguments=array())
    {
        $this->oProp->aWidgetArguments = $aArguments;
    }
}
