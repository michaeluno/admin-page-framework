<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides common methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @since       3.8.0       Changed the visibility scope to `abstract` to make it clear it is an abstract class.
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/CSS
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
abstract class AdminPageFramework_Form_View___CSS_Base extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores additional CSS rules.
     */
    public $aAdded = array();
    
    /**
     * Adds css rules in a property. When the `get()` method is performed, 
     * the added ones will be returned together.
     * @return  void
     * @since   3.7.0
     */
    public function add( $sCSSRules ) {
        $this->aAdded[] = $sCSSRules;
    }
    
    /**
     * @return      string
     * @since       3.7.0
     */
    public function get() {
        
        $_sCSSRules  = $this->_get() . PHP_EOL;
        $_sCSSRules .= $this->_getVersionSpecific();
        $_sCSSRules .= implode( PHP_EOL, $this->aAdded );
        return $_sCSSRules;
    
    }
    
        /**
         * @remark      Override this method in an extended class.
         * @since       3.7.0
         * @return      string
         */
        protected function _get() {
            return '';
        }
        /**
         * @remark      Override this method in an extended class.
         * @since       3.7.0
         * @return      string
         */
        protected function _getVersionSpecific() {
            return '';
        }
        
}
