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
    
    public $aArguments              = array();
    public $aSectionset             = array();
    public $aStructure              = array();
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
    public function __construct( /* $aArguments, $aSectionset, $aStructure, $aFieldsetsPerSection, $aSavedData, $aFieldErrors, $aCallbacks=array(), $oMsg */ ) {
      
        $_aParameters = func_get_args() + array( 
            $this->aArguments,
            $this->aSectionset,
            $this->aStructure, // needed for nested sections
            $this->aFieldsetsPerSection,
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg,
        );
        $this->aArguments            = $this->getAsArray( $_aParameters[ 0 ] );
        $this->aSectionset           = $this->getAsArray( $_aParameters[ 1 ] );
        $this->aStructure            = $this->getAsArray( $_aParameters[ 2 ] );
        $this->aFieldsetsPerSection  = $this->getAsArray( $_aParameters[ 3 ] );
        $this->aSavedData            = $this->getAsArray( $_aParameters[ 4 ] );
        $this->aFieldErrors          = $this->getAsArray( $_aParameters[ 5 ] );
        $this->aFieldTypeDefinitions = $this->getAsArray( $_aParameters[ 6 ] );
        $this->aCallbacks            = $this->getAsArray( $_aParameters[ 7 ] ) + $this->aCallbacks;
        $this->oMsg                  = $_aParameters[ 8 ];

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
        
        $_oSectionTableAttributes     = new AdminPageFramework_Form_View___Attribute_SectionTable( $this->aSectionset );
        $_oSectionTableBodyAttributes = new AdminPageFramework_Form_View___Attribute_SectionTableBody( $this->aSectionset );
        
        $_aOutput       = array();
        $_aOutput[]     = "<table " . $_oSectionTableAttributes->get() . ">"
                . $_oTableCaption->get()
                . "<tbody " . $_oSectionTableBodyAttributes->get() . ">"
                    . $this->_getSectionContent( $_iSectionIndex )
                . "</tbody>"
            . "</table>";

        $_oSectionTableContainerAttributes  = new AdminPageFramework_Form_View___Attribute_SectionTableContainer( $this->aSectionset );
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
                            . $this->_getCustomSectionContent()
                        . "</td>"
                    . "</tr>";
            }
            
            $_oFieldsets = new AdminPageFramework_Form_View___FieldsetRows(
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
            // return $this->getFieldsetRows( 
                // $this->aFieldsetsPerSection, 
                // $_iSectionIndex 
            // );
        
        }
     
            /**
             * Returns a custom section content.
             * 
             * If an array is set, it is considered a nested section.
             * 
             * @return      string
             * @since       DEVVER
             */
            private function _getCustomSectionContent() {
                
                if ( is_scalar( $this->aSectionset[ 'content' ] ) ) {
                    return "<tr>"
                        . "<td>"
                            . $this->aSectionset[ 'content' ]
                        . "</td>"
                    . "</tr>";
                }
      
                // Retrieve the formatted sectionsets of the content.
                $_sSectionPath = $this->aSectionset[ '_section_path' ];
                $_aSectionsets = $this->aStructure[ 'sectionsets' ];
                if ( ! isset( $_aSectionsets[ $_sSectionPath ] ) ) {    // @todo    not sure what this check is for
                    return '';
                }          
                
                // Generate nested section paths.
                unset( $_aSectionsets[ $_sSectionPath ] ); // remove this subject section assigned to this class
                $_aNestedSectionPaths = $this->_getNestedSectionPaths( 
                    $_sSectionPath,
                    $this->aSectionset[ 'content' ],
                    $_aSectionsets  // all sectionsets list
                );

                // Extract sectonsets of the section paths and set.
                $_aSectionsets = array_intersect_key(
                    $_aSectionsets, // precedence
                    $_aNestedSectionPaths 
                );
                
                // The passing structure should have only nested items.
                $_aStructure = $this->aStructure;
                $_aStructure[ 'sectionsets' ] = $_aSectionsets;

                $_aArguments = array(  
                    'nested_depth'  => $this->getElement( 
                        $this->aArguments, 
                        'nested_depth', 
                        0 
                    ) + 1
                ) + $this->aArguments;
       
                // Retrieve the output of the nested sections.
                $_oFormTables = new AdminPageFramework_Form_View___Sectionsets(
                    $_aArguments,
                    $_aStructure,
                    $this->aSavedData,            
                    $this->aFieldErrors,
                    $this->aCallbacks,
                    $this->oMsg
                );        
                $_sNestedOutput = $_oFormTables->get();
                return $_sNestedOutput;
            }        
                /**
                 * @since       DEVVER
                 * @return      array
                 */
                private function _getNestedSectionPaths( $sSubjectSectionPath, array $aNestedSctionsets, array $aSectionsets ) {
                    
                    $_aNestedSectionPaths = array();
                    
                    // List the section paths of the direct children
                    foreach( $aNestedSctionsets as $_aNestedSectionset ) {
                        $_sThisSectionPath = $sSubjectSectionPath . '|' . $_aNestedSectionset[ 'section_id' ];
                        $_aNestedSectionPaths[ $_sThisSectionPath ] = $_sThisSectionPath;
                    }                    
                    
                    // Now we need children's children.
                    $_aChildSectionPaths = array();
                    foreach( $_aNestedSectionPaths as $_sNestedSectionPath ) {
                        $_aNestedSectionsets = $this->getElementAsArray( 
                            $aSectionsets, 
                            array( $_sNestedSectionPath, 'content' )
                        );
                        $_aChildSectionPaths = $_aChildSectionPaths 
                            + $this->_getNestedSectionPaths( $_sNestedSectionPath, $_aNestedSectionsets, $aSectionsets );
                    }
                    
                    return $_aNestedSectionPaths + $_aChildSectionPaths;
                    
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
         /* public function getFieldsetRows( array $aFieldsetsPerSection, $iSectionIndex=null ) {

            $_aOutput = array();
            foreach( $aFieldsetsPerSection as $_aFieldset ) {

                $_oFieldsetOutputFormatter = new AdminPageFramework_Form_Model___Format_FieldsetOutput( 
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
            
        }          */
    
}