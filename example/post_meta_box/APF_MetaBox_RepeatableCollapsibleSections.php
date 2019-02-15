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

class APF_MetaBox_RepeatableCollapsibleSections extends AdminPageFramework_MetaBox {

    /**
     * Do set-ups.
     */
    public function setUp() {

        /*
         * Create tabbed sections.
         */
        $this->addSettingSections(
            array(
                'section_id'        => '_repeatable_collapsible_section',
                'title'             => __( 'Repeatable Collapsible Section', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'toggle_all_button'    => array( 'top-right', 'bottom-right' ),
                    'container'            => 'section',   // either 'sections' or 'section'. For repeatable collapsible sections, use 'section'.
                ),
                'repeatable'        => true,
                'sortable'          => true,
            )
        );

        /*
         * Add form fields into the meta box.
         */
        $this->addSettingFields(
            '_repeatable_collapsible_section',
            array(
                'field_id'      => 'section_title_field_of_repeatable_collapsible_sections',
                'label'         => __( 'Section Name', 'admin-page-framework-loader' ),
                'type'          => 'section_title',
            ),
            array(
                'field_id'      => 'color_field_of_repeatable_collapsible_sections',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );
    }

    /**
     * The 'do_{instantiated class name}' hook.
     *
     */
    public function do_APF_MetaBox_RepeatableCollapsibleSections() {

        echo "<p>"
                . __( 'This section is repeatable and collapsible.', 'admin-page-framework-loader' )
            . "</p>";

    }
}

new APF_MetaBox_RepeatableCollapsibleSections(
    null,   // meta box id
    __( 'Repeatable Collapsible Sections', 'admin-page-framework-loader' ),
    array( 'apf_posts' ),
    'normal',
    'low'
);
