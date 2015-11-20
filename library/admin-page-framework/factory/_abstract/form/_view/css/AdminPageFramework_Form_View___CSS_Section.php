<?php
class AdminPageFramework_Form_View___CSS_Section extends AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        return $this->_getFormSectionRules();
    }
    private function _getFormSectionRules() {
        return <<<CSSRULES
.admin-page-framework-section {
    margin-bottom: 1em; /* gives a margin between sections. This helps for the debug info in each sectionset and collapsible sections. */
}            
.admin-page-framework-sectionset {
    margin-bottom: 1em; 
    display:inline-block;
    width:100%;
}            
/* Nested sections */
.admin-page-framework-section > .admin-page-framework-sectionset {
    margin-left: 2em;
}

CSSRULES;
        
    }
}