<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
class AdminPageFramework_Form_View___Generate_FlatFieldName extends AdminPageFramework_Form_View___Generate_FieldName {
    
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
        return $this->get() . '|' . $this->sIndexMark;
    }
        
        /**
         * @return      string
         */
        protected function _getFlatFieldName() {
            
            $_sSectionIndex = isset( $this->aArguments[ 'section_id' ], $this->aArguments[ '_section_index' ] ) 
                ? "|{$this->aArguments[ '_section_index' ]}" 
                : '';                 
                
            return $this->getAOrB(
                $this->_isSectionSet(),
                "{$this->aArguments[ '_section_path' ]}{$_sSectionIndex}|{$this->aArguments[ '_field_path' ]}",
                "{$this->aArguments[ '_field_path' ]}"
            );
            
        }
 
}
