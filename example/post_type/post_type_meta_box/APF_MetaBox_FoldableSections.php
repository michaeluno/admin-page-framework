<?php
class APF_MetaBox_FoldableSections extends AdminPageFramework_MetaBox {
        
    /**
     * Do set-ups.
     */
    public function setUp() {
        
        /*
         * Create tabbed sections.
         */
        $this->addSettingSections(
            array(
                'section_id'        => 'foldable_section_a',
                'title'             => __( 'Foldable Section A', 'admin-page-framework-demo' ),
                'foldable'          => true,
            ),
            array(
                'section_id'        => 'foldable_section_b',
                'title'             => __( 'Foldable Section B', 'admin-page-framework-demo' ),
                'foldable'          => true,
            ),
            array(
                'section_id'        => 'foldable_section_c',
                'title'             => __( 'Foldable Section C', 'admin-page-framework-demo' ),
                'foldable'          => true,
            )            
        );
        
        /*
         * Add form fields into the meta box.
         */ 
        $this->addSettingFields(     
            'foldable_section_a',
            array(
                'field_id'      => 'repeatable_field_in_forldable_sections',
                'title'         => __( 'Repeatable Field', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );
        $this->addSettingFields( 
            'foldable_section_b',
            array(
                'field_id'      => 'size_in_foldable_sections',
                'title'         => __( 'Size', 'admin-page-framework-demo' ),
                'type'          => 'size',
            )
        );    
        $this->addSettingFields(     
            'foldable_section_c',
            array(
                'field_id'      => 'select_in_foldable_sections',
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
    
