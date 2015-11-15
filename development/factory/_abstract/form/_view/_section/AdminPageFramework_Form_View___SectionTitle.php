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
 * @since       DEVVER      Changed the name from `AdminPageFramework_FormPart_SectionTitle`.
 * @internal
 */
class AdminPageFramework_Form_View___SectionTitle extends AdminPageFramework_Form_View___Section_Base {            
  
    public $aArguments      = array(
        'title'         => null,
        'tag'           => null,
        'section_index' => null,
    );
    public $aFieldsets               = array();
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $oMsg;
    public $aCallbacks = array(
        'fieldset_output',
        'is_fieldset_visible'   => null,
    );
    
    /**
     * Sets up properties.
     * @since       3.6.0
     * @since       DEVVER      Changed the parameter structure.
     */
    public function __construct( /* $aArguments, $aFieldsets, $aSavedData, $aFieldErrors, $aFieldTypeDefinitions, $oMsg, $aCallbacks */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aArguments,
            $this->aFieldsets,            
            $this->aSavedData,   
            $this->aFieldErrors, 
            $this->aFieldTypeDefinitions, 
            $this->oMsg,
            $this->aCallbacks // field output element callables.                  
        );

        $this->aArguments               = $_aParameters[ 0 ] + $this->aArguments;
        $this->aFieldsets               = $_aParameters[ 1 ];
        $this->aSavedData               = $_aParameters[ 2 ];
        $this->aFieldErrors             = $_aParameters[ 3 ];
        $this->aFieldTypeDefinitions    = $_aParameters[ 4 ];
        $this->oMsg                     = $_aParameters[ 5 ];        
        $this->aCallbacks               = $_aParameters[ 6 ];        
        
    }

    /**
     * Returns HTML formatted description blocks by the given description definition.
     * 
     * @return      string      The output.
     */
    public function get() {
        return $this->_getSectionTitle( 
            $this->aArguments[ 'title' ], 
            $this->aArguments[ 'tag' ],
            $this->aFieldsets,
            $this->aArguments[ 'section_index' ],
            $this->aFieldTypeDefinitions
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
         * @since       DEVVER      Moved from `AdminPageFramework_FormPart_SectionTitle`.
         * @return      string      The section title output. 
         */
        protected function _getSectionTitle( $sTitle, $sTag, $aFieldsets, $iSectionIndex=null, $aFieldTypeDefinitions=array() ) {
       
            $_aSectionTitleField = $this->_getSectionTitleField( $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions );
            return $_aSectionTitleField
                ? $this->getFieldsetOutput( $_aSectionTitleField )
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
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_SectionTitle`.
             */
            private function _getSectionTitleField( array $aFieldsetsets, $iSectionIndex, $aFieldTypeDefinitions ) {   
            
                foreach( $aFieldsetsets as $_aFieldsetset ) {
                    
                    if ( 'section_title' !== $_aFieldsetset[ 'type' ] ) {
                        continue;
                    }
                    
                    // Return the first found one.    
                    $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                        $_aFieldsetset, 
                        $iSectionIndex,
                        $aFieldTypeDefinitions
                    );                    
                    return $_oFieldsetOutputFormatter->get(); 
                    
                }
            }

}