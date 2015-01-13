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

class APF_Demo_BuiltinFieldTypes_Sections {
 
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
       
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'sections';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = '';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
              
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'         => __( 'Sections', 'admin-page-framework-demo' ),    
            )    
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Sections
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'        => 'section_title_field_type',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Section Title', 'admin-page-framework-demo' ),
                'description'       => __( 'The <code>section_title</code> field type will be placed in the position of the section title if set. If not set, the set section title will be placed. Only one <code>section_title</code> field is allowed per section.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id'        => 'repeatable_sections',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Repeatable Sections', 'admin-page-framework-demo' ),
                'description'       => __( 'As of v3, it is possible to repeat sections.', 'admin-page-framework-demo' ),
                // 'repeatable'        => true,     // this makes the section repeatable
                'repeatable'    => array(   
                    'max' => 5,
                    // 'min' => 2,
                ),  
            ),
            array(
                'section_id'        => 'tabbed_sections_a',
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab A', 'admin-page-framework-demo' ),
                'description'       => __( 'This is the first item of the tabbed section.', 'admin-page-framework-demo' ),
            ),
            array(         
                'section_id'        => 'tabbed_sections_b',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Section Tab B', 'admin-page-framework-demo' ),
                'description'       => __( 'This is the second item of the tabbed section.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id'        => 'repeatable_tabbed_sections',
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => 'repeatable_tabbes_sections',
                'title'             => __( 'Repeatable', 'admin-page-framework-demo' ),
                'description'       => __( 'It is possible to tab repeatable sections.', 'admin-page-framework-demo' ),
                'repeatable'        => true, // this makes the section repeatable
            )              
        );        
 
        // Collapsible sections examples
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug  
            array(
                'section_id'        => 'collapsible_section_a',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Collapsible Section A', 'admin-page-framework-demo' ),
                'description'       => __( 'This section can be expanded and collapsed.', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'toggle_all_button' => 'top-right',
                ),
            ),
            array(         
                'section_id'        => 'collapsible_section_b',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Collapsible Section B', 'admin-page-framework-demo' ),
                'description'       => __( 'The <code>is_collapsed</code> argument can determine the default state of whether it is collapsed or expanded.', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'is_collapsed'     => false,
                ),
            ),
            array(         
                'section_id'        => 'collapsible_section_c',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Collapsible Section C', 'admin-page-framework-demo' ),
                'description'       => __( 'With the <code>collapse_others_on_expand</code> argument, you can set wether the other collapsible sections should be collapsed when the section is expanded.', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'collapse_others_on_expand' => false,
                    'toggle_all_button' => 'bottom-right',
                ),
            )         
        );
        // Collapsible repeatable sections examples
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug  
            array(
                'section_id'        => 'collapsible_repeatable_section',
                'tab_slug'          => $this->sTabSlug,                
                'title'             => __( 'Collapsible Repeatable Section', 'admin-page-framework-demo' ),
                'description'       => __( 'This section can be expanded, collapsed and repeated.', 'admin-page-framework-demo' ),
                'collapsible'       => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                ),
                'repeatable'        => true, // this makes the section repeatable
            )
        );
     
        // Fields
        $oAdminPage->addSettingFields(
            'section_title_field_type', // the target section ID
            array(
                'field_id' => 'section_title_field',
                'type' => 'section_title',
                'label' => '<h3>' 
                        . __( 'Section Name', 'admin-page-framework-demo' ) 
                    . '</h3>',
                'attributes' => array(
                    'size' => 30,
                ),
            )
        );
        $oAdminPage->addSettingFields(    
            'repeatable_sections', // the target section ID
            array(
                'field_id'      => 'text_field_in_repeatable_sections',
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'default'       => 'xyz',
            ),
            array(
                'field_id'      => 'repeatable_field_in_repeatable_sections',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    =>    true,
            ),     
            array(
                'field_id'      => 'color_in_repeatable_sections',
                'title'         => __( 'Color', 'admin-page-framework-demo' ),
                'type'          => 'color',
            ),
            array(
                'field_id'      => 'radio_in_repeatable_sections',
                'title'         => __( 'Radio', 'admin-page-framework-demo' ),
                'type'          => 'radio',
                'default'       => 'b',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            ),     
            array()
        );     
        $oAdminPage->addSettingFields(    
            'tabbed_sections_a', // the target section ID
            array(
                'field_id' => 'text_field_in_tabbed_section',
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'xyz',
            ),
            array(
                'field_id' => 'repeatable_field_in_tabbed_sections',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type' => 'text',
                'repeatable' =>    true,
            ),     
            'tabbed_sections_b', // the target section ID
            array(
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
            ),     
            array()
        );     
        $oAdminPage->addSettingFields(
            'repeatable_tabbed_sections', // the target section ID
             array(
                'field_id' => 'tab_title',
                'type' => 'section_title',
                'label' => __( 'Name', 'admin-page-framework-demo' ),
                'attributes' => array(
                    'size' => 10,
                    // 'type' => 'number', // change the input type 
                ),
            ),
            array(
                'field_id' => 'text_field_in_tabbed_section_in_repeatable_sections',
                'title' => __( 'Text', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'xyz',
            ),
            array(
                'field_id' => 'repeatable_field_in_tabbed_sections_in_repetable_sections',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type' => 'text',
                'repeatable' => true,
            ),     
            array(
                'field_id' => 'size_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Size', 'admin-page-framework-demo' ),
                'type' => 'size',
            ),
            array(
                'field_id' => 'select_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Select', 'admin-page-framework-demo' ),
                'type' => 'select',
                'default' => 'b',
                'label' => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'c',     
                ),
            ),     
            array(
                'field_id' => 'color_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Color', 'admin-page-framework-demo' ),
                'type' => 'color',
                'repeatable' =>    true,
                'sortable' =>    true,
            ), 
            array(
                'field_id' => 'image_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Image', 'admin-page-framework-demo' ),
                'type' => 'image',
                'repeatable' =>    true,
                'sortable' =>    true,
                'attributes' => array(
                    'style' => 'max-width:300px;',
                ),
            ),     
            array(
                'field_id' => 'media_in_tabbed_sections_in_repeatable_sections',
                'title' => __( 'Media', 'admin-page-framework-demo' ),
                'type' => 'media',
                'repeatable' =>    true,
                'sortable' =>    true,
            ),                 
            array()
        );      

        $oAdminPage->addSettingFields(    
            'collapsible_section_a', // the target section ID
            array(
                'field_id'      => 'text_field_in_collapsible_section',
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            ),     
            'collapsible_section_b', // the target section ID
            array(
                'field_id'      => 'radio_in_collapsible_section',
                'title'         => __( 'Radio', 'admin-page-framework-demo' ),
                'type'          => 'radio',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
                'default'       => 'b',
            ),
            'collapsible_section_c', // the target section ID
            array(
                'field_id'      => 'select_in_collapsible_section',
                'title'         => __( 'Dropdown', 'admin-page-framework-demo' ),
                'type'          => 'select',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
            ),
            array()
        );             
        $oAdminPage->addSettingFields(    
            'collapsible_repeatable_section', // the target section ID
            array(
                'field_id'  => 'section_title_in_collapsible_repeatable_section',
                'type'      => 'section_title',
                'label'     => __( 'Section Name', 'admin-page-framework-demo' ),
            ),            
            array(
                'field_id'      => 'text_field_in_collapsible_repeatable_section',
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );
        
    }
    
}