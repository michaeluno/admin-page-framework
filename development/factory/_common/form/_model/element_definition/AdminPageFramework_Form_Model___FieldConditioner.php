<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

 
/**
 * Provides methods to build forms.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model
 * @since       3.7.0
 * @internal
 * @todo        This may be deprecated. Investigate what this was for and why this is not used at the moment. 
 * It seems the form works properly without this conditioning routine.
 */
class AdminPageFramework_Form_Model___FieldConditioner extends AdminPageFramework_Form_Model___SectionConditioner {
    
    public $aSectionsets  = array();
    public $aFieldsets    = array();

    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( /* $aSectionsets, $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSectionsets, 
            $this->aFieldsets,
        );
        $this->aSectionsets  = $_aParameters[ 0 ];                    
        $this->aFieldsets    = $_aParameters[ 1 ];
        
    }

    /**
     * @since       3.7.0
     * @return      array       The conditioned fieldsets array.
     */
    public function get() {
        return $this->_getFieldsConditioned( 
            $this->aFieldsets,
            $this->aSectionsets
        );
    }
        /**
         * Returns a fields-array by applying the conditions.
         * 
         * @remark      Assumes sections are conditioned already.
         * @since       3.0.0
         * @since       3.5.3       Added type hints to the parameters and removed default values.
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition`. Changed the name from `getConditionedFields()`.
         * @return      array       The conditioned fieldsets array.
         */
        private function _getFieldsConditioned( array $aFields, array $aSections ) {

            // Drop keys of fields-array which do not exist in the sections-array. 
            // For this reasons, the sections-array should be conditioned first before applying this method.
            $aFields    = $this->castArrayContents( $aSections, $aFields );

            $_aNewFields = array();
            foreach( $aFields as $_sSectionID => $_aSubSectionOrFields ) {
                
                // This type check is important as the parsing field array is content-cast, which can set null value to elements.
                if ( ! is_array( $_aSubSectionOrFields ) ) { 
                    continue; 
                }
                            
                $this->_setConditionedFields( 
                    $_aNewFields,   // by reference - gets updated in the method.
                    $_aSubSectionOrFields, 
                    $_sSectionID
                );
          
            }
                    
            return $_aNewFields;
            
        }     
            /**
             * Updates the given array of conditioned fields.
             * 
             * @since       3.5.3
             * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition`.
             * @internal
             * @return      void
             */
            private function _setConditionedFields( array &$_aNewFields, $_aSubSectionOrFields, $_sSectionID ) {
                
                foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
                    
                    // If it is a sub-section array.
                    if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
                        $_sSubSectionIndex  = $_sIndexOrFieldID;
                        $_aFields           = $_aSubSectionOrField;
                        foreach( $_aFields as $_aField ) {
                            if ( ! $this->_isAllowed( $_aField ) ) {
                                continue;
                            }
                            $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField[ 'field_id' ] ] = $_aField;
                        }
                        continue;
                        
                    }
                    
                    // Otherwise, insert the formatted field definition array.
                    $_aField = $_aSubSectionOrField;
                    if ( ! $this->_isAllowed( $_aField ) ) {
                        continue;
                    }
                    $_aNewFields[ $_sSectionID ][ $_aField[ 'field_id' ] ] = $_aField;
                    
                }            
                
            }
   
    
}
