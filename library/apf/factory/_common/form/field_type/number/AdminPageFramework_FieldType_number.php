<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_number extends AdminPageFramework_FieldType_text {
    public $aFieldTypeSlugs = array( 'number', 'range' );
    protected $aDefaultKeys = array( 'attributes' => array( 'size' => 30, 'maxlength' => 400, 'class' => null, 'min' => null, 'max' => null, 'step' => null, 'readonly' => null, 'required' => null, 'placeholder' => null, 'list' => null, 'autofocus' => null, 'autocomplete' => null, ), );
}
