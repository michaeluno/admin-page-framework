<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a fieldset row.
 * 
 * There are types of fields that do not use form tables such as taxonomy fields. 
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_FormPart_FieldsetRow extends AdminPageFramework_FormPart_TableRow {            
    
    /**
     * Returns the given field output without a table row tag.
     * 
     * @internal
     * @since       3.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`. Changed the name from `_getFieldset()`.
     */
    protected function _getRow( array $aFieldset, $hfCallback )  {
        
        if ( 'section_title' === $aFieldset[ 'type' ] ) { 
            return ''; 
        }
        
        $_oFieldrowAttribute = new AdminPageFramework_Attribute_Fieldrow( $aFieldset );
        
        return $this->_getFieldByContainer( 
            $aFieldset, 
            $hfCallback,
            array(
                'open_main'     => "<div " . $_oFieldrowAttribute->get() . ">",
                'close_main'    => "</div>",
            )
        );    
        
    }
    
    
}