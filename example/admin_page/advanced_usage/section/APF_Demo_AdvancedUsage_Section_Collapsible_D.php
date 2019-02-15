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
class APF_Demo_AdvancedUsage_Section_Collapsible_D {
    
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
    public $sSectionID  = 'collapsible_section_d';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'        => $this->sSectionID,
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Custom Content', 'admin-page-framework-loader' ),
                'content'           => "<p>"
                    . __( 'This custom output is inserted with the <code>content</code> argument.', 'admin-page-framework-loader' )
                    . "</p>",
                'collapsible'       => array(
                    'collapse_others_on_expand' => false,
                    'toggle_all_button' => 'bottom-right',
                ),
            )
        );   
    }

}
