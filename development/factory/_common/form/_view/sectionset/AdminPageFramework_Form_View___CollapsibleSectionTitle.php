<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to render collapsible section title.
 *
 * @package     AdminPageFramework/Common/Form/View/Section
 * @since       3.6.0
 * @since       3.7.0      Renamed from `AdminPageFramework_FormPart_CollapsibleSectionTitle`.
 * @internal
 */
class AdminPageFramework_Form_View___CollapsibleSectionTitle extends AdminPageFramework_Form_View___SectionTitle {

    public $aArguments      = array(
        'title'             => null,
        'tag'               => null,
        'section_index'     => null,
        'collapsible'       => array(),
        'container_type'    => 'section', // section or sections
        'sectionset'        => array(),  // 3.7.0+ sectionset definition array
    );
    public $aFieldsets               = array();
    public $aSavedData              = array();
    public $aFieldErrors            = array();
    public $aFieldTypeDefinitions   = array();
    public $oMsg;
    public $aCallbacks = array(
        'fieldset_output',
        'is_fieldset_visible'   => null,
    );

    /**
     * Returns HTML formatted collapsible section title blocks by the given section
     *
     * @return      string      The output.
     */
    public function get() {

        if ( empty( $this->aArguments[ 'collapsible' ] ) ) {
            return '';
        }
        $this->___enqueueScript();
        return $this->___getCollapsibleSectionTitleBlock(
            $this->aArguments[ 'collapsible' ],
            $this->aArguments[ 'container_type' ],
            $this->aArguments[ 'section_index' ]
        );
    }
        private function ___enqueueScript() {
            /**
             * @var AdminPageFramework_Form $_oForm
             */
            $_oForm = $this->callBack( $this->aCallbacks[ 'get_form_object' ], array() );
            $_oForm->addResource( 'src_scripts', array(
                'handle_id' => 'admin-page-framework-script-form-collapsible-sections',
            ) );
        }
        /**
         * Returns the output of a title block of the given collapsible section.
         *
         * @since       3.4.0
         * @since       3.6.0           Moved from `AdminPageFramework_FormPart_Table_Base`.
         * @param       array|boolean   $aCollapsible       The collapsible argument.
         * @param       string          $sContainer          The position context. Accepts either 'sections' or 'section'. If the set position in the argument array does not match this value, the method will return an empty string.
         */
        private function ___getCollapsibleSectionTitleBlock( array $aCollapsible, $sContainer='sections', $iSectionIndex=null ) {

            if ( $sContainer !== $aCollapsible[ 'container' ] ) {
                return '';
            }

            $_sSectionTitle = $this->_getSectionTitle(
                $this->aArguments[ 'title' ],
                $this->aArguments[ 'tag' ],
                $this->aFieldsets,
                $iSectionIndex,
                $this->aFieldTypeDefinitions,
                $aCollapsible
            );

            $_aSectionset        = $this->aArguments[ 'sectionset' ];
            $_sSectionTitleTagID = str_replace( '|', '_', $_aSectionset[ '_section_path' ]  ) . '_' . $iSectionIndex;

            return "<div " . $this->getAttributes(
                    array(
                        'id'    => $_sSectionTitleTagID,
                        'class' => $this->getClassAttribute(
                            'admin-page-framework-section-title',
                            $this->getAOrB(
                                'box' === $aCollapsible[ 'type' ],
                                'accordion-section-title',
                                ''
                            ),
                            'admin-page-framework-collapsible-title',
                            $this->getAOrB(
                                'sections' === $aCollapsible[ 'container' ],
                                'admin-page-framework-collapsible-sections-title',
                                'admin-page-framework-collapsible-section-title'
                            ),
                            $this->getAOrB(
                                $aCollapsible[ 'is_collapsed' ],
                                'collapsed',
                                ''
                            ),
                            'admin-page-framework-collapsible-type-' . $aCollapsible[ 'type' ]
                        ),
                    )
                    + $this->getDataAttributeArray( $aCollapsible )
                ) . ">"
                        . $_sSectionTitle
                    . "</div>";

        }

}