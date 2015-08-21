<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field id.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Generate_FieldName extends AdminPageFramework_Generate_Field_Base {
    
    /**
     * 
     * @return      string       The generated string value.
     */
    public function get() {
        return $this->_getFiltered( $this->_getFieldName() );
    }
        
    public function getModel()     {
        return $this->get() . '[-fi-]';
    }
        
        /**
         * @return      string
         */
        protected function _getFieldName() {
// @todo if a parent field object exists, use the parent object value and append the dimension of this field level.
            $_sSectionIndex = isset( $this->aFieldset[ 'section_id' ], $this->aFieldset[ '_section_index' ] ) 
                ? "[{$this->aFieldset[ '_section_index' ]}]" 
                : "";
            return $this->getAOrB(
                $this->_isSectionSet(),
                $this->aFieldset[ 'section_id' ] . $_sSectionIndex . "[" . $this->aFieldset[ 'field_id' ] . "]",
                $this->aFieldset[ 'field_id' ]
            );
            
        }
 
}
