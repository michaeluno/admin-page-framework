<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides abstract methods to format format field container HTML attributes.
 * 
 * @package     AdminPageFramework
 * @subpackage  Attribute
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Attribute_FieldContainer_Base extends AdminPageFramework_WPUtility {
    
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
    public $aFieldset = array();
    
    public $aAttributes = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aFieldset, $aAttributes */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->aAttributes,
        );
        $this->aFieldset    = $_aParameters[ 0 ];        
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
         * @since       3.0.0
         * @since       3.3.1       Changed the name from `_getAttributes()`. Added the <var>$sContext</var> parameter. Moved from `AdminPageFramework_FormTable_Base`.
         * @since       3.6.0       Moved from `AdminPageFramework_FormOutput`.
         * @return      array       The formatted attributes array.
         */
        protected function _getFormattedAttributes() {
            
            // 3.3.1+ Changed the custom attributes to take its precedence.
            $_aAttributes = $this->uniteArrays( 
                $this->getElementAsArray( $this->aFieldset, array( 'attributes', $this->sContext ) ),
                $this->aAttributes + $this->_getAttributes()
            );
                        
            $_aAttributes[ 'class' ]   = $this->getClassAttribute( 
                $this->getElement( $_aAttributes, 'class', array() ),
                $this->getElement( $this->aFieldset, array( 'class', $this->sContext ), array() )
            );
            
            return $_aAttributes;
            
        }
        /**
         * @since       3.6.0
         * @return      array
         */
        protected function _getAttributes() {
            return array();
        }
    
           
}