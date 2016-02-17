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
 * Adds a tab in a page.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_AdvancedUsage_Nesting {
       
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'nesting';
    
    /**
     * Sets up hooks.
     */
    public function __construct( $oFactory ) {
                              
        // Tab
        $oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Nesting', 'admin-page-framework-loader' ),
            )
        );
        
        add_action(
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToLoadTab' )
        );
        
    }
    
    /**
     * Adds form sections.
     * 
     * Triggered when the tab is loaded.
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        $_aClasses = array(
             'APF_Demo_AdvancedUsage_Nesting_SectionA',
             'APF_Demo_AdvancedUsage_Nesting_SectionB',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }

    }
    
}
