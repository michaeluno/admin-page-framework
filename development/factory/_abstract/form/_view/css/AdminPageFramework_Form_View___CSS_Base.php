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
class AdminPageFramework_Form_View___CSS_Base extends AdminPageFramework_WPUtility {
    
    /**
     * @return      string
     */
    public function get() {
        
        $_sCSSRules  = $this->_get() . PHP_EOL;
        $_sCSSRules .= $this->_getVersionSpecific();
        return $this->isDebugMode()
            ? trim( $_sCSSRules )
            : $this->minifyCSS( $_sCSSRules );
    
    }
    
        /**
         * @since       DEVVER
         * @return      string
         */
        protected function _get() {
            return '';
        }
        /**
         * @since       DEVVER
         * @return      string
         */
        protected function _getVersionSpecific() {
            return '';
        }
}