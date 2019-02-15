<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field related strings.
 * 
 * @package     AdminPageFramework/Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Form_View___Generate_Field_Base extends AdminPageFramework_Form_View___Generate_Section_Base {
       
    /**
     * A field definition array will be set.
     */
    public $aArguments = array();
       
    /**
     * Checks whether a section is set.
     * 
     * @internal
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @param       array       $aFieldset     a field definition array.
     * @return      boolean
     */
    protected function _isSectionSet() {
        return isset( $this->aArguments[ 'section_id' ] ) 
            && $this->aArguments[ 'section_id' ] 
            && '_default' !== $this->aArguments[ 'section_id' ];
    }       
    
}
