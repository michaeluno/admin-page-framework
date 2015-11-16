<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides common methods that deal with field definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Form_Model___Format_FormField_Base extends AdminPageFramework_Format_Base {
    
    /**
     * Checks whether a section is set.
     * @return      boolean
     * @internal
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @param       array       $aField     a field definition array.
     */
    protected function _isSectionSet( array $aField ) {
        return isset( $aField[ 'section_id' ] ) 
            && $aField[ 'section_id' ] 
            && '_default' !== $aField['section_id'];
    }       
           
}