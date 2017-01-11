<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a table row of a form section.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Field
 * @since       3.6.0 
 * @since       3.7.0      Moved from `AdminPageFramework_FormPart_TableRow`.
 * @internal
 */
class AdminPageFramework_Form_View___FieldsetTableRow extends AdminPageFramework_Form_View___Section_Base {

    public $aFieldset               = array();
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $aCallbacks              = array();
    public $oMsg;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* array $aFieldset, $aSavedData, $aFieldErrors, $aFieldTypeDefinitions, $oMsg, $aCallbacks */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->aSavedData,    // passed by reference. @todo: examine why it needs to be passed by reference.
            $this->aFieldErrors, 
            $this->aFieldTypeDefinitions, 
            $this->aCallbacks, // field output element callables.        
            $this->oMsg,
        );
        $this->aFieldset                = $_aParameters[ 0 ];
        $this->aSavedData               = $_aParameters[ 1 ];
        $this->aFieldErrors             = $_aParameters[ 2 ]; 
        $this->aFieldTypeDefinitions    = $_aParameters[ 3 ];
        $this->aCallbacks               = $_aParameters[ 4 ];
        $this->oMsg                     = $_aParameters[ 5 ];     
        
    }

    /**
     * Returns an HTML output of a fieldset row enclosed in a table row.
     * 
     * @since       3.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`. Changed the name from `_getFieldRow()`.
     * @since       3.7.0      Moved from `AdminPageFramework_FormPart_TableRow`.
     * Changed the name from `_getRow()`.
     * @return      string      The output of a field set row.
     */
    public function get() {
        
        $aFieldset = $this->aFieldset;

        if ( ! $this->isNormalPlacement( $aFieldset ) ) {
            return '';
        }

        $_oFieldrowAttribute   = new AdminPageFramework_Form_View___Attribute_Fieldrow( 
            $aFieldset,
            array( 
                'id'        => 'fieldrow-' . $aFieldset[ 'tag_id' ],
                'valign'    => 'top',
                'class'     => 'admin-page-framework-fieldrow',
            )                
        );
        
        return $this->_getFieldByContainer( 
            $aFieldset, 
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
         * @since       3.7.0       Moved from `AdminPageFramework_FormPart_TableRow`.
         * @param       array       $aFieldset         The passed intact field definition array. The field rendering class needs non-finalized field array to construct the field array. 
         */
        protected function _getFieldByContainer( array $aFieldset, array $aOpenCloseTags ) {
            
            $aOpenCloseTags = $aOpenCloseTags + array(
                'open_container'    => '',
                'close_container'   => '',
                'open_title'        => '',
                'close_title'       => '',
                'open_main'         => '',
                'close_main'        => '',
            );
            
            $_oFieldTitle = new AdminPageFramework_Form_View___FieldTitle( 
                $aFieldset, 
                '', 
                $this->aSavedData,   
                $this->aFieldErrors, 
                $this->aFieldTypeDefinitions, 
                $this->aCallbacks,
                $this->oMsg
            );
            $_aOutput   = array();
            if ( $aFieldset[ 'show_title_column' ] ) {
                $_aOutput[] = $aOpenCloseTags[ 'open_title' ]
                        . $_oFieldTitle->get()
                    . $aOpenCloseTags[ 'close_title' ];
            }
            $_aOutput[] = $aOpenCloseTags[ 'open_main' ]
                    // . call_user_func_array( $hfCallback, array( $aFieldset ) )
                    . $this->getFieldsetOutput( $aFieldset )
                . $aOpenCloseTags[ 'close_main' ];
                
            return $aOpenCloseTags[ 'open_container' ]
                    . implode( PHP_EOL, $_aOutput )
                . $aOpenCloseTags[ 'close_container' ];
            
        }        
                    
}
