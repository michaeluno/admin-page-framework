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
class APF_Demo_AdvancedUsage_Complex_Collapsible {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'complex';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'collapsible';
        
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
                'title'         => __( 'More Complex Form', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                    'is_collapsed'      => false,
                ),
                'repeatable'        => true, // this makes the section repeatable
                'sortable'          => true,
            )            
        );   

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID       
            array(
                'field_id'         => 'name',
                'type'             => 'section_title',
                'before_input'     => "<strong>"
                    . __( 'Name', 'fine-ad' ) 
                    . "</strong>:&nbsp; ",
                'attributes'       => array(              
                    'size'          => 80,
                    'style'         => 'width: 92%;',
                    'placeholder'   => __( 'Enter a name', 'fine-ad' ),
                ),
            ), 
            array(
                'field_id'         => 'status',
                'type'             => 'radio',
                'title'            => __( 'Status', 'fine-ad' ),
                'placement'        => 'section_title',
                'label'            => array(
                    1   => __( 'On', 'fine-ad' ),
                    0   => __( 'Off', 'fine-ad' ),
                ),
                'default' => 1,
            ),
            array(
                'field_id'         => 'text',
                'type'             => 'textarea',
                'title'            => __( 'Content', 'fine-ad' ),
                'rich'             => true,
            ),            
            array(
                'field_id'         => 'field_title',
                'title'            => __( 'Field Title Fields', 'fine-ad' ),
                'content'          => array(
                    array(
                        'field_id'  => 'field_title_checkbox',
                        'type'      => 'checkbox',
                        'placement' => 'field_title',
                        'label'     => __( 'Toggle', 'fine-ad' ),
                    ),
                    array(
                        'field_id'   => 'text',
                        'type'       => 'text',
                        'repeatable' => true,
                        'sortable'   => true,
                    ),                    
                ),
            ),                        
            array()
        );

    }

}
