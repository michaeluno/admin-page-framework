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

class APF_MetaBox_CollapsibleSections extends AdminPageFramework_MetaBox {
        
    /**
     * Do set-ups.
     */
    public function setUp() {
        
        /*
         * Create tabbed sections.
         */
        $this->addSettingSections(
            array(
                'section_id'        => 'collapsible_section_a',
                'title'             => __( 'Collapsible Section A', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'toggle_all_button' => 'top-left',
                ),
            ),
            array(
                'section_id'        => 'collapsible_section_b',
                'title'             => __( 'Collapsible Section B', 'admin-page-framework-demo' ),
                'collapsible'       => true,
            ),
            array(
                'section_id'        => 'collapsible_section_c',
                'title'             => __( 'Collapsible Section C', 'admin-page-framework-demo' ),
                'collapsible'       => true,
            )            
        );
        
        /*
         * Add form fields into the meta box.
         */ 
        $this->addSettingFields(     
            'collapsible_section_a',
            array(
                'field_id'      => 'repeatable_field_in_collapsible_sections',
                'title'         => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );
        $this->addSettingFields( 
            'collapsible_section_b',
            array(
                'field_id'      => 'size_in_collapsible_sections',
                'title'         => __( 'Size', 'admin-page-framework-demo' ),
                'type'          => 'size',
            )
        );    
        $this->addSettingFields(     
            'collapsible_section_c',
            array(
                'field_id'      => 'select_in_collapsible_sections',
                'title'         => __( 'Select', 'admin-page-framework-demo' ),
                'type'          => 'select',
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
    
