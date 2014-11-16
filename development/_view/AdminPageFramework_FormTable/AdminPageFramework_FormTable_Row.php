<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable_Row' ) ) :
/**
 * Provides methods to render table rows for form fields.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.3.4
 * @internal
 */
class AdminPageFramework_FormTable_Row extends AdminPageFramework_FormTable_Base {
 
    /**
     * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
     * 
     * @since 3.0.0    
     */
    public function getFieldRows( $aFields, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { return ''; }
        $_aOutput = array();
        foreach( $aFields as $aField ) {
            $_aOutput[] = $this->_getFieldRow( $aField, $hfCallback );
        } 
        return implode( PHP_EOL, $_aOutput );
        
    }
        /**
         * Returns the field output enclosed in a table row.
         * 
         * @since 3.0.0
         */
        protected function _getFieldRow( $aField, $hfCallback ) {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            
            $_aOutput           = array();
            $_aField            = $this->_mergeDefault( $aField );
            $_sAttributes_TR    = $this->_getFieldContainerAttributes( 
                $_aField,
                array( 
                    'id'        => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagBaseID( $_aField ),
                    'valign'    => 'top',
                    'class'     => 'admin-page-framework-fieldrow',
                ),
                'fieldrow'
            );
            $_sAttributes_TD    = $this->generateAttributes( 
                array(
                    'colspan'   => $_aField['show_title_column'] ? 1 : 2,
                    'class'     => $_aField['show_title_column'] ? null : 'admin-page-framework-field-td-no-title',
                )
            );
            $_aOutput[]         = "<tr {$_sAttributes_TR}>";
                if ( $_aField['show_title_column'] ) {
                    $_aOutput[] = "<th>" . $this->_getFieldTitle( $_aField ) . "</th>";
                }
                $_aOutput[] = "<td {$_sAttributes_TD}>" 
                        . call_user_func_array( $hfCallback, array( $aField ) ) 
                    . "</td>"; // $aField is passed, not $_aField as $_aField do not respect subfields.
            $_aOutput[] = "</tr>";
            return implode( PHP_EOL, $_aOutput );
                
        }
    
    /**
     * Returns a set of fields output from the given field definition array.
     * 
     * @remark This is similar to getFieldRows() but without the enclosing table row tag. Used for taxonomy fields.
     * @since 3.0.0
     */
    public function getFields( $aFields, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { return ''; }
        $_aOutput = array();
        foreach( $aFields as $_aField ) {
            $_aOutput[] = $this->_getField( $_aField, $hfCallback );
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
    
        /**
         * Returns the given field output without a table row tag.
         * 
         * @internal
         * @since 3.0.0
         */
        private function _getField( $aField, $hfCallback )  {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            $_aOutput   = array();
            $_aField    = $this->_mergeDefault( $aField );
            $_aOutput[] = "<div " . $this->_getFieldContainerAttributes( $_aField, array(), 'fieldrow' ) . ">";
            if ( $_aField['show_title_column'] ) {
                $_aOutput[] = $this->_getFieldTitle( $_aField );
            }
            $_aOutput[] = call_user_func_array( $hfCallback, array( $aField ) ); // $aField is passed, not $_aField as $_aField do not respect subfields.
            $_aOutput[] = "</div>";
            return implode( PHP_EOL, $_aOutput );     
            
        }
            
}
endif;