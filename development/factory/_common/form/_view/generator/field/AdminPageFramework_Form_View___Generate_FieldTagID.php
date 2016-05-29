<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field container tag id.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FieldTagID extends AdminPageFramework_Form_View___Generate_Field_Base {
        
    /**
     * Returns the field input base ID used for field container elements.
     * 
     * The returning value does not represent the exact ID of the field input tag. 
     * This is because each input tag has an index for sub-fields.
     * 
     * @remark      This is called from the fields table class to insert the row id.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Changed the name from `_getInputTagID()`.
     * @since       3.6.0       Moved from `AdminPageFramework_FormField`.
     * @return      string      The generated string value.
     */
    public function get() {            
        return $this->_getFiltered( $this->_getBaseFieldTagID() );
    }
    
    /**
     * Returns an id model that indicates where to be replaced with an index.
     * @since       3.6.0
     * @return      string
     */
    public function getModel() {
        return $this->get() . '__' . $this->sIndexMark;
    }
        /**
         * @since       3.6.0
         * @return      string      Returns the base tag id meant to be used before applying to a filter callback,
         */
        protected function _getBaseFieldTagID() {
            
            // 3.8.0+
            if ( $this->aArguments[ '_parent_tag_id' ] ) {
                return $this->aArguments[ '_parent_tag_id' ] . '_' . $this->aArguments[ 'field_id' ];
            }
        
            $_sSectionIndex = isset( $this->aArguments[ '_section_index' ] )
                ? '__' . $this->aArguments[ '_section_index' ] 
                : '';
            $_sSectionPart = implode( '_', $this->aArguments[ '_section_path_array' ] );
            $_sFieldPart   = implode( '_', $this->aArguments[ '_field_path_array' ] );

            return $this->_isSectionSet()
                ? $_sSectionPart . $_sSectionIndex . '_' . $_sFieldPart
                : $_sFieldPart;
            
        }

}
