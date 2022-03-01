<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_posttype extends AdminPageFramework_FieldType_checkbox {
    public $aFieldTypeSlugs = array( 'posttype', );
    protected $aDefaultKeys = array( 'slugs_to_remove' => null, 'query' => array(), 'operator' => 'and', 'attributes' => array( 'size' => 30, 'maxlength' => 400, ), 'select_all_button' => true, 'select_none_button' => true, 'save_unchecked' => true, );
    protected $aDefaultRemovingPostTypeSlugs = array( 'revision', 'attachment', 'nav_menu_item', );
    protected function getField($aField)
    {
        $this->_sCheckboxClassSelector = '';
        $aField[ 'label' ] = $this->_getPostTypeArrayForChecklist(isset($aField[ 'slugs_to_remove' ]) ? $this->getAsArray($aField[ 'slugs_to_remove' ]) : $this->aDefaultRemovingPostTypeSlugs, $aField[ 'query' ], $aField[ 'operator' ]);
        return parent::getField($aField);
    }
    private function _getPostTypeArrayForChecklist($aSlugsToRemove, $asQueryArgs=array(), $sOperator='and')
    {
        $_aPostTypes = array();
        foreach (get_post_types($asQueryArgs, 'objects') as $_oPostType) {
            if (isset($_oPostType->name, $_oPostType->label)) {
                $_aPostTypes[ $_oPostType->name ] = $_oPostType->label;
            }
        }
        return array_diff_key($_aPostTypes, array_flip($aSlugsToRemove));
    }
}
