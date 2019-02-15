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

class APF_MetaBox_TabbedSections extends AdminPageFramework_MetaBox {

    /**
     * Do set-ups.
     */
    public function setUp() {

        /*
         * Create tabbed sections.
         */
        $this->addSettingSections(
            array(
                'section_id'        => '_tabbed_sections_a',
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab A', 'admin-page-framework-loader' ),
                'description'       => __( 'This is the first item of the tabbed section.', 'admin-page-framework-loader' ),
            )
        );
        $this->addSettingSections(
            array(
                'section_id'        => '_tabbed_sections_b',
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab B', 'admin-page-framework-loader' ),
                'description'       => __( 'This is the second item of the tabbed section.', 'admin-page-framework-loader' ),
            )
        );

        /*
         * Add form fields into the meta box.
         */
        $this->addSettingFields(
            '_tabbed_sections_a',
            array(
                'field_id'          => 'text_field_in_tabbed_section',
                'title'             => __( 'Text', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => 'xyz',
            ),
            array(
                'field_id'          => 'repeatable_field_in_tabbed_sections',
                'title'             => __( 'Repeatable Field', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'repeatable'        => true,
            )
        );
        $this->addSettingFields(
            '_tabbed_sections_b',
            array(
                'field_id'          => 'size_in_tabbed_sections',
                'title'             => __( 'Size', 'admin-page-framework-loader' ),
                'type'              => 'size',
            ),
            array(
                'field_id'          => 'select_in_tabbed_sections',
                'title'             => __( 'Select', 'admin-page-framework-loader' ),
                'type'              => 'select',
                'default'           => 'b',
                'label'             => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
            )
        );

    }

}

new APF_MetaBox_TabbedSections(
    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
    __( 'Section Tabs', 'admin-page-framework-loader' ), // title
    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
    'normal',                                           // context (what kind of metabox this is)
    'default'                                           // priority
);
