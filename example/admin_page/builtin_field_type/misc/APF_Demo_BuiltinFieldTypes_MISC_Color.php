<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_MISC_Color {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'misc';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'color_picker';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Colors', 'admin-page-framework-loader' ),
                'description'       => __( 'These are color picker fields.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array( // Color Picker
                'field_id'      => 'color_picker_field',
                'title'         => __( 'Color Picker', 'admin-page-framework-loader' ),
                'type'          => 'color',
            ),
            array( // Multiple Color Pickers
                'field_id'      => 'multiple_color_picker_field',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'label'         => __( 'First', 'admin-page-framework-loader' ),
                'delimiter'     => '<br />',
                array(
                    'label' => __( 'Second', 'admin-page-framework-loader' ),
                ),
                array(
                    'label' => __( 'Third', 'admin-page-framework-loader' ),
                ),
            ),
            array( // Repeatable Color Pickers
                'field_id'      => 'color_picker_repeatable_field',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'repeatable'    => true,
                'default'       => '', // set an empty so that repeated element has this default value.
            ),
            array( // Repeatable Color Pickers
                'field_id'      => 'color_picker_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'color',
                'sortable'      => true,
                array(),    // the second item
                array(),    // the third item
            )
        );
      
    }

}
