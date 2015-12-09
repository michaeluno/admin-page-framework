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
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_FieldError extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {        
        return $this->_getFieldErrorRules();
    }
        /**
         * Returns CSS rules for field errors.
         * @since       3.2.1
         * @since       3.7.0      Moved from `AdminPageFramework_CSS`.
         * @return      string
         */
        private function _getFieldErrorRules() {
            return <<<CSSRULES
.field-error, 
.section-error
{
  color: red;
  float: left;
  clear: both;
  margin-bottom: 0.5em;
}
.repeatable-section-error,
.repeatable-field-error {
  float: right;
  clear: both;
  color: red;
  margin-left: 1em;
}
CSSRULES;
        }        
        
    
}