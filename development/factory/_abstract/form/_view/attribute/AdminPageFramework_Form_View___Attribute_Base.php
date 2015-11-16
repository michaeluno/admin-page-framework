<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides abstract methods to format HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Form_View___Attribute_Base extends AdminPageFramework_WPUtility {
 
    /**
     * Indicates the context of the attribute.
     * 
     * e.g. fieldset, fieldrow etc.
     * 
     * @since       3.6.0
     */
    public $sContext    = '';
 
    /**
     * 
     * @since       3.6.0
     */
    public $aArguments  = array();
    
    public $aAttributes = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $aAttributes */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments, 
            $this->aAttributes,
        );
        $this->aArguments   = $_aParameters[ 0 ];        
        $this->aAttributes  = $_aParameters[ 1 ];
        
    }
    
    /**
     * Returns the formatted attribute array.
     * @since       3.6.0
     * @return      string
     */
    public function get() {
        return $this->getAttributes(
            $this->_getFormattedAttributes()
        );
    }
        /**
         * Formats attributes array.
         * @since       3.6.0       Moved from `AdminPageFramework_FormOutput`.
         * @return      array       The formatted attributes array.
         */
        protected function _getFormattedAttributes() {
            return $this->aAttributes + $this->_getAttributes();         
        }

           
        /**
         * @since       3.6.0
         * @remark      Used by extended classes.
         * @return      array
         */
        protected function _getAttributes() {
            return array();
        }           
}