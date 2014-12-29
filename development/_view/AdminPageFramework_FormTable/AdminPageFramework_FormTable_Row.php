<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render table rows for form fields.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.4.0
 * @internal
 */
class AdminPageFramework_FormTable_Row extends AdminPageFramework_FormTable_Base {
 
    /**
     * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
     * 
     * @since 3.0.0    
     */
    public function getFieldRows( array $aFields, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { return ''; }
        $_aOutput = array();
        foreach( $aFields as $_aField ) {
            $_aOutput[] = $this->_getFieldRow( $_aField, $hfCallback );
        } 
        return implode( PHP_EOL, $_aOutput );
        
    }
        /**
         * Returns the field output enclosed in a table row.
         * 
         * @since 3.0.0
         */
        private function _getFieldRow( array $aField, $hfCallback ) {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            
            $_aFieldFinal             = $this->_mergeFieldTypeDefault( $aField );
            return $this->_getFieldByContainer( 
                $aField, 
                $_aFieldFinal,
                $hfCallback,
                array(
                    'open_container'    => "<tr " 
                        . $this->_getFieldContainerAttributes( 
                                $_aFieldFinal,
                                array( 
                                    'id'        => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagBaseID( $_aFieldFinal ),
                                    'valign'    => 'top',
                                    'class'     => 'admin-page-framework-fieldrow',
                                ),
                                'fieldrow'
                            )
                        . ">",
                    'close_container'   => "</tr>",
                    'open_title'        => "<th>",
                    'close_title'       => "</th>",
                    'open_main'         => "<td " 
                        . $this->generateAttributes( 
                            array(
                                'colspan'   => $_aFieldFinal['show_title_column'] ? 1 : 2,
                                'class'     => $_aFieldFinal['show_title_column'] ? null : 'admin-page-framework-field-td-no-title',
                            )
                        )
                        . ">",
                    'close_main'        => "</td>",       
                ) 
            );

        }
    
    /**
     * Returns a set of fields output from the given field definition array.
     * 
     * @remark This is similar to getFieldRows() but without the enclosing table row tag. Used for taxonomy fields.
     * @since 3.0.0
     */
    public function getFields( array $aFields, $hfCallback ) {
        
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
         * @since       3.0.0
         */
        private function _getField( array $aField, $hfCallback )  {
            
            if ( 'section_title' === $aField['type'] ) { return ''; }
            
            $_aFieldFinal    = $this->_mergeFieldTypeDefault( $aField );
            return $this->_getFieldByContainer( 
                $aField, 
                $_aFieldFinal,
                $hfCallback,
                array(
                    'open_main'     => "<div " 
                            . $this->_getFieldContainerAttributes( $_aFieldFinal, array(), 'fieldrow' ) 
                        . ">",
                    'close_main'    => "</div>",
                )
            );    
            
        }
            
        /**
         * Returns the field output with the given opening and closing HTML tags.
         * 
         * @since       3.4.0
         * @todo        Examine whether it is necessary to pass the raw field definition array to the callback because the %aFieldFinal can be used instead of $aField and reduce the parameter. 
         * @param       array       $aField         The passed intact field definition array. The field rendering class needs non-finalized field array to construct the field array. 
         * @param       array       $aFieldFinal    The field array merged with the default values of the field type. 
         */
        private function _getFieldByContainer( array $aField, array $aFieldFinal, $hfCallback, array $aOpenCloseTags ) {
            
            $aOpenCloseTags = $aOpenCloseTags + array(
                'open_container'    => '',
                'close_container'   => '',
                'open_title'        => '',
                'close_title'       => '',
                'open_main'         => '',
                'close_main'        => '',
            );
            $_aOutput   = array();
            if ( $aField['show_title_column'] ) {
                $_aOutput[] = $aOpenCloseTags['open_title']
                    . $this->_getFieldTitle( $aFieldFinal )
                    . $aOpenCloseTags['close_title'];
            }
            $_aOutput[] = $aOpenCloseTags['open_main']
                . call_user_func_array( $hfCallback, array( $aField ) )
                . $aOpenCloseTags['close_main'];
            return $aOpenCloseTags['open_container']
                . implode( PHP_EOL, $_aOutput )
                . $aOpenCloseTags['close_container'];
            
        }
            
        /**
         * Merge the given field definition array with the field type default key array that holds default values.
         * 
         * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
         * which affects the way of rendering the row that contains the field output (by the field output callback).
         * 
         * @internal
         * @since       3.0.0
         * @since       3.4.0       Changed the name from `_mergeDefault()`.
         * @remark      The returning merged field definition array does not respect sub-fields so when passing the field definition to the callback,
         * do not use the array returned from this method but the raw (non-merged) array.
         */
        private function _mergeFieldTypeDefault( array $aField ) {

            return $this->uniteArrays( 
                $aField, 
                isset( $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] ) 
                    ? $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] 
                    : array()
            );
            
        }
            
            
        /**
         * Returns the title part of the field output.
         * 
         * @since       3.0.0
         * @internal
         */
        private function _getFieldTitle( array $aField ) {
            
            return "<label for='" . AdminPageFramework_FormField::_getInputID( $aField ). "'>"
                . "<a id='{$aField['field_id']}'></a>"
                    . "<span title='" 
                            . esc_attr( strip_tags( 
                                isset( $aField['tip'] ) 
                                    ? $aField['tip'] 
                                    : ( 
                                        is_array( $aField['description'] 
                                            ? implode( '&#10;', $aField['description'] )
                                            : $aField['description'] 
                                        ) 
                                    ) 
                            ) ) 
                        . "'>"
                            . $aField['title'] 
                        . ( in_array( $aField[ '_fields_type' ], array( 'widget', 'post_meta_box', 'page_meta_box' ) ) && isset( $aField['title'] ) && '' !== $aField['title']
                            ? "<span class='title-colon'>:</span>" 
                            : ''
                        )
                    . "</span>"
                . "</label>";
            
        }
                     
}