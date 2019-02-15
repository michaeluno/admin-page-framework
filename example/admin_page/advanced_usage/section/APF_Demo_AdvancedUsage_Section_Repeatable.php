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
class APF_Demo_AdvancedUsage_Section_Repeatable {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'sections';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'repeatable_sections';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'             => __( 'Repeatable Sections', 'admin-page-framework-loader' ),
                'description'       => array(
                    __( 'As of v3, it is possible to repeat sections.', 'admin-page-framework-loader' ) . ' '
                    . __( 'As of v3.6, it is possible to sort sections.', 'admin-page-framework-loader' ),
                ),
                // 'repeatable'        => true,     // this makes the section repeatable
                'repeatable'    => array(
                    'max' => 5,
                    // 'min' => 2,
                ),
                'sortable'          => true,
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
           array(
                'field_id'      => 'text_field_in_repeatable_sections',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'default'       => 'xyz',
            ),
            array(
                'field_id'      => 'repeatable_field_in_repeatable_sections',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
            ),
            array(
                'field_id'      => 'color_in_repeatable_sections',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
                'type'          => 'color',
            ),
            array(
                'field_id'      => 'radio_in_repeatable_sections',
                'title'         => __( 'Radio', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'default'       => 'b',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',
                ),
            )
        );

    }

}
