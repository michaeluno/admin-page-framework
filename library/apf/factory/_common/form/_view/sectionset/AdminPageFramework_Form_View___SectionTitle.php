<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___SectionTitle extends AdminPageFramework_Form_View___Section_Base {
    public $aArguments = array( 'title' => null, 'tag' => null, 'section_index' => null, 'sectionset' => array(), );
    public $aFieldsets = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $oMsg;
    public $aCallbacks = array( 'fieldset_output', 'is_fieldset_visible' => null, );
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->aFieldsets, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->oMsg, $this->aCallbacks );
        $this->aArguments = $_aParameters[ 0 ] + $this->aArguments;
        $this->aFieldsets = $_aParameters[ 1 ];
        $this->aSavedData = $_aParameters[ 2 ];
        $this->aFieldErrors = $_aParameters[ 3 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 4 ];
        $this->oMsg = $_aParameters[ 5 ];
        $this->aCallbacks = $_aParameters[ 6 ];
    }
    public function get()
    {
        $_sTitle = $this->_getSectionTitle($this->aArguments[ 'title' ], $this->aArguments[ 'tag' ], $this->aFieldsets, $this->aArguments[ 'section_index' ], $this->aFieldTypeDefinitions);
        return $_sTitle;
    }
    protected function _getSectionTitle($sTitle, $sTag, $aFieldsets, $iSectionIndex=null, $aFieldTypeDefinitions=array(), $aCollapsible=array())
    {
        $_aSectionTitleFieldset = $this->_getSectionTitleField($aFieldsets, $iSectionIndex, $aFieldTypeDefinitions);
        $_sFieldsInSectionTitle = $this->_getFieldsetsOutputInSectionTitleArea($aFieldsets, $iSectionIndex, $aFieldTypeDefinitions);
        $_sTitle = empty($_aSectionTitleFieldset) ? $this->_getSectionTitleOutput($sTitle, $sTag, $aCollapsible) : $this->getFieldsetOutput($_aSectionTitleFieldset);
        $_bHasOtherFields = $_sFieldsInSectionTitle ? ' has-fields' : '';
        $_sOutput = $_sTitle . $_sFieldsInSectionTitle;
        return $_sOutput ? "<div class='section-title-height-fixer'></div>" . "<div class='section-title-outer-container'>" . "<div class='section-title-container{$_bHasOtherFields}'>" . $_sOutput . "</div>" . "</div>" : '';
    }
    private function _getSectionTitleOutput($sTitle, $sTag, $aCollapsible)
    {
        return $sTitle ? "<{$sTag} class='section-title'>" . $this->_getCollapseButton($aCollapsible) . $sTitle . $this->_getToolTip($this->aArguments[ 'sectionset' ]) . "</{$sTag}>" : '';
    }
    private function _getToolTip($_aSectionset)
    {
        $_sSectionTitleTagID = str_replace('|', '_', $_aSectionset[ '_section_path' ]) . '_' . $this->aArguments[ 'section_index' ];
        $_oToolTip = new AdminPageFramework_Form_View___ToolTip($_aSectionset[ 'tip' ], $_sSectionTitleTagID);
        return $_oToolTip->get();
    }
    private function _getCollapseButton($aCollapsible)
    {
        $_sExpand = esc_attr($this->oMsg->get('click_to_expand'));
        $_sCollapse = esc_attr($this->oMsg->get('click_to_collapse'));
        return $this->getAOrB('button' === $this->getElement($aCollapsible, 'type', 'box'), "<span class='admin-page-framework-collapsible-button admin-page-framework-collapsible-button-expand' title='{$_sExpand}'>&#9658;</span>" . "<span class='admin-page-framework-collapsible-button admin-page-framework-collapsible-button-collapse' title='{$_sCollapse}'>&#9660;</span>", '');
    }
    private function _getFieldsetsOutputInSectionTitleArea(array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions)
    {
        $_sOutput = '';
        foreach ($this->_getFieldsetsInSectionTitleArea($aFieldsets, $iSectionIndex, $aFieldTypeDefinitions) as $_aFieldset) {
            if (empty($_aFieldset)) {
                continue;
            }
            $_sOutput .= $this->getFieldsetOutput($_aFieldset);
        }
        return $_sOutput;
    }
    private function _getFieldsetsInSectionTitleArea(array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions)
    {
        $_aFieldsetsInSectionTitle = array();
        foreach ($aFieldsets as $_aFieldset) {
            if ('section_title' !== $_aFieldset[ 'placement' ]) {
                continue;
            }
            $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput($_aFieldset, $iSectionIndex, $aFieldTypeDefinitions);
            $_aFieldsetsInSectionTitle[] = $_oFieldsetOutputFormatter->get();
        }
        return $_aFieldsetsInSectionTitle;
    }
    private function _getSectionTitleField(array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions)
    {
        foreach ($aFieldsets as $_aFieldset) {
            if ('section_title' !== $_aFieldset[ 'type' ]) {
                continue;
            }
            $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput($_aFieldset, $iSectionIndex, $aFieldTypeDefinitions);
            return $_oFieldsetOutputFormatter->get();
        }
    }
}
