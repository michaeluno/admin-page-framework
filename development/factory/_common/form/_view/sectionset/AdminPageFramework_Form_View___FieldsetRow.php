<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a fieldset.
 * 
 * @package     AdminPageFramework/Common/Form/View/Field
 * @since       3.6.0 
 * @internal
 */
class AdminPageFramework_Form_View___FieldsetRow extends AdminPageFramework_Form_View___FieldsetTableRow {

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
   
        $_oFieldrowAttribute = new AdminPageFramework_Form_View___Attribute_Fieldrow( 
            $aFieldset 
        );
        
        return $this->_getFieldByContainer( 
            $aFieldset, 
            array(
                'open_main'     => "<div " . $_oFieldrowAttribute->get() . ">",
                'close_main'    => "</div>",
            )
        ); 
  

    }
    
}
