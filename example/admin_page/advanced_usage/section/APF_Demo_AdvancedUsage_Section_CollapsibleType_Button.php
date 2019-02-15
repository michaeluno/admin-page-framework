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
class APF_Demo_AdvancedUsage_Section_CollapsibleType_Button {

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
    public $sSectionID  = 'collapsible_type_button';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Button Type Collapsible Section', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'container' => 'section',
                    'type'      => 'button',
                    'collapsed' => true,
                ),
                'tip'               => __( 'When the <code>type</code> argument is <code>button</code>, the toggle button will be used instead of a container box.', 'admin-page-framework-loader' ),
            )
        );
        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'field_in_button_type_collapsible_seciton_b',
                'type'          => 'color',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
                'sortable'      => true,
                'repeatable'    => true,
            )
        );

    }

}
