<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       DEVVER
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_Section extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       DEVVER
     * @return      string
     */
    protected function _get() {
        return $this->_getFormSectionRules();
    }
         /**
         * Returns the CSS rules for form fields.
         * 
         * @since       3.4.0
         * @since       DEVVER      Moved from `AdminPageFramework_CSS`.
         * @internal
         */    
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
CSSRULES;
            
        }
    
}