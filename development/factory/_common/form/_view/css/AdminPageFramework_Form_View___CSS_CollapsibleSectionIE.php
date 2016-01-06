<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_CollapsibleSectionIE extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {
        return $this->_getCollapsibleSectionsRules();
    }
        /**
         * Returns the collapsible sections specific CSS rules.
         * 
         * @since       3.4.0
         * @internal
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @return      string
         */
        private function _getCollapsibleSectionsRules() {

            return <<<CSSRULES
/* Collapsible sections - in IE tbody and tr cannot set paddings */        
tbody.admin-page-framework-collapsible-content > tr > th,
tbody.admin-page-framework-collapsible-content > tr > td
{
    padding-right: 20px;
    padding-left: 20px;
}

CSSRULES;

        }  

}