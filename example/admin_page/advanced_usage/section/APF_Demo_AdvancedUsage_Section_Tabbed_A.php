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
class APF_Demo_AdvancedUsage_Section_Tabbed_A {
    
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
    public $sSectionID  = 'tabbed_sections_a';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'section_tab_slug'  => 'tabbed_sections',
                'title'             => __( 'Section Tab A', 'admin-page-framework-loader' ),
                'description'       => __( 'This is the first item of the tabbed section.', 'admin-page-framework-loader' ),
            )
        );   

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array(
                'field_id'      => 'text_field_in_tabbed_section',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'default'       => 'xyz',
            ),
            array(
                'field_id'      => 'repeatable_field_in_tabbed_sections',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
            )
        );              
      
    }

}
