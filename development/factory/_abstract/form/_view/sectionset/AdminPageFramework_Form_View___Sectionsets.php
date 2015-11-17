<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render forms.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View___Sectionsets extends AdminPageFramework_Form_View___Section_Base {

    public $aArguments = array(
        'structure_type' => 'admin_page',  
        'capability'     => '',
    );
       
    public $aStructure = array(
        'field_type_definitions'    => array(),
        'sectionsets'               => array(),
        'fieldsets'                 => array(),
    );
           
    public $aSavedData = array();
    
    public $aFieldErrors = array();
                
    /**
     * Stores callback functions.
     */
    public $aCallbacks = array(
        'section_head_output'  => null,
        'fieldset_output'      => null,
    );
    
    public $oMsg;
    
    /**
     * Sets up properties.
     * @since       DEVVER
     */
    public function __construct( /* $aArguments, $aStructure, $aSavedData, $aFieldErrors, $aCallbacks=array(), $oMsg */ ) {
      
        $_aParameters = func_get_args() + array( 
            $this->aArguments,
            $this->aStructure,
            $this->aSavedData,
            $this->aFieldErrors,
            $this->aCallbacks,
            $this->oMsg,
        );
        $this->aArguments            = $this->getAsArray( $_aParameters[ 0 ] ) + $this->aArguments;
        $this->aStructure            = $this->getAsArray( $_aParameters[ 1 ] ) + $this->aStructure;
        $this->aSavedData            = $this->getAsArray( $_aParameters[ 2 ] );
        $this->aFieldErrors          = $this->getAsArray( $_aParameters[ 3 ] );
        $this->aCallbacks            = $this->getAsArray( $_aParameters[ 4 ] ) + $this->aCallbacks;
        $this->oMsg                  = $_aParameters[ 5 ];

    }
    
    /**
     * Generates a set of HTML table outputs consisting of form sections and fields.
     * 
     * Currently there are mainly two types of structures.
     * 1. Normal Sections - Vertically arranged sections. They can be repeatable.
     * <code>
     *  <div class="admin-page-framework-sectionset">
     *      <div class="admin-page-framework-sections">
     *          <div class="admin-page-framework-section">
     *              <table class="form-table">
     *                  <caption>       
     *                      <div class="admin-page-framework-section-title">...</div>
     *                      <div class="admin-page-framework-section-description">...</div>
     *                  </caption>
     *                  <tbody>
     *                      <tr>a field goes here.</tr>
     *                      <tr>a field goes here.</tr>
     *                      <tr>a field goes here.</tr>
     *                  </tbody>
     *              </table>
     *          </div>
     *          <div class="admin-page-framework-section">
     *              if repeatable sections, this container is repeated
     *          </div>
     *      </div>
     *  </div>
     * </code>
     * 2. Tabbed Sections - Horizontally arranged grouped sections. They can be repeatable.
     * <code>
     *  <div class="admin-page-framework-sectionset">
     *      <div class="admin-page-framework-sections">
     *          <ul class="admin-page-framework-section-tabs">
     *              <li> ... </li>
     *              <li> ... </li>
     *          </ul>
     *          <div class="admin-page-framework-section">
     *              <table class="form-table">
     *                  <caption>       
     *                      <div class="admin-page-framework-section-title">...</div>
     *                      <div class="admin-page-framework-section-description">...</div>
     *                  </caption>
     *                  <tbody>
     *                      <tr>a field goes here.</tr>
     *                      <tr>a field goes here.</tr>
     *                      <tr>a field goes here.</tr>
     *                  </tbody>
     *              </table>
     *          </div>
     *          <div class="admin-page-framework-section">
     *              if repeatable sections, this container is repeated
     *          </div>
     *      </div>
     *  </div>
     * </code>
     * @since       3.0.0
     * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`. 
     * Renamed from `getFormTables()`.
     * @return      string      The generated HTML form tables output.
     */
    public function get() {
        
        $_oFormatSectionsetsByTab  = new AdminPageFramework_Form_View___Format_SectionsetsByTab(
            $this->aStructure[ 'sectionsets' ],
            $this->aStructure[ 'fieldsets' ]
        );

        $_aOutput     = array();
        foreach( $_oFormatSectionsetsByTab->getTabs() as $_sSectionTabSlug ) {
            $_aOutput[] = $this->_getFormOutput(
                $_oFormatSectionsetsByTab->getSectionsets( $_sSectionTabSlug ),
                $_oFormatSectionsetsByTab->getFieldsets( $_sSectionTabSlug ),
                $_sSectionTabSlug,
                $this->aCallbacks
            );   
        }
        $_oDebugInfo = new AdminPageFramework_Form_View___DebugInfo( 
            $this->aArguments[ 'structure_type' ], // Structure type (for debug info) - this must be done before the $aSections array gets updated below.
            $this->oMsg
        );
        
        return implode( PHP_EOL, $_aOutput )
            . AdminPageFramework_Form_View___Script_SectionTab::getEnabler()
            . $_oDebugInfo->get();        
        
    }
        /**
         * Returns a generated HTML form table output.
         * @since       3.5.3
         * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`. 
         * Changed the first parameter to accepts fieldsets array which already belongs to the given section tab.
         * Changed the name from `_getFormTable()`. Combined the 4th and 5th parameters to one parameter in an array.
         * @return      string      The generated HTML form table.
         */
        private function _getFormOutput( array $aSectionsets, array $aFieldsets, $sSectionTabSlug, $aCallbacks ) {
            
            // A sectionset is a set of sections.
            $_sSectionSet = $this->_getSectionsetsTables( 
                $aSectionsets,     // sectionset definition (already devided by section tab)
                $aFieldsets, // fieldset definitions (already devided by section tab)
                $aCallbacks
            );
            return $_sSectionSet
                ? "<div " . $this->getAttributes(
                        array(
                            'class' => 'admin-page-framework-sectionset',
                            'id'    => "sectionset-{$sSectionTabSlug}_" . md5( serialize( $aSectionsets ) ),
                        ) 
                    ) . ">"
                        . $_sSectionSet
                    . "</div>"
                : '';
            
        }
                
        /**
         * Returns an output string of sections tables.
         * 
         * The returned output element is wrapped in a div container of the 'admin-page-framework-sections' class selector.
         * And inside it 'admin-page-framework-section' elements will be placed.
         * 
         * @since       3.0.0
         * @since       3.4.0       Removed the $sSectionTabSlug parameter. Changed the name from `_getFormTablesBySectionTab()`.
         * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
         * Renamed from `_getSectionsTables()`.
         * @param       array       $aSectionsets        A sections definition array. (already divided by section tab).
         * @param       array       $aFieldsets          A fields definition array. (already divided by section tab).
         * @param       array       $aCallbacks
         * @return      string
         */
        private function _getSectionsetsTables( array $aSectionsets, array $aFieldsets, array $aCallbacks ) {
            
            if ( empty( $aSectionsets ) ) { 
                return ''; 
            } 
            if ( ! count( $aFieldsets ) ) {
                return ''; 
            }
            
            $_aFirstSectionset  = $this->getFirstElement( $aSectionsets );
            $_sSectionTabSlug   = '';
            $_aOutputs          = array(
                'section_tab_list'  => array(),
                'section_contents'  => array(),
                'count_subsections' => 0,
            );
            $_sThisSectionID    = $_aFirstSectionset[ 'section_id' ];
            $_sSectionsID       = 'sections-' . $_sThisSectionID;
            $_aCollapsible      = $this->_getCollapsibleArgumentForSections( 
                $_aFirstSectionset 
            );
                        
            foreach( $aSectionsets as $_aSectionset ) {
                
                // Need to be referred outside the loop.
                $_sSectionID        = $_aSectionset[ 'section_id' ];
                $_sSectionTabSlug   = $aSectionsets[ $_sSectionID ][ 'section_tab_slug' ]; 
                $_aOutputs          = $this->_getSectionsetTable( 
                    $_aOutputs,
                    $_sSectionsID,
                    $_aSectionset,
                    $aFieldsets
                );
                
            } 
            
            $_aOutputs[ 'section_contents' ] = array_filter( $_aOutputs[ 'section_contents' ] );
            return $this->_getFormattedSectionsTablesOutput( 
                $_aOutputs,
                $_aFirstSectionset, 
                $_sSectionsID,
                $this->getAsArray( $_aCollapsible ),
                $_sSectionTabSlug
            );
            
        }
     
            /**
             * Returns the collapsible argument array from the given sections definition array.
             * 
             * @since       3.5.3
             * @since       3.6.0       Changed the first parameter from `$aSections`.
             * @since       DEVVER      Moved from `AdminPageFrmework_FormPart_Table`.
             * Changed the visibility scope to private. Changed the 1st parameter from `$aSection`.
             * @return      array
             */
            private function _getCollapsibleArgumentForSections( array $aSectionset=array() ) {  
                
                $_oArgumentFormater = new AdminPageFramework_Form_Model___Format_CollapsibleSection(
                    $aSectionset[ 'collapsible' ],
                    $aSectionset[ 'title' ],
                    $aSectionset    
                );
                $_aCollapsible = $this->getAsArray( $_oArgumentFormater->get() );
// @todo reduce the conditional statements by using getElement()                
                return isset( $_aCollapsible[ 'container' ] ) && 'sections' === $_aCollapsible[ 'container' ] 
                    ? $_aCollapsible 
                    : array();    
                    
            }
          
            /**
             * Returns an updated sections table output array.
             * @since       3.5.3
             * @since       3.6.0       Removed the `$_sSectionID` parameter.\
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
             * Renamed from `_getSectionsTable()`.
             * @return      array       The updated sections table output array.
             */
            private function _getSectionsetTable( $_aOutputs, $_sSectionsID, array $_aSection, array $aFieldsInSections ) {
                                
                if ( ! $this->isSectionsetVisible( $_aSection ) ) {
                    return $_aOutputs;
                }
                                
                // For repeatable sections - note that sub-sections are divided field definition arrays by sub-section index, not section definition arrays.
                $_aSubSections      = $this->getIntegerKeyElements( 
                    $this->getElementAsArray(
                        $aFieldsInSections, // subject
                        $_aSection[ 'section_id' ],   // dimensional key
                        array() // default
                    )
                );

                // If the 'save' argument is false, insert a flag that disables saving the section inputs.
                $_aOutputs[ 'section_contents' ][] = $this->_getUnsetFlagSectionInputTag( $_aSection );
                
                // Check sub-sections.
                $_aOutputs[ 'count_subsections' ] = count( $_aSubSections );
                if ( $_aOutputs[ 'count_subsections' ] ) {

                    // Add the repeatable sections enabler script.
                    if ( ! empty( $_aSection[ 'repeatable' ] ) ) {
                        $_aOutputs[ 'section_contents' ][] = AdminPageFramework_Form_View___Script_RepeatableSection::getEnabler( 
                            $_sSectionsID, 
                            $_aOutputs[ 'count_subsections' ], 
                            $_aSection[ 'repeatable' ],
                            $this->oMsg
                        );
                        $_aOutputs[ 'section_contents' ][] = $this->_getRepeatableSectionFlagTag( $_aSection );
                    }
                    // Add the sortable sections enabler script. 3.6.0+
                    if ( ! empty( $_aSection[ 'sortable' ] ) ) {
// @todo Change the name of the class to AdminPageFramework_Form_Script_...
                        $_aOutputs[ 'section_contents' ][] = AdminPageFramework_Form_View___Script_SortableSection::getEnabler( 
                            $_sSectionsID, 
                            $_aSection[ 'sortable' ],
                            $this->oMsg
                        );
                        $_aOutputs[ 'section_contents' ][] = $this->_getSortableSectionFlagTag( $_aSection );
                    }
                    
                    // Get the section tables.
                    $_aSubSections = $this->numerizeElements( $_aSubSections ); // will include the main section as well.
                    foreach( $_aSubSections as $_iIndex => $_aFields ) { 

                        $_oEachSectionArguments = new AdminPageFramework_Form_Model___Format_EachSection(
                            $_aSection,
                            $_iIndex,
                            $_aSubSections,
                            $_sSectionsID
                        );                    
                        $_aOutputs = $this->_getSectionTableWithTabList(
                            $_aOutputs,
                            $_oEachSectionArguments->get(), // $_aSection, 
                            $_aFields
                        );
                        
                    }
                    return $_aOutputs;
                } 

                // The normal section.
                $_oEachSectionArguments = new AdminPageFramework_Form_Model___Format_EachSection(
                    $_aSection,
                    null, // sub-section index
                    array(), // sub-sections
                    $_sSectionsID
                );                    
                
                $_aOutputs = $this->_getSectionTableWithTabList(
                    $_aOutputs,
                    $_oEachSectionArguments->get(), // $_aSection, 
                    $this->getElementAsArray( 
                        $aFieldsInSections, 
                        $_aSection[ 'section_id' ], 
                        array() 
                    ) // fieldset definitions      
                );
                return $_aOutputs;
              
            }
                /**
                 * Returns an HTML internal hidden input tag for the 'repeatable' arguments.
                 * @since       3.6.2
                 * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string
                 */
                private function _getRepeatableSectionFlagTag( array $aSection ) {
                    return $this->getHTMLTag( 
                        'input',
                        array(
                            'class'                     => 'element-address',
                            'type'                      => 'hidden',
                            'name'                      => '__repeatable_elements_' . $aSection[ '_structure_type' ] 
                                . '[ ' . $aSection[ 'section_id' ] . ' ]',
                            // @todo examine whether this value should include a section index.
                            'value' => $aSection[ 'section_id' ],                            
                        )
                    );
                }                    
                /**
                 * Returns an HTML internal hidden input tag for the 'sortable' arguments.
                 * @since       3.6.2
                 * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string
                 */
                private function _getSortableSectionFlagTag( array $aSection ) {
                    return $this->getHTMLTag( 
                        'input',
                        array(
                            'class'                     => 'element-address',
                            'type'                      => 'hidden',
                            'name'                      => '__sortable_elements_' . $aSection[ '_structure_type' ] 
                                . '[ ' . $aSection[ 'section_id' ] . ' ]',
                            // @todo examine whether this value should include a section index.
                            'value' => $aSection[ 'section_id' ],                            
                        )
                    );
                }                
                
                /**
                 * Embeds an internal hidden input for the 'save' argument.
                 * @since       3.6.0
                 * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string
                 */
                private function _getUnsetFlagSectionInputTag( array $aSection ) {
                    
                    if ( false !== $aSection[ 'save' ] ) {                
                        return '';
                    }
                    return $this->getHTMLTag( 
                        'input',
                        array(
                            'type'  => 'hidden',
                            'name'  => '__unset_' .  $aSection[ '_structure_type' ] . '[ ' . $aSection[ 'section_id' ] . ' ]',
                            'value' => "__dummy_option_key|" . $aSection[ 'section_id' ],
                            'class' => 'unset-element-names element-address',
                        )
                    );            
                    
                }                
                /**
                 * Returns an section table output array by adding a section output with a tab list.
                 * @since       3.5.3
                 * @since       3.6.0       Removed the `$_sSectionID` and `$iSectionIndex` parameters.
                 * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      The updated section table output array.
                 */
                private function _getSectionTableWithTabList( array $_aOutputs, array $aSectionset, $aFieldsetsPerSection ) {
                                        
                    // Tab list
                    $_aOutputs[ 'section_tab_list' ][] = $this->_getTabList( 
                        $aSectionset, 
                        $aFieldsetsPerSection, 
                        $this->aCallbacks[ 'fieldset_output' ]
                    );                
                    
                    // Section container
                    $_oSectionTable = new AdminPageFramework_Form_View___Section(
                        $aSectionset,
                        $aFieldsetsPerSection,
                        $this->aSavedData,
                        $this->aFieldErrors,
                        $this->aStructure[ 'field_type_definitions' ],            
                        $this->aCallbacks,
                        $this->oMsg
                    );
                    $_aOutputs[ 'section_contents' ][] = $_oSectionTable->get();                    
                   
                    return $_aOutputs;
                 
                }
            
            /**
             * Returns a formatted sections tables HTMl output.
             * 
             * @internal
             * @since       3.5.3
             * @since       3.6.0       Removed the `$sSectionID` parameter. Added the `$aSectionset` parameter.
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
             * @return      string      The formatted sections table HTML output.
             */
            private function _getFormattedSectionsTablesOutput( array $aOutputs, $aSectionset, $sSectionsID, array $aCollapsible, $sSectionTabSlug ) {
                
                if ( empty( $aOutputs[ 'section_contents' ] ) ) {
                    return '';
                }
                                
                $_oCollapsibleSectionTitle = new AdminPageFramework_Form_View___CollapsibleSectionTitle(
                    array(
                        'title'             => $this->getElement( $aCollapsible, 'title', '' ),
                        'tag'               => 'h3',
                        'section_index'     => null,
                        'collapsible'       => $aCollapsible,
                        'container_type'    => 'sections', // section or sections                    
                    ),
                    array(),            // fieldsets
                    $this->aSavedData,   
                    $this->aFieldErrors, 
                    $this->aStructure[ 'field_type_definitions' ], 
                    $this->oMsg,
                    $this->aCallbacks // field output element callables.                     
                    
                );

                $_oSectionsTablesContainerAttributes = new AdminPageFramework_Form_View___Attribute_SectionsTablesContainer(
                    $aSectionset,
                    $sSectionsID,
                    $sSectionTabSlug,
                    $aCollapsible,
                    $aOutputs[ 'count_subsections' ]
                );
                return $_oCollapsibleSectionTitle->get()
                    . "<div " . $_oSectionsTablesContainerAttributes->get() . ">"
                        . $this->_getSectionTabList( $sSectionTabSlug, $aOutputs[ 'section_tab_list' ] )
                        . implode( PHP_EOL, $aOutputs[ 'section_contents' ] )
                    . "</div>";
                    
            }
                /**
                 * Returns an HTML section tab list.
                 * @internal
                 * @since       3.5.3
                 * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string      The generated section tab list as HTML.
                 */
                private function _getSectionTabList( $sSectionTabSlug, array $aSectionTabList ) {
                   return $sSectionTabSlug 
                        ? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" 
                            . implode( PHP_EOL, $aSectionTabList ) 
                            . "</ul>"
                        : '';
                }
                
            /**
             * Returns the output of a list tab element for tabbed sections.
             * 
             * @since       3.4.0
             * @since       3.6.0       Removed the `$iSectionIndex` parameter.\
             * @since       DEVVER      Moved from `AdminPageFramework_FormPart_Table`.
             * @return      string
             */
            private function _getTabList( array $aSection, array $aFields, $hfFieldCallback ) {
                                
                if ( ! $aSection[ 'section_tab_slug' ] ) {
                    return '';
                }
                
                $iSectionIndex      = $aSection[ '_index' ];

                $_sSectionTagID     = 'section-' . $aSection[ 'section_id' ] . '__' . $iSectionIndex;
                $_aTabAttributes    = $aSection[ 'attributes' ][ 'tab' ]
                    + array(
                        'class' => 'admin-page-framework-section-tab nav-tab',
                        'id'    => "section_tab-{$_sSectionTagID}",
                        'style' => null
                    );
                $_aTabAttributes[ 'class' ] = $this->getClassAttribute( $_aTabAttributes[ 'class' ], $aSection[ 'class' ][ 'tab' ] );  // 3.3.1+
                $_aTabAttributes[ 'style' ] = $this->getStyleAttribute( $_aTabAttributes[ 'style' ], $aSection[ 'hidden' ] ? 'display:none' : null );  // 3.3.1+
                
                $_oSectionTitle = new AdminPageFramework_Form_View___SectionTitle(                    
                    array(
                        'title'         => $aSection[ 'title' ],
                        'tag'           => 'h4',
                        'section_index' => $iSectionIndex,
                    ),
                    $aFields,            
                    $this->aSavedData,   
                    $this->aFieldErrors, 
                    $this->aStructure[ 'field_type_definitions' ],
                    $this->oMsg,
                    $this->aCallbacks // field output element callables.                    
                );                        
                
                return "<li " . $this->getAttributes( $_aTabAttributes ) . ">"
                    . "<a href='#{$_sSectionTagID}'>"
                        . $_oSectionTitle->get()
                    ."</a>"
                . "</li>";
                
            }        
               
}