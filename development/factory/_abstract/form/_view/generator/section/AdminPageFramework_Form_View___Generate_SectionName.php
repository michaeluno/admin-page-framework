<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates section related strings.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_SectionName extends AdminPageFramework_Form_View___Generate_Section_Base {

    /**
     * 
     * @return      string       The generated string value.
     */
    public function get() {
        return $this->_getFiltered( $this->_getSectionName() );
    }
        
    public function getModel()     {
        return $this->get() . '[' . $this->sIndexMark . ']';
        // return $this->_getSectionName( $this->sIndexMark );
    }
        
        /**
         * @return      string
         */
        protected function _getSectionName( $isIndex=null ) {
// @todo if a parent field object exists, use the parent object value and append the dimension of this field level.
            $this->aArguments = $this->aArguments + array(
                'section_id' => null,
                '_index'     => null,
            );
            if( isset( $isIndex ) ) {
                $this->aArguments[ '_index' ] = $isIndex;
            }
            $_sSectionIndex = isset( $this->aArguments[ 'section_id' ], $this->aArguments[ '_index' ] )
                ? "[{$this->aArguments[ '_index' ]}]" 
                : "";
            return $this->aArguments[ 'section_id' ] . $_sSectionIndex;            
        }

}