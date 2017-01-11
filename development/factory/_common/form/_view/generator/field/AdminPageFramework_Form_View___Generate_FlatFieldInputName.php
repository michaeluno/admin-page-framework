<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates flat field input name.
 * 
 * @package     AdminPageFramework/Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FlatFieldInputName extends AdminPageFramework_Form_View___Generate_FieldInputName {

    /**
     * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
     * 
     * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
     * This is used to create a reference to the submit field name to determine which button is pressed.
     * 
     * @remark      Used by the import and submit field types.
     * @since       2.0.0
     * @since       2.1.5       Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_FormField.
     * @since       3.0.0       Moved from the submit field type class. Dropped the page slug dimension.
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.6.0       Changed the scope to `private` from `protected` to help understand this method is only accessed internally.
     * @return      string       The generated string value.
     */
    public function get() {
        
        $_sIndex = $this->getAOrB(
            '0' !== $this->sIndex && empty( $this->sIndex ),
            '',
            "|{$this->sIndex}"
        );                
        return $this->_getFiltered( $this->_getFlatFieldName() . $_sIndex );
        
    }
 
}
