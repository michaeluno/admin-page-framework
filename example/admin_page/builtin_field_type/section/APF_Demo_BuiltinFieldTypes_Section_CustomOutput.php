<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_Section_CustomOutput {
    
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
    public $sSectionID  = 'custom_output';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Custom Section Output', 'admin-page-framework-loader' ),
                'content'       => "<p>" 
                        . __( 'This is inserted with the <code>content</code> argument.', 'admin-page-framework-loader' )
                    . "</p>",
                'description'   => __( 'The description part is reserved.', 'admin-page-framework-loader' ),
            )
        );   

    }

}