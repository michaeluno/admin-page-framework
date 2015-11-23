<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a caption for form section tables.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View___SectionCaption extends AdminPageFramework_WPUtility {
  
    public $aSectionset             = array();
    public $iSectionIndex           = null;
    public $aFieldsets              = array();
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $aCallbacks              = array();
    public $oMsg                    = null;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* array $aSectionset, $iSectionIndex, $aFieldsets, $aSavedData, $aFieldErrors, $aFieldTypeDefinitions, $aCallbacks, $oMsg */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aSectionset, 
            $this->iSectionIndex, 
            $this->aFieldsets, 
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg,
        ); 
        $this->aSectionset           = $_aParameters[ 0 ];
        $this->iSectionIndex         = $_aParameters[ 1 ];
        $this->aFieldsets            = $_aParameters[ 2 ];
        $this->aSavedData            = $_aParameters[ 3 ];
        $this->aFieldErrors          = $_aParameters[ 4 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 5 ];
        $this->aCallbacks            = $_aParameters[ 6 ];
        $this->oMsg                  = $_aParameters[ 7 ];

    }

    /**
     * Returns an HTML output of a form table caption.
     * 
     * @return      string      The output of a form table caption.
     */
    public function get() {
        return $this->_getCaption( 
            $this->aSectionset, 
            $this->iSectionIndex, 
            $this->aFieldsets, 
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg
        );
    }
    
        /**
         * Returns the output of the table caption block.
         * 
         * @since       3.4.0
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`. Added the `$aFieldErrors` and `$oMsg` parameters.
         * @since       DEVVER      Moved from `AdminPageFramework_FormPart_TableCaptions`.
         * @return      string
         */
        private function _getCaption( array $aSectionset, $iSectionIndex, $aFieldsets, $aFieldErrors, $aFieldTypeDefinitions, $aCallbacks, $oMsg ) {
            
            if ( ! $aSectionset[ 'description' ] && ! $aSectionset[ 'title' ] ) {
                return "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>";
            }    

            $_oArgumentFormater = new AdminPageFramework_Form_Model___Format_CollapsibleSection(
                $aSectionset[ 'collapsible' ],
                $aSectionset[ 'title' ],
                $aSectionset            
            );
            $_abCollapsible = $_oArgumentFormater->get();            
            
            $_oCollapsibleSectionTitle = new AdminPageFramework_Form_View___CollapsibleSectionTitle(
                array(
                    'title'             => $this->getElement( 
                        $_abCollapsible, 
                        'title', 
                        $aSectionset[ 'title' ] 
                    ),
                    'tag'               => 'h3',
                    'section_index'     => $iSectionIndex,
                    'collapsible'       => $_abCollapsible,
                    'container_type'    => 'section', // section or sections                    
                    
                    'sectionset'        => $aSectionset,    // DEVVER+ for tooltip
                ),
                $aFieldsets,            
                $this->aSavedData,   
                $this->aFieldErrors, 
                $aFieldTypeDefinitions, 
                $oMsg,
                $aCallbacks // field output element callables.                
            );            
            
            $_bShowTitle    = empty( $_abCollapsible ) && ! $aSectionset[ 'section_tab_slug' ];
            return 
                "<caption " . $this->getAttributes( 
                    array(
                        'class'             => 'admin-page-framework-section-caption',
                        // data-section_tab is referred by the repeater script to hide/show the title and the description
                        'data-section_tab'  => $aSectionset[ 'section_tab_slug' ],
                    ) 
                ) . ">"
                    . $_oCollapsibleSectionTitle->get()
                    . $this->getAOrB(
                        $_bShowTitle,
                        $this->_getCaptionTitle( 
                            $aSectionset, 
                            $iSectionIndex, 
                            $aFieldsets, 
                            $aFieldTypeDefinitions 
                        ),
                        ''
                    )
                    . $this->_getCaptionDescription( $aSectionset, $aCallbacks[ 'section_head_output' ] )
                    . $this->_getSectionError( $aSectionset, $aFieldErrors )
                . "</caption>";
            
        }   
            /**
             * Returns the section validation error message.
             * 
             * @since       3.4.0
             * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`. Added the `$aFieldErrors` parameter. 
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_TableCaptions`.
             * @return      string
             */
            private function _getSectionError( $aSectionset, $aFieldErrors ) {
          
                $_sSectionID    = $aSectionset[ 'section_id' ];
                $_sSectionError = isset( $aFieldErrors[ $_sSectionID ] ) && is_string( $aFieldErrors[ $_sSectionID ] )
                    ? $aFieldErrors[ $_sSectionID ]
                    : '';          
                return $_sSectionError  
                    ? "<div class='admin-page-framework-error'><span class='section-error'>* "
                            . $_sSectionError 
                        .  "</span></div>"
                    : '';  
                    
            }
            /**
             * Returns the section title block for the section table caption block.
             * 
             * @since       3.4.0
             * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`.
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_TableCaptions`. 
             * Removed the `$hfFieldCallback` parameter.
             * @return      string
             */
            private function _getCaptionTitle( $aSectionset, $iSectionIndex, $aFieldsets, $aFieldTypeDefinitions ) {
                $_oSectionTitle = new AdminPageFramework_Form_View___SectionTitle(
                    array(
                        'title'         => $aSectionset[ 'title' ],
                        'tag'           => 'h3',
                        'section_index' => $iSectionIndex,
                        
                        'sectionset'    => $aSectionset,    // DEVVER+ for tooltip
                    ),
                    $aFieldsets,            
                    $this->aSavedData,   
                    $this->aFieldErrors, 
                    $aFieldTypeDefinitions, 
                    $this->oMsg,
                    $this->aCallbacks // field output element callables.
                );
                return "<div " . $this->getAttributes(
                        array(
                            'class' => 'admin-page-framework-section-title',
                            'style' => $this->getAOrB(
                                $this->_shouldShowCaptionTitle( $aSectionset, $iSectionIndex ),
                                '',
                                'display: none;'
                            ),
                        )
                    ). ">" 
                        . $_oSectionTitle->get()
                    . "</div>";                
            }
            /**
             * Returns the section description for the section table caption block.
             * @since       3.4.0
             * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`.
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_TableCaptions`.
             * @return      string
             */
            private function _getCaptionDescription( $aSectionset, $hfSectionCallback ) {
                
                if ( $aSectionset[ 'collapsible' ] ) {
                    return '';
                }
                if ( ! is_callable( $hfSectionCallback ) ) {
                    return '';
                }
                
                // The class selector 'admin-page-framework-section-description' is referred by the repeatable section buttons
                // @todo        Use a different selector name other than 'admin-page-framework-section-description' as it is used in the inner <p> tag element as well.
                // Descriptions
                $_oSectionDescription = new AdminPageFramework_Form_View___Description(
                    $aSectionset[ 'description' ],
                    'admin-page-framework-section-description'    // class selector
                );            
                return "<div class='admin-page-framework-section-description'>"     
                    . call_user_func_array(
                        $hfSectionCallback, 
                        array( 
                            $_oSectionDescription->get(),
                            $aSectionset 
                        ) 
                    )
                . "</div>";

            }
            /**
             * Returns whether the title in the caption block should be displayed or not.
             * 
             * @since   3.4.0
             * @since   3.6.0       Moved from `AdminPageFramework_FormTable_Caption`.
             * @since   DEVVER      Moved from `AdminPageFramework_FormPart_TableCaptions`.
             * @return  boolean
             */
            private function _shouldShowCaptionTitle( $aSectionset, $iSectionIndex ) {
                
                if ( ! $aSectionset[ 'title' ] ){
                    return false;
                }
                if ( $aSectionset[ 'collapsible' ] ) {
                    return false;
                }
                if ( $aSectionset[ 'section_tab_slug' ] ) {
                    return false;
                }
                if ( $aSectionset[ 'repeatable' ] && $iSectionIndex != 0 ) {
                    return false;
                }
                return true;                
                
            }     
    
}