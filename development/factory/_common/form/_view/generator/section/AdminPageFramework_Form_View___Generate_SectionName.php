<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
    }
        
        /**
         * @return      string
         */
        protected function _getSectionName( $isIndex=null ) {

            $this->aArguments = $this->aArguments + array(
                'section_id' => null,
                '_index'     => null,
            );
            if( isset( $isIndex ) ) {
                $this->aArguments[ '_index' ] = $isIndex;
            }
            
            $_aNameParts = $this->aArguments[ '_section_path_array' ];
            if ( isset( $this->aArguments[ 'section_id' ], $this->aArguments[ '_index' ] ) ) {
                $_aNameParts[] = $this->aArguments[ '_index' ];
            }
            $_sResult = $this->_getInputNameConstructed( $_aNameParts );

            return $_sResult;
            
            // @deprecated
            // $_sSectionIndex = isset( $this->aArguments[ 'section_id' ], $this->aArguments[ '_index' ] )
                // ? "[{$this->aArguments[ '_index' ]}]" 
                // : "";
            // return $this->aArguments[ 'section_id' ] . $_sSectionIndex;   

        }

}
