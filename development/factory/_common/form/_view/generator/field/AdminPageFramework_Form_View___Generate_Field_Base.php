<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field related strings.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Form_View___Generate_Field_Base extends AdminPageFramework_Form_View___Generate_Section_Base {
            
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
