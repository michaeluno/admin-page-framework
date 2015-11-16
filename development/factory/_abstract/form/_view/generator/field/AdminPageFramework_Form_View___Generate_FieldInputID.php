<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field id.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FieldInputID extends AdminPageFramework_Form_View___Generate_FieldTagID {
    
    public $isIndex = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $isIndex, $hfCallback */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments, 
            $this->isIndex,
            $this->hfCallback,
        );
        $this->aArguments  = $_aParameters[ 0 ];        
        $this->isIndex     = $_aParameters[ 1 ];
        $this->hfCallback  = $_aParameters[ 2 ];
        
    }    
    
    /**
     * Returns the input id attribute value.
     * 
     * e.g. "{$aField['field_id']}__{$isIndex}";
     * 
     * @remark      The index keys are prefixed with double-underscores.
     * @remark      `AdminPageFramework_FormPart_Table_Row` will also access this method so this method is public.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Made it static public because the `<for>` tag needs to refer to it and it is called from another class that renders the form table. Added a default value for the <var>$isIndex</var> parameter.
     * @since       3.6.0       Moved from `AdminPageFramework_FormField`. Changed the scope to be not static.
     * @return      string       The generated string value.
     */
    public function get() {
        return $this->_getFiltered( $this->_getBaseFieldTagID() . '__' . $this->isIndex );
    }
           
}