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
class APF_Demo_AdvancedUsage_Nested_SectionB {

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
    public $sSectionID  = 'B';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Sections
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Section B', 'admin-page-framework-loader' ),
                'description'       => __( 'This is a second tabbed section.', 'admin-page-framework-loader' ),
                'section_tab_slug'  => 'root_section_tab',
            )
        );

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID - pass dimensional keys of the section
            array(
                'field_id'      => 'color_in_nesting_section_b',
                'title'         => __( 'color', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );

    }

}
