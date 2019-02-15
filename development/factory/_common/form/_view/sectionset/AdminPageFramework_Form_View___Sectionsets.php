<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to render forms.
 *
 * @package     AdminPageFramework/Common/Form/View/Section
 * @since       3.7.0
 * @internal
 */
class AdminPageFramework_Form_View___Sectionsets extends AdminPageFramework_Form_View___Section_Base {

    public $aArguments = array(
        'structure_type'    => 'admin_page',
        'capability'        => '',
        'nested_depth'      => 0,
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
     * @since       3.7.0
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
     * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
     * Renamed from `getFormTables()`.
     * @return      string      The generated HTML form tables output.
     */
    public function get() {

        $_oFormatSectionsetsByTab  = new AdminPageFramework_Form_View___Format_SectionsetsByTab(
            $this->aStructure[ 'sectionsets' ],
            $this->aStructure[ 'fieldsets' ],
            $this->aArguments[ 'nested_depth' ]
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

        // Note that the `show_debug_info` section definition argument won't take effect here but the page/in-page-tab setting will take effect.
        $_oDebugInfo = new AdminPageFramework_Form_View___DebugInfo(
            $this->aArguments[ 'structure_type' ], // Structure type (for debug info) - this must be done before the $aSections array gets updated below.
            $this->aCallbacks,
            $this->oMsg
        );

        // Generate id for this output
        $_sOutput    = implode( PHP_EOL, $_aOutput );
        $_sElementID = "admin-page-framework-sectionsets-" . uniqid();
        return $this->_getSpinnerOutput( $_sOutput )
            .   "<div id='{$_sElementID}' class='admin-page-framework-sctionsets admin-page-framework-form-js-on'>"
                . $_sOutput
                . AdminPageFramework_Form_View___Script_SectionTab::getEnabler()
                . $_oDebugInfo->get()
            . "</div>"
            ;

    }
        /**
         * @since       3.7.0
         * @return      string
         */
        private function _getSpinnerOutput( $_sOutput ) {

            if ( trim( $_sOutput ) ) {
                return "<div class='admin-page-framework-form-loading' style='display: none;'>"
                    . $this->oMsg->get( 'loading' )
                . "</div>";
            }
            return '';
        }
        /**
         * Returns a generated HTML form table output.
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
         * Changed the first parameter to accepts field-sets array which already belongs to the given section tab.
         * Changed the name from `_getFormTable()`. Combined the 4th and 5th parameters to one parameter in an array.
         * @return      string      The generated HTML form table.
         */
        private function _getFormOutput( array $aSectionsets, array $aFieldsets, $sSectionTabSlug, $aCallbacks ) {

            // A sectionset is a set of sections.
            $_sSectionSet = $this->_getSectionsetsTables(
                $aSectionsets,  // section-set definition (already divided by section tab)
                $aFieldsets,    // field-set definitions (already divided by section tab)
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
         * The returned output element is wrapped in a `div` container of the 'admin-page-framework-sections' class selector.
         * And inside it 'admin-page-framework-section' elements will be placed.
         *
         * @since       3.0.0
         * @since       3.4.0       Removed the $sSectionTabSlug parameter. Changed the name from `_getFormTablesBySectionTab()`.
         * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
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

            /**
             * If there is no field overall to the section and its section tab, return an empty string.
             * Otherwise, the section-sets container gets rendered and its CSS rules such as margins give unwanted results.
             */
            if ( ! count( $aFieldsets ) ) {
                return '';
            }

            $_aFirstSectionset  = $this->getFirstElement( $aSectionsets );
            $_aOutputs          = array(
                'section_tab_list'  => array(),
                'section_contents'  => array(),
                'count_subsections' => 0,
            );
            $_sSectionTabSlug   = $_aFirstSectionset[ 'section_tab_slug' ];
            $_sThisSectionID    = $_aFirstSectionset[ 'section_id' ];
            $_sSectionsID       = 'sections-' . $_sThisSectionID;
            $_aCollapsible      = $this->_getCollapsibleArgumentForSections(
                $_aFirstSectionset
            );

            foreach( $aSectionsets as $_aSectionset ) {
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
             * @since       3.6.0      Changed the first parameter from `$aSections`.
             * @since       3.7.0      Moved from `AdminPageFrmework_FormPart_Table`.
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
                return isset( $_aCollapsible[ 'container' ] ) && 'sections' === $_aCollapsible[ 'container' ]
                    ? $_aCollapsible
                    : array();

            }

            /**
             * Returns an updated sections table output array.
             * @since       3.5.3
             * @since       3.6.0       Removed the `$_sSectionID` parameter.\
             * @since       3.7.0       Moved from `AdminPageFramework_FormPart_Table`.
             * @since       3.7.0       Renamed from `_getSectionsTable()`.
             * @param       array       $_aOutputs      Holds output elements - contents, section tab list, count of subsections.
             * @param       string      $_sSectionsID   The container id of sections.
             * @param       array       $_aSection
             * @param       array       $_aFieldsInSections     A field-sets array already divided by section tab.
             * @return      array       The updated sections table output array.
             */
            private function _getSectionsetTable( $_aOutputs, $_sSectionsID, array $_aSection, array $aFieldsInSections ) {

                if ( ! $this->isSectionsetVisible( $_aSection ) ) {
                    return $_aOutputs;
                }

                // If the 'save' argument is false, insert a flag that disables saving the section inputs.
                $_aOutputs[ 'section_contents' ][] = $this->_getUnsetFlagSectionInputTag( $_aSection );

                // For repeatable sections - sub-sections are divided field definition arrays by sub-section index,
                // not section definition arrays.
                $_aSubSections      = $this->getIntegerKeyElements(
                    $this->getElementAsArray(
                        $aFieldsInSections, // subject array
                        $_aSection[ '_section_path' ],  // dimensional path
                        array() // default
                    )
                );

                $_aOutputs[ 'count_subsections' ] = count( $_aSubSections );
                if ( $_aOutputs[ 'count_subsections' ] ) {
                    return $this->_getSubSections(
                        $_aOutputs,
                        $_sSectionsID,
                        $_aSection,
                        $_aSubSections
                    );
                }

                // A normal section.
                $_oEachSectionArguments = new AdminPageFramework_Form_Model___Format_EachSection(
                    $_aSection,
                    null, // sub-section index
                    array(), // sub-sections
                    $_sSectionsID
                );
                $_aOutputs = $this->_getSectionTableWithTabList(
                    $_aOutputs, // data to update
                    $_oEachSectionArguments->get(), // $_aSection,
                    $this->getElementAsArray(
                        $aFieldsInSections,
                        $_aSection[ '_section_path' ], // $_aSection[ 'section_id' ],
                        array()
                    ) // field-set definitions
                );
                return $_aOutputs;

            }
                /**
                 * Returns the output of sub-sections for repeatable and sortable sections.
                 *
                 * @since       3.7.0
                 * @return      array
                 */
                private function _getSubSections( $_aOutputs, $_sSectionsID, $_aSection, $_aSubSections ) {

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

                /**
                 * Returns an HTML internal hidden input tag for the 'repeatable' arguments.
                 * @since       3.6.2
                 * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string
                 */
                private function _getRepeatableSectionFlagTag( array $aSection ) {
                    return $this->getHTMLTag(
                        'input',
                        array(
                            'class'                     => 'element-address',
                            'type'                      => 'hidden',
                            'name'                      => '__repeatable_elements_' . $aSection[ '_structure_type' ]
                                . '[' . $aSection[ 'section_id' ] . ']',
                            // @todo examine whether this value should include a section index.
                            'value' => $aSection[ 'section_id' ],
                        )
                    );
                }
                /**
                 * Returns an HTML internal hidden input tag for the 'sortable' arguments.
                 * @since       3.6.2
                 * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
                 * @return      string
                 */
                private function _getSortableSectionFlagTag( array $aSection ) {
                    return $this->getHTMLTag(
                        'input',
                        array(
                            'class'                     => 'element-address',
                            'type'                      => 'hidden',
                            'name'                      => '__sortable_elements_' . $aSection[ '_structure_type' ]
                                . '[' . $aSection[ 'section_id' ] . ']',
                            // @todo examine whether this value should include a section index.
                            'value' => $aSection[ 'section_id' ],
                        )
                    );
                }

                /**
                 * Embeds an internal hidden input for the 'save' argument.
                 * @since       3.6.0
                 * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
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
                            'name'  => '__unset_' .  $aSection[ '_structure_type' ] . '[' . $aSection[ 'section_id' ] . ']',
                            'value' => "__dummy_option_key|" . $aSection[ 'section_id' ],
                            'class' => 'unset-element-names element-address',
                        )
                    );

                }
                /**
                 * Returns a section table output array by adding a section output with a tab list.
                 * @since       3.5.3
                 * @since       3.6.0      Removed the `$_sSectionID` and `$iSectionIndex` parameters.
                 * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
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
                        $this->aArguments,  // for nested sections
                        $aSectionset,
                        $this->aStructure,
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
             * @since       3.7.0       Moved from `AdminPageFramework_FormPart_Table`.
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
                        'sectionset'        => $aSectionset,    // 3.7.0+ for tooltip
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
                 * @since       3.7.0       Moved from `AdminPageFramework_FormPart_Table`.
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
             * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
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

                        'sectionset'    => $aSection,   // 3.7.0+      for tooltip
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
