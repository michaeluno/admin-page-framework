<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render section title.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @since       3.7.0      Changed the name from `AdminPageFramework_FormPart_SectionTitle`.
 * @internal
 */
class AdminPageFramework_Form_View___SectionTitle extends AdminPageFramework_Form_View___Section_Base {            
  
    public $aArguments      = array(
        'title'         => null,
        'tag'           => null,
        'section_index' => null,
        
        'sectionset'    => array(),
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
     * @since       3.7.0      Changed the parameter structure.
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
        $_sTitle = $this->_getSectionTitle( 
            $this->aArguments[ 'title' ], 
            $this->aArguments[ 'tag' ],
            $this->aFieldsets,
            $this->aArguments[ 'section_index' ],
            $this->aFieldTypeDefinitions
        );       
        return $_sTitle;
    }
        /**
         * 
         */
        private function _getToolTip() {
            
            $_aSectionset        = $this->aArguments[ 'sectionset' ];
            $_sSectionTitleTagID = str_replace( '|', '_', $_aSectionset[ '_section_path' ]  ) . '_' . $this->aArguments[ 'section_index' ];
            $_oToolTip           = new AdminPageFramework_Form_View___ToolTip(
                $_aSectionset[ 'tip' ],
                $_sSectionTitleTagID
            );            
            return $_oToolTip->get();
            
        }
        /**
         * Returns the section title output.
         * 
         * @scope       protected   An extended class accesses this method.
         * @since       3.0.0
         * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
         * @since       3.6.0       Added the `$iSectionIndex` and `$aFieldTypeDefinitions` parameters.
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Base`. Changed the name from `_getSectionTitle()`. 
         * @since       3.7.0      Moved from `AdminPageFramework_FormPart_SectionTitle`.
         * @return      string      The section title output. 
         */
        protected function _getSectionTitle( $sTitle, $sTag, $aFieldsets, $iSectionIndex=null, $aFieldTypeDefinitions=array(), $aCollapsible=array() ) {
       
            $_aSectionTitleField = $this->_getSectionTitleField( $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions );
            return $_aSectionTitleField
                ? $this->getFieldsetOutput( $_aSectionTitleField )
                : "<{$sTag}>" 
                        . $this->_getCollapseButton( $aCollapsible )
                        . $sTitle 
                        . $this->_getToolTip()
                    . "</{$sTag}>";
            
        }    
            /**
             * Returns a collapse button for the 'button' collapsible type.
             * @since       3.7.0
             * @return      string
             */
            private function _getCollapseButton( $aCollapsible ) {
                $_sExpand   = esc_attr( $this->oMsg->get( 'click_to_expand' ) );
                $_sCollapse = esc_attr( $this->oMsg->get( 'click_to_collapse' ) );
                return $this->getAOrB(
                    'button' === $this->getElement( $aCollapsible, 'type', 'box' ),
                    "<span class='admin-page-framework-collapsible-button admin-page-framework-collapsible-button-expand' title='{$_sExpand}'>&#9658;</span>"
                    . "<span class='admin-page-framework-collapsible-button admin-page-framework-collapsible-button-collapse' title='{$_sCollapse}'>&#9660;</span>",
                    ''
                );                
            }
            /**
             * Returns the first found `section_title` field.
             * 
             * @since       3.0.0
             * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
             * @since       3.6.0       Added the `$iSectionIndex` parameter. Added the `$aFieldTypeDefinitions` parameter.
             * @since       3.7.0      Moved from `AdminPageFramework_FormPart_SectionTitle`.
             */
            private function _getSectionTitleField( array $aFieldsetsets, $iSectionIndex, $aFieldTypeDefinitions ) {   
            
                foreach( $aFieldsetsets as $_aFieldsetset ) {
                    
                    if ( 'section_title' !== $_aFieldsetset[ 'type' ] ) {
                        continue;
                    }
                    
                    // Return the first found one.    
                    $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput( 
                        $_aFieldsetset, 
                        $iSectionIndex,
                        $aFieldTypeDefinitions
                    );                    
                    return $_oFieldsetOutputFormatter->get(); 
                    
                }
            }

}