<?php
class AdminPageFramework_Model_FormRegistration extends AdminPageFramework_WPUtility {
    public $oFactory;
    public function __construct($oFactory) {
        $this->oFactory = $oFactory;
        add_action("load_after_{$this->oFactory->oProp->sClassName}", array($this, '_replyToRegisterSettings'), 20);
    }
    public function _replyToRegisterSettings() {
        if (!$this->oFactory->_isInThePage()) {
            return;
        }
        $this->oFactory->oForm->aSections = $this->addAndApplyFilter($this->oFactory, "sections_{$this->oFactory->oProp->sClassName}", $this->oFactory->oForm->aSections);
        foreach ($this->oFactory->oForm->aFields as $_sSectionID => & $_aFields) {
            $_aFields = $this->addAndApplyFilter($this->oFactory, "fields_{$this->oFactory->oProp->sClassName}_{$_sSectionID}", $_aFields);
            unset($_aFields);
        }
        $this->oFactory->oForm->aFields = $this->addAndApplyFilter($this->oFactory, "fields_{$this->oFactory->oProp->sClassName}", $this->oFactory->oForm->aFields);
        $this->oFactory->oForm->setDefaultPageSlug($this->oFactory->oProp->sDefaultPageSlug);
        $this->oFactory->oForm->setOptionKey($this->oFactory->oProp->sOptionKey);
        $this->oFactory->oForm->setCallerClassName($this->oFactory->oProp->sClassName);
        $this->oFactory->oForm->format();
        $_sCurrentPageSlug = $this->oFactory->oProp->getCurrentPageSlug();
        $this->oFactory->oForm->setCurrentPageSlug($_sCurrentPageSlug);
        $this->oFactory->oForm->setCurrentTabSlug($this->oFactory->oProp->getCurrentTabSlug($_sCurrentPageSlug));
        $this->oFactory->oForm->applyConditions();
        $this->oFactory->oForm->applyFiltersToFields($this->oFactory, $this->oFactory->oProp->sClassName);
        $this->oFactory->oForm->setDynamicElements($this->oFactory->oProp->aOptions);
        $this->oFactory->loadFieldTypeDefinitions();
        foreach ($this->oFactory->oForm->aConditionedSections as $_aSection) {
            if (empty($_aSection['help'])) {
                continue;
            }
            $this->oFactory->addHelpTab(array('page_slug' => $_aSection['page_slug'], 'page_tab_slug' => $_aSection['tab_slug'], 'help_tab_title' => $_aSection['title'], 'help_tab_id' => $_aSection['section_id'], 'help_tab_content' => $_aSection['help'], 'help_tab_sidebar_content' => $_aSection['help_aside'] ? $_aSection['help_aside'] : "",));
        }
        $this->oFactory->registerFields($this->oFactory->oForm->aConditionedFields);
        $this->oFactory->oProp->bEnableForm = true;
    }
}