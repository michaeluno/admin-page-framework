<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a table row.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_FormPart_TableRow extends AdminPageFramework_FormPart_Base {            

    public $aFieldset               = array();
    public $hfCallback              = null;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* array $aFieldset, $hfCallback */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->hfCallback
        );
        $this->aFieldset                = $_aParameters[ 0 ];
        $this->hfCallback               = $_aParameters[ 1 ];

    }

    /**
     * Returns an HTML output of a fieldset row.
     * 
     * @return      string      The output of a field set row.
     */
    public function get() {
        return $this->_getRow( 
            $this->aFieldset, 
            $this->hfCallback 
        );
    }
    
        /**
         * Returns the field output enclosed in a table row.
         * 
         * @since       3.0.0
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`. Changed the name from `_getFieldRow()`.
         * @return      string
         */
        protected function _getRow( array $aFieldset, $hfCallback ) {
            
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
                        . $this->getAttributes( 
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
             * Returns the field output with the given opening and closing HTML tags.
             * 
             * @since       3.4.0
             * @since       3.6.0       Removed the `$aFieldFinal` parameter. Changed the first parameter name from `$aField`.
             * @param       array       $aFieldset         The passed intact field definition array. The field rendering class needs non-finalized field array to construct the field array. 
             */
            protected function _getFieldByContainer( array $aFieldset, $hfCallback, array $aOpenCloseTags ) {
                
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
                 * @internal
                 * @since       3.0.0
                 * @return      string
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
    
}