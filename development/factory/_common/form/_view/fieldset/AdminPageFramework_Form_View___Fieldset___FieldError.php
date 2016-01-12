<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render field error messages for a fieldset.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form_View___Fieldset___FieldError extends AdminPageFramework_FrameworkUtility {

    public $aErrors         = array();
    public $aSectionPath    = array();
    public $aFieldPath      = array();
    public $sHeadingMessage = '';
    
    /**
     * Sets up properties.
     * @since       3.7.0
     */
    public function __construct( /* $aErrors, $sSectionPath, $sFieldPath */ ) {
      
        $_aParameters = func_get_args() + array( 
            $this->aErrors, 
            $this->aSectionPath, 
            $this->aFieldPath,
            $this->sHeadingMessage,
        );
        $this->aErrors         = $_aParameters[ 0 ];
        $this->aSectionPath    = $_aParameters[ 1 ];
        $this->aFieldPath      = $_aParameters[ 2 ];
        $this->sHeadingMessage = $_aParameters[ 3 ]; // an error message to put before the main error
        
    }
    
    /**
     * Returns the output of an error of the field if exists.
     * 
     * @return      string
     * @since       3.7.0
     */
    public function get() {
        return $this->_getFieldError(
            $this->aErrors,
            $this->_getSectionPathSanitized( $this->aSectionPath ),
            $this->aFieldPath,
            $this->sHeadingMessage
        );
    }
        /**
         * Removes the '_default' dimension if exists.
         * 
         * The structure of field error arrays corresponds to the options array structure 
         * and there is no internal default section dimensions, meaning fields without a section
         * is stored directly in the root dimension.
         * 
         * @return      array
         */
        private function _getSectionPathSanitized( $aSectionPath ) {
            if ( '_default' === $this->getElement( $aSectionPath, 0 ) ) {
                array_shift( $aSectionPath );
            }
            return $aSectionPath;
        }
        /**
         * Returns the set field error message to the section or field.
         * 
         * @since       3.1.0
         * @since       3.7.0      Moved from `AdminPageFramework_Form_View___Fieldset`.
         * @return      string      The error string message. An empty value if not found.
         */
        private function _getFieldError( $aErrors, $aSectionPath, $aFieldPath, $sHeadingMessage ) {

            // If this field has a section and the error element is set
            $_aErrorPath   = array_merge( $aSectionPath, $aFieldPath );
            if ( $this->_hasFieldError( $aErrors, $_aErrorPath ) ) {
                return "<span class='field-error'>*&nbsp;"
                        . $sHeadingMessage
                        . $this->getElement( $aErrors, $_aErrorPath )
                    . "</span>";
            }  
            return '';
            
        }    
            /**
             * Checks whether the given field has a field error.
             * @internal
             * @since       3.5.3
             * @since       3.7.0      Changed the 2nd parameter to accept an array of field address.
             * @return      boolean
             */
            private function _hasFieldError( $aErrors, array $aFieldAddress ) {
                return is_scalar( $this->getElement( $aErrors, $aFieldAddress ) );
            }    
    
    
}
