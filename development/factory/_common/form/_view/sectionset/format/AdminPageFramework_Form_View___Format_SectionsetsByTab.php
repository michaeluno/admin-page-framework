<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format an array holding section-sets definitions and separate them by section tabs.
 *
 * @package     AdminPageFramework/Common/Form/View/Section
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View___Format_SectionsetsByTab extends AdminPageFramework_FrameworkUtility {

    public $aSectionsets  = array();
    public $aFieldsets    = array();

    /**
     * Specifies the nesting level of the sections to retrieve.
     *
     * Only one depth at a time will be parsed.
     */
    public $iNestedDepth  = 0;

    /**
     * Stores the generated section tab slugs.
     */
    public $aSectionTabs = array();


    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( /* array $aSectionsets, $aFieldsets, $iNestedDepth */ ) {

        $_aParameters = func_get_args() + array(
            $this->aSectionsets,
            $this->aFieldsets,
            $this->iNestedDepth,
        );
        $this->aSectionsets     = $_aParameters[ 0 ];
        $this->aFieldsets       = $_aParameters[ 1 ];
        $this->iNestedDepth     = $_aParameters[ 2 ];

        // Perform formatting
        $this->_divideElementsBySectionTabs(
            $this->aSectionsets,
            $this->aFieldsets,
            $this->aSectionTabs
        );

    }

    /**
     * @since       3.7.0
     * @return      array       The conditioned fieldsets array.
     */
    public function getSectionsets( $sTabSlug ) {
        return $this->getElementAsArray(
            $this->aSectionsets,
            $sTabSlug
        );
    }

    /**
     * @since       3.7.0
     * @return      array
     */
    public function getFieldsets( $sTabSlug ) {
        return $this->getElementAsArray(
            $this->aFieldsets,
            $sTabSlug
        );
    }
    /**
     * @return      array       An array holding section tab slugs.
     */
    public function getTabs() {
        return $this->aSectionTabs;
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
         * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
         * @return      void
         */
        private function _divideElementsBySectionTabs( array &$aSectionsets, array &$aFieldsets, array &$aSectionTabs ) {

            $_aSectionsBySectionTab = array();
            $_aFieldsBySectionTab   = array();
            $_iIndex                = 0;

            foreach( $aSectionsets as $_sSectionPath => $_aSectionset ) {

                // If no fields for the section, no need to add the section
                // unless the custom sectionset output is defined.
                if ( ! isset( $aFieldsets[ $_sSectionPath ] ) && ! $this->_isCustomContentSet( $_aSectionset ) ) {
                    continue;
                }

                // Skip items with a depth below or above the current depth
                if ( $this->iNestedDepth <> $_aSectionset[ '_nested_depth' ] ) {
                    continue;
                }

                $_sSectionTaqbSlug = $this->getAOrB(
                    $_aSectionset[ 'section_tab_slug' ],
                    $_aSectionset[ 'section_tab_slug' ],
                    '_default_' . $this->iNestedDepth . '_' . ( ++$_iIndex )
                );
                $_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionPath ] = $_aSectionset;
                $_aFieldsBySectionTab[ $_sSectionTaqbSlug ][ $_sSectionPath ]   = $this->getElement(
                    $aFieldsets,
                    $_sSectionPath
                );
                $aSectionTabs[ $_sSectionTaqbSlug ] = $_sSectionTaqbSlug;

            }

            // Set new values.
            $aSectionsets  = $_aSectionsBySectionTab;
            $aFieldsets    = $_aFieldsBySectionTab;

        }
            /**
             * @since       3.6.1
             * @since       3.7.0      Moved from `AdminPageFramework_FormPart_Table`.
             * @return      boolean     True if a custom content value is set.
             */
            private function _isCustomContentSet( array $aSectionset, array $aKeys=array( 'content' ) ) {
                return isset( $aSectionset[ 'content' ] );
                // @deprecated
                // foreach( $aKeys as $_sKey ) {

                    // if ( ! isset( $aSectionset[ $_sKey ] ) ) {
                        // continue;
                    // }

                    // // For nested sections, the 'content' will hold arrays of child sections. Handle them properly here.
                    // if ( is_scalar( $aSectionset[ $_sKey ] ) ) {
                        // return true;
                    // }

                // }
                // return false;

            }


}
