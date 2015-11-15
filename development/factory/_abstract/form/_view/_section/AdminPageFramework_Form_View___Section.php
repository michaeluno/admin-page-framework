<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render individual form section.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View___Section extends AdminPageFramework_WPUtility {
    
    public $aSectionset             = array();
    public $aFieldsetsPerSection    = array();
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $aCallbacks              = array();
    public $oMsg;
    
    /**
     * Sets up properties.
     * @since       DEVVER
     */
    public function __construct( /* $aSectionset, $aFieldsetsPerSection, $aSavedData, $aFieldErrors, $aCallbacks=array(), $oMsg */ ) {
      
        $_aParameters = func_get_args() + array( 
            $this->aSectionset,
            $this->aFieldsetsPerSection,
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg,
        );
        $this->aSectionset           = $this->getAsArray( $_aParameters[ 0 ] );
        $this->aFieldsetsPerSection  = $this->getAsArray( $_aParameters[ 1 ] );
        $this->aSavedData            = $this->getAsArray( $_aParameters[ 2 ] );
        $this->aFieldErrors          = $this->getAsArray( $_aParameters[ 3 ] );
        $this->aFieldTypeDefinitions = $this->getAsArray( $_aParameters[ 4 ] );
        $this->aCallbacks            = $this->getAsArray( $_aParameters[ 5 ] ) + $this->aCallbacks;
        $this->oMsg                  = $_aParameters[ 6 ];

    }
    
    /**
     * 
     */
    public function get() {

        $_iSectionIndex = $this->aSectionset[ '_index' ];
               
        $_oTableCaption = new AdminPageFramework_Form_View___SectionCaption(
            $this->aSectionset, 
            $_iSectionIndex,
            $this->aFieldsetsPerSection, 
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg
        );
        
        $_oSectionTableAttributes     = new AdminPageFramework_Attribute_SectionTable( $this->aSectionset );
        $_oSectionTableBodyAttributes = new AdminPageFramework_Attribute_SectionTableBody( $this->aSectionset );
        
        $_aOutput       = array();
        $_aOutput[]     = "<table " . $_oSectionTableAttributes->get() . ">"
                . $_oTableCaption->get()
                . "<tbody " . $_oSectionTableBodyAttributes->get() . ">"
                    . $this->_getSectionContent( $_iSectionIndex )
                . "</tbody>"
            . "</table>";

        $_oSectionTableContainerAttributes  = new AdminPageFramework_Attribute_SectionTableContainer( $this->aSectionset );
        return "<div " . $_oSectionTableContainerAttributes->get() . ">"
                . implode( PHP_EOL, $_aOutput )
            . "</div>";    
        
    }        
        /**
         * Returns the output of seciton contents.
         * @sicne       DEVVER
         * @return      string
         */
        private function _getSectionContent( $_iSectionIndex ) {
            
            if ( $this->aSectionset[ 'content' ] ) {
                return "<tr>"
                    . "<td>"
                        . $this->aSectionset[ 'content' ]
                    . "</td>"
                . "</tr>";
            }
            
            $_oFieldsets = new AdminPageFramework_Form_View___Fieldsets(
                $this->aFieldsetsPerSection, 
                $_iSectionIndex,
                $this->aSavedData,
                $this->aFieldErrors,
                $this->aFieldTypeDefinitions,
                $this->aCallbacks,
                $this->oMsg            
            );
            return $_oFieldsets->get();
            
            // @deprecated      DEVVER
            return $this->getFieldsetRows( 
                $this->aFieldsetsPerSection, 
                $_iSectionIndex 
            );
        
        }
        
        /**
         * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
         * 
         * @since       3.0.0
         * @since       3.6.0       Added the `$iSectionIndex` parameter. Changed the name from `getFIeldRows`.
         * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`.
         * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
         * @return      string
         * @deprecated  DEVVER
         */
         public function getFieldsetRows( array $aFieldsetsPerSection, $iSectionIndex=null ) {

            $_aOutput = array();
            foreach( $aFieldsetsPerSection as $_aFieldset ) {

                $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                    $_aFieldset, 
                    $iSectionIndex,
                    $this->aFieldTypeDefinitions
                );
                $_aFieldset = $_oFieldsetOutputFormatter->get();

                $_oFieldsetRow = new AdminPageFramework_Form_View___FieldsetTableRow(
                    $_aFieldset, // $_aFieldset
                    $this->aSavedData,
                    $this->aFieldErrors,
                    $this->aFieldTypeDefinitions,
                    $this->aCallbacks,
                    $this->oMsg
                );
                $_aOutput[] = $_oFieldsetRow->get();

            } 
           
            return implode( PHP_EOL, $_aOutput );
            
        }         
    
}