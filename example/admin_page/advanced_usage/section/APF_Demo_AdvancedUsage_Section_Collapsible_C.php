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
class APF_Demo_AdvancedUsage_Section_Collapsible_C {

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
    public $sSectionID  = 'collapsible_section_c';

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
                'title'             => __( 'Collapsible Section C', 'admin-page-framework-loader' ),
                'description'       => __( 'With the <code>collapse_others_on_expand</code> argument, you can set wether the other collapsible sections should be collapsed when the section is expanded.', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'collapse_others_on_expand' => false,
                ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'      => 'select_in_collapsible_section',
                'title'         => __( 'Dropdown', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
            )
        );

    }

}
