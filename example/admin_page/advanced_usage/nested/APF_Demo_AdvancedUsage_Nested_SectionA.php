<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a section in a tab.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_AdvancedUsage_Nested_SectionA {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'nested';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'A';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Sections
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Section A', 'admin-page-framework-loader' ),
                'description'       => __( 'Sections can be nested.', 'admin-page-framework-loader' )
                    . ' ' . __( 'Pass section definitions to the <code>content</code> argument.', 'admin-page-framework-loader' ),
                'section_tab_slug'  => 'root_section_tab',
                'content'           => array(
                    array(
                        'section_id'    => 'i',
                        'title'         => __( 'A', 'admin-page-framework-loader' )
                            . ' &raquo; ' . __( 'i', 'admin-page-framework-loader' ),
                        'description'   => __( 'Nesting one level deep.', 'admin-page-framework-loader' ),
                        'collapsible'   => array(
                            'toggle_all_button' => 'top-right',
                        ),
                    ),
                    array(
                        'section_id'    => 'ii',
                        'title'         => __( 'A', 'admin-page-framework-loader' )
                            . ' &raquo; ' . __( 'ii', 'admin-page-framework-loader' ),
                        'description'   => __( 'Nesting one level deep.', 'admin-page-framework-loader' ),
                        'collapsible'   => true,
                        'content'       => array(
                            array(
                                'section_id'    => 'X',
                                'title'         => __( 'A', 'admin-page-framework-loader' )
                                    . ' &raquo; ' . __( 'ii', 'admin-page-framework-loader' )
                                    . ' &raquo; ' . __( 'x', 'admin-page-framework-loader' ),
                                'description'   => __( 'Nesting two level deep.', 'admin-page-framework-loader' ),
                            ),
                            array(
                                'section_id'    => 'Y',
                                'title'         => __( 'A', 'admin-page-framework-loader' )
                                    . ' &raquo; ' . __( 'ii', 'admin-page-framework-loader' )
                                    . ' &raquo; ' . __( 'y', 'admin-page-framework-loader' ),
                                'description'   => __( 'Nesting two level deep.', 'admin-page-framework-loader' ),
                            ),
                        ),
                    ),
                    array(
                        'section_id'    => 'iii',
                        'title'         => __( 'A', 'admin-page-framework-loader' )
                            . ' &raquo; ' . __( 'iii', 'admin-page-framework-loader' ),
                        'description'   => array(
                            __( 'This is a description of the nested section.', 'admin-page-framework-loader' ),
                         ),
                        'collapsible'   => array(
                            'toggle_all_button' => 'bottom-right',
                        ),
                        'content'       => '<p>'
                            . __( 'An area without any form fields can be used for additional information such as help and contact information etc.', 'admin-page-framework-loader' )
                            . '</p>',
                    ),
                ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            array( $this->sSectionID, 'i', ), // the target section ID - pass dimensional keys of the section
            array(
                'field_id'      => 'text_in_nested_section',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'description'   => __( 'Added to a nested section.', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            ),
            array(
                'field_id'      => 'color_in_nested_section',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );

        $oFactory->addSettingFields(
            array( $this->sSectionID, 'ii', 'X' ), // the target section ID - pass dimensional keys of the section
            array(
                'field_id'      => 'textarea_in_nested_section',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'description'   => __( 'Added to a 2nd depth section.', 'admin-page-framework-loader' ),
                'default'       => __( 'This is a default value.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'image_in_nested_section',
                'title'         => __( 'image', 'admin-page-framework-loader' ),
                'type'          => 'image',
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;',
                    ),
                ),
                'repeatable'    => true,
                'sortable'      => true,
            )
        );

        $oFactory->addSettingFields(
            array( $this->sSectionID, 'ii', 'Y' ), // the target section ID - pass dimensional keys of the section
            array(
                'field_id'      => 'checkbox_in_nested_section',
                'title'         => __( 'Checkbox', 'admin-page-framework-loader' ),
                'description'   => __( 'Added to a 2nd depth section.', 'admin-page-framework-loader' ),
                'type'          => 'checkbox',
                'label'         => __( 'Check me.', 'admin-page-framework-loader'  ),
                'default'       => true,
            ),
            array(
                'field_id'      => 'checkboxes_in_nested_section',
                'title'         => __( 'Check Boxes', 'admin-page-framework-loader' ),
                'type'          => 'checkbox',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
                'default'       => array(
                    'b' => true,
                ),
            ),
            array(
                'field_id'      => 'select_in_nested_section',
                'title'         => __( 'Select', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'multiple'      => true,
                'label'         => array(
                    'a' => __( 'Apple', 'admin-page-framework-loader' ),
                    'b' => __( 'Banana', 'admin-page-framework-loader' ),
                    'c' => __( 'Cherry', 'admin-page-framework-loader' ),
                ),
                'default'       => 'b',
            ),
            array(
                'field_id'      => 'selects_in_nested_section',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'is_multiple'   => true,
                'label'         => array(
                    'red'       => __( 'Red', 'admin-page-framework-loader' ),
                    'green'     => __( 'Green', 'admin-page-framework-loader' ),
                    'yellow'    => __( 'Yellow', 'admin-page-framework-loader' ),
                ),
                // 'default'       => array( 'green', 'yellow' ),
            )
        );

        // validation_{class name}_{seciton path}
        add_filter(
            'validation_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID . '_' . 'nested_section_a',
            array( $this, 'replyToValidateNestedSectionA' ),
            10,
            4
        );

    }

    /**
     * @callback     filter     validation_{class name}_{seciton path}
     * @return       array
     */
    public function replyToValidateNestedSectionA( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        // AdminPageFramework_Debug::log( $aInputs );
        return $aInputs;

    }

}
