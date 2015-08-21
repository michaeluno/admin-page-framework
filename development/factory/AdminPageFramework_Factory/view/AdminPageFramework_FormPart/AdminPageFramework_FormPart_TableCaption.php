<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render a table caption.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_FormPart_TableCaption extends AdminPageFramework_FormPart_Base {            

    public $aSection                = array();
    public $hfSectionCallback       = null;
    public $iSectionIndex           = null;
    public $aFields                 = array();
    public $hfFieldCallback         = null;
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $oMsg                    = null;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* array $aSection, $hfSectionCallback, $iSectionIndex, $aFields, $hfFieldCallback, $aFieldErrors, $aFieldTypeDefinitions, $oMsg */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aSection, 
            $this->hfSectionCallback, 
            $this->iSectionIndex, 
            $this->aFields, 
            $this->hfFieldCallback,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->oMsg,
        );
        $this->aSection              = $_aParameters[ 0 ];
        $this->hfSectionCallback     = $_aParameters[ 1 ];
        $this->iSectionIndex         = $_aParameters[ 2 ];
        $this->aFields               = $_aParameters[ 3 ];
        $this->hfFieldCallback       = $_aParameters[ 4 ];
        $this->aFieldErrors          = $_aParameters[ 5 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 6 ];
        $this->oMsg                  = $_aParameters[ 7 ];

    }

    /**
     * Returns an HTML output of a form table caption.
     * 
     * @return      string      The output of a form table caption.
     */
    public function get() {
        return $this->_getCaption( 
            $this->aSection, 
            $this->hfSectionCallback, 
            $this->iSectionIndex, 
            $this->aFields, 
            $this->hfFieldCallback,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->oMsg
        );
    }
    
        /**
         * Returns the output of the table caption block.
         * 
         * @since       3.4.0
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`. Added the `$aFieldErrors` and `$oMsg` parameters.
         * @return      string
         */
        private function _getCaption( array $aSection, $hfSectionCallback, $iSectionIndex, $aFields, $hfFieldCallback, $aFieldErrors, $aFieldTypeDefinitions, $oMsg ) {
            
            if ( ! $aSection['description'] && ! $aSection['title'] ) {
                return "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>";
            }    

            $_oArgumentFormater = new AdminPageFramework_Format_CollapsibleSection(
                $aSection[ 'collapsible' ],
                $aSection[ 'title' ],
                $aSection            
            );
            $_abCollapsible = $_oArgumentFormater->get();            
            
            $_oCollapsibleSectionTitle = new AdminPageFramework_FormPart_CollapsibleSectionTitle(
                isset( $_abCollapsible[ 'title' ] )    
                    ? $_abCollapsible[ 'title' ]
                    : $aSection[ 'title' ],
                'h3',
                $aFields,  // fields
                $hfFieldCallback,  // field callback
                $iSectionIndex,  // section index
                $aFieldTypeDefinitions,                
                $_abCollapsible, 
                'section',
                $oMsg
            );            
            
            $_bShowTitle    = empty( $_abCollapsible ) && ! $aSection[ 'section_tab_slug' ];
            return 
                "<caption " . $this->generateAttributes( 
                    array(
                        'class'             => 'admin-page-framework-section-caption',
                        // data-section_tab is referred by the repeater script to hide/show the title and the description
                        'data-section_tab'  => $aSection['section_tab_slug'],
                    ) 
                ) . ">"
                    . $_oCollapsibleSectionTitle->get()
                    . $this->getAOrB(
                        $_bShowTitle,
                        $this->_getCaptionTitle( 
                            $aSection, 
                            $iSectionIndex, 
                            $aFields, 
                            $hfFieldCallback, 
                            $aFieldTypeDefinitions 
                        ),
                        ''
                    )
                    . $this->_getCaptionDescription( $aSection, $hfSectionCallback )
                    . $this->_getSectionError( $aSection, $aFieldErrors )
                . "</caption>";
            
        }   
            /**
             * Returns the section validation error message.
             * 
             * @since       3.4.0
             * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Caption`. Added the `$aFieldErrors` parameter. 
             * @return      string
             */
            private function _getSectionError( $aSection, $aFieldErrors ) {
          
                $_sSectionID    = $aSection[ 'section_id' ];
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
             * @return      string
             */
            private function _getCaptionTitle( $aSection, $iSectionIndex, $aFields, $hfFieldCallback, $aFieldTypeDefinitions ) {
                $_oSectionTitle = new AdminPageFramework_FormPart_SectionTitle(
                    $aSection[ 'title' ],
                    'h3', 
                    $aFields, 
                    $hfFieldCallback, 
                    $iSectionIndex, 
                    $aFieldTypeDefinitions
                );
                return "<div " . $this->generateAttributes(
                        array(
                            'class' => 'admin-page-framework-section-title',
                            'style' => $this->getAOrB(
                                $this->_shouldShowCaptionTitle( $aSection, $iSectionIndex ),
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
             * @return      string
             */
            private function _getCaptionDescription( $aSection, $hfSectionCallback ) {
                
                if ( $aSection['collapsible'] ) {
                    return '';
                }
                if ( ! is_callable( $hfSectionCallback ) ) {
                    return '';
                }
                
                // The class selector 'admin-page-framework-section-description' is referred by the repeatable section buttons
                // @todo        Use a different selector name other than 'admin-page-framework-section-description' as it is used in the inner <p> tag element as well.
                // Descriptions
                $_oSectionDescription = new AdminPageFramework_FormPart_Description(
                    $aSection['description'],
                    'admin-page-framework-section-description'    // class selector
                );            
                return "<div class='admin-page-framework-section-description'>"     
                    . call_user_func_array(
                        $hfSectionCallback, 
                        array( 
                            $_oSectionDescription->get(),
                            $aSection 
                        ) 
                    )
                . "</div>";

            }
            /**
             * Returns whether the title in the caption block should be displayed or not.
             * 
             * @since   3.4.0
             * @since   3.6.0       Moved from `AdminPageFramework_FormTable_Caption`.
             * @return  boolean
             */
            private function _shouldShowCaptionTitle( $aSection, $iSectionIndex ) {
                
                if ( ! $aSection[ 'title' ] ){
                    return false;
                }
                if ( $aSection[ 'collapsible' ] ) {
                    return false;
                }
                if ( $aSection[ 'section_tab_slug' ] ) {
                    return false;
                }
                if ( $aSection[ 'repeatable' ] && $iSectionIndex != 0 ) {
                    return false;
                }
                return true;                
                
            }     
    
}