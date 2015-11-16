<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render collapsible section title.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @since       DEVVER      Renamed from `AdminPageFramework_FormPart_CollapsibleSectionTitle`.
 * @internal
 */
class AdminPageFramework_Form_View___CollapsibleSectionTitle extends AdminPageFramework_Form_View___SectionTitle {
            
    public $aArguments      = array(
        'title'             => null,
        'tag'               => null,
        'section_index'     => null,
        'collapsible'       => array(),
        'container_type'    => 'section', // section or sections
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
     * Returns HTML formatted collapsible section title blocks by the given section
     * 
     * @return      string      The output.
     */
    public function get() {
        
        if ( empty( $this->aArguments[ 'collapsible' ] ) ) {
            return '';
        }
        return $this->_getCollapsibleSectionTitleBlock( 
            $this->aArguments[ 'collapsible' ], 
            $this->aArguments[ 'container_type' ], 
            $this->aArguments[ 'section_index' ]
        );
    }
        /**
         * Returns the output of a title block of the given collapsible section.
         * 
         * @since       3.4.0
         * @since       3.6.0           Moved from `AdminPageFramework_FormPart_Table_Base`.
         * @param       array|boolean   $aCollapsible       The collapsible argument.
         * @param       string          $sContainer          The position context. Accepts either 'sections' or 'section'. If the set position in the argument array does not match this value, the method will return an empty string.
         */
        private function _getCollapsibleSectionTitleBlock( array $aCollapsible, $sContainer='sections', $iSectionIndex=null ) {

            if ( $sContainer !== $aCollapsible[ 'container' ] ) { 
                return ''; 
            }
              
            $_sSectionTitle = $this->_getSectionTitle( 
                $this->aArguments[ 'title' ], 
                $this->aArguments[ 'tag' ],
                $this->aFieldsets,
                $iSectionIndex,
                $this->aFieldTypeDefinitions         
            );
            
            return $this->_getCollapsibleSectionsEnablerScript()
                . "<div " . $this->getAttributes(
                    array(
                        'class' => $this->getClassAttribute( 
                            'admin-page-framework-section-title',
                            'accordion-section-title',
                            'admin-page-framework-collapsible-title',
                            'sections' === $aCollapsible['container']
                                ? 'admin-page-framework-collapsible-sections-title'
                                : 'admin-page-framework-collapsible-section-title',
                            $aCollapsible[ 'is_collapsed' ] 
                                ? 'collapsed' 
                                : ''
                        ),
                    ) 
                    + $this->getDataAttributeArray( $aCollapsible )
                ) . ">"  
                        . $_sSectionTitle
                    . "</div>";
            
        }        
    /**
     * Indicates whether the collapsible script is loaded or not.
     * 
     * @since   3.4.0
     * @since   3.6.0       Moved from `AdminPageFramework_FormPart_Table`.
     */
    static private $_bLoaded = false;    
    /**
     * Returns the enabler script of collapsible sections.
     * @since   3.4.0
     * @since   3.6.0       Moved from `AdminPageFramework_FormPart_Table`.
     */
    protected function _getCollapsibleSectionsEnablerScript() {
        
        if ( self::$_bLoaded ) {
            return;
        }
        self::$_bLoaded = true;
        new AdminPageFramework_Script_CollapsibleSection( $this->oMsg );     
   
    }     
    
}