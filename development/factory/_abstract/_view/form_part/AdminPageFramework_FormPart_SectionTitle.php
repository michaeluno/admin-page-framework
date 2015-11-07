<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render section title.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_FormPart_SectionTitle extends AdminPageFramework_FormPart_Base {            

    public $sTitle                  = '';
    public $sTag                    = '';
    public $aFields                 = array();
    public $hfFieldCallback         = null;
    public $iSectionIndex           = null;
    public $aFieldTypeDefinitions   = array();
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $sTitle, $sTag, $aFields, $hfFieldCallback, $iSectionIndex=null, $aFieldTypeDefinitions=array() */ ) {

        $_aParameters = func_get_args() + array( 
            $this->sTitle, 
            $this->sTag,
            $this->aFields,
            $this->hfFieldCallback,
            $this->iSectionIndex,
        );
        $this->sTitle                   = $_aParameters[ 0 ];
        $this->sTag                     = $_aParameters[ 1 ];
        $this->aFields                  = $_aParameters[ 2 ];
        $this->hfFieldCallback          = $_aParameters[ 3 ];
        $this->iSectionIndex            = $_aParameters[ 4 ];
        $this->aFieldTypeDefinitions    = $_aParameters[ 5 ];

    }

    /**
     * Returns HTML formatted description blocks by the given description definition.
     * 
     * @return      string      The output.
     */
    public function get() {
        return $this->_getSectionTitle( 
            $this->sTitle, 
            $this->sTag, 
            $this->aFields, 
            $this->hfFieldCallback, 
            $this->iSectionIndex
        );       
    }

        /**
         * Returns the section title output.
         * 
         * @scope       protected   An extended class accesses this method.
         * @since       3.0.0
         * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
         * @since       3.6.0       Added the `$iSectionIndex` and `$aFieldTypeDefinitions` parameters.
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Base`. Changed the name from `_getSectionTitle()`. 
         * @return      string      The section title output. 
         */
        protected function _getSectionTitle( $sTitle, $sTag, $aFields, $hfFieldCallback, $iSectionIndex=null, $aFieldTypeDefinitions=array() ) {
            
            $_aSectionTitleField = $this->_getSectionTitleField( $aFields, $iSectionIndex, $aFieldTypeDefinitions );
            return $_aSectionTitleField
                ? call_user_func_array( $hfFieldCallback, array( $_aSectionTitleField ) )
                : "<{$sTag}>" 
                        . $sTitle 
                    . "</{$sTag}>";
            
        }    
            /**
             * Returns the first found `section_title` field.
             * 
             * @since       3.0.0
             * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
             * @since       3.6.0       Added the `$iSectionIndex` parameter. Added the `$aFieldTypeDefinitions` parameter.
             */
            private function _getSectionTitleField( array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions ) {   
                foreach( $aFieldsets as $_aFieldset ) {
                    // Return the first found one.
                    if ( 'section_title' === $_aFieldset[ 'type' ] ) {
                        
                        $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                            $_aFieldset, 
                            $iSectionIndex,
                            $aFieldTypeDefinitions
                        );                    
                        return $_oFieldsetOutputFormatter->get(); 
                        
                    }
                }
            }

}