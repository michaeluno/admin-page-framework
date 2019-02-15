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
class APF_Demo_AdvancedUsage_Argument_Capability {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'argument';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'capability';        
        
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
                'title'             => __( 'Capabilities', 'admin-page-framework-loader' ),
                'description'       => array(
                    __( 'By using the <code>capability</code> argument, you can control whether a field/section should be displayed to the user.', 'admin-page-framework-loader' ),
                    __( 'This section is only shown to the users with the <code>edit_pages</code> capability.', 'admin-page-framework-loader' ),
                ),
                'capability'        => 'edit_pages',
            )
        );   

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array(
                'field_id'          => 'for_site_admin',
                'title'             => __( 'Site Admin Only', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'This field is only shown to the users with the <code>manage_options</code> capability.', 'admin-page-framework-loader' ),
                'capability'        => 'manage_options',
            ),
            array(
                'field_id'          => 'for_editors',
                'title'             => __( 'Editors or Higher Users Only', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'This field is only shown to the users with the <code>edit_pages</code> capability.', 'admin-page-framework-loader' ),
                // 'capability'        => 'edit_pages', // this should be inherited from the section.
            )            
        );              
      
    }

}
