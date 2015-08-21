<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates flat field name.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Generate_FlatFieldName extends AdminPageFramework_Generate_FieldName {
    
    /**
     * 
     * @return      string       The generated string value.
     */
    public function get() {
        return $this->_getFiltered( $this->_getFlatFieldName() );
    }
    
    /**
     * Returns a name model that indicates which part is an index to be incremented / decremented.
     * 
     * @return      string      The generated field name model.
     */
    public function getModel() {
        return $this->get() . '|-fi';
    }
        
        /**
         * @return      string
         */
        protected function _getFlatFieldName() {
// @todo if a parent field object exists, use the parent object value and append the dimension of this field level.

            $_sSectionIndex = isset( $this->aFieldset['section_id'], $this->aFieldset['_section_index'] ) 
                ? "|{$this->aFieldset['_section_index']}" 
                : '';

            return $this->getAOrB(
                $this->_isSectionSet(),
                "{$this->aFieldset[ 'section_id' ]}{$_sSectionIndex}|{$this->aFieldset[ 'field_id' ]}",
                "{$this->aFieldset['field_id']}"
            );
            
        }
 
}
