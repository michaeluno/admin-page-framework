<?php
class AdminPageFramework_FormPart_CollapsibleSectionTitle extends AdminPageFramework_FormPart_SectionTitle {
    public $sTitle = '';
    public $sTag = '';
    public $aFields = array();
    public $hfFieldCallback = null;
    public $iSectionIndex = null;
    public $aFieldTypeDefinitions = array();
    public $aCollapsible = array();
    public $sContainer = '';
    public $oMsg = null;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->sTitle, $this->sTag, $this->aFields, $this->hfFieldCallback, $this->iSectionIndex, $this->aCollapsible, $this->sContainer, $this->oMsg,);
        $this->sTitle = $_aParameters[0];
        $this->sTag = $_aParameters[1];
        $this->aFields = $_aParameters[2];
        $this->hfFieldCallback = $_aParameters[3];
        $this->iSectionIndex = $_aParameters[4];
        $this->aFieldTypeDefinitions = $_aParameters[5];
        $this->aCollapsible = $this->getAsArray($_aParameters[6]);
        $this->sContainer = $_aParameters[7];
        $this->oMsg = $_aParameters[8];
    }
    public function get() {
        return $this->_getCollapsibleSectionTitleBlock($this->aCollapsible, $this->sContainer, $this->aFields, $this->hfFieldCallback, $this->iSectionIndex);
    }
    private function _getCollapsibleSectionTitleBlock(array $aCollapsible, $sContainer = 'sections', array $aFields = array(), $hfFieldCallback = null, $iSectionIndex = null) {
        if (empty($aCollapsible)) {
            return '';
        }
        if ($sContainer !== $aCollapsible['container']) {
            return '';
        }
        $_sSectionTitle = $this->_getSectionTitle($this->sTitle, $this->sTag, $this->aFields, $this->hfFieldCallback, $this->iSectionIndex);
        return $this->_getCollapsibleSectionsEnablerScript() . "<div " . $this->getAttributes(array('class' => $this->getClassAttribute('admin-page-framework-section-title', 'accordion-section-title', 'admin-page-framework-collapsible-title', 'sections' === $aCollapsible['container'] ? 'admin-page-framework-collapsible-sections-title' : 'admin-page-framework-collapsible-section-title', $aCollapsible['is_collapsed'] ? 'collapsed' : ''),) + $this->getDataAttributeArray($aCollapsible)) . ">" . $_sSectionTitle . "</div>";
    }
    static private $_bLoadedCollapsibleSectionsEnablerScript = false;
    protected function _getCollapsibleSectionsEnablerScript() {
        if (self::$_bLoadedCollapsibleSectionsEnablerScript) {
            return;
        }
        self::$_bLoadedCollapsibleSectionsEnablerScript = true;
        new AdminPageFramework_Script_CollapsibleSection($this->oMsg);
    }
}