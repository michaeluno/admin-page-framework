<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format and generate <em>sections tables</em> HTML attributes.
 *
 * @package     AdminPageFramework/Common/Form/View/Attribute
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_View___Attribute_Base
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_SectionsTablesContainer extends AdminPageFramework_Form_View___Attribute_Base {

    // public $sContext    = 'sections_tables_container';

    /**
     *
     * @since       3.6.0
     */
    public $aSectionset = array();
    public $sSectionsID = '';
    public $sSectionTabSlug = '';
    public $aCollapsible = array();
    public $iSubSectionCount = 0;
    /**
     * Sets up properties.
     */
    public function __construct( /* $aSectionset, $sSectionsID, $sSectionTabSlug, array $aCollapsible, $iSubSectionCount */ ) {

        $_aParameters = func_get_args() + array(
            $this->aSectionset,
            $this->sSectionsID,
            $this->sSectionTabSlug,
            $this->aCollapsible,
            $this->iSubSectionCount,
        );
        $this->aSectionset      = $_aParameters[ 0 ];
        $this->sSectionsID      = $_aParameters[ 1 ];
        $this->sSectionTabSlug  = $_aParameters[ 2 ];
        $this->aCollapsible     = $_aParameters[ 3 ];
        $this->iSubSectionCount = $_aParameters[ 4 ];

    }

    /**
     * Returns an attribute array.
     *
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {

        return array(
            'id'    => $this->sSectionsID,
            'class' => $this->getClassAttribute(
                'admin-page-framework-sections',
                $this->getAOrB(
                    ! $this->sSectionTabSlug || '_default' === $this->sSectionTabSlug,
                    null,
                    'admin-page-framework-section-tabs-contents'
                ),
                $this->getAOrB(
                    empty( $this->aCollapsible ),
                    null,
                    'admin-page-framework-collapsible-sections-content' . ' '
                        . 'admin-page-framework-collapsible-content' . ' '
                        . 'accordion-section-content'

                ),
                $this->getAOrB(
                    empty( $this->aSectionset[ 'sortable' ] ),
                    null,
                    'sortable-section'
                )
            ),
            // 3.4.3+ to help find the sections container for custom scripts that groups sections.
            'data-seciton_id'   => $this->aSectionset[ 'section_id' ],

            // 3.6.0+ - dimensional section address without the option key, used by the 'save' argument and when sorting dynamic elements.
            'data-section_address'          => $this->aSectionset[ 'section_id' ],
            'data-section_address_model'    => $this->aSectionset[ 'section_id' ] . '|' . '___i___',

        )
        + $this->_getDynamicElementArguments( $this->aSectionset );

    }
        /**
         *
         * @since       3.6.0
         * @return      array
         */
        private function _getDynamicElementArguments( $aSectionset ) {

            if ( empty( $aSectionset[ 'repeatable' ] ) && empty( $aSectionset[ 'sortable' ] ) ) {
                return array();
            }

            /**
             * Repeatable fields example
             *   id="fields-repeatable_sections__0_repeatable_field_in_repeatable_sections"
             *   class="admin-page-framework-fields repeatable dynamic-fields"
             *   data-type="text"
             *   data-largest_index="0"
             *   data-field_name_model="APF_Demo[repeatable_sections][0][repeatable_field_in_repeatable_sections][___i___]"
             *   data-field_name_flat="APF_Demo|repeatable_sections|0|repeatable_field_in_repeatable_sections"
             *   data-field_name_flat_model="APF_Demo|repeatable_sections|0|repeatable_field_in_repeatable_sections|___i___"
             *   data-field_tag_id_model="repeatable_sections__0_repeatable_field_in_repeatable_sections_____i___"
             *   data-field_address="repeatable_sections|0|repeatable_field_in_repeatable_sections"
             *   data-field_address_model="repeatable_sections|0|repeatable_field_in_repeatable_sections|___i___">
             */

            $aSectionset[ '_index' ] = null; // generate id and names without sub-sections.
            $_oSectionNameGenerator = new AdminPageFramework_Form_View___Generate_SectionName(
                $aSectionset,
                $aSectionset[ '_caller_object' ]->aCallbacks[ 'hfSectionName' ]
            );
            return array(
                // 3.6.0+ Stores the total number of dynamic elements, used to generate the input id and name of repeated sections which contain an incremented index number.
                'data-largest_index'            => max(
                    ( int ) $this->iSubSectionCount - 1,  // zero-base index
                    0
                ), // convert negative numbers to zero.

                'data-section_id_model'             => $aSectionset[ 'section_id' ] . '__' . '___i___',
                'data-flat_section_name_model'      => $aSectionset[ 'section_id' ] . '|___i___',
            // @todo apply a callback
                // 'data-section_name_model'           => $aSectionset[ 'section_id' ] . '[___i___]',
                'data-section_name_model'           => $_oSectionNameGenerator->getModel(),

            );

        }
}
