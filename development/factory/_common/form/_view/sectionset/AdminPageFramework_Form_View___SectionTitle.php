<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render section title.
 * 
 * @package     AdminPageFramework/Common/Form/View/Section
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
         * Returns the section title output.
         * 
         * The `section_title` field will override the given section title.
         * 
         * @access      protected   An extended class accesses this method.
         * @since       3.0.0
         * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
         * @since       3.6.0       Added the `$iSectionIndex` and `$aFieldTypeDefinitions` parameters.
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Base`. Changed the name from `_getSectionTitle()`. 
         * @since       3.7.0      Moved from `AdminPageFramework_FormPart_SectionTitle`.
         * @return      string      The section title output. 
         */
        protected function _getSectionTitle( $sTitle, $sTag, $aFieldsets, $iSectionIndex=null, $aFieldTypeDefinitions=array(), $aCollapsible=array() ) {

            $_aSectionTitleFieldset = $this->_getSectionTitleField( $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions );
            $_sFieldsInSectionTitle = $this->_getFieldsetsOutputInSectionTitleArea( $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions );
            $_sTitle                = empty( $_aSectionTitleFieldset )
                ? $this->_getSectionTitleOutput( $sTitle, $sTag, $aCollapsible )
                : $this->getFieldsetOutput( $_aSectionTitleFieldset );
            $_bHasOtherFields       = $_sFieldsInSectionTitle
                ? ' has-fields'
                : '';
            $_sOutput               = $_sTitle . $_sFieldsInSectionTitle;
            return $_sOutput
                ? "<div class='section-title-height-fixer'></div>"
                . "<div class='section-title-outer-container'>" // 3.8.13+ For vertical alignment
                    . "<div class='section-title-container{$_bHasOtherFields}'>"
                        . $_sOutput
                    . "</div>"
                . "</div>"
                : '';
            
        }    
            /**
             * @since       3.8.7
             * @return      string
             */
            private function _getSectionTitleOutput( $sTitle, $sTag, $aCollapsible ) {
                
                $_aSectionset = $this->aArguments[ 'sectionset' ];
                return $sTitle
                    ? "<{$sTag} class='section-title'>"
                        . $this->_getCollapseButton( $aCollapsible )
                        . $sTitle
                        . $this->_getToolTip( $_aSectionset )
//                        . $this->_getDebugInfo( $_aSectionset )       // @deprecated 3.8.22
                    . "</{$sTag}>"
                    : '';

            }
                /**
                 * @return      string
                 */
                private function _getToolTip( $_aSectionset ) {
                    $_sSectionTitleTagID = str_replace( '|', '_', $_aSectionset[ '_section_path' ]  ) . '_' . $this->aArguments[ 'section_index' ];
                    $_oToolTip           = new AdminPageFramework_Form_View___ToolTip(
                        $_aSectionset[ 'tip' ],
                        $_sSectionTitleTagID
                    );            
                    return $_oToolTip->get();
                    
                }            
                /**
                 * Returns an output of the passed field argument.
                 * @since       3.8.8
                 * @deprecatd       3.8.22
                 * @return      string
                 */
                private function _getDebugInfo( $aSectionset ) {
                                        
                    if ( ! $aSectionset[ 'show_debug_info' ] ) {
                        return '';
                    }
                    $_oToolTip           = new AdminPageFramework_Form_View___ToolTip(
                        array(
                            'title'         => $this->oMsg->get( 'section_arguments' ),
                            'dash-icon'     => 'dashicons-info',
                            'icon_alt_text' => '[' . $this->oMsg->get( 'debug' ) . ' ]',
                            'content'       => AdminPageFramework_Debug::getDetails( $aSectionset )
                                . '<span class="admin-page-framework-info">'
                                    . $this->getFrameworkNameVersion()
                                    . '  ('
                                        . $this->oMsg->get( 'debug_info_will_be_disabled' )
                                      . ')'
                                . '</span>',
                            'attributes'    => array(
                                'container' => array(
                                    'class' => 'debug-info-field-arguments'
                                ),
                            )
                        ),
                        $aSectionset[ '_section_path' ] . '_debug'
                    );            
                    return $_oToolTip->get();                    
                    
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
             * @return      string
             * @since       3.8.0
             * @internal
             */
            private function _getFieldsetsOutputInSectionTitleArea( array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions ) {   

                $_sOutput = '';
                foreach( $this->_getFieldsetsInSectionTitleArea( $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions ) as $_aFieldset ) {
                    if ( empty( $_aFieldset ) )  {
                        continue;
                    }
                    $_sOutput .= $this->getFieldsetOutput( $_aFieldset );
                }
                return $_sOutput;
                
            }
                /**
                 * @internal
                 * @since       3.8.0
                 * @return      array       field-set definition arrays
                 */
                private function _getFieldsetsInSectionTitleArea( array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions ) {
                    
                    $_aFieldsetsInSectionTitle = array();
                    foreach( $aFieldsets as $_aFieldset ) {
                        
                        if ( 'section_title' !== $_aFieldset[ 'placement' ] ) {
                            continue;
                        }
                        
                        $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput( 
                            $_aFieldset, 
                            $iSectionIndex,
                            $aFieldTypeDefinitions
                        );         
                        $_aFieldsetsInSectionTitle[] = $_oFieldsetOutputFormatter->get();
                        
                    }
                    return $_aFieldsetsInSectionTitle;
                    
                }
            
                /**
                 * Returns the first found `section_title` field.
                 * 
                 * @internal
                 * @since       3.0.0
                 * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
                 * @since       3.6.0       Added the `$iSectionIndex` parameter. Added the `$aFieldTypeDefinitions` parameter.
                 * @since       3.7.0       Moved from `AdminPageFramework_FormPart_SectionTitle`.
                 * @return      array|void
                 */
                private function _getSectionTitleField( array $aFieldsets, $iSectionIndex, $aFieldTypeDefinitions ) {   
                
                    foreach( $aFieldsets as $_aFieldset ) {
                        
                        if ( 'section_title' !== $_aFieldset[ 'type' ] ) {
                            continue;
                        }
                        
                        // Return the first found one.    
                        $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput( 
                            $_aFieldset, 
                            $iSectionIndex,
                            $aFieldTypeDefinitions
                        );                    
                        return $_oFieldsetOutputFormatter->get(); 
                        
                    }
                    
                }

}
