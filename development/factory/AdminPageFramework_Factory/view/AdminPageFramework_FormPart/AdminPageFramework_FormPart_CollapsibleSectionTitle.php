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
 * @internal
 */
class AdminPageFramework_FormPart_CollapsibleSectionTitle extends AdminPageFramework_FormPart_SectionTitle {

    public $sTitle                  = '';
    public $sTag                    = '';
    public $aFields                 = array();
    public $hfFieldCallback         = null;
    public $iSectionIndex           = null;
    public $aFieldTypeDefinitions   = array();
    public $aCollapsible            = array();
    public $sContainer              = '';
    public $oMsg                    = null;
            
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $sTitle, $sTag, $aFields, $hfFieldCallback, $iSectionIndex=null, $aFieldTypeDefinitions=array(), $aCollapsible, $sContainer, $oMsg */ ) {

        $_aParameters = func_get_args() + array( 
            $this->sTitle, 
            $this->sTag,
            $this->aFields,
            $this->hfFieldCallback,
            $this->iSectionIndex,
            $this->aCollapsible,
            $this->sContainer,  
            $this->oMsg,
        );
        $this->sTitle                   = $_aParameters[ 0 ];
        $this->sTag                     = $_aParameters[ 1 ];
        $this->aFields                  = $_aParameters[ 2 ];
        $this->hfFieldCallback          = $_aParameters[ 3 ];
        $this->iSectionIndex            = $_aParameters[ 4 ];
        $this->aFieldTypeDefinitions    = $_aParameters[ 5 ];
        $this->aCollapsible             = $this->getAsArray( $_aParameters[ 6 ] );
        $this->sContainer               = $_aParameters[ 7 ];
        $this->oMsg                     = $_aParameters[ 8 ];

    }

    /**
     * Returns HTML formatted collapsible section title blocks by the given section
     * 
     * @return      string      The output.
     */
    public function get() {
        return $this->_getCollapsibleSectionTitleBlock( 
            $this->aCollapsible, 
            $this->sContainer,
            $this->aFields,
            $this->hfFieldCallback,
            $this->iSectionIndex
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
        private function _getCollapsibleSectionTitleBlock( array $aCollapsible, $sContainer='sections', array $aFields=array(), $hfFieldCallback=null, $iSectionIndex=null ) {

            if ( empty( $aCollapsible ) ) { 
                return ''; 
            }
            if ( $sContainer !== $aCollapsible[ 'container' ] ) { 
                return ''; 
            }
              
            $_sSectionTitle = $this->_getSectionTitle( 
                $this->sTitle, 
                $this->sTag, 
                $this->aFields, 
                $this->hfFieldCallback, 
                $this->iSectionIndex
            );
            
            return $this->_getCollapsibleSectionsEnablerScript()
                . "<div " . $this->generateAttributes(
                    array(
                        'class' => $this->generateClassAttribute( 
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
    static private $_bLoadedCollapsibleSectionsEnablerScript = false;    
    /**
     * Returns the enabler script of collapsible sections.
     * @since   3.4.0
     * @since   3.6.0       Moved from `AdminPageFramework_FormPart_Table`.
     */
    protected function _getCollapsibleSectionsEnablerScript() {
        
        if ( self::$_bLoadedCollapsibleSectionsEnablerScript ) {
            return;
        }
        self::$_bLoadedCollapsibleSectionsEnablerScript = true;
        new AdminPageFramework_Script_CollapsibleSection( $this->oMsg );     
   
    }     
    
}