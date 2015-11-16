<?php
class AdminPageFramework_Form_View___CollapsibleSectionTitle extends AdminPageFramework_Form_View___SectionTitle {
    public $aArguments = array('title' => null, 'tag' => null, 'section_index' => null, 'collapsible' => array(), 'container_type' => 'section',);
    public $aFieldsets = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $oMsg;
    public $aCallbacks = array('fieldset_output', 'is_fieldset_visible' => null,);
    public function get() {
        if (empty($this->aArguments['collapsible'])) {
            return '';
        }
        return $this->_getCollapsibleSectionTitleBlock($this->aArguments['collapsible'], $this->aArguments['container_type'], $this->aArguments['section_index']);
    }
    private function _getCollapsibleSectionTitleBlock(array $aCollapsible, $sContainer = 'sections', $iSectionIndex = null) {
        if ($sContainer !== $aCollapsible['container']) {
            return '';
        }
        $_sSectionTitle = $this->_getSectionTitle($this->aArguments['title'], $this->aArguments['tag'], $this->aFieldsets, $iSectionIndex, $this->aFieldTypeDefinitions);
        return $this->_getCollapsibleSectionsEnablerScript() . "<div " . $this->getAttributes(array('class' => $this->getClassAttribute('admin-page-framework-section-title', 'accordion-section-title', 'admin-page-framework-collapsible-title', 'sections' === $aCollapsible['container'] ? 'admin-page-framework-collapsible-sections-title' : 'admin-page-framework-collapsible-section-title', $aCollapsible['is_collapsed'] ? 'collapsed' : ''),) + $this->getDataAttributeArray($aCollapsible)) . ">" . $_sSectionTitle . "</div>";
    }
    static private $_bLoaded = false;
    protected function _getCollapsibleSectionsEnablerScript() {
        if (self::$_bLoaded) {
            return;
        }
        self::$_bLoaded = true;
        new AdminPageFramework_Form_View___Script_CollapsibleSection($this->oMsg);
    }
}