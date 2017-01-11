<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides common methods that deal with formatting field definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_Utility
 * @internal
 */
abstract class AdminPageFramework_Form_Model___Format_FormField_Base extends AdminPageFramework_Form_Utility {
    
    /**
     * Checks whether a section is set.
     * @return      boolean
     * @internal
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @param       array       $aField     a field definition array.
     * @deprecated  3.7.0      Seems not used at the moment
     */
// @todo Find a new way for nested sections    
    // protected function _isSectionSet( array $aField ) {
        // return isset( $aField[ 'section_id' ] ) 
            // && $aField[ 'section_id' ] 
            // && '_default' !== $aField['section_id'];
    // }
           
}
