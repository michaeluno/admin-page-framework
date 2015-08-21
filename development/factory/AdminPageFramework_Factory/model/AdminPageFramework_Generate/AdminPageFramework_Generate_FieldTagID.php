<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field container tag id.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Generate_FieldTagID extends AdminPageFramework_Generate_Field_Base {
        
    /**
     * Returns the field input base ID used for field container elements.
     * 
     * The returning value does not represent the exact ID of the field input tag. 
     * This is because each input tag has an index for sub-fields.
     * 
     * @remark      This is called from the fields table class to insert the row id.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Changed the name from `_getInputTagID()`.
     * @since       3.6.0       Moved from `AdminPageFramework_FormField`.
     * @return      string      The generated string value.
     */
    public function get() {            
        return $this->_getFiltered( $this->_getBaseFieldTagID() );
    }
    
    /**
     * Returns an id model that indicates where to be replaced with an index.
     * @since       3.6.0
     * @return      string
     */
    public function getModel() {
        return $this->get() . '__-fi-';
    }
        /**
         * @since       3.6.0
         * @return      string      Returns the base tag id meant to be used before applying to a filter callback,
         */
        protected function _getBaseFieldTagID() {
            
// @todo if a parent field object exists, use the parent object value and append the dimension of this field level.

            $_sSectionIndex = isset( $this->aFieldset[ '_section_index' ] )
                ? '__' . $this->aFieldset[ '_section_index' ] 
                : '';
                
            return $this->_isSectionSet()
                ? $this->aFieldset[ 'section_id' ] . $_sSectionIndex . '_' . $this->aFieldset[ 'field_id' ]
                : $this->aFieldset[ 'field_id' ];            
            
        }
    
}
