<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
     * @since       3.0.0
     * @since       3.6.0       Added the `$iSectionIndex` parameter. Changed the name from `getFIeldRows`.
     * @return      string
     */
    public function getFieldsetRows( array $aFieldsets, $hfCallback, $iSectionIndex=null ) {
        
        if ( ! is_callable( $hfCallback ) ) { 
            return ''; 
        }
        
        $_aOutput = array();
        foreach( $aFieldsets as $_aFieldset ) {
            
            $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                $_aFieldset, 
                $iSectionIndex,
                $this->aFieldTypeDefinitions
            );
            $_aOutput[] = $this->_getFieldRow( 
                $_oFieldsetOutputFormatter->get(), 
                $hfCallback 
            );
            
        } 
        return implode( PHP_EOL, $_aOutput );
        
    }
        /**
         * Returns the field output enclosed in a table row.
         * 
         * @since       3.0.0
         * @return      string
         */
        private function _getFieldRow( array $aFieldset, $hfCallback ) {
            
            if ( 'section_title' === $aFieldset[ 'type' ] ) { 
                return ''; 
            }
            
            $_oFieldrowAttribute   = new AdminPageFramework_Attribute_Fieldrow( 
                $aFieldset,
                array( 
                    'id'        => 'fieldrow-' . $aFieldset[ 'tag_id' ],
                    'valign'    => 'top',
                    'class'     => 'admin-page-framework-fieldrow',
                )                
            );
            
            return $this->_getFieldByContainer( 
                $aFieldset, 
                $hfCallback,
                array(
                    'open_container'    => "<tr " . $_oFieldrowAttribute->get() . ">",
                    'close_container'   => "</tr>",
                    'open_title'        => "<th>",
                    'close_title'       => "</th>",
                    'open_main'         => "<td " 
                        . $this->generateAttributes( 
                            array(
                                'colspan'   => $aFieldset[ 'show_title_column' ] 
                                    ? 1 
                                    : 2,
                                'class'     => $aFieldset[ 'show_title_column' ] 
                                    ? null 
                                    : 'admin-page-framework-field-td-no-title',
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
     * @remark      This is similar to getFieldsetRows() but without the enclosing table row tag. 
     * @remark      Used for taxonomy fields.
     * @since       3.0.0
     * @return      string
     */
    public function getFieldsets( array $aFieldsets, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { 
            return ''; 
        }
        
        $_aOutput = array();
        foreach( $aFieldsets as $_aFieldset ) {
            $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                $_aFieldset, 
                null, // section index
                $this->aFieldTypeDefinitions
            );            
            $_aOutput[] = $this->_getFieldset( 
                $_oFieldsetOutputFormatter->get(),
                $hfCallback 
            );
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
    
        /**
         * Returns the given field output without a table row tag.
         * 
         * @internal
         * @since       3.0.0
         */
        private function _getFieldset( array $aFieldset, $hfCallback )  {
            
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
            
        /**
         * Returns the field output with the given opening and closing HTML tags.
         * 
         * @since       3.4.0
         * @since       3.6.0       Removed the `$aFieldFinal` parameter. Changed the first parameter name from `$aField`.
         * @param       array       $aFieldset         The passed intact field definition array. The field rendering class needs non-finalized field array to construct the field array. 
         */
        private function _getFieldByContainer( array $aFieldset, $hfCallback, array $aOpenCloseTags ) {
            
            $aOpenCloseTags = $aOpenCloseTags + array(
                'open_container'    => '',
                'close_container'   => '',
                'open_title'        => '',
                'close_title'       => '',
                'open_main'         => '',
                'close_main'        => '',
            );
            
            $_aOutput   = array();
            if ( $aFieldset[ 'show_title_column' ] ) {
                $_aOutput[] = $aOpenCloseTags[ 'open_title' ]
                        . $this->_getFieldTitle( $aFieldset )
                    . $aOpenCloseTags[ 'close_title' ];
            }
            $_aOutput[] = $aOpenCloseTags[ 'open_main' ]
                    . call_user_func_array( $hfCallback, array( $aFieldset ) )
                . $aOpenCloseTags[ 'close_main' ];
                
            return $aOpenCloseTags[ 'open_container' ]
                    . implode( PHP_EOL, $_aOutput )
                . $aOpenCloseTags[ 'close_container' ];
            
        }
            
            
        /**
         * Returns the title part of the field output.
         * 
         * @since       3.0.0
         * @internal
         */
        private function _getFieldTitle( array $aField ) {
            
            $_oInputTagIDGenerator = new AdminPageFramework_Generate_FieldInputID( 
                $aField,
                0   // the first item
            );
            return "<label for='" . $_oInputTagIDGenerator->get() . "'>"
                    . "<a id='{$aField['field_id']}'></a>"  // to allow the browser to link to the element.
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
 
        /**
         * Merge the given field definition array with the field type default key array that holds default values.
         * 
         * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
         * which affects the way of rendering the row that contains the field output (by the field output callback).
         * 
         * @internal
         * @since       3.0.0
         * @since       3.4.0       Changed the name from `_mergeDefault()`.
         * @since       3.6.0       Changed the name from `mergeFIeldTYpeDefault`.
         * @remark      The returning merged field definition array does not respect sub-fields so when passing the field definition to the callback,
         * do not use the array returned from this method but the raw (non-merged) array.
         * @deprecated  3.6.0
         */
        private function _getMergedFieldTypeDefault( array $aFieldset, array $aFieldTypeDefinitions ) {
            return $this->uniteArrays( 
                $aFieldset, 
                $this->getElementAsArray(
                    $aFieldTypeDefinitions,
                    array( $aFieldset[ 'type' ], 'aDefaultKeys' ),
                    array()
                )
            );
        }        
 
}