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
     * @since 3.0.0
     */
    public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
        
        $_aOutput     = array();
        $_sFieldsType = $this->_getSectionsFieldsType( $aSections );    // must be done before the $aSections array gets updated below.
        
        // Update the array structure by tab slug (passed by reference).
        $this->_divideElementsBySectionTabs( $aSections, $aFieldsInSections );
        foreach( $aSections as $_sSectionTabSlug => $_aSectionsBySectionTab ) {
            
            if ( ! count( $aFieldsInSections[ $_sSectionTabSlug ] ) ) { continue; }
          
            $_sSectionSet = $this->_getSectionsTables( 
                $_aSectionsBySectionTab, 
                $aFieldsInSections[ $_sSectionTabSlug ],
                $hfSectionCallback, 
                $hfFieldCallback 
            );
            if ( ! $_sSectionSet ) { continue; }
            
            $_aOutput[] = "<div " . $this->generateAttributes(
                    array(
                        'class' => 'admin-page-framework-sectionset',
                        'id'    => "sectionset-{$_sSectionTabSlug}_" . md5( serialize( $_aSectionsBySectionTab ) ),
                    ) 
                ) . ">" 
                    . $_sSectionSet
                . "</div>";
                
        }
    
        return implode( PHP_EOL, $_aOutput ) 
            . $this->_getSectionTabsEnablerScript()
            . ( defined( 'WP_DEBUG' ) && WP_DEBUG && in_array( $_sFieldsType, array( 'widget', 'post_meta_box', 'page_meta_box', ) )
                ? "<div class='admin-page-framework-info'>" 
                        . 'Debug Info: ' . AdminPageFramework_Registry::Name . ' '. AdminPageFramework_Registry::getVersion() 
                    . "</div>"
                : ''
            );
            
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
         */
        private function _getSectionsTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {

            // if empty, return a blank string.
            if ( empty( $aSections ) ) { return ''; } 
            
            $_sSectionTabSlug   = '';
            $_aSectionTabList   = array();
            $_aOutput           = array();
            $_sThisSectionID    = $this->_getSectionsSectionID( $aSections );
            $_sSectionsID       = 'sections-' . $_sThisSectionID; // md5( serialize( $aSections ) );
            $_aCollapsible      = $this->_getCollapsibleArgument( $aSections );
            $_aCollapsible      = isset( $_aCollapsible['container'] ) && 'sections' === $_aCollapsible['container'] ? $_aCollapsible : array();
        
            foreach( $aSections as $_sSectionID => $_aSection ) {
                
                // Need to be referred outside the loop.
                $_sSectionTabSlug   = $aSections[ $_sSectionID ]['section_tab_slug']; 
                
                // For repeatable sections - note that sub-sections are divided field definition arrays by sub-section index, not section definition arrays.
                $_aSubSections      = $this->getIntegerElements( isset( $aFieldsInSections[ $_sSectionID ] ) ? $aFieldsInSections[ $_sSectionID ] : array() );
                $_iCountSubSections = count( $_aSubSections ); // Check sub-sections.                
                if ( $_iCountSubSections ) {

                    // Add the repeatable sections enabler script.
                    if ( $_aSection['repeatable'] ) {
                        $_aOutput[] = $this->_getRepeatableSectionsEnablerScript( $_sSectionsID, $_iCountSubSections, $_aSection['repeatable'] );
                    }
                    
                    // Get the section tables.
                    $_aSubSections = $this->numerizeElements( $_aSubSections );
                    foreach( $_aSubSections as $_iIndex => $_aFields ) { // will include the main section as well.
                        
                        $_aSection[ '_is_first_index' ] = $this->isFirstElement( $_aSubSections, $_iIndex );
                        $_aSection[ '_is_last_index' ] = $this->isLastElement( $_aSubSections, $_iIndex );
                        
                        // Tab list
                        // if ( empty( $_aCollapsible ) ) {    // this check is just a remain of an attempt to make section tabs and collapsible section work together but it was not possible.
                            $_aSectionTabList[] = $this->_getTabList( $_sSectionID, $_iIndex, $_aSection, $_aFields, $hfFieldCallback );
                        // }
                        // Section container
                        $_aOutput[] = $this->_getSectionTable( $_sSectionID, $_iIndex, $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback );
                        
                    }
                    continue;
                } 

                // The normal section
                $_aFields = isset( $aFieldsInSections[ $_sSectionID ] ) ? $aFieldsInSections[ $_sSectionID ] : array();
                
                // Tab list
                $_aSectionTabList[] = $this->_getTabList( $_sSectionID, 0, $_aSection, $_aFields, $hfFieldCallback );                
                // Section container
                $_aOutput[] = $this->_getSectionTable( $_sSectionID, 0, $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback );
             
            } 
            
            return empty( $_aOutput )
                ? ''
                : ( empty( $_aCollapsible ) ? '' : $this->_getCollapsibleSectionTitleBlock( $_aCollapsible, 'sections' ) )
                    . "<div " . $this->generateAttributes(
                            array(
                                'id'    => $_sSectionsID, 
                                'class' => $this->generateClassAttribute( 
                                    'admin-page-framework-sections',
                                    ! $_sSectionTabSlug || '_default' === $_sSectionTabSlug 
                                        ? null 
                                        : 'admin-page-framework-section-tabs-contents',
                                    empty( $_aCollapsible )
                                        ? null
                                        : 'admin-page-framework-collapsible-sections-content admin-page-framework-collapsible-content accordion-section-content'
                                ),
                                'data-seciton_id'   => $_sThisSectionID,   // 3.4.3+ to help find the sections container for custom scripts that groups sections.
                            )
                        ) . ">"                 
                        . ( $_sSectionTabSlug // if the section tab slug yields true, insert the section tab list
                            ? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" . implode( PHP_EOL, $_aSectionTabList ) . "</ul>"
                            : ''
                        )    
                        . implode( PHP_EOL, $_aOutput )
                    . "</div>";
                
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

        if ( count( $aFields ) <= 0 ) { return ''; }
        
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