<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
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
                'section_id'        => 'tabbed_sections_a',
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab A', 'admin-page-framework-demo' ),
                'description'       => __( 'This is the first item of the tabbed section.', 'admin-page-framework-demo' ),
            )
        );
        $this->addSettingSections(
            array(
                'section_id'        => 'tabbed_sections_b',
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab B', 'admin-page-framework-demo' ),
                'description'       => __( 'This is the second item of the tabbed section.', 'admin-page-framework-demo' ),
            )
        );
        
        /*
         * Add form fields into the meta box.
         */ 
        $this->addSettingFields(     
            array(
                'section_id' => 'tabbed_sections_a',
                'field_id' => 'text_field_in_tabbed_section',
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'xyz',
            ),
            array(
                'field_id' => 'repeatable_field_in_tabbed_sections',
                'title' => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type' => 'text',
                'repeatable' => true,
            )
        );
        $this->addSettingFields( 
            array(
                'section_id' => 'tabbed_sections_b',
                'field_id' => 'size_in_tabbed_sections',
                'title' => __( 'Size', 'admin-page-framework-demo' ),
                'type' => 'size',
            ),
            array(
                'field_id' => 'select_in_tabbed_sections',
                'title' => __( 'Select', 'admin-page-framework-demo' ),
                'type' => 'select',
                'default' => 'b',
                'label' => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            )
        );    
      
    }
     
}
    
