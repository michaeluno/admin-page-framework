<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router {
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        add_filter('field_types_' . $oProp->sClassName, array( $this, '_replyToFilterFieldTypeDefinitions' ));
    }
    public function _replyToFieldsetResourceRegistration($aFieldset)
    {
        $aFieldset = $aFieldset + array( 'help' => null, 'title' => null, 'help_aside' => null, );
        if (! $aFieldset[ 'help' ]) {
            return;
        }
        $this->oHelpPane->_addHelpTextForFormFields($aFieldset[ 'title' ], $aFieldset[ 'help' ], $aFieldset[ 'help_aside' ]);
    }
    public function _replyToFilterFieldTypeDefinitions($aFieldTypeDefinitions)
    {
        if (method_exists($this, 'field_types_' . $this->oProp->sClassName)) {
            return call_user_func_array(array( $this, 'field_types_' . $this->oProp->sClassName ), array( $aFieldTypeDefinitions ));
        }
        return $aFieldTypeDefinitions;
    }
    public function _replyToModifySectionsets($aSectionsets)
    {
        return $this->oUtil->addAndApplyFilter($this, "sections_{$this->oProp->sClassName}", $aSectionsets);
    }
    public function _replyToModifyFieldsets($aFieldsets, $aSectionsets)
    {
        foreach ($aFieldsets as $_sSectionPath => $_aFields) {
            $_aSectionPath = explode('|', $_sSectionPath);
            $_sFilterSuffix = implode('_', $_aSectionPath);
            $aFieldsets[ $_sSectionPath ] = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}_{$_sFilterSuffix}", $_aFields);
        }
        $aFieldsets = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}", $aFieldsets);
        if (count($aFieldsets)) {
            $this->oProp->bEnableForm = true;
        }
        return $aFieldsets;
    }
    public function _replyToModifyFieldsetsDefinitions($aFieldsets)
    {
        return $this->oUtil->addAndApplyFilter($this, "field_definition_{$this->oProp->sClassName}", $aFieldsets);
    }
    public function _replyToModifyFieldsetDefinitionAfterFormatting($aFieldset)
    {
        return $this->oUtil->addAndApplyFilter($this, $this->_getHookNameByFieldsetAndPrefix('field_definition_', $aFieldset), $aFieldset, $aFieldset[ '_subsection_index' ]);
    }
    public function _replyToModifyFieldsetDefinitionBeforeFormatting($aFieldset)
    {
        return $this->oUtil->addAndApplyFilter($this, $this->_getHookNameByFieldsetAndPrefix('field_definition_before_formatting_', $aFieldset), $aFieldset);
    }
    private function _getHookNameByFieldsetAndPrefix($sPrefix, $aFieldset)
    {
        $_sFieldPart = '_' . implode('_', $aFieldset[ '_field_path_array' ]);
        $_sSectionPart = implode('_', $aFieldset[ '_section_path_array' ]);
        $_sSectionPart = $this->oUtil->getAOrB('_default' === $_sSectionPart, '', '_' . $_sSectionPart);
        return $sPrefix . $this->oProp->sClassName . $_sSectionPart . $_sFieldPart;
    }
    public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets)
    {}
    public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets)
    {
        return $aFieldset;
    }
    public function _replyToFormatSectionsetDefinition($aSectionset)
    {
        if (empty($aSectionset)) {
            return $aSectionset;
        }
        $aSectionset = $aSectionset + array( '_fields_type' => $this->oProp->_sPropertyType, '_structure_type' => $this->oProp->_sPropertyType, );
        return $aSectionset;
    }
    public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed)
    {
        return $this->_isInThePage();
    }
    public function _replyToGetCapabilityForForm($sCapability)
    {
        return $this->oProp->sCapability;
    }
    public function _replyToGetSavedFormData()
    {
        $this->oProp->aOptions = $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->oProp->aOptions);
        return $this->oProp->aOptions;
    }
    public function _replyToDetermineWhetherToShowDebugInfo()
    {
        return $this->oProp->bShowDebugInfo;
    }
    public function getSavedOptions()
    {
        return $this->oForm->aSavedData;
    }
    public function getFieldErrors()
    {
        return $this->oForm->getFieldErrors();
    }
    protected function _getFieldErrors()
    {
        return $this->oForm->getFieldErrors();
    }
    public function setLastInputs(array $aLastInputs)
    {
        return $this->oForm->setLastInputs($aLastInputs);
    }
    public function _setLastInput($aLastInputs)
    {
        return $this->setLastInputs($aLastInputs);
    }
}
