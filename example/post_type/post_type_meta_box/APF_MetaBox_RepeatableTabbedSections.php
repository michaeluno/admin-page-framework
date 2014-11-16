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

class APF_MetaBox_RepeatableTabbedSections extends AdminPageFramework_MetaBox {
        
    /**
     * Do set-ups.
     */
    public function setUp() {
        
        /*
         * Create tabbed sections.
         */
        $this->addSettingSections(   
            array(
                'section_id'        => 'repeatable_tabbed_sections',
                'section_tab_slug'  => 'repeatable_tabbes_sections',
                'title'             => __( 'Repeatable', 'admin-page-framework-demo' ),
                'description'       => __( 'It is possible to tab repeatable sections.', 'admin-page-framework-demo' ),
                'repeatable'        => true, // this makes the section repeatable
            )
        );
        
        /*
         * Add form fields into the meta box.
         */ 
        $this->addSettingFields(    
            'repeatable_tabbed_sections',   // section id
            array(
                'field_id'      => 'tab_title',
                'type'          => 'section_title',
                'label'         => __( 'Name', 'admin-page-framework-demo' ),
                'attributes'    => array(
                    'size' => 10,
                    // 'type' => 'number', // change the input type 
                ),
            ),     
            array(
                'field_id'      => 'text_field_in_tabbed_section_in_repeatable_sections',
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'default'       => 'xyz',
            ),
            array(
                'field_id'      => 'repeatable_field_in_tabbed_sections_in_repetable_sections',
                'title'         => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    =>    true,
            ),     
            array(
                'field_id'      => 'size_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Size', 'admin-page-framework-demo' ),
                'type'          => 'size',
            ),
            array(
                'field_id'      => 'select_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Select', 'admin-page-framework-demo' ),
                'type'          => 'select',
                'default'       => 'b',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            ),     
            array(
                'field_id'      => 'color_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Color', 'admin-page-framework-demo' ),
                'type'          => 'color',
            ),     
            array()
        );      
      
    }
  
}   