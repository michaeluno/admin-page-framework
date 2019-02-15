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
class APF_Demo_AdvancedUsage_Section_Collapsible_Repeatable {

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
    public $sSectionID  = 'collapsible_repeatable_section';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Collapsible Repeatable Section', 'admin-page-framework-loader' ),
                'description'       => __( 'This section can be expanded, collapsed and repeated.', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                ),
                'repeatable'        => true,
                'sortable'          => true,
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'      => 'section_title_in_collapsible_repeatable_section',
                'type'          => 'section_title',
                'label'         => __( 'Section Name', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'text_field_in_collapsible_repeatable_section',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => __( 'This field is repeatable and sortable.', 'admin-page-framework-loader' ),
            )
        );

    }

}
