<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_file extends AdminPageFramework_FieldType_text {
    public $aFieldTypeSlugs = array( 'file', );
    protected $aDefaultKeys = array( 'attributes' => array( 'accept' => 'audio/*|video/*|image/*|MIME_type', ), );
    protected function getField($aField)
    {
        return parent::getField($aField) . $this->getHTMLTag('input', array( 'type' => 'hidden', 'value' => '', 'name' => $aField[ 'attributes' ][ 'name' ] . '[_dummy_value]', )) . $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => '__unset_' . $aField[ '_structure_type' ] . '[' . $aField[ '_input_name_flat' ] . '|_dummy_value' . ']', 'value' => $aField[ '_input_name_flat' ] . '|_dummy_value', 'class' => 'unset-element-names element-address', ));
    }
}
