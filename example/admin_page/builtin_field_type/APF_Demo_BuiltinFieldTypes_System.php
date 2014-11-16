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

class APF_Demo_BuiltinFieldTypes_System {
 
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
    public $sTabSlug    = 'system';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'system';
    
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
                'title'         => __( 'System', 'admin-page-framework-demo' ),    
            )      
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'System Custom Field Type', 'admin-page-framework-demo' ),
                'description'       => __( 'Displays the system information.', 'admin-page-framework-demo' ),     
            )
        );        
        
        /**
         * The 'system' field type examples.
         */
        $oAdminPage->addSettingFields(     
            $this->sSectionID,  // target section id
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',     
                'title'         => __( 'System Information', 'admin-page-framework-demo' ),
                'data'          => array(
                    __( 'Custom Data', 'admin-page-framework-demo' )    => __( 'Her you can can isert own custom data with the data argument.', 'admin-page-framework-demo' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'name'  => '',
                ),
            ),
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',     
                'title'         => __( 'Saved Options', 'admin-page-framework-demo' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'Admin Page Framework'  => '', 
                    'WordPress'             => '', 
                    'PHP'                   => '', 
                    'MySQL'                 => '', 
                    'Server'                => '',
                ) 
                + $oAdminPage->oProp->aOptions,
                'attributes'    => array(
                    'name'  => '',
                    'rows'   => 20,
                ),        
            ),            
            array()

        );


    }
    
}