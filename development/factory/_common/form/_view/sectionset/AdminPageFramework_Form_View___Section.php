<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render individual form section.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Section
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View___Section extends AdminPageFramework_FrameworkUtility {
    
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
     * @since       3.7.0
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
         * @sicne       3.7.0
         * @return      string
         */
        private function _getSectionContent( $_iSectionIndex ) {

            if ( $this->aSectionset[ 'content' ] ) {
                return $this->_getCustomSectionContent();
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
        
        }
     
            /**
             * Returns a custom section content.
             * 
             * If an array is set, it is considered a nested section.
             * 
             * @return      string
             * @since       3.7.0
             */
            private function _getCustomSectionContent() {
                
                if ( is_scalar( $this->aSectionset[ 'content' ] ) ) {
                    return "<tr class='admin-page-framework-custom-content'>" 
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
                return "<tr class='admin-page-framework-nested-sectionsets'>" 
                        . "<td>"
                            . $_oFormTables->get()
                        . "</td>"
                    . "</tr>";                   
                
            }        
                /**
                 * @since       3.7.0
                 * @return      array
                 */
                private function _getNestedSectionPaths( $sSubjectSectionPath, array $aNestedSctionsets, array $aSectionsets ) {
                    
                    $_aNestedSectionPaths = array();
                    
                    // List the section paths of the direct children
                    foreach( $aNestedSctionsets as $_aNestedSectionset ) {
                        
                        // 3.7.6+ There were cases non array gets passed (like the FAQ page in the demo)
                        // and caused warnings in PHP 7.
                        if ( ! is_array( $_aNestedSectionset ) ) {
                            continue;
                        }
                        
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
    
}
