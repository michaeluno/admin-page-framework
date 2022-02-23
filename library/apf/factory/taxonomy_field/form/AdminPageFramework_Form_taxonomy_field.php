<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_taxonomy_field extends AdminPageFramework_Form
{
    public $sStructureType = 'taxonomy_field';
    public function get()
    {
        $this->sCapability = $this->callback($this->aCallbacks[ 'capability' ], '');
        if (! $this->canUserView($this->sCapability)) {
            return '';
        }
        $this->_formatElementDefinitions($this->aSavedData);
        $_oFieldsets = new AdminPageFramework_Form_View___FieldsetRows($this->getElementAsArray($this->aFieldsets, '_default'), null, $this->aSavedData, $this->getFieldErrors(), $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldsets->get();
    }
}
