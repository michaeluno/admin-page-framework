<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render setting sections and fields.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormTable extends AdminPageFramework_FormTable_Caption {
        
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
        return implode( PHP_EOL, $_aOutput ) 
            . $this->_getSectionTabsEnablerScript()
            . $this->_getDebugInfo( $_sFieldsType )
            ;
            
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
                $aSectionsBySectionTab, 
                $aFieldsInSections[ $sSectionTabSlug ],
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
                
                $_sSectionTaqbSlug = $_aSection['section_tab_slug'] 
                    ? $_aSection['section_tab_slug']
                    : '_default_' . ( ++$_iIndex );                
                                        
                $_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionID ] = $_aSection;
                $_aFieldsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionID ]   = $aFields[ $_sSectionID ];
                    
            }
            
            $aSections  = $_aSectionsBySectionTab;
            $aFields    = $_aFieldsBySectionTab;

        }      
    
        /**
         * Returns the fields type of the given sections.
         * 
         * @since   3.3.3
         */
        private function _getSectionsFieldsType( array $aSections=array() ) {
            // Only the first iteration item is needed
            foreach( $aSections as $_aSection ) {
                return $_aSection['_fields_type'];
            }
        }
        /**
         * Returns the section ID of the first found item of the given sections.
         * 
         * @since   3.4.3
         */
        private function _getSectionsSectionID( array $aSections=array() ) {
            // Only the first iteration item is needed
            foreach( $aSections as $_aSection ) {
                return $_aSection['section_id'];
            }
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

            // if empty, return a blank string.
            if ( empty( $aSections ) ) { 
                return ''; 
            } 
            
            $_sSectionTabSlug   = '';
            $_aOutputs          = array(
                'section_tab_list'  => array(),
                'section_contents'  => array(),
            );
            $_sThisSectionID    = $this->_getSectionsSectionID( $aSections );
            $_sSectionsID       = 'sections-' . $_sThisSectionID; // md5( serialize( $aSections ) );
            $_aCollapsible      = $this->_getCollapsibleArgumentForSections( $aSections );
            foreach( $aSections as $_sSectionID => $_aSection ) {
                
                // Need to be referred outside the loop.
                $_sSectionTabSlug   = $aSections[ $_sSectionID ]['section_tab_slug']; 
                
                $_aOutputs = $this->_getSectionsTable( 
                    $_aOutputs,
                    $_sSectionID, 
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
                $_sThisSectionID, 
                $_sSectionsID,
                $this->getAsArray( $_aCollapsible ),
                $_sSectionTabSlug
            );
            
        }
            /**
             * Returns the collapsible argument array from the given sections definition array.
             * 
             * @since       3.5.3
             * @return      array
             */
            protected function _getCollapsibleArgumentForSections( array $aSections=array() ) {  
                $_aCollapsible      = $this->_getCollapsibleArgument( $aSections );
                return isset( $_aCollapsible['container'] ) && 'sections' === $_aCollapsible['container'] 
                    ? $_aCollapsible 
                    : array();            
            }
            
            /**
             * Returns an upadted sections table output array.
             * @since       3.5.3
             * @todo        finish this method
             * @return      array       The upadted sections table output array.
             */
            private function _getSectionsTable( $_aOutputs, $_sSectionID, $_sSectionsID, array $_aSection, array $aFieldsInSections, $hfSectionCallback, $hfFieldCallback  ) {
             
                // For repeatable sections - note that sub-sections are divided field definition arrays by sub-section index, not section definition arrays.
                $_aSubSections      = $this->getIntegerKeyElements( 
                    $this->getElementAsArray(
                        $aFieldsInSections, // subject
                        $_sSectionID,   // dimensional key
                        array() // default
                    )
                );
                
                $_iCountSubSections = count( $_aSubSections ); // Check sub-sections.
                if ( $_iCountSubSections ) {

                    // Add the repeatable sections enabler script.
                    if ( $_aSection['repeatable'] ) {
                        $_aOutputs[ 'section_contents' ][] = $this->_getRepeatableSectionsEnablerScript( 
                            $_sSectionsID, 
                            $_iCountSubSections, 
                            $_aSection['repeatable'] 
                        );
                    }
                    
                    // Get the section tables.
                    $_aSubSections = $this->numerizeElements( $_aSubSections ); // will include the main section as well.
                    foreach( $_aSubSections as $_iIndex => $_aFields ) { 
                        
                        $_aSection[ '_is_first_index' ] = $this->isFirstElement( $_aSubSections, $_iIndex );
                        $_aSection[ '_is_last_index' ]  = $this->isLastElement( $_aSubSections, $_iIndex );
                        $_aOutputs = $this->_getSectionTableWithTabList(
                            $_aOutputs,
                            $_sSectionID, 
                            $_iIndex, 
                            $_aSection, 
                            $_aFields, 
                            $hfSectionCallback,
                            $hfFieldCallback                     
                        );
                        
                    }
                    return $_aOutputs;
                } 

                // The normal section.
                $_aOutputs = $this->_getSectionTableWithTabList(
                    $_aOutputs,
                    $_sSectionID, 
                    0, 
                    $_aSection, 
                    $this->getElementAsArray( $aFieldsInSections, $_sSectionID, array() ), 
                    $hfSectionCallback,
                    $hfFieldCallback                     
                );
                return $_aOutputs;
              
            }
                /**
                 * Returns an section table output array by adding a section output with a tab list.
                 * @since       3.5.3
                 * @return      The updated section table output array.
                 */
                private function _getSectionTableWithTabList( array $_aOutputs, $_sSectionID, $_iIndex, array $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback ) {
                 
                    // Tab list
                    $_aOutputs[ 'section_tab_list' ][] = $this->_getTabList( 
                        $_sSectionID, 
                        $_iIndex, 
                        $_aSection, 
                        $_aFields, 
                        $hfFieldCallback 
                    );                
                    
                    // Section container
                    $_aOutputs[ 'section_contents' ][] = $this->_getSectionTable( 
                        $_sSectionID, 
                        $_iIndex, 
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
             * @return      string      The formatted sections table HTML output.
             */
            private function _getFormattedSectionsTablesOutput( array $aOutputs, $sSectionID, $sSectionsID, array $aCollapsible, $sSectionTabSlug ) {
                
                return empty( $aOutputs['section_contents'] )
                    ? ''
                    : $this->_getCollapsibleSectionTitleBlock( $aCollapsible, 'sections' )
                        . "<div " . $this->generateAttributes( 
                            $this->_getSectionsTablesContainerAttributes(
                                $sSectionID,
                                $sSectionsID,
                                $sSectionTabSlug,
                                $aCollapsible                            
                            ) 
                        ) . ">" 
                            . $this->_getSectionTabList( $sSectionTabSlug, $aOutputs['section_tab_list'] )
                            . implode( PHP_EOL, $aOutputs['section_contents'] )
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
                 * Returns a generated sections-tables container attribute array.
                 * 
                 * @internal
                 * @since       3.5.3
                 * @return      array       Returns the generated sections-tables container attribute array.
                 */
                private function _getSectionsTablesContainerAttributes( $sSectionID, $sSectionsID, $sSectionTabSlug, array $aCollapsible ) {
                    return array(
                        'id'    => $sSectionsID, 
                        'class' => $this->generateClassAttribute( 
                            'admin-page-framework-sections',
                            ! $sSectionTabSlug || '_default' === $sSectionTabSlug 
                                ? null 
                                : 'admin-page-framework-section-tabs-contents',
                            empty( $aCollapsible )
                                ? null
                                : 'admin-page-framework-collapsible-sections-content admin-page-framework-collapsible-content accordion-section-content'
                        ),
                        // 3.4.3+ to help find the sections container for custom scripts that groups sections.
                        'data-seciton_id'   => $sSectionID,   
                    );                
                }
                
            /**
             * Returns the output of a list tab element for tabbed sections.
             * 
             * @since       3.4.0
             */
            private function _getTabList( $sSectionID, $iIndex, array $aSection, array $aFields, $hfFieldCallback ) {
                
                if ( ! $aSection['section_tab_slug'] ) {
                    return '';
                }
                $_sSectionTagID     = 'section-' . $sSectionID . '__' . $iIndex;
                $_aTabAttributes    = $aSection['attributes']['tab']
                    + array(
                        'class' => 'admin-page-framework-section-tab nav-tab',
                        'id'    => "section_tab-{$_sSectionTagID}",
                        'style' => null
                    );
                $_aTabAttributes['class'] = $this->generateClassAttribute( $_aTabAttributes['class'], $aSection['class']['tab'] );  // 3.3.1+
                $_aTabAttributes['style'] = $this->generateStyleAttribute( $_aTabAttributes['style'], $aSection['hidden'] ? 'display:none' : null );  // 3.3.1+
                return "<li " . $this->generateAttributes( $_aTabAttributes ) . ">"
                    . "<a href='#{$_sSectionTagID}'>"
                        . $this->_getSectionTitle( $aSection['title'], 'h4', $aFields, $hfFieldCallback )
                    ."</a>"
                . "</li>";
                
            }        
               
    /**
     * Returns a single HTML table output of a form section (a set of fields) generated from the given field definition arrays.
     * 
     * @since       3.0.0
     * @since       3.3.1       Now the first parameter is for the section ID not the tag ID.
     * @since       3.4.0       Changed the name from `getFormTable()`.
     * @param       string      $sSectionID          The section ID specified by the user.
     * @param       integer     $iSectionIndex       The section index. Zero based.
     * @param       array       $aSection            The section definition array,
     * @param       array       $sFields             The array holding field definition arrays.
     * @param       callable    $hfSectionCallback   The callback for the section header output.
     * @param       callable    $hfFieldCallback     The callback for the field output.
     */
    private function _getSectionTable( $sSectionID, $iSectionIndex, $aSection, $aFields, $hfSectionCallback, $hfFieldCallback ) {

        if ( count( $aFields ) <= 0 ) { 
            return ''; 
        }
        
        $_bCollapsible  = $aSection['collapsible'] && 'section' === $aSection['collapsible']['container'];
        $_sSectionTagID = 'section-' . $sSectionID . '__' . $iSectionIndex;
        $_aOutput       = array();
        $_aOutput[]     = "<table "
            . $this->generateAttributes(  
                    array( 
                        'id'    => 'section_table-' . $_sSectionTagID,
                        'class' =>  $this->generateClassAttribute(
                            'form-table',
                            'admin-page-framework-section-table'   // referred by the collapsible section script
                        ),
                    )
                )
            . ">"
                . $this->_getCaption( $aSection, $hfSectionCallback, $iSectionIndex, $aFields, $hfFieldCallback )
                . "<tbody " 
                    . $this->generateAttributes( 
                        array(
                            'class' => $_bCollapsible
                                ? 'admin-page-framework-collapsible-section-content admin-page-framework-collapsible-content accordion-section-content'
                                : null,
                        ) 
                    )
                . ">"
                    . $this->getFieldRows( $aFields, $hfFieldCallback )
                . "</tbody>"
            . "</table>";
            
        $_aSectionAttributes    = $this->uniteArrays(
            $this->dropElementsByType( $aSection['attributes'] ),   // remove elements of an array.
            array( 
                'id'            => $_sSectionTagID, // section-{section id}__{index}
                'class'         => $this->generateClassAttribute( 
                    'admin-page-framework-section',
                    $aSection['section_tab_slug'] 
                        ? 'admin-page-framework-tab-content' 
                        : null,
                    $_bCollapsible
                        ? 'is_subsection_collapsible' // when this is present, the section repeater script does not repeat tabs.
                        : null
                ),
                // [3.3.1+] The repeatable script refers to this model value to generate new IDs.
                'data-id_model' => 'section-' . $sSectionID . '__' . '-si-',
            )     
        );
        $_aSectionAttributes['class']   = $this->generateClassAttribute( $_aSectionAttributes['class'], $this->dropElementsByType( $aSection['class'] ) );  // 3.3.1+
        $_aSectionAttributes['style']   = $this->generateStyleAttribute( $_aSectionAttributes['style'], $aSection['hidden'] ? 'display:none' : null );  // 3.3.1+        

        return "<div "
                . $this->generateAttributes( $_aSectionAttributes )
            . ">"
                . implode( PHP_EOL, $_aOutput )
            . "</div>";
        
    }
       
}