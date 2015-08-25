<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render setting sections and fields.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.0.0
 * @since       3.6.0       Changed the name from `AdminPageFramework_FormTable`.
 * @internal
 */
class AdminPageFramework_FormPart_Table extends AdminPageFramework_WPUtility {

    /**
     * Sets up properties and hooks.
     * 
     * @since 3.0.0
     * @since 3.0.4     The `$aFieldErrors` parameter was added.
     */
    public function __construct( $aFieldTypeDefinitions, array $aFieldErrors, $oMsg=null ) {
        
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions; // used to merge the field definition array with the default field type definition. 
        $this->aFieldErrors             = $aFieldErrors;
        $this->oMsg                     = $oMsg 
            ? $oMsg
            : AdminPageFramework_Message::getInstance();
        
    
    }
        
    /**
     * Returns a set of HTML table outputs consisting of form sections and fields.
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
     * @return      string      The generated HTML form tables output.
     */
    public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
        
        // Fields type (for debug info) - this must be done before the $aSections array gets updated below.
        $_sFieldsType = $this->_getSectionsFieldsType( $aSections );    
        
        // Update the array structure by tab slug (passed by reference).
        $this->_divideElementsBySectionTabs( $aSections, $aFieldsInSections );
     
        $_aOutput     = array();
        foreach( $aSections as $_sSectionTabSlug => $_aSectionsBySectionTab ) {        
            $_aOutput[] = $this->_getFormTable(
                $aFieldsInSections,
                $_sSectionTabSlug,
                $_aSectionsBySectionTab,
                $hfSectionCallback, 
                $hfFieldCallback                 
            );   
        }
        
        $_oDebugInfo = new AdminPageFramework_FormPart_DebugInfo( $_sFieldsType );
        
        return implode( PHP_EOL, $_aOutput ) 
            // . $this->_getSectionTabsEnablerScript()
            . AdminPageFramework_Script_Tab::getEnabler()
            . $_oDebugInfo->get();

            
    }
        /**
         * Returns the fields type of the given sections.
         * 
         * @since       3.3.3
         * @return      string
         * @remark      The first iteration item of the given array will be returned,
         */
        private function _getSectionsFieldsType( array $aSections=array() ) {
            foreach( $aSections as $_aSection ) {
                return $_aSection[ '_fields_type' ];
            }
        }    
        /**
         * Returns a generated HTML form table output.
         * @since       3.5.3
         * @return      string      The generated HTML form table.
         */
        private function _getFormTable( array $aFieldsInSections, $sSectionTabSlug, array $aSectionsBySectionTab, $hfSectionCallback, $hfFieldCallback ) {
            
            if ( ! count( $aFieldsInSections[ $sSectionTabSlug ] ) ) { 
                return ''; 
            }
          
            $_sSectionSet = $this->_getSectionsTables( 
                $aSectionsBySectionTab,     // sectionset definition
                $aFieldsInSections[ $sSectionTabSlug ], // fieldset definitions
                $hfSectionCallback, 
                $hfFieldCallback 
            );
            
            return $_sSectionSet
                ? "<div " . $this->generateAttributes(
                        array(
                            'class' => 'admin-page-framework-sectionset',
                            'id'    => "sectionset-{$sSectionTabSlug}_" . md5( serialize( $aSectionsBySectionTab ) ),
                        ) 
                    ) . ">"
                        . $_sSectionSet
                    . "</div>"
                : '';
            
        }
    
        /**
         * Divides the given sections array and the fields array by section tabs.
         * 
         * The structure will be changed.
         * From
         * <code>
         * array(
         *      'section id_a'    => array( 'section arguments' ),
         *      'section id_b'    => array( 'section arguments' ),
         *      'section id_c'    => array( 'section arguments' ),
         *          ...
         * )
         * </code>
         * To
         * <code>
         * array(
         *      'section tab_a'   => array( 
         *          'section id_a'    => array( 'section arguments' ),
         *          'section id_b'    => array( 'section arguments' ),
         *      ),
         *      'section_tab_b'   => array(
         *          'section id_c'    => array( 'section arguments' ),
         *      ),
         *          ...
         * )
         * </code>
         * @since       3.4.0
         * @return      void
         */
        private function _divideElementsBySectionTabs( array &$aSections, array &$aFields ) {

            $_aSectionsBySectionTab = array();
            $_aFieldsBySectionTab   = array();
            $_iIndex                = 0;

            foreach( $aSections as $_sSectionID => $_aSection ) {

                // If no fields for the section, no need to add the section
                if ( ! isset( $aFields[ $_sSectionID ] ) ) {
                    continue;
                }            
                             
                $_sSectionTaqbSlug = $this->getAOrB(
                    $_aSection['section_tab_slug'],
                    $_aSection['section_tab_slug'],
                    '_default_' . ( ++$_iIndex )
                );
                $_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionID ] = $_aSection;
                $_aFieldsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionID ]   = $aFields[ $_sSectionID ];
                    
            }
            
            $aSections  = $_aSectionsBySectionTab;
            $aFields    = $_aFieldsBySectionTab;

        }      
        
        /**
         * Returns an output string of sections tables.
         * 
         * The returned output element is wrapped in a div container of the 'admin-page-framework-sections' class selector.
         * And inside it 'admin-page-framework-section' elements will be placed.
         * 
         * @since       3.0.0
         * @since       3.4.0       Removed the $sSectionTabSlug parameter. Changed the name from `_getFormTablesBySectionTab()`.
         * @param       array       $aSections                  A sections definition array. (already divided by section tab).
         * @param       array       $aFieldsInSections          A fields definition array. (already divided by section tab).
         * @param       callable    $hfSectionCallback      
         * @param       callable    $hfFieldCallback      
         * @return      string
         */
        private function _getSectionsTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {

            if ( empty( $aSections ) ) { 
                return ''; 
            } 
            
            $_sSectionTabSlug   = '';
            $_aOutputs          = array(
                'section_tab_list'  => array(),
                'section_contents'  => array(),
                'count_subsections' => 0,
            );
            
            $_aFirstSectionset  = $this->getFirstEelement( $aSections );
            $_sThisSectionID    = $_aFirstSectionset[ 'section_id' ];
            $_sSectionsID       = 'sections-' . $_sThisSectionID;
            
            $_aCollapsible      = $this->_getCollapsibleArgumentForSections( $_aFirstSectionset );
                        
            foreach( $aSections as $_aSection ) {
                
                // Need to be referred outside the loop.
                $_sSectionID        = $_aSection[ 'section_id' ];
                $_sSectionTabSlug   = $aSections[ $_sSectionID ][ 'section_tab_slug' ]; 
                
                $_aOutputs = $this->_getSectionsTable( 
                    $_aOutputs,
                    $_sSectionsID,
                    $_aSection,
                    $aFieldsInSections,
                    $hfSectionCallback, 
                    $hfFieldCallback 
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
             * @since       3.6.0       Changed the first parameter to `$aSection` from `$aSections`.
             * @return      array
             */
            protected function _getCollapsibleArgumentForSections( array $aSection=array() ) {  
                // $_aCollapsible      = $this->_getCollapsibleArgument( $aSections );
                $_oArgumentFormater = new AdminPageFramework_Format_CollapsibleSection(
                    $aSection[ 'collapsible' ],
                    $aSection[ 'title' ],
                    $aSection    
                );
                $_aCollapsible = $this->getAsArray( $_oArgumentFormater->get() );
                return isset( $_aCollapsible['container'] ) && 'sections' === $_aCollapsible['container'] 
                    ? $_aCollapsible 
                    : array();            
            }
          
            /**
             * Returns an updated sections table output array.
             * @since       3.5.3
             * @since       3.6.0       Removed the `$_sSectionID` parameter.
             * @return      array       The updated sections table output array.
             */
            private function _getSectionsTable( $_aOutputs, $_sSectionsID, array $_aSection, array $aFieldsInSections, $hfSectionCallback, $hfFieldCallback  ) {
                                
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
                        $_aOutputs[ 'section_contents' ][] = AdminPageFramework_Script_RepeatableSection::getEnabler( 
                            $_sSectionsID, 
                            $_aOutputs[ 'count_subsections' ], 
                            $_aSection[ 'repeatable' ],
                            $this->oMsg
                        );
                        $_aOutputs[ 'section_contents' ][] = $this->_getDynamicElementFlagFieldInputTag( $_aSection );
                    }
                    // Add the sortable sections enabler script. 3.6.0+
                    if ( ! empty( $_aSection[ 'sortable' ] ) ) {
                        $_aOutputs[ 'section_contents' ][] = AdminPageFramework_Script_SortableSection::getEnabler( 
                            $_sSectionsID, 
                            $_aSection[ 'sortable' ],
                            $this->oMsg
                        );
                        $_aOutputs[ 'section_contents' ][] = $this->_getDynamicElementFlagFieldInputTag( $_aSection );
                    }
                    
                    // Get the section tables.
                    $_aSubSections = $this->numerizeElements( $_aSubSections ); // will include the main section as well.
                    foreach( $_aSubSections as $_iIndex => $_aFields ) { 

                        $_oEachSectionArguments = new AdminPageFramework_Format_EachSection(
                            $_aSection,
                            $_iIndex,
                            $_aSubSections,
                            $_sSectionsID
                        );                    
                        $_aOutputs = $this->_getSectionTableWithTabList(
                            $_aOutputs,
                            $_oEachSectionArguments->get(), // $_aSection, 
                            $_aFields, 
                            $hfSectionCallback,
                            $hfFieldCallback                     
                        );
                        
                    }
                    return $_aOutputs;
                } 

                // The normal section.
                $_oEachSectionArguments = new AdminPageFramework_Format_EachSection(
                    $_aSection,
                    null, // sub-section index
                    array(), // sub-sections
                    $_sSectionsID
                );                    
                
                $_aOutputs = $this->_getSectionTableWithTabList(
                    $_aOutputs,
                    $_oEachSectionArguments->get(), // $_aSection, 
                    $this->getElementAsArray( $aFieldsInSections, $_aSection[ 'section_id' ], array() ), // fieldset definitions
                    $hfSectionCallback,
                    $hfFieldCallback                     
                );
                return $_aOutputs;
              
            }
                /**
                 * Embeds an internal hidden input for the 'sortable' and 'repeatable' arguments.
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getDynamicElementFlagFieldInputTag( array $aSection ) {
                    return $this->getHTMLTag( 
                        'input',
                        array(
                            'type'  => 'hidden',
                            'name'  => '__dynamic_elements_' . $aSection[ '_fields_type' ] . '[' . $aSection[ 'section_id' ] . ']',
                            'class' => 'dynamic-element-names',
                            
                            // @todo examine whether this value should include a section index.
                            'value' => $aSection[ 'section_id' ],
                        )
                    );
                }     
                
                /**
                 * Embeds an internal hidden input for the 'save' argument.
                 * @since       3.6.0
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
                            'name'  => "__unset[{$aSection[ 'section_id' ]}]",
                            'value' => "__dummy_option_key|" . $aSection[ 'section_id' ],
                        )
                    );            
                    
                }                
                /**
                 * Returns an section table output array by adding a section output with a tab list.
                 * @since       3.5.3
                 * @since       3.6.0       Removed the `$_sSectionID` and `$iSectionIndex` parameters.
                 * @return      The updated section table output array.
                 */
                private function _getSectionTableWithTabList( array $_aOutputs, array $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback ) {
                                        
                    // Tab list
                    $_aOutputs[ 'section_tab_list' ][] = $this->_getTabList( 
                        $_aSection, 
                        $_aFields, 
                        $hfFieldCallback 
                    );                
                    
                    // Section container
                    $_aOutputs[ 'section_contents' ][] = $this->_getSectionTable( 
                        $_aSection, 
                        $_aFields, 
                        $hfSectionCallback, 
                        $hfFieldCallback 
                    );
                   
                    return $_aOutputs;
                 
                }
            
            /**
             * Returns a formatted sections tables HTMl output.
             * 
             * @internal
             * @since       3.5.3
             * @since       3.6.0       Removed the `$sSectionID` parameter. Added the `$aSectionset` parameter.
             * @return      string      The formatted sections table HTML output.
             */
            private function _getFormattedSectionsTablesOutput( array $aOutputs, $aSectionset, $sSectionsID, array $aCollapsible, $sSectionTabSlug ) {
                
                if ( empty( $aOutputs[ 'section_contents' ] ) ) {
                    return '';
                }
                                
                $_oCollapsibleSectionTitle = new AdminPageFramework_FormPart_CollapsibleSectionTitle(
                    isset( $aCollapsible[ 'title' ] ) 
                        ? $aCollapsible[ 'title' ]
                        : '',
                    'h3',
                    array(),  // fields
                    null,  // field callback
                    null,  // section index
                    $this->aFieldTypeDefinitions,                
                    $aCollapsible, 
                    'sections',
                    $this->oMsg
                );

                $_oSectionsTablesContainerAttributes = new AdminPageFramework_Attribute_SectionsTablesContainer(
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
             * @since       3.6.0       Removed the `$iSectionIndex` parameter.
             */
            private function _getTabList( array $aSection, array $aFields, $hfFieldCallback ) {
                                
                if ( ! $aSection['section_tab_slug'] ) {
                    return '';
                }
                
                $iSectionIndex      = $aSection[ '_index' ];

                $_sSectionTagID     = 'section-' . $aSection[ 'section_id' ] . '__' . $iSectionIndex;
                $_aTabAttributes    = $aSection['attributes']['tab']
                    + array(
                        'class' => 'admin-page-framework-section-tab nav-tab',
                        'id'    => "section_tab-{$_sSectionTagID}",
                        'style' => null
                    );
                $_aTabAttributes['class'] = $this->generateClassAttribute( $_aTabAttributes['class'], $aSection['class']['tab'] );  // 3.3.1+
                $_aTabAttributes['style'] = $this->generateStyleAttribute( $_aTabAttributes['style'], $aSection['hidden'] ? 'display:none' : null );  // 3.3.1+
                
                $_oSectionTitle = new AdminPageFramework_FormPart_SectionTitle(
                    $aSection[ 'title' ],
                    'h4', 
                    $aFields, 
                    $hfFieldCallback, 
                    $iSectionIndex, 
                    $this->aFieldTypeDefinitions
                );                        
                
                return "<li " . $this->generateAttributes( $_aTabAttributes ) . ">"
                    . "<a href='#{$_sSectionTagID}'>"
                        . $_oSectionTitle->get()
                    ."</a>"
                . "</li>";
                
            }        
               
    /**
     * Returns a single HTML table output of a form section (a set of fields) generated from the given field definition arrays.
     * 
     * @since       3.0.0
     * @since       3.3.1       Now the first parameter is for the section ID not the tag ID.
     * @since       3.4.0       Changed the name from `getFormTable()`.
     * @since       3.6.0       Removed the `$sSectionID` and `$iSectionIndex` parameters.
     * @param       integer     $iSectionIndex       The section index. Zero based.
     * @param       array       $aSection            The section definition array,
     * @param       array       $sFields             The array holding field definition arrays.
     * @param       callable    $hfSectionCallback   The callback for the section header output.
     * @param       callable    $hfFieldCallback     The callback for the field output.
     * @return      string
     */
    private function _getSectionTable( $aSection, $aFields, $hfSectionCallback, $hfFieldCallback ) {

        if ( count( $aFields ) <= 0 ) { 
            return ''; 
        }
               
        $iSectionIndex = $aSection[ '_index' ];
               
        $_oTableCaption = new AdminPageFramework_FormPart_TableCaption(
            $aSection, 
            $hfSectionCallback,
            $iSectionIndex,
            $aFields, 
            $hfFieldCallback,
            $this->aFieldErrors,
            $this->aFieldTypeDefinitions,
            $this->oMsg
        );
        
        $_oSectionTableAttributes     = new AdminPageFramework_Attribute_SectionTable( $aSection );
        $_oSectionTableBodyAttributes = new AdminPageFramework_Attribute_SectionTableBody( $aSection );
        
        $_aOutput       = array();
        $_aOutput[]     = "<table " . $_oSectionTableAttributes->get() . ">"
                . $_oTableCaption->get()
                . "<tbody " . $_oSectionTableBodyAttributes->get() . ">"
                    . $this->getFieldsetRows( $aFields, $hfFieldCallback, $iSectionIndex )
                . "</tbody>"
            . "</table>";
        
        $_oSectionTableContainerAttributes  = new AdminPageFramework_Attribute_SectionTableContainer( $aSection );
        return "<div " . $_oSectionTableContainerAttributes->get() . ">"
                . implode( PHP_EOL, $_aOutput )
            . "</div>";
        
    }
    
    /**
     * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
     * 
     * @since       3.0.0
     * @since       3.6.0       Added the `$iSectionIndex` parameter. Changed the name from `getFIeldRows`.
     * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`.
     * @return      string
     */
    public function getFieldsetRows( array $aFieldsets, $hfCallback, $iSectionIndex=null ) {
        
        if ( ! is_callable( $hfCallback ) ) { 
            return ''; 
        }
        
        $_aOutput = array();
        foreach( $aFieldsets as $_aFieldset ) {
            
            $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                $_aFieldset, 
                $iSectionIndex,
                $this->aFieldTypeDefinitions
            );
            $_oFieldsetRow = new AdminPageFramework_FormPart_TableRow(
                $_oFieldsetOutputFormatter->get(), 
                $hfCallback             
            );
            $_aOutput[] = $_oFieldsetRow->get();

        } 
        return implode( PHP_EOL, $_aOutput );
        
    }
      
    /**
     * Returns a set of fields output from the given field definition array.
     * 
     * @remark      This is similar to getFieldsetRows() but without the enclosing table row tag. 
     * @remark      Used for taxonomy fields.
     * @since       3.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormTable_Row`.
     * @return      string
     */
    public function getFieldsets( array $aFieldsets, $hfCallback ) {
        
        if ( ! is_callable( $hfCallback ) ) { 
            return ''; 
        }
        
        $_aOutput = array();
        foreach( $aFieldsets as $_aFieldset ) {
            $_oFieldsetOutputFormatter = new AdminPageFramework_Format_FieldsetOutput( 
                $_aFieldset, 
                null, // section index
                $this->aFieldTypeDefinitions
            );            
            $_oFieldsetRow = new AdminPageFramework_FormPart_FieldsetRow(
                $_oFieldsetOutputFormatter->get(),
                $hfCallback             
            );
            $_aOutput[]    = $_oFieldsetRow->get();

        }
        return implode( PHP_EOL, $_aOutput );
        
    }    
       
}