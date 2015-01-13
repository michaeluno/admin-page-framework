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
                'section_id'        => 'repeatable_collapsible_section',
                'title'             => __( 'Repeatable Collapsible Section', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'toggle_all_button'    => array( 'top-right', 'bottom-right' ),
                    'container'            => 'section',   // either 'sections' or 'section'. For repeatable collapsible sections, use 'section'.
                ),
                'repeatable'        => true,
            )
        );
        
        /*
         * Add form fields into the meta box.
         */ 
        $this->addSettingFields(     
            'repeatable_collapsible_section',
            array(
                'field_id'      => 'section_title_field_of_repeatable_collapsible_sections',
                'label'         => __( 'Section Name', 'admin-page-framework-demo' ),
                'type'          => 'section_title',
            ),
            array(
                'field_id'      => 'color_field_of_repeatable_collapsible_sections',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-demo' ),
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
                . __( 'This section is repeatable and collapsible.', 'admin-page-framework-demo' ) 
            . "</p>";
        
    }
}
    
