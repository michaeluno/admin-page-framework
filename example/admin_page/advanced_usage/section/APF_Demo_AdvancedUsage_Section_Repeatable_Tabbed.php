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
class APF_Demo_AdvancedUsage_Section_Repeatable_Tabbed {
    
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
    public $sSectionID  = 'repeatable_tabbed_sections';
        
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
                'section_tab_slug'  => 'repeatable_tabbes_sections',
                'title'             => __( 'Repeatable', 'admin-page-framework-loader' ),
                'description'       => __( 'It is possible to repeat and sort tabbed sections.', 'admin-page-framework-loader' ),
                'repeatable'        => true,
                'sortable'          => true,
            )
        );   

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
             array(
                'field_id'      => 'tab_title',
                'type'          => 'section_title',
                'label'         => __( 'Name', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'size' => 10,
                    // 'type' => 'number', // change the input type 
                ),
            ),
            array(
                'field_id'      => 'text_field_in_tabbed_section_in_repeatable_sections',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'default'       => 'xyz',
            ),
            array(
                'field_id'      => 'repeatable_field_in_tabbed_sections_in_repetable_sections',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
            ),     
            array(
                'field_id'      => 'size_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Size', 'admin-page-framework-loader' ),
                'type'          => 'size',
            ),
            array(
                'field_id'      => 'radio_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Radio', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'label'         => array(
                    'one'   => __( 'One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Three', 'admin-page-framework-loader' ),
                ),
                'default'       => 'two',
            ),            
            array(
                'field_id'      => 'select_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Select', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'default'       => 'b',
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',     
                ),
            ),     
            array(
                'field_id'      => 'color_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'repeatable'    => true,
                'sortable'      => true,
            ), 
            array(
                'field_id'      => 'image_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
                'type'          => 'image',
                'repeatable'    => true,
                'sortable'      => true,
                'attributes'    => array(
                    'style' => 'max-width:300px;',
                ),
            ),     
            array(
                'field_id'      => 'media_in_tabbed_sections_in_repeatable_sections',
                'title'         => __( 'Media', 'admin-page-framework-loader' ),
                'type'          => 'media',
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => __( 'This field is repeatable and sortable.', 'admin-page-framework-loader' ),
            )
        );              
      
    }

}
